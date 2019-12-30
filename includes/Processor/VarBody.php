<?php

/**
 * Post variable
 *
 * METADATA
 * {
 *    "type":"postVar",
 *    "meta":{
 *      "var":<processor|mixed>,
 *    }
 *  }
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Debug;
use phpDocumentor\Reflection\Types\Integer;

class VarBody extends VarMixed
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (Body)',
        'machineName' => 'var_body',
        'description' => 'Fetch the entire body of a post.',
        'menu' => 'Primitive',
        'input' => [
            'type' => [
                // phpcs:ignore
                'description' => 'The expected data type in the body. If type is not defined, then GaterData will attempt automatically set the data type.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [
                    'boolean',
                    'integer',
                    'float',
                    'json',
                    'html',
                    'xml',
                    'text',
                    'image',
                    'file',
                ],
                'default' => '',
            ],
            'nullable' => [
                'description' => 'Throw an error if the body is empty.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => 'true',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $type = $this->val('type', true);
        $nullable = $this->val('nullable', true);
        $data = file_get_contents('php://input');

        if (!$nullable && empty($data)) {
            throw new ApiException("Body is empty", 6, $this->id);
        }

        return new Core\DataContainer($data, $type);
    }
}
