<?php

/**
 * Class ResourceValidator.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use ADOConnection;
use ADODB_mysqli;

/**
 * Class ResourceValidator
 *
 * Validate a resource definition.
 */
class ResourceValidator
{
    /**
     * Processor helper class.
     *
     * @var ProcessorHelper
     */
    protected ProcessorHelper $helper;

    /**
     * DB connection class.
     *
     * @var ADODB_mysqli
     */
    private $db;

    /**
     * Logging class.
     *
     * @var StreamLogger
     */
    private StreamLogger $logger;

    /**
     * Constructor. Store processor metadata and request data in object.
     *
     * @param ADOConnection $db Database.
     * @param StreamLogger $logger Logger.
     */
    public function __construct(ADOConnection $db, StreamLogger $logger)
    {
        $this->helper = new ProcessorHelper();
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Validate input data is well formed.
     *
     * @param array $data Resource metadata array.
     *
     * @return void
     *
     * @throws ApiException Input data not well formed.
     */
    public function validate(array $data)
    {
        $this->logger->notice('api', 'Validating the new resource...');
        // Check mandatory elements exists in data.
        if (empty($data)) {
            $message = 'Empty resource uploaded';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (!isset($data['process'])) {
            $message = 'Missing process in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }

        // Validate for identical IDs.
        $this->validateIdenticalIds($data);

        // Validate dictionaries.
        if (isset($data['security'])) {
            $this->validateDetails($data['security']);
        }
        if (!empty($data['fragments'])) {
            if (!Utilities::isAssoc($data['fragments'])) {
                $message = 'Invalid fragments structure in new resource';
                $this->logger->error('api', $message);
                throw new ApiException($message, 6, -1, 400);
            }
            foreach ($data['fragments'] as $fragKey => $fragVal) {
                $this->validateDetails($fragVal);
            }
        }

        if (!$this->helper->isProcessor($data['process'])) {
            $message = 'Invalid process declaration, only processors allowed';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        $this->validateDetails($data['process']);

        if (isset($data['output'])) {
            if ($this->helper->isProcessor($data['output'])) {
                $this->validateDetails($data['output']);
            } elseif (is_array($data['output'])) {
                foreach ($data['output'] as $key => $output) {
                    if ($this->helper->isProcessor($output)) {
                        $this->validateDetails($output);
                    } elseif ($output != 'response') {
                        $message = 'Invalid output declaration. ';
                        $message .= 'Only processor, array of processors or "response" allowed';
                        $this->logger->error('api', $message);
                        throw new ApiException($message, 6, -1, 400);
                    }
                }
            } else {
                if ($data['output'] != 'response') {
                    $message = 'Invalid output declaration. ';
                    $message .= 'Only processor, array of processors or "response" allowed';
                    $this->logger->error('api', $message);
                    throw new ApiException($message, 6, -1, 400);
                }
            }
        }
    }

    /**
     * Search for identical IDs.
     *
     * @param array $meta Resource metadata array.
     *
     * @return boolean
     *
     * @throws ApiException Identical ID found.
     */
    private function validateIdenticalIds(array $meta): bool
    {
        $id = [];
        $stack = [$meta];

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                if (in_array($node['id'], $id)) {
                    $this->logger->error('api', 'identical IDs in new resource: ' . $node['id']);
                    throw new ApiException(
                        'identical IDs in new resource: ' . $node['id'],
                        6,
                        -1,
                        400
                    );
                }
                $id[] = $node['id'];
            }
            if (is_array($node)) {
                foreach ($node as $item) {
                    array_unshift($stack, $item);
                }
            }
        }

        return true;
    }

    /**
     * Validate the details of a security or process processor.
     *
     * @param array $meta Resource metadata array.
     *
     * @return void
     *
     * @throws ApiException|\ReflectionException Error found in validating the resource.
     */
    private function validateDetails(array $meta)
    {
        $stack = array($meta);

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                $classStr = $this->helper->getProcessorString($node['processor']);
                $class = new \ReflectionClass($classStr);
                $parents = [];
                while ($parent = $class->getParentClass()) {
                    $parents[] = $parent->getName();
                    $class = $parent;
                }
                if (in_array('ApiOpenStudio\Output\Output', $parents)) {
                    $class = new $classStr([], 0, $this->logger, []);
                } else {
                    $request = new Request();
                    $class = new $classStr($meta, $request, $this->db, $this->logger);
                }
                $details = $class->details();
                $id = $node['id'];
                $this->logger->notice('api', 'Validating: ' . $id);

                foreach ($details['input'] as $inputKey => $inputDef) {
                    $min = $inputDef['cardinality'][0];
                    $max = $inputDef['cardinality'][1];
                    $literalAllowed = $inputDef['literalAllowed'];
                    $limitProcessors = $inputDef['limitProcessors'];
                    $limitTypes = $inputDef['limitTypes'];
                    $limitValues = $inputDef['limitValues'];
                    $count = 0;

                    if (!empty($node[$inputKey])) {
                        $input = $node[$inputKey];

                        if ($this->helper->isProcessor($input)) {
                            if (!empty($limitProcessors) && !in_array($input['processor'], $limitProcessors)) {
                                $message = 'processor ' . $input['id'] . ' is an invalid processor type (only "'
                                    . implode('", ', $limitProcessors) . '" allowed)';
                                $this->logger->error('api', $message);
                                throw new ApiException($message, 6, $id, 400);
                            }
                            array_unshift($stack, $input);
                            $count = 1;
                        } elseif (is_array($input)) {
                            foreach ($input as $item) {
                                if ($this->helper->isProcessor($item)) {
                                    array_unshift($stack, $item);
                                } else {
                                    $this->validateTypeValue($item, $limitTypes, $id);
                                }
                            }
                            $count = sizeof($input);
                        } elseif (!$literalAllowed) {
                            $message = "literals not allowed as input for '$inputKey' in processor: $id";
                            $this->logger->error('api', $message);
                            throw new ApiException($message, 6, $id, 400);
                        } else {
                            if (!empty($limitValues) && !in_array($input, $limitValues)) {
                                $message = "invalid value type for '$inputKey' in processor: $id";
                                $this->logger->error('api', $message);
                                throw new ApiException($message, 6, $id, 400);
                            }
                            if (!empty($limitTypes)) {
                                $this->validateTypeValue($input, $limitTypes, $id);
                            }
                            $count = 1;
                        }
                    }

                    // validate cardinality
                    if ($count < $min) {
                        // check for nothing to validate and if that is ok.
                        $message = "input '$inputKey' in processor '" . $node['id'] . "' requires min $min";
                        $this->logger->error('api', $message);
                        throw new ApiException($message, 6, $id, 400);
                    }
                    if ($max != '*' && $count > $max) {
                        $message = "input '$inputKey' in processor '" . $node['id'] . "' requires max $max";
                        $this->logger->error('api', $message);
                        throw new ApiException($message, 6, $id, 400);
                    }
                }
            } elseif (is_array($node)) {
                if (isset($node['processor']) && empty($node['id'])) {
                    $this->logger->error('api', 'Invalid processor, id attribute missing');
                    throw new ApiException('Invalid processor, id attribute missing', 6, -1, 400);
                }
                foreach ($node as $key => $value) {
                    array_unshift($stack, $value);
                }
            }
        }
    }

