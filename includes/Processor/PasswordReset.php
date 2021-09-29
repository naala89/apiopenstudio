<?php

/**
 * Class PasswordReset.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
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
use ApiOpenStudio\Db;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

/**
 * Class PasswordReset
 *
 * Processor class te reset a user's password.
 */
class PasswordReset extends Core\ProcessorEntity
{
    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    private Db\UserMapper $userMapper;

    /**
     * Config class.
     *
     * @var Core\Config
     */
    private Core\Config $settings;

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
        'name' => 'Password reset',
        'machineName' => 'password_reset',
        'description' => 'Reset a users password',
        'menu' => 'Admin',
        'input' => [
            'email' => [
                'description' => 'The users email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'token' => [
                'description' => 'The password reset token.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'The new password.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * PasswordReset constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\StreamLogger $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\StreamLogger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db, $logger);
        $this->varStoreMapper = new Db\VarStoreMapper($db, $logger);
        $this->settings = new Core\Config();
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

            $account = $this->accountMapper->findByName('apiopenstudio');
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
