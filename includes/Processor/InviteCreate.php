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
use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

/**
 * Class InviteCreate
 *
 * Processor class create an invite.
 */
class InviteCreate extends Core\ProcessorEntity
{
    /**
     * Config class.
     *
     * @var Core\Config
     */
    private Core\Config $settings;

    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    private Db\UserMapper $userMapper;

    /**
     * Invite mapper class.
     *
     * @var Db\InviteMapper
     */
    private Db\InviteMapper $inviteMapper;

    /**
     * Var store mapper class.
     *
     * @var Db\VarStoreMapper
     */
    private Db\VarStoreMapper $varStoreMapper;

    /**
     * Account mapper class.
     *
     * @var Db\AccountMapper
     */
    private Db\AccountMapper $accountMapper;

    /**
     * Application mapper class.
     *
     * @var Db\ApplicationMapper
     */
    private Db\ApplicationMapper $applicationMapper;

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
     * InviteCreate constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Core\Config();
        $this->userMapper = new Db\UserMapper($db, $logger);
        $this->inviteMapper = new Db\InviteMapper($db, $logger);
        $this->varStoreMapper = new Db\VarStoreMapper($db, $logger);
        $this->accountMapper = new Db\AccountMapper($db, $logger);
        $this->applicationMapper = new Db\ApplicationMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
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
                    throw new Core\ApiException("User already exists: $email", 6, $this->id, 400);
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
            } catch (Core\ApiException $e) {
                throw new Core\ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
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

                $token = Core\Utilities::randomString(32);
                $finalMessage = str_replace('[token]', $token, $message);
                $mailer = new Swift_Mailer($transport);
                $emailMessage = (new Swift_Message($subject))
                    ->setFrom([$fromEmail => $fromName])
                    ->setTo($email)
                    ->setBody($finalMessage)
                    ->setContentType('text/html');

                if ($mailer->send($emailMessage) > 0) {
                    $invite = new Db\Invite(null, $email, $token);
                    $this->inviteMapper->save($invite);

                    $result['success'][] = "$email";
                } else {
                    $result['fail'][] = "$email";
                }
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new Core\DataContainer($result, 'json');
    }
}
