<?php

/**
 * Class ProcessorEntity.
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

use ApiOpenStudio\Config;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use Monolog\Logger;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class ProcessorEntity
 *
 * Base class for all entities.
 */
abstract class ProcessorEntity extends Entity
{
    /**
     * Processor ID.
     *
     * @var integer Processor ID.
     */
    protected $id = '';

    /**
     * Meta required for this processor.
     *
     * @var mixed Processor metadata.
     */
    protected $meta;

    /**
     * All of the request details.
     *
     * @var Request Request.
     */
    protected $request;

    /**
     * Logget object.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * An array of details of the processor, used to configure the frontend GUI and metadata construction.
     *
     * @var array Details of the processor.
     *
     * Indexes:
     *  name: Human readable name of the processor.
     *
     *  machineName: Machine name of the processor.
     *
     *  description: Description of the processor.
     *
     *  menu: Lists the immediate menu parents.
     *
     *    examples:
     *      'menu' => 'menu1' - belongs to menu1
     *
     *  input: List the input nodes for this processor
     *    This is an array with the following indexes:
     *
     *    description (string): description of what the processor does
     *
     *    cardinality: (int min, mixed max)
     *      e.g. [0, 1]
     *      max can be integer or '*'. '*' = infinite
     *
     *    literalAllowed (boolean): Allow liter values.
     *
     *    limitValues (array|mixed): Limit the sult values passed into the processor.
     *
     *    limitProcessors (array|string): Limit the input processors.
     *
     *    limitTypes: (array): an array of input type this processor will accept.
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
     *          t, which can take or or many input of Processor Field.
     */
    protected $details = array();

    /**
     * DB connections.
     *
     * @var \ADOConnection $dbLayer
     */
    protected $db;

