<?php
/**
 * Class VarUri.
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

/**
 * Class VarUri
 *
 * Processor class to return a value from the request URI.
 */
class VarUri extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (URI)',
        'machineName' => 'var_uri',
        // phpcs:ignore
        'description' => 'A url-decoded value from the request URI. It fetches the value of a particular param in the URI, based on the index value.',
        'menu' => 'Primitive',
        'input' => [
            'index' => [
                'description' => 'The index of the variable, starting with 0 after the client ID, request path.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
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
        
        $index = intval($this->val('index', true));
        $args = $this->request->getArgs();

        if (!isset($args[$index])) {
            return new Core\DataContainer('', 'string');
        }

        return new Core\DataContainer(urldecode($args[$index]));
    }
}
