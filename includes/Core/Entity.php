<?php

/**
 * Class Entity.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

/**
 * Class Entity
 *
 * Base entity class.
 */
abstract class Entity
{
    /**
     * All the request details.
     *
     * @var Request Request.
     */
    protected Request $request;

    /**
     * Processor ID.
     *
     * @var mixed Processor ID.
     */
    public $id = '';

    /**
     * Meta required for this processor.
     *
     * @var mixed Processor metadata.
     */
    protected $meta;

    /**
     * Logger object.
     *
     * @var MonologWrapper
     */
    protected MonologWrapper $logger;

    /**
     * An array of details of the processor, used to configure the frontend GUI and metadata construction.
     *
     * @var array Details of the processor.
     *
     * Indexes:
     *  'name' (string): Human readable name of the processor.
     *
     *  'machineName' (string): Machine name of the processor in snake case.
     *
     *  'description' (string): Description of the processor.
     *
     *  'menu' (string): Lists the immediate menu parents.
     *
     *    examples:
     *      'menu' => 'menu1' - belongs to menu1
     *
     *  'conditional' (optional, boolean):
     *
     *    ApiOpenStudio usually parses the node tree using depth-first iteration. In some cases, this may be wasteful
     *    because a processor may require conditional branching, in which case, we do not want to parse all comparison
     *    values until we know what branch we will take, i.e. in if_then_else processor, we do not need to process both
     *    the 'then' and the 'else' branches.
     *
     *    If omitted or set to false:
     *       The parsing will continue as normal (depth-first).
     *
     *    If set to true:
     *      The processor will be calculated and then the result branch will be returned and added to the stack.
     *
     *  'input': List the input nodes for this processor
     *    This is an array with the following indexes:
     *
     *    'description' (string): description of what the processor does
     *
     *    'cardinality': (int min, mixed max)
     *      e.g. [0, 1]
     *      max can be integer or '*'. '*' = infinite
     *
     *    'literalAllowed' (boolean): Allow liter values.
     *
     *    'limitValues' (array|mixed): Limit the sult values passed into the processor.
     *
     *    'limitProcessors' (array|string): Limit the input processors.
     *
     *    'limitTypes' (array): an array of input type this processor will accept.
     *      Possible values:
     *        file
     *        literal
     *        bool
     *        numeric
     *        integer
     *        text
     *        float
     *        bool
     *
     *    'conditional' (optional): In the case of branching processors (see 'preprocess' above),
     *       this indicates if the input is a conditional input or required for the logic comparison.
     *
     *    examples:
     *      input => [
     *        'sources' => [
     *            'description' => 'desc1',
     *            'cardinality' => [1, '*'],
     *            type => ['literal']
     *         ]
     *      ]
     *      This processor has only one input, called sources.
     *      Sources must contain at least one value.
     *      The inputs can only be a literal value.
     *
     *      input => [
     *        'method' => [
     *          'description' => 'desc1',
     *          'cardinality' => [1, 1],
     *          'literalAllowed': true
     *          'limitType': ['text'],
     *          'limitValues' => ["get", "post"]
     *        ],
     *        'auth' => [
     *           'description' => 'desc2',
     *           'cardinality' => [1, 1],
     *           'limitProcessors' => ['var_get'],
     *        ],
     *        'vars' => [
     *           'description' => 'desc3',
     *           'cardinality' => [0, '*'],
     *            limitTypes => ['integer'],
     *        ],
     *        't' => [
     *           'description' => 'desc4',
     *           'cardinality' => [0, '*'],
     *           'limitProcessors' => ['field'],
     *        ],
     *      ]
     *
     *      This Processor has 4 inputs:
     *
     *          method, which has only one input, of type text, with only 2 possible values ('get' and 'post'),
     *              literals are allowed.
     *          auth, which has only one value, of type processor (var_get).
     *          vars, which can contain:
     *              0 or many values
     *              Must be an integer
     *              with no limit on value
     *          t, which can take or many input of Processor Field.
     */
    protected array $details = array();

    /**
     * Entity constructor.
     *
     * @param $meta
     *   Metadata for the processor.
     * @param Request $request
     *   The full request object.
     * @param MonologWrapper|null $logger
     *   The logger.
     */
    public function __construct($meta, Request &$request, MonologWrapper $logger = null)
    {
        $this->meta = $meta;
        $this->request = $request;
        $this->id = $meta->id ?? -1;
        $this->logger = $logger;
    }

