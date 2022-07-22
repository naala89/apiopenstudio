<?php

/**
 * Class InviteCreate.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\Invite;
use ApiOpenStudio\Db\InviteMapper;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\VarStoreMapper;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

/**
 * Class InviteCreate
 *
 * Processor class create an invite.
 */
class InviteCreate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Create a user invite',
        'machineName' => 'invite_create',
        'description' => 'Invite a user to ApiOpenStudio.',
        'menu' => 'Admin',
        'input' => [
            'email' => [
                'description' => 'The email of the user. Comma separated for multiple addresses.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

    /**
     * Config class.
     *
     * @var Config
     */
    private Config $settings;

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private UserMapper $userMapper;

    /**
     * Invite mapper class.
     *
     * @var InviteMapper
     */
    private InviteMapper $inviteMapper;

    /**
     * Var store mapper class.
     *
     * @var VarStoreMapper
     */
    private VarStoreMapper $varStoreMapper;

    /**
     * Account mapper class.
     *
     * @var AccountMapper
     */
    private AccountMapper $accountMapper;

    /**
     * Application mapper class.
     *
     * @var ApplicationMapper
     */
    private ApplicationMapper $applicationMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
        $this->userMapper = new UserMapper($db, $logger);
        $this->inviteMapper = new InviteMapper($db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $emailString = $this->val('email', true);
        $emails = [$emailString];
        if (strpos($emailString, ',') !== false) {
            $emails = explode(',', $emailString);
        }

        try {
            foreach ($emails as $email) {
                $user = $this->userMapper->findByEmail(trim($email));
                if (!empty($user->getUid())) {
                    throw new ApiException("User already exists: $email", 6, $this->id, 400);
                }
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        try {
            $account = $this->accountMapper->findByName('apiopenstudio');
            $application = $this->applicationMapper->findByAccidAppname($account->getAccid(), 'core');
            $var = $this->varStoreMapper->findByAppIdKey($application->getAppid(), 'user_invite_subject');
            $subject = $var->getVal();
            $var = $this->varStoreMapper->findByAppIdKey($application->getAppid(), 'user_invite_message');
            $message = $var->getVal();
            try {
                $domain = $this->settings->__get(['api', 'url']);
                $fromEmail = $this->settings->__get(['email', 'from', 'email']);
                $fromName = $this->settings->__get(['email', 'from', 'name']);
                $emailUsername = $this->settings->__get(['email', 'username']);
                $emailPassword = $this->settings->__get(['email', 'password']);
                $emailHost = $this->settings->__get(['email', 'host']);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            $message = str_replace('[domain]', $domain, $message);

            $transport = (new Swift_SmtpTransport($emailHost, 25))
                ->setUsername($emailUsername)
                ->setPassword($emailPassword);

            $result = [];
            foreach ($emails as $email) {
                $invite = $this->inviteMapper->findByEmail($email);
                if (!empty($invite->getIid())) {
                    $this->inviteMapper->delete($invite);
                    $result['resent'][] = $email;
                }

                $token = Utilities::randomString(32);
                $finalMessage = str_replace('[token]', $token, $message);
                $mailer = new Swift_Mailer($transport);
                $emailMessage = (new Swift_Message($subject))
                    ->setFrom([$fromEmail => $fromName])
                    ->setTo($email)
                    ->setBody($finalMessage)
                    ->setContentType('text/html');

                if ($mailer->send($emailMessage) > 0) {
                    $invite = new Invite(null, $email, $token);
                    $this->inviteMapper->save($invite);

                    $result['success'][] = "$email";
                } else {
                    $result['fail'][] = "$email";
                }
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new DataContainer($result, 'json');
    }
}
