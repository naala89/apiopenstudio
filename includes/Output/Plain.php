<?php

namespace Gaterdata\Output;

class Plain extends Text
{
    /**
     * {@inheritDoc}
     */
    protected $header = 'Content-Type:text/plain';

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Plain',
        'machineName' => 'output_plain',
        'description' => 'Output in the results of the resource in plain-text format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'destination' => [
                'description' => 'Destination URLs for the output.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'method' => [
                'description' => 'HTTP delivery method when sending output. Only used in the output section.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post'],
                'default' => '',
            ],
            'options' => [
                // phpcs:ignore
                'description' => 'Extra Curl options to be applied when sent to the destination (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => ['field'],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Output: ' . $this->details()['machineName']);
        return parent::process();
    }

    /**
     * {@inheritDoc}
     */
    protected function fromBoolean(&$data)
    {
        return $data ? 'true' : 'false';
    }

    /**
     * {@inheritDoc}
     */
    protected function fromInteger(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromFloat(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromXml(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromHtml(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromText(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromArray(&$data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromJson(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromImage(&$data)
    {
        return $data;
    }
}