    /**
     * Main processor function.
     *
     * This is where the magic happens, and should be overridden by all derived classes.
     *
     * Fetches and process the processor described in the metadata.
     * It is also the 1st stop to recursive processing of processors, so the place validate user credentials.
     *
     * @throws ApiException
     */
    public function process()
    {
        try {
            $this->logger->info('api', 'Evaluating processor: ' . $this->details()['machineName']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }

    /**
     * Return details for processor.
     *
     * @return array
     */
    public function details(): array
    {
        return $this->details;
    }

    /**
     * Process a variable into a final result for the processor.
     *
     * This method can be used to process a value in its meta to return a final result that it can use.
     * If the object is a processor, then it will process that down to a final return value,
     * or if the obj is a simple value, then it will return that. Anything else will return an error object.
     *
     * Setting $realValue to true will force the value to be the actual value, rather than a potential dataContainer.
     *
     * @param string $key The key for the input variable in the meta.
     * @param bool|null $rawData Return the raw data or a DataContainer.
     *
     * @return mixed|DataContainer
     *
     * @throws ApiException Invalid key or data.
     */
    public function val(string $key, bool $rawData = null)
    {
        $this->logger->debug(
            'api',
            "Fetching val for entity (raw data): $key (" . ($rawData ? 'true' : 'false') . ')'
        );
        $inputDet = $this->details['input'];
        if (!isset($inputDet[$key])) {
            // undefined input key for this processor type
            throw new ApiException("invalid key: $key", 1, $this->id, 500);
        }

        $min = $inputDet[$key]['cardinality'][0];
        $max = $inputDet[$key]['cardinality'][1];
        $limitValues = $inputDet[$key]['limitValues'];
        $limitTypes = $inputDet[$key]['limitTypes'];
        $default = $inputDet[$key]['default'];

        $count = (empty($this->meta->$key) ? 0 : is_array($this->meta->$key)) ? sizeof($this->meta->$key) : 1;
        if ($count < $min || ($max != '*' && $count > $max)) {
            // invalid cardinality
            throw new ApiException(
                "invalid number of inputs ($count) in $key, requires $min - $max",
                1,
                $this->id,
                400
            );
        }

        // Set data to default if empty.
        if (!isset($this->meta->$key)) {
            $test = null;
        } else {
            $test = $this->isDataContainer($this->meta->$key) ? $this->meta->$key->getData() : $this->meta->$key;
        }
        if ($test === null) {
            try {
                $this->meta->$key = new DataContainer($default);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
        }

        try {
            $container = $this->isDataContainer($this->meta->$key)
                ? $this->meta->$key
                : new DataContainer($this->meta->$key);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        try {
            $this->validateAllowedValues($container->getData(), $limitValues, $min, $key);
            $this->validateAllowedTypes($container->getType(), $limitTypes, $min, $key);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        $this->logger->debug(
            'api',
            "Got value for entity ($key): " . print_r($container->getData(), true)
        );

        return $rawData ? $container->getData() : $container;
    }

    /**
     * Validate an input for allowed values.
     *
     * @param mixed $val Input value.
     * @param array $limitValues List of allowed values.
     * @param integer $min Minimum number of values.
     * @param string $key The key of the input being validated.
     *
     * @return void
     *
     * @throws ApiException Invalid value.
     */
    private function validateAllowedValues($val, array $limitValues, int $min, string $key): void
    {
        if (empty($limitValues) || ($min < 1 && empty($val))) {
            return;
        }
        if (!in_array($val, $limitValues)) {
            throw new ApiException("invalid value ($val). Only '"
                . implode("', '", $limitValues)
                . "' allowed in input '$key'", 6, $this->id, 400);
        }
    }

    /**
     * Validate an input for allowed variable types
     *
     * @param string $type Input value type.
     * @param array $limitTypes List of limit on valiable types.
     * @param integer $min Minimum number of values.
     * @param string $key The key of the input being validated.
     *
     * @return void
     *
     * @throws ApiException Invalid data type.
     */
    private function validateAllowedTypes(
        string $type,
        array $limitTypes,
        int $min,
        string $key
    ): void {
        if (empty($limitTypes) || ($min < 1 && $type == 'undefined')) {
            return;
        }
        if (!in_array($type, $limitTypes)) {
            throw new ApiException(
                "invalid type ($type), only '" . implode("', '", $limitTypes) . "' allowed in input '$key'",
                6,
                $this->id,
                400
            );
        }
    }

    /**
     * Validate if a set of data is wrapped in a DataContainer object.
     *
     * @param mixed $data DataContainer or raw data.
     *
     * @return bool
     */
    protected function isDataContainer($data): bool
    {
        return is_object($data) && get_class($data) == 'ApiOpenStudio\Core\DataContainer';
    }
}
