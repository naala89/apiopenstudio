<?php

/**
 * Class JsonPath.
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
use JsonPath\InvalidJsonException;
use JsonPath\JsonObject;

/**
 * Class JsonPath
 *
 * Processor class to fetch/set values in JSON or an array
 */
class JsonPath extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Json Path',
        'machineName' => 'json_path',
        'description' => <<<DESCRIPTION
Get, set, add or remove values in a JSON string or an array (decoded JSON string).

Examples:

    Source JSON:
        {
            "store": {
                "book": [
                    {
                        "category": "fiction",
                        "author": "Evelyn Waugh",
                        "title": "Sword of Honour",
                        "price": 12.99,
                        "available": false
                    }
                ],
                "bicycle": {
                    "color": "red",
                    "price": 19.95,
                    "available": true
                }
            },
            "authors": [
                "Nigel Rees",
                "Evelyn Waugh",
                "Herman Melville",
                "J. R. R. Tolkien"
            ]
        }

    Get operation:
    
        processor: json_path
        id: get_example
        data: json or array input
        operation: get
        stripslashes: true
        escape_wrapping_quotes: true
        expression: "$.store.bicycle.price"
        
        Result:
            15.95
    
    Set operation:
    
        processor: json_path
        id: set_example
        data: json or array input
        operation: set
        expression: "$.store.bicycle.price"
        value: 100000000.95
        
        Result:
            {
                "store": {
                    "book": [
                        {
                            "category": "fiction",
                            "author": "Evelyn Waugh",
                            "title": "Sword of Honour",
                            "price": 12.99,
                            "available": false
                        }
                    ],
                    "bicycle": {
                        "color": "red",
                        "price": 100000000.95,
                        "available": true
                    }
                },
                "authors": [
                    "Nigel Rees",
                    "Evelyn Waugh",
                    "Herman Melville",
                    "J. R. R. Tolkien"
                ]
            }
    
    Add operation:
    
        processor: json_path
        id: add_example
        data: json or array input
        operation: add
        expression: "$.store.book"
        value: "{'category':'drama','author': 'Jane Austen','title': 'Killing Heidi','price': 0.99,'available': true}"
        
        Result:
            {
                "store": {
                    "book": [
                        {
                            "category": "fiction",
                            "author": "Evelyn Waugh",
                            "title": "Sword of Honour",
                            "price": 12.99,
                            "available": false
                        }, {
                            'category': 'drama',
                            'author': 'Jane Austen',
                            'title': 'Killing Heidi',
                            'price': 0.99,
                            'available': true
                        }
                    ],
                    "bicycle": {
                        "color": "red",
                        "price": 100000000.95,
                        "available": true
                    }
                },
                "authors": [
                    "Nigel Rees",
                    "Evelyn Waugh",
                    "Herman Melville",
                    "J. R. R. Tolkien"
                ]
            }
    
    Remove operation:
    
        processor: json_path
        id: remove_example
        data: json or array input
        operation: remove
        expression: "$.store.book"
        field_name: title
        
        Result:
            {
                "store": {
                    "book": [
                        {
                            "category": "fiction",
                            "author": "Evelyn Waugh",
                            "price": 12.99,
                            "available": false
                        }
                    ],
                    "bicycle": {
                        "color": "red",
                        "price": 19.95,
                        "available": true
                    }
                },
                "authors": [
                    "Nigel Rees",
                    "Evelyn Waugh",
                    "Herman Melville",
                    "J. R. R. Tolkien"
                ]
            }
DESCRIPTION,
        'menu' => 'Data operation',
        'input' => [
            'data' => [
                'description' => 'The input JSON or array.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json', 'array'],
                'limitValues' => [],
                'default' => null,
            ],
            'expression' => [
                'description' => 'The JSON path expression.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '$.*',
            ],
            'operation' => [
                'description' => 'The operation ("get", "set", "add" or "remove").',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'set', 'add', 'remove'],
                'default' => 'get',
            ],
            'remove_wrapping_quotes' => [
                // phpcs:ignore
                'description' => 'By default, JSON will return strings wrapped in quotes if the result is a literal string (i.e. N/V pair value). This will result in a data_type of JSON wrapped in double quotes and you will be unable to convert to a literal string or number datatype',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'stripslashes' => [
                // phpcs:ignore
                'description' => 'By default, JSON will escape special characters with a backslash. Setting this to true will allow you to remove the escaping',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'field_name' => [
                // phpcs:ignore
                'description' => 'The data name/key to insert or delete (used for "add" "remove" operations). If empty and the operation is "add", then is similar to append to array.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
            ],
            'value' => [
                'description' => 'The data value to insert (used for "set" or "add" operations).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
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

        $data = $this->val('data', true);
        $expression = $this->val('expression', true);
        $operation = $this->val('operation', true);
        $value = $this->val('value');
        $field_name = $this->val('field_name', true);
        $remove_wrapping_quotes = $this->val('remove_wrapping_quotes', true);
        $stripslashes = $this->val('stripslashes', true);

        try {
            $jsonObject = new JsonObject($data, true);
        } catch (InvalidJsonException $e) {
            throw new Core\ApiException($e->getMessage(), 6, $this->id, 400);
        }

        if ($operation == 'get') {
            $result = $jsonObject->get($expression);
            if ($result === false) {
                $result = null;
            } elseif (!is_numeric($result)) {
                $result = json_encode($result);
            }
        } elseif ($operation == 'set') {
            $result = $jsonObject->set($expression, $value->getData())->getJson();
        } elseif ($operation == 'add') {
            if ($value->getType() == 'json') {
                $value = json_decode($value->getData());
            } else {
                $value = $value->getData();
            }
            $result = $jsonObject->add($expression, $value, $field_name)->getJson();
        } else {
            $result = $jsonObject->remove($expression, $field_name)->getJson();
        }

        if ($remove_wrapping_quotes === true && $result !== null) {
            $result = trim($result, '"');
        }
        if ($stripslashes === true && $result !== null) {
            $result = stripslashes($result);
        }

        return new Core\DataContainer($result);
    }
}
