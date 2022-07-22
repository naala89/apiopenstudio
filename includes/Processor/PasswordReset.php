<?php

/**
 * Class PasswordReset.
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
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\VarStoreMapper;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

/**
 * Class PasswordReset
 *
 * Processor class te reset a user's password.
 */
class PasswordReset extends ProcessorEntity
{
    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private UserMapper $userMapper;

    /**
     * Config class.
     *
     * @var Config
     */
    private Config $settings;

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
                'default' => null,
            ],
            'token' => [
                'description' => 'The password reset token.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'password' => [
                'description' => 'The new password.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
        $this->settings = new Config();
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

        $email = $this->val('email', true);
        $token = $this->val('token', true);
        $password = $this->val('password', true);

        if (!empty($email)) {
            // Initial password reset request.
            // Set the token and send the email.
            try {
                $user = $this->userMapper->findByEmail($email);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($user->getUid())) {
                return new DataContainer('true', 'text');
            }

            $token = Utilities::randomString(32);

            try {
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
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            $emailMessage = (new Swift_Message($subject))
                ->setFrom([$fromEmail => $fromName])
                ->setTo($email)
                ->setBody($message)
                ->setContentType('text/html');
            if ($mailer->send($emailMessage) == 0) {
                throw new ApiException("Reset password email send failed", 6, $this->id, 400);
            }

            $user->setPasswordReset($token);
            $user->setPasswordResetTtl(Utilities::datePhp2mysql(strtotime("+15 minute")));
            try {
                $this->userMapper->save($user);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }

            return new DataContainer('true', 'text');
        }

        // Final password reset step - we should have a password and token.
        if (empty($token) || empty($password)) {
            return new DataContainer('true', 'text');
        }

        try {
            $user = $this->userMapper->findByPasswordToken($token);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($user->getUid())) {
            return new DataContainer('true', 'text');
        }

        try {
            $user->setPassword($password);
            $this->userMapper->save($user);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new DataContainer('true', 'text');
    }
}
