<?php

namespace Gaterdata\Output;

use Gaterdata\Config;
use Gaterdata\Core;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Class Email
 * @package Gaterdata\Output
 */
class Email extends Output
{
    /**
     * @var array default email values.
     */
    private $defaults = array(
        'subject' => 'Datagator resourve result',
        'from' => 'resource@datagator.com.au',
        'format' => 'json',
    );

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Email',
        'machineName' => 'output_email',
        'description' => 'Output the results of the resource into an email.',
        'menu' => 'Output',
        'input' => [
            'to' => [
                'description' => 'The destination emails for the output.',
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'from_email' => [
                'description' => 'The from email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'from_name' => [
                'description' => 'The from name.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'subject' => [
                'description' => 'The subject for the email.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'message' => [
                'description' => 'The body of the email.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'format' => [
                'description' => 'The format of the body of the email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['html', 'plain', 'text', 'json', 'xml'],
                'limitValues' => [],
                'default' => 'html',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Output: ' . $this->details()['machineName']);

        $to = $this->val('to', true);
        $fromEmail = $this->val('from_email', true);
        $fromEmail = empty($fromEmail) ? Core\Config::__get(['email', 'from', 'email']) : $fromEmail;
        $fromName = $this->val('from_name', true);
        $fromName = empty($fromName) ? Core\Config::__get(['email', 'from', 'name']) : $fromName;
        $subject = $this->val('subject', true);
        $message = $this->val('message', true);
        $format = $this->val('format', true);

        $class = '\\Gaterdata\\Output\\' . $format;
        $obj = new $class($message, 200, '');
        $message = $obj->getData();

        $transport = (new Swift_SmtpTransport(Core\Config::__get(['email', 'host']), 25))
            ->setUsername(Core\Config::__get(['email', 'username']))
            ->setPassword(Core\Config::__get(['email', 'password']));
        $mailer = new Swift_Mailer($transport);
        $email = (new Swift_Message($subject))
            ->setFrom([$fromEmail => $fromName])
            ->setTo($to)
            ->setBody($message);
        $result = $mailer->send($email);

        if (!$result) {
            throw new Core\ApiException('Email message send failed', 1, $this->id, 500);
        }
        return "$result messages sent.";
    }

    /**
     * {@inheritDoc}
     */
    protected function getData()
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromXml(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromInteger(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromBoolean(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromFloat(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromJson(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromHtml(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromText(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromArray(&$data)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function fromImage(&$data)
    {
    }
}
