<?php

/**
 * Password reset.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class PasswordReset extends Core\ProcessorEntity
{
    /**
     * @var Db\UserMapper
     */
    private $userMapper;

    /**
     * @var Core\Config
     */
    private $settings;

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
        'name' => 'Password reset',
        'machineName' => 'password_reset',
        'description' => 'Reset a users password',
        'menu' => 'Admin',
        'input' => [
            'email' => [
                'description' => 'The users email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'token' => [
                'description' => 'The password reset token.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'The new password.',
                'cardinality' => [0, 1],
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
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->userMapper = new Db\UserMapper($db);
        $this->varStoreMapper = new Db\VarStoreMapper($db);
        $this->settings = new Core\Config();
        $this->accountMapper = new Db\AccountMapper($db);
        $this->applicationMapper = new Db\ApplicationMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $email = $this->val('email', true);
        $token = $this->val('token', true);
        $password = $this->val('password', true);

        if (!empty($email)) {
            // Initial password reset request.
            // Set the token and send the email.
            $user = $this->userMapper->findByEmail($email);
            if (empty($user->getUid())) {
                return new Core\DataContainer('true', 'text');
            }

            $token = Core\Utilities::randomString(32);

            $account = $this->accountMapper->findByName('gaterdata');
            $application = $this->applicationMapper->findByAccidAppname($account->getAccid(), 'core');
            $var = $this->varStoreMapper->findByAppIdKey($application->getAppid(), 'password_reset_subject');
            $subject = $var->getVal();
            $var = $this->varStoreMapper->findByAppIdKey($application->getAppid(), 'password_reset_message');
            $message = $var->getVal();
            $domain = $this->settings->__get(['admin', 'url']);
            $message = str_replace('[domain]', $domain, $message);
            $message = str_replace('[token]', $token, $message);

            $transport = (new Swift_SmtpTransport($this->settings->__get(['email', 'host']), 25))
                ->setUsername($this->settings->__get(['email', 'username']))
                ->setPassword($this->settings->__get(['email', 'password']));
            $mailer = new Swift_Mailer($transport);
            $fromEmail = $this->settings->__get(['email', 'from', 'email']);
            $fromName = $this->settings->__get(['email', 'from', 'name']);
            $emailMessage = (new Swift_Message($subject))
                ->setFrom([$fromEmail => $fromName])
                ->setTo($email)
                ->setBody($message)
                ->setContentType('text/html');
            if ($mailer->send($emailMessage) == 0) {
                throw new Core\ApiException("Reset password email send failed", 6, $this->id, 400);
            }

            $user->setPasswordReset($token);
            $user->setPasswordResetTtl(Core\Utilities::datePhp2mysql(strtotime("+15 minute")));
            $this->userMapper->save($user);

            return new Core\DataContainer('true', 'text');
        }

        // Final password reset step - we should have a password and token.
        if (empty($token) || empty($password)) {
            return new Core\DataContainer('true', 'text');
        }

        $user = $this->userMapper->findByPasswordToken($token);
        if (empty($user->getUid())) {
            return new Core\DataContainer('true', 'text');
        }

        $user->setPassword($password);
        $this->userMapper->save($user);

        return new Core\DataContainer('true', 'text');
    }

}
