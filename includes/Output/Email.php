<?php
/**
 * Class Email.
 *
 * @package    ApiOpenStudio
 * @subpackage Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Output;

use ApiOpenStudio\Config;
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
     * Default email values.
     *
     * @var array default email values.
     */
    private $defaults = array(
        'subject' => 'Datagator resourve result',
        'from' => 'resource@datagator.com.au',
        'format' => 'json',
    );

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Email',
        'machineName' => 'email',
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

        $class = '\\ApiOpenStudio\\Output\\' . $format;
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
     *
     * @param string $data Incoming data.
     *
     * @return void
     */
    protected function fromXml(string &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param integer $data Incoming data.
     *
     * @return void
     */
    protected function fromInteger(int &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $data Incoming data.
     *
     * @return void
     */
    protected function fromBoolean(bool &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data Incoming data.
     *
     * @return void
     */
    protected function fromFloat(float &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Incoming data.
     *
     * @return void
     */
    protected function fromJson(string &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Incoming data.
     *
     * @return void
     */
    protected function fromHtml(string &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Incoming data.
     *
     * @return void
     */
    protected function fromText(string &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data Incoming data.
     *
     * @return void
     */
    protected function fromArray(array &$data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data Incoming data.
     *
     * @return void
     */
    protected function fromImage(&$data)
    {
    }
}
