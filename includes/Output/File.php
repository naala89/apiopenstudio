<?php

namespace Gaterdata\Output;

class File extends Output
{
    /**
     * {@inheritDoc}
     */
    protected $header = 'Content-Type: application/octet-stream';

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'File',
        'machineName' => 'file',
        'description' => 'Output a file.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output suggested filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'gaterdata.txt',
            ],
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
     * Set the Content-Type header .
     */
    public function setHeader()
    {
        parent::setHeader();
        $filename = $this->val('filename', true);
        header("Content-Disposition: attachment; filename='$filename'");
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
        return \json_encode($data);
    }

    protected function fromImage(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromJson(&$data)
    {
        return is_string($data) ? $data : \json_encode($data);
    }
}
