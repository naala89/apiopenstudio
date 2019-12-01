<?php

/**
 * Post variable
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class VarFile extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
    'name' => 'Var (File)',
        'machineName' => 'var_file',
        'description' => 'Fetch file/s from a request.',
        'menu' => 'Primitive',
        'input' => [
            'key' => [
                // phpcs:ignore
                'description' => 'The name of the file/s in the request. If empty, all files from the request will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the POST file does not exist.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $key = $this->val('key', true);
        $files = $this->request->getFiles();
        $nullable = filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN);

        if (!empty($key)) {
            if (empty($files) || !isset($files[$key])) {
                if ($nullable) {
                    return new Core\DataContainer([], 'array');
                }
                throw new Core\ApiException("file ($key) not received", 5, $this->id, 417);
            }
            return new Core\DataContainer($files[$key], 'array');
        }

        if (!$nullable && empty($files)) {
            throw new Core\ApiException("files not received", 5, $this->id, 417);
        }

        return new Core\DataContainer($files, 'array');
    }
}
