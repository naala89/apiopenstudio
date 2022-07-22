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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\OutputEntity;
use ApiOpenStudio\Core\OutputRemote;
use ApiOpenStudio\Core\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email as SymfonyEmail;

/**
 * Class Email
 *
 * Outputs the results as an email.
 */
class Email extends OutputRemote
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
                'description' => 'The destination email/s for the output.',
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'cc' => [
                'description' => 'The cc email/s for the output.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'bcc' => [
                'description' => 'The bcc email/s for the output.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'from' => [
                'description' => 'The from email/s.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'reply_to' => [
                'description' => 'The reply-to email.',
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
            'text' => [
                'description' => 'The body "text" fallback if using HTML email.',
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
     * @var Config Config object.
     */
    protected Config $settings;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?MonologWrapper $logger, $data)
    {
        parent::__construct($meta, $request, $logger, $data);
        $this->settings = new Config();
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if email send failed.
     */
    public function process(): DataContainer
    {
        OutputEntity::process();

        $to = $this->val('to', true);
        $cc = $this->val('cc', true);
        $bcc = $this->val('to', true);
        $from = $this->val('from', true);
        $replyTo = $this->val('reply_to', true);
        $subject = $this->val('subject', true);
        $text = $this->val('text', true);

        try {
            $dsn = $this->settings->__get(['email', 'dsn']);
            $defaultFrom = $this->settings->__get(['email', 'from']);
            $defaultReplyTo = $this->settings->__get(['email', 'reply_to']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $transport = Transport::fromDSN($dsn);
        $mailer = new Mailer($transport);
        $email = new SymfonyEmail();

        $email->subject($subject);

        if (empty($from)) {
            $from = $defaultFrom;
            if (empty($replyTo)) {
                $replyTo = $defaultReplyTo;
            }
        }
        if (empty($replyTo)) {
            $replyTo = $from;
        }

        if (is_array($from)) {
            foreach ($from as $item) {
                $email->addFrom($item);
            }
        } else {
            $email->from($from);
        }

        if (is_array($to)) {
            foreach ($to as $item) {
                $email->to($item);
            }
        } else {
            $email->to($to);
        }

        if (!empty($replyTo)) {
            $email->replyTo($replyTo);
        }

        if (!empty($cc)) {
            if (is_array($cc)) {
                foreach ($cc as $item) {
                    $email->addCc($item);
                }
            } else {
                $email->cc($cc);
            }
        }

        if (!empty($bcc)) {
            if (is_array($bcc)) {
                foreach ($bcc as $item) {
                    $email->addBcc($item);
                }
            } else {
                $email->bcc($bcc);
            }
        }

        if ($this->data->getType() == 'html') {
            $email->html($this->data->getData());
            if (empty($text)) {
                $email->text($this->data->getData());
            } else {
                $email->text($text);
            }
        } else {
            $email->text($this->data->getData());
        }

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new ApiException($e->getMessage(), 8, $this->id, 500);
        }
        return new DataContainer(true, 'boolean');
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
