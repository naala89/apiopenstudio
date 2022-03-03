<?php

/**
 * Class Email.
 *
 * @package    ApiOpenStudio\Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Output;

use ApiOpenStudio\Core;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Class Email
 *
 * Outputs the results as an email.
 */
class Email extends Output
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Email',
        'machineName' => 'email',
        'description' => 'Output the results of the resource into an email.',
        'menu' => 'Output',
        'input' => [
            'to' => [
                'description' => 'The destination emails for the output.',
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'from_email' => [
                'description' => 'The from email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'from_name' => [
                'description' => 'The from name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'subject' => [
                'description' => 'The subject for the email.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'message' => [
                'description' => 'The body of the email.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'format' => [
                'description' => 'The format of the body of the email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => ['html', 'plain', 'text', 'json', 'xml'],
                'default' => 'html',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if email send failed.
     */
    public function process(): Core\DataContainer
    {
        $this->logger->info('api', 'Output: ' . $this->details()['machineName']);
        $config = new Core\Config();

        $to = $this->val('to', true);
        $fromEmail = $this->val('from_email', true);
        $fromEmail = empty($fromEmail) ? $config->__get(['email', 'from', 'email']) : $fromEmail;
        $fromName = $this->val('from_name', true);
        $fromName = empty($fromName) ? $config->__get(['email', 'from', 'name']) : $fromName;
        $subject = $this->val('subject', true);
        $message = $this->val('message', true);
        $format = $this->val('format', true);

        $class = '\\ApiOpenStudio\\Output\\' . $format;
        $obj = new $class($message, 200, '');
        $message = $obj->getData();

        $transport = (new Swift_SmtpTransport($config->__get(['email', 'host']), 25))
            ->setUsername($config->__get(['email', 'username']))
            ->setPassword($config->__get(['email', 'password']));
        $mailer = new Swift_Mailer($transport);
        $email = (new Swift_Message($subject))
            ->setFrom([$fromEmail => $fromName])
            ->setTo($to)
            ->setBody($message);
        $result = $mailer->send($email);

        if (!$result) {
            throw new Core\ApiException('Email message send failed', 1, $this->id, 500);
        }

        return new Core\DataContainer("$result messages sent.", 'text');
    }

    /**
     * Cast the data to email.
     *
     * Nothing to do here.
     */
    protected function castData(): void
    {
    }
}
