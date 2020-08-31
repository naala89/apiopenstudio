<?php
/**
 * Class ProcessorEntity.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

use Gaterdata\Config;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\UserRoleMapper;
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
     * @var integer
     */
    protected $id = '';

    /**
     * Meta required for this processor.
     * @var mixed
     */
    protected $meta;

    /**
     * All of the request details.
     * @var Request
     */
    protected $request;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var array Details of the processor.
     *
     * An array of details of the processor, used to configure the frontend GUI and metadata construction.
     *
     * Indexes:
     *  name: name of the processor.
     *
     *  machineName: machine name of the processor.
     *
     *  description: description of the processor.
     *
     *  account: The account that can use the processor.
     *
     *  menu: lists the immediate menu parents.
     *
     *    examples:
     *      'menu' => 'menu1' - belongs to menu1
     *
     *  input: list the input nodes for this processor
     *    This is an array with the following indexes:
     *    description (string): description of what the processor does
     *    cardinality: (int min, mixed max)
     *      e.g. [0, 1]
     *      max can be integer or '*'. '*' = infinite
     *    type: (array): an array of input type this processor will accept.
     *      Possible values:
     *        processor - any processor
     *        processor <name> - specific processor
     *        "predefined string"
     *        file
     *        literal
     *        bool
     *        numeric
     *        integer
     *        string
     *        float
     *        bool
     *
     *    examples:
     *      input => [
     *        'sources' => [
     *            'description' => 'desc1',
     *            'cardinality' => [1, '*'],
     *            type => ['function', 'literal']
     *         ]
     *      ]
     *      This processor has only one input, called sources.
     *      Sources must contain at least one value.
     *      The inputs can only be string or another processor.
     *
     *      input => [
     *        'method' => [
     *          'description' => 'desc1',
     *          'cardinality' => [1, 1],
     *          'accepts' => [
     *            'literal' => ['"get"', '"post"']
     *          ],
     *        ],
     *        'auth' => ['description' => 'desc2', 'cardinality' => [1, 1], 'accepts' => ['function'],
     *        'vars' => ['description' => 'desc3', 'cardinality' => [0, '*'],
     *            type => ['function', 'integer']],
     *        't' => ['description' => 'desc4', 'cardinality' => [0, '*'],
     *            type => ['processor field', 'string']]
     *      ]
     *          This Processor has 3 inputs:
     *          method, which has only one sub-input, of type string, with only 2 possible values ('get' and 'post')
     *          auth, which has only one value, of type processor
     *          vars, which can contain:
     *              an infinite number of values
     *              be of type processor or integer
     *              with no limit on value
     *          t, which can take or or many input of Processor Field or a string.
     */
    protected $details = array();

    /**
     * @var \ADOConnection $dbLayer
     */
    protected $db;

    /**
     * Constructor. Store processor metadata and request data in object.
     *
     * @param mixed $meta The processor metadata.
     * @param Request $request Request object.
     * @param \ADODB_mysqli $db Database object.
     * @param \Monolog\Logger $logger Logger object.
     */
    public function __construct($meta, Request &$request, \ADODB_mysqli $db, Logger $logger)
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

        $count = empty($this->meta->$key) ? 0 : is_array($this->meta->$key) ? sizeof($this->meta->$key) : 1;
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

        $this->_validateAllowedValues($container->getData(), $limitValues, $min, $key);
        $this->_validateAllowedTypes($container->getType(), $limitTypes, $min, $key);

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
        return is_object($data) && get_class($data) == 'Gaterdata\Core\DataContainer';
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
     * @param integer$uid User ID.
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
    private function _validateAllowedValues($val, array $limitValues, int $min, string $key)
    {
        if (empty($limitValues) || ($min < 1 && empty($val))) {
            return true;
        }
        if (!in_array($val, $limitValues)) {
            throw new ApiException("invalid value ($val). Only '"
                . implode("', '", $limitValues)
                . "' allowed in input '$key'", 7, $this->id, 417);
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
    private function _validateAllowedTypes(string $type, array $limitTypes, int $min, string $key)
    {
        if (empty($limitTypes) || ($min < 1 && $type == 'empty')) {
            return true;
        }
        if (!in_array($type, $limitTypes)) {
            throw new ApiException("invalid type ($type), only '"
                . implode("', '", $limitTypes)
                . "' allowed in input '$key'", 7, $this->id, 417);
        }
    }
}