    /**
     * Constructor. Store processor metadata and request data in object.
     *
     * @param $meta
     *   Metadata for the processor.
     * @param Request $request
     *   The full request object.
     * @param \ADOConnection|null $db
     *   The DB connection object.
     * @param Logger|null $logger
     *   The logger.
     */
    public function __construct($meta, Request &$request, $db = null, Logger $logger = null)
    {
        $this->meta = $meta;
        $this->request = $request;
        $this->id = isset($meta->id) ? $meta->id : -1;
        $this->db = $db;
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
     * @return array|Error
     */
    abstract public function process();

    /**
     * Return details for processor.
     *
     * @return array
     */
    public function details()
    {
        return $this->details;
    }

    /**
     * Process a variable into a final result for the processor.
     *
     * This method can be used to process a value in it's meta to return a final result that it can use.
     * If the object is a processor, then it will process that down to a final return value,
     * or if the obj is a simple value, then it will return that. Anything else will return an error object.
     *
     * Setting $realValue to true will force the value to be the actual value, rather than a potential dataContainer.
     *
     * @param string $key The key for the input variable in the meta.
     * @param boolean $realValue Return the real value or a dataContainer.
     *
     * @return mixed|DataContainer
     *
     * @throws ApiException Invalid key or data.
     */
    public function val(string $key, bool $realValue = null)
    {
        $this->logger->debug("Fetching val for: $key");
        $this->logger->debug("Real value: $realValue");
        $inputDet = $this->details['input'];
        if (!isset($inputDet[$key])) {
            // undefined input key for this processor type
            throw new ApiException("invalid key: $key", 1, $this->id);
        }

        $min = $inputDet[$key]['cardinality'][0];
        $max = $inputDet[$key]['cardinality'][1];
        $limitValues = $inputDet[$key]['limitValues'];
        $limitTypes = $inputDet[$key]['limitTypes'];
        $default = $inputDet[$key]['default'];

        $count = (empty($this->meta->$key) ? 0 : is_array($this->meta->$key)) ? sizeof($this->meta->$key) : 1;
        if ($count < $min || ($max != '*' && $count > $max)) {
            // invalid cardinality
            throw new ApiException("invalid number of inputs ($count) in $key, requires $min - $max", 7, $this->id);
        }

        // Set data to default if empty.
        $test = $this->isDataContainer($this->meta->$key) ? $this->meta->$key->getData() : $this->meta->$key;
        if ($test === null || $test === '') {
            $this->meta->$key = new DataContainer($default);
        }

        $container = $this->isDataContainer($this->meta->$key)
            ? $this->meta->$key
            : new DataContainer($this->meta->$key);

        $this->validateAllowedValues($container->getData(), $limitValues, $min, $key);
        $this->validateAllowedTypes($container->getType(), $limitTypes, $min, $key);

        $this->logger->debug('Value: ' . $container->getData());

        return $realValue ? $container->getData() : $container;
    }

    /**
     * Validate if a set of data is wrapped in a DataContainer object.
     *
     * @param mixed $data DataContainer or raw data.
     *
     * @return boolean
     */
    protected function isDataContainer($data)
    {
        return is_object($data) && get_class($data) == 'ApiOpenStudio\Core\DataContainer';
    }

    /**
     * Generate the params array for the sql search.
     *
     * @param string $keyword Search keyword.
     * @param array $keywordCols Columns to search for the keyword.
     * @param string $orderBy Order by column.
     * @param string $direction Order direction.
     *
     * @return array
     */
    protected function generateParams(string $keyword, array $keywordCols, string $orderBy, string $direction)
    {
        $params = [];
        if (!empty($keyword) && !empty($keywordCols)) {
            foreach ($keywordCols as $keywordCol) {
                $params['filter'][] = ['keyword' => "%$keyword%", 'column' => $keywordCol];
            }
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }
        return $params;
    }

    /**
     * Get the accids for accounts that the user has roles for.
     *
     * @param integer $uid User ID.
     *
     * @return DataContainer
     */
    protected function getUserAccids(int $uid)
    {
        $accountMapper = new AccountMapper($this->db);
        $accounts = $accountMapper->findByUid($uid);
        $accids = [];
        foreach ($accounts as $account) {
            $accids[] = $account->getAccid();
        }
        return $accids;
    }

    /**
     * Get the appids for applications that the user has roles for.
     *
     * @param integer $uid User ID.
     *
     * @return DataContainer Array of appid.
     *
     * @throws ApiException Exception flowing though.
     */
    protected function getUserAppids(int $uid)
    {
        $userRoleMapper = new UserRoleMapper($this->db);
        $applicationMapper = new ApplicationMapper($this->db);
        $userRoles = $userRoleMapper->findByFilter(['col' => ['uid' => $uid]]);
        $appids = [];

        foreach ($userRoles as $userRole) {
            $appid = $userRole->getAppid();
            $accid = $userRole->getAccid();

            if (empty($accid)) {
                $applications = $applicationMapper->findAll();
                $appids = [];
                foreach ($applications as $application) {
                    $appids[] = $application->getAppid();
                }
                return new DataContainer($appids, 'array');
            }

            if (empty($appid)) {
                $applications = $applicationMapper->findByAccid($accid);
                foreach ($applications as $application) {
                    if (!in_array($application->getAppid(), $appids)) {
                        $appids[] = $application->getAppid();
                    }
                }
            } else {
                if (!in_array($appid, $appids)) {
                    $appids[] = $appid;
                }
            }
        }

        return new DataContainer($appids, 'array');
    }

    /**
     * Validate an input for allowed values.
     *
     * @param mixed $val Input value.
     * @param array $limitValues List of allowed values.
     * @param integer $min Minimum number of values.
     * @param string $key The key of the input being validated.
     *
     * @return boolean
     *
     * @throws ApiException Invalid value.
     */
    private function validateAllowedValues($val, array $limitValues, int $min, string $key)
    {
        if (empty($limitValues) || ($min < 1 && empty($val))) {
            return true;
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
     * @return boolean
     *
     * @throws ApiException Invalid data type.
     */
    private function validateAllowedTypes(
        string $type,
        array $limitTypes,
        int $min,
        string $key
    ) {
        if (empty($limitTypes) || ($min < 1 && $type == 'empty')) {
            return true;
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
}
