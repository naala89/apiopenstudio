<?php

/**
 * Class Cast.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\ConvertToArrayTrait;
use ApiOpenStudio\Core\ConvertToBooleanTrait;
use ApiOpenStudio\Core\ConvertToFloatTrait;
use ApiOpenStudio\Core\ConvertToHtmlTrait;
use ApiOpenStudio\Core\ConvertToImageTrait;
use ApiOpenStudio\Core\ConvertToIntegerTrait;
use ApiOpenStudio\Core\ConvertToJsonTrait;
use ApiOpenStudio\Core\ConvertToTextTrait;
use ApiOpenStudio\Core\ConvertToXmlTrait;
use ApiOpenStudio\Core\DetectTypeTrait;

/**
 * Class Cast
 *
 * Processor class to cast an input value (or DataContainer) to another data type.
 */
class Cast extends Core\ProcessorEntity
{
    // phpcs:ignore
    use DetectTypeTrait, ConvertToBooleanTrait, ConvertToIntegerTrait, ConvertToFloatTrait, ConvertToTextTrait, ConvertToArrayTrait, ConvertToJsonTrait, ConvertToXmlTrait, ConvertToHtmlTrait, ConvertToImageTrait {
        ConvertToJsonTrait::xml2json insteadof ConvertToArrayTrait;
    }

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Cast',
        'machineName' => 'cast',
        'description' => 'Change the data type of an input data.',
        'menu' => 'Data operation',
        'input' => [
            'data' => [
                'description' => 'The input data that needs to be cast.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'data_type' => [
                'description' => 'The data type to cast to.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [
                    'boolean',
                    'integer',
                    'float',
                    'text',
                    'array',
                    'json',
                    'xml',
                    'html',
                    'image',
                    'file',
                    'empty',
                ],
                'default' => '',
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
    public function process(): Core\DataContainer
    {
        parent::process();

        $container = $this->val('data');
        $dataType = $this->val('data_type', true);

        try {
            $method = 'from' . ucfirst(strtolower($container->getType())) . 'To' . ucfirst(strtolower($dataType));
            if (!method_exists(__CLASS__, $method)) {
                throw new ApiException("unable to cast data, method not found: $method");
            }
            $container->setData($this->$method($container->getData()));
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), 6, $this->id, 400);
        }

        return $container;
    }
}
