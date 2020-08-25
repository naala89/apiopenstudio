<?php

/**
 * User invite.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class InviteCreate extends Core\ProcessorEntity
{
    /**
     * @var Config
     */
    private $settings;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var Db\InviteMapper
     */
    private $inviteMapper;

    /**
     * @var Db\VarStoreMapper
     */
    private $varStoreMapper;

    /**
     * @var Db\AccountMapper
     */
    private $accountMapper;

    /**
     * @var Db\ApplicationMapper
     */
    private $applicationMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Create a user invite',
        'machineName' => 'invite_create',
        'description' => 'Invite a user to GaterData.',
        'menu' => 'Admin',
        'input' => [
            'email' => [
                'description' => 'The email of the user. Comma separated for multiple addresses.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db, $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Core\Config();
        $this->userMapper = new Db\UserMapper($db);
        $this->inviteMapper = new Db\InviteMapper($db);
        $this->varStoreMapper = new Db\VarStoreMapper($db);
        $this->accountMapper = new Db\AccountMapper($db);
        $this->applicationMapper = new Db\ApplicationMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $emailString = $this->val('email', true);

        $emails = [$emailString];
        if (strpos($emailString, ',') !== false) {
            $emails = explode(',', $emailString);
        }

        foreach ($emails as $key => $email) {
            $user = $this->userMapper->findByEmail(trim($email));
            if (!empty($user->getUid())) {
                throw new Core\ApiException("User already exists: $email", 6, $this->id, 400);
            }
        }

        $account = $this->accountMapper->findByName('gaterdata');
        $application = $this->applicationMapper->findByAccidAppname($account->getAccid(), 'core');
        $var = $this->varStoreMapper->findByAppIdKey($application->getAppid(), 'user_invite_subject');
        $subject = $var->getVal();
        $var = $this->varStoreMapper->findByAppIdKey($application->getAppid(), 'user_invite_message');
        $message = $var->getVal();
        $domain = $this->settings->__get(['admin', 'url']);
        $message = str_replace('[domain]', $domain, $message);
        $fromEmail = $this->settings->__get(['email', 'from', 'email']);
        $fromName = $this->settings->__get(['email', 'from', 'name']);

        $transport = (new Swift_SmtpTransport($this->settings->__get(['email', 'host']), 25))
            ->setUsername($this->settings->__get(['email', 'username']))
            ->setPassword($this->settings->__get(['email', 'password']));


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
            }
            else {
                $result['fail'][] = "$email";
            }
        }

        return new Core\DataContainer($result, 'json');
    }
}
