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
                'description' => 'The output filename.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
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
        $filename = $this->val('filename', true);
        header("Content-Disposition: attachment; filename='$filename'");
        parent::setHeader();
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
