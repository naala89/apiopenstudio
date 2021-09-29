<?php

/**
 * Class ConvertToJson.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core;
use ApiOpenStudio\Output\Json;

/**
 * Class ConvertToJson
 *
 * Processor class to convert data to JSON.
 */
class ConvertToJson extends Json
{
    /**
     * {@inheritDoc}
     *
     * @var mixed The output data.
     */
    protected $data;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Convert to JSON',
        'machineName' => 'convert_to_json',
        // phpcs:ignore
        'description' => 'Convert an input data into a different JSON data type (i.e. array, XML or object into a JSON string).',
        'menu' => 'Data operation',
        'input' => [
            'source' => [
                'description' => 'The source data.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ConvertToJson constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\StreamLogger $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\StreamLogger $logger)
    {
        Core\ProcessorEntity::__construct($meta, $request, $db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $this->data = $this->val('source');

        return new Core\DataContainer($this->getData(), 'json');
    }
}