    /**
     * Compare an element type and possible literal value or type in the input resource with the definition in the
     * Processor it refers to. If the element type is processor, recursively iterate through, using the calling
     * function _validateProcessor().
     *
     * @param mixed $element Literal value in a resource to validate against $accepts.
     * @param array $accepts Array of types the processor can accept.
     * @param string|integer $id Processor ID.
     *
     * @return boolean
     *
     * @throws ApiException Invalid $element.
     */
    private function validateTypeValue($element, array $accepts, $id): bool
    {
        if (empty($accepts)) {
            return true;
        }
        $valid = false;

        foreach ($accepts as $accept) {
            if ($accept == 'file') {
                $valid = true;
                break;
            } elseif ($accept == 'literal' && (is_string($element) || is_numeric($element))) {
                $valid = true;
                break;
            } elseif (
                $accept == 'boolean'
                && filter_var($element, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $valid = true;
                break;
            } elseif (
                $accept == 'integer'
                && filter_var($element, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $valid = true;
                break;
            } elseif ($accept == 'text' && is_string($element)) {
                $valid = true;
                break;
            } elseif (
                $accept == 'float'
                && filter_var($element, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $valid = true;
                break;
            } elseif ($accept == 'array' && is_array($element)) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            $message = 'invalid literal in new resource (' . print_r($element) . '. only "' .
                implode("', '", $accepts) . '" accepted';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, $id, 400);
        }
        return true;
    }
}
