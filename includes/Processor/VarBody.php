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
                'description' => 'The expected data type in the body. A value of "auto" will auto detect the content type, but this can only detect boolean, integer, float, valid json, valid xml, valid html or text.',
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
                    'auto',
                ],
                'default' => 'auto',
            ],
            'nullable' => [
                'description' => 'Thow an error if the body is empty.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [
                    true,
                    false,
                ],
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
        if ($type != 'auto') {
            return new Core\DataContainer($data, $type);
        }

        $type = $this->detectType($data);
        switch ($type) {
            case 'boolean':
                return new Core\DataContainer(filter_var($data, FILTER_VALIDATE_BOOLEAN), $type);
                break;
            case 'integer':
                return new Core\DataContainer(intval($data), $type);
                break;
            case 'float':
                return new Core\DataContainer(floatval($data), $type);
                break;
            case 'json':
            case 'html':
            case 'xml':
            case 'text':
            case 'image':
            case 'file':
                return new Core\DataContainer($data, $type);
                break;
        }
    }
}
