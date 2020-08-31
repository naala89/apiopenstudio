<?php
/**
 * Class VarBody.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Debug;

/**
 * Class VarBody
 *
 * Processor class to return the contents of the body as a variable.
 */
class VarBody extends VarMixed
{
    /**
     * @var array Details of the processor.
     *
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
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $type = $this->val('type', true);
        $nullable = $this->val('nullable', true);
        $data = file_get_contents('php://input');

        if (!$nullable && empty($data)) {
            throw new ApiException("Body is empty", 6, $this->id);
        }

        return new Core\DataContainer($data, $type);
    }
}
