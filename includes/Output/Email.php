<?php

namespace Gaterdata\Output;

use Gaterdata\Config;
use Gaterdata\Core;

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
        'machineName' => 'email',
        'description' => 'Output in the results of the resource into an email.',
        'menu' => 'Output',
        'input' => [
            'to' => [
                'description' => 'Destination emails for the output.',
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'from' => [
                'description' => 'From email address for the email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'subject' => [
                'description' => 'Subject for the email.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'GaterData',
            ],
            'format' => [
                'description' => 'Format for the results to be formatted into.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['json', 'html', 'xml', 'text'],
                'default' => 'json'
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::message("Output Email");

        $mail = new \PHPMailer();
        $to = $this->val('to', true);
        $from = !empty($this->meta->from) ? $this->val('from', true) : $this->defaults['from'];
        $subject = !empty($this->meta->subject) ?
            $this->val('subject', true) : $this->defaults['subject'];
        $format = $this->val('format', true);
        $class = '\\Gaterdata\\Output\\' . $format;
        $obj = new $class($this->data, 200, '');
        $data = $obj->getData();
        $altData = $data;
        $html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" ';
        $html .= '"http://www.w3.org/TR/html4/loose.dtd">';
        $html .= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
        $html .= '<title>PHPMailer Test</title></head><body>';
        $html .= '<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;"><p>';
        switch ($format) {
            case 'html':
                $html .= $data . '</p>';
            break;
            case 'json':
            case 'text':
            default:
                $html .= $data . '</p></div></body></xml>';
            break;
        }

        // email params
        switch (Config::$emailService) {
            case 'smtp':
                $mail->isSMTP();
                $mail->Host = Config::$emailHost;
                $mail->SMTPAuth = Config::$emailAuth;
                $mail->Username = Config::$emailHost;
                $mail->Password = Config::$emailPass;
                $mail->SMTPSecure = Config::$emailSecure;
                $mail->Port = Config::$emailPort;
            break;
            case 'sendmail':
                $mail->isSendmail();
            break;
            case 'qmail':
                $mail->isQmail();
            break;
            case 'mail':
            default:
            break;
        }

        $mail->setFrom($from);
        if (!is_array($to)) {
            $mail->addAddress($to);
        } else {
            foreach ($to as $email) {
                $mail->addAddress($email);
            }
        }
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->AltBody = $altData;

        if (!$mail->send()) {
            $message = 'email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            throw new Core\ApiException($message, 1, $this->id, 500);
        }

        return true;
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
