<?php

/**
 * Class ResourceValidator.
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

use ADOConnection;
use ADODB_mysqli;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ReflectionClass;
use ReflectionException;

/**
 * Class ResourceValidator
 *
 * Validate a resource definition.
 */
class ResourceValidator
{
    /**
     * @var ProcessorHelper Processor helper class.
     */
    protected ProcessorHelper $helper;

    /**
     * @var ADODB_mysqli DB connection class.
     */
    private $db;

    /**
     * @var MonologWrapper Logging class.
     */
    private MonologWrapper $logger;

    /**
     * @var array Resource metadata.
     */
    private array $meta;

    /**
     * @var Config Config class.
     */
    private Config $settings;

    /**
     * @var ApplicationMapper Application mapper class.
     */
    private ApplicationMapper $applicationMapper;

    /**
     * @var AccountMapper Account mapper class.
     */
    private AccountMapper $accountMapper;

    /**
     * Constructor. Store processor metadata and request data in object.
     *
     * @param ADOConnection $db Database.
     * @param MonologWrapper $logger Logger.
     */
    public function __construct(ADOConnection $db, MonologWrapper $logger)
    {
        $this->helper = new ProcessorHelper();
        $this->db = $db;
        $this->logger = $logger;
        $this->settings = new Config();
        $this->applicationMapper = new ApplicationMapper($this->db, $this->logger);
        $this->accountMapper = new AccountMapper($this->db, $this->logger);
    }

    /**
     * Validate a complete resource metadata is well-formed.
     * An exception is thrown if the resource is invalid.
     *
     * @param array $meta Resource metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    public function validate(array $meta)
    {
        $this->logger->notice('api', 'Validating a new resource...');

        $this->meta = $meta;

        $this->validateRequiredResourceAttributes();

        $this->validateNonMetaValues();

        $this->validateCoreProtection();

        $this->validateIdenticalIds();

        if (isset($this->meta['meta']['security'])) {
            $this->validateSection([$this->meta['meta']['security']], false);
        }

        if (isset($meta['meta']['fragments'])) {
            if (!Utilities::isAssoc($meta['meta']['fragments'])) {
                $message = 'Invalid fragments structure in new resource';
                $this->logger->error('api', $message);
                throw new ApiException($message, 6, -1, 400);
            }
            foreach ($meta['meta']['fragments'] as $fragVal) {
                $this->validateSection($fragVal, true);
            }
        }

        $this->validateSection([$meta['meta']['process']], true);

        if (isset($meta['meta']['output'])) {
            if ($this->helper->isProcessor($meta['meta']['output'])) {
                $this->validateSection([$meta['meta']['output']], true);
            } elseif (is_array($meta['meta']['output'])) {
                foreach ($meta['meta']['output'] as $output) {
                    if ($this->helper->isProcessor($output)) {
                        $this->validateSection([$output], true);
                    } elseif ($output != 'response') {
                        $message = 'Invalid output declaration. ';
                        $message .= "Only a processor, array of processors or 'response' allowed";
                        $this->logger->error('api', $message);
                        throw new ApiException($message, 6, -1, 400);
                    }
                }
            } else {
                if ($meta['meta']['output'] != 'response') {
                    $message = 'Invalid output declaration. ';
                    $message .= "Only a processor, array of processors or 'response' allowed";
                    $this->logger->error('api', $message);
                    throw new ApiException($message, 6, -1, 400);
                }
            }
        }
    }

    /**
     * Validate non-meta security/fragment/process/output values 9in the resource.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateNonMetaValues()
    {
        // Validate TTL in the imported file.
        if ($this->meta['ttl'] < 0) {
            $this->logger->error('api', 'Negative ttl in new resource');
            throw new ApiException("Negative ttl in new resource", 6, -1, 400);
        }

        // Validate the application exists.
        $application = $this->applicationMapper->findByAppid($this->meta['appid']);
        if (empty($application)) {
            $this->logger->error('api', 'Invalid application: ' . $this->meta['appid']);
            throw new ApiException(
                'Invalid application: ' . $this->meta['appid'],
                6,
                -1,
                400
            );
        }
    }

    /**
     * Validate the required resource attributes exist in the metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateRequiredResourceAttributes()
    {
        // Check mandatory elements exists in data.
        if (empty($this->meta)) {
            $message = 'Empty resource uploaded';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (empty($this->meta['name'])) {
            $message = 'Missing or empty name in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (empty($this->meta['description'])) {
            $message = 'Missing or empty description in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (empty($this->meta['uri'])) {
            $message = 'Missing or empty uri in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (empty($this->meta['method'])) {
            $message = 'Missing or empty method in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (empty($this->meta['appid'])) {
            $message = 'Missing or empty appid in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (!isset($this->meta['ttl'])) {
            $message = 'Missing or empty ttl in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
        if (empty($this->meta['meta']['process'])) {
            $message = 'Missing or empty process in new resource';
            $this->logger->error('api', $message);
            throw new ApiException($message, 6, -1, 400);
        }
    }

    /**
     * Search for identical IDs.
     *
     * @return void
     *
     * @throws ApiException Identical ID found.
     */
    protected function validateIdenticalIds()
    {
        $ids = [];
        $meta = $this->meta['meta'];

        // Stack array for parsing for identical IDs.
        $stack = [$meta['process']];
        if (!empty($meta['security'])) {
            $stack[] = $meta['security'];
        }
        if (!empty($meta['output'])) {
            $stack[] = $meta['output'];
        }
        if (!empty($meta['fragments'])) {
            $stack[] = $meta['fragments'];
        }

        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                $id = $node['id'];
                if (in_array($id, $ids)) {
                    $message = "identical IDs in new resource: $id";
                    $this->logger->error('api', $message);
                    throw new ApiException($message, 6, -1, 400);
                }
                $ids[] = $id;
                foreach ($node as $item) {
                    array_unshift($stack, $item);
                }
            } elseif (is_array($node)) {
                foreach ($node as $item) {
                    array_unshift($stack, $item);
                }
            }
        }
    }

    /**
     * Validate the details of a metadata section (security, fragment, process or output).
     *
     * @param array $stack Stack of nodes to parse.
     * @param bool $allowFragments Allow fragments in the processor.
     *
     * @return void
     *
     * @throws ApiException Error found in validating the resource.
     */
    private function validateSection(array $stack, bool $allowFragments)
    {
        while ($node = array_shift($stack)) {
            if ($this->helper->isProcessor($node)) {
                if (!$allowFragments && $node['processor'] == 'fragment') {
                    throw new ApiException('Fragment not allowed in this section: ' . $node['id'], 6, -1, 400);
                }
                $class = $this->getProcessorClass($node);
                $details = $class->details();
                $this->validateNode($node, $details);
                foreach ($node as $input) {
                    if ($this->helper->isProcessor($input)) {
                        array_unshift($stack, $input);
                    }
                }
            } elseif (is_array($node)) {
                foreach ($node as $item) {
                    array_unshift($stack, $item);
                }
            }
        }
    }

    /**
     * Validate the attributes on a node in the metadata.
     *
     * @param array $node
     * @param array $details
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateNode(array $node, array $details)
    {
        $this->validateMissingInputs($node, $details['input']);
        $this->validateExtraInputs($node, $details['input']);
        $id = $node['id'];

        foreach ($details['input'] as $inputKey => $inputDef) {
            $this->logger->notice('api', "Validating input processor $id input: $inputKey");
            $this->validateCardinality($inputKey, $inputDef['cardinality'], $node);
            $this->validateLiteralAllowed($inputKey, $inputDef['literalAllowed'], $node);
            $this->validateLimitProcessors($inputKey, $inputDef['limitProcessors'], $node);
            $this->validateLimitTypes($inputKey, $inputDef['limitTypes'], $node);
            $this->validateLimitValues($inputKey, $inputDef['limitValues'], $node);
        }
    }

    /**
     * Validate that no required inputs are missing in a node.
     *
     * @param array $node
     * @param array $inputs
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateMissingInputs(array $node, array $inputs)
    {
        if (empty($node['id'])) {
            throw new ApiException("Missing processor id", 6, -1, 400);
        }
        $id = $node['id'];
        if (empty($node['processor'])) {
            throw new ApiException("Missing processor attribute in $id", 6, -1, 400);
        }
        $keys = array_keys($inputs);
        foreach ($keys as $key) {
            if (!isset($node[$key]) && $inputs[$key]['cardinality'][0] > 0) {
                throw new ApiException("Missing processor attribute $key in $id", 6, -1, 400);
            }
        }
    }

    /**
     * Validate that no extra inputs in a node.
     *
     * @param array $node
     * @param array $inputs
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateExtraInputs(array $node, array $inputs)
    {
        $id = $node['id'];
        foreach ($node as $key => $val) {
            if ($key == 'id' || $key == 'processor') {
                continue;
            }
            if (!isset($inputs[$key])) {
                throw new ApiException("Invalid input '$key' in processor '$id'", 6, -1, 400);
            }
        }
    }

    /**
     * Validate the cardinality of an input.
     *
     * @param string $inputKey Input key.
     * @param array $cardinality Processor input cardinality.
     * @param array $node processor metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateCardinality(string $inputKey, array $cardinality, array $node)
    {
        $min = $cardinality[0];
        $max = $cardinality[1];
        $id = $node['id'];

        if (empty($node[$inputKey]) && $min > 0) {
            throw new ApiException(
                "Input is empty in '$inputKey' in '$id', $min expected",
                6,
                -1,
                400
            );
        } elseif (!is_array($node[$inputKey])) {
            if ($min > 1) {
                throw new ApiException(
                    "Bad minimum inputs in '$inputKey' in '$id', $min expected",
                    6,
                    -1,
                    400
                );
            }
        } else {
            if (count($node[$inputKey]) < $min) {
                throw new ApiException(
                    "Bad minimum inputs in '$inputKey' in '$id', $min expected",
                    6,
                    -1,
                    400
                );
            }
            if ($max != '*' && !$this->helper->isProcessor($node[$inputKey]) && count($node[$inputKey]) > $max) {
                throw new ApiException(
                    "Bad maximum inputs in '$inputKey' in '$id', $max expected",
                    6,
                    -1,
                    400
                );
            }
        }
    }

    /**
     * Validate the cardinality of an input.
     *
     * @param string $inputKey Input key.
     * @param bool $literalAllowed Literal allowed for an input.
     * @param array $node processor metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateLiteralAllowed(string $inputKey, bool $literalAllowed, array $node)
    {
        if (!$literalAllowed && !$this->helper->isProcessor($node[$inputKey])) {
            throw new ApiException("Literal not allowed in $inputKey in " . $node['id'], 6, -1, 400);
        }
    }

    /**
     * Validate the processors in an input.
     *
     * @param string $inputKey Input key.
     * @param array $limitProcessors Limit processors allowed in an input.
     * @param array $node processor metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateLimitProcessors(string $inputKey, array $limitProcessors, array $node)
    {
        if (empty($limitProcessors)) {
            return;
        }
        if (
            $this->helper->isProcessor($node[$inputKey])
            && !in_array($node[$inputKey]['processor'], $limitProcessors)
        ) {
            throw new ApiException("Invalid processor in $inputKey in " . $node['id'], 6, -1, 400);
        } elseif (is_array($node[$inputKey])) {
            foreach ($node[$inputKey] as $index => $item) {
                if ($this->helper->isProcessor($item) && !in_array($item['processor'], $limitProcessors)) {
                    throw new ApiException("Invalid processor in $inputKey ($index) in " . $node['id'], 6, -1, 400);
                }
            }
        }
    }

    /**
     * Validate the literal input types in an input.
     *
     * @param string $inputKey Input key.
     * @param array $limitTypes Limit var types allowed in an input.
     * @param array $node processor metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateLimitTypes(string $inputKey, array $limitTypes, array $node)
    {
        if (empty($limitTypes) || empty($node[$inputKey]) || $this->helper->isProcessor($node[$inputKey])) {
            return;
        }
        $type = '';
        $type = is_array($node[$inputKey]) ? 'array' : $type;
        $type = is_string($node[$inputKey]) ? 'text' : $type;
        $type = filter_var($node[$inputKey], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 'boolean' : $type;
        $type = filter_var($node[$inputKey], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ? 'integer' : $type;
        $type = filter_var($node[$inputKey], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) ? 'float' : $type;

        if (!in_array($type, $limitTypes)) {
            throw new ApiException("Invalid type in $inputKey ($type) in " . $node['id'], 6, -1, 400);
        }
    }

    /**
     * Validate the literal values in an input.
     *
     * @param string $inputKey Input key.
     * @param array $limitValues Limit values allowed in an input.
     * @param array $node processor metadata.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function validateLimitValues(string $inputKey, array $limitValues, array $node)
    {
        if (empty($limitValues) || !isset($node[$inputKey]) || $this->helper->isProcessor($node[$inputKey])) {
            return;
        } elseif (!is_array($node[$inputKey]) && !in_array($node[$inputKey], $limitValues)) {
            throw new ApiException("Invalid value in $inputKey in " . $node['id'], 6, -1, 400);
        }
    }

    /**
     * Fetch the class for a processor.
     *
     * @param array $node
     *
     * @return mixed
     *
     * @throws ApiException
     */
    protected function getProcessorClass(array $node)
    {
        $classStr = $this->helper->getProcessorString($node['processor']);

        try {
            $class = new ReflectionClass($classStr);
        } catch (ReflectionException $e) {
            throw new ApiException($e->getMessage(), 6, -1, 500);
        }

        $parents = [];
        while ($parent = $class->getParentClass()) {
            $parents[] = $parent->getName();
            $class = $parent;
        }
        if (in_array('ApiOpenStudio\Core\OutputRemote', $parents)) {
            $request = new Request();
            $class = new $classStr($this->meta, $request, $this->logger, new DataContainer('', 'text'));
        } elseif (in_array('ApiOpenStudio\Core\OutputResponse', $parents)) {
            $request = new Request();
            $class = new $classStr($this->meta, $request, $this->logger, new DataContainer('', 'text'), 0);
        } else {
            $request = new Request();
            $class = new $classStr($this->meta, $request, $this->db, $this->logger);
        }

        return $class;
    }

    /**
     * Validate application is not core and core not locked.
     *
     * @throws ApiException
     */
    protected function validateCoreProtection()
    {
        $application = $this->applicationMapper->findByAppid($this->meta['appid']);
        $account = $this->accountMapper->findByAccid($application->getAccid());
        $coreAccount = $this->settings->__get(['api', 'core_account']);
        $coreApplication = $this->settings->__get(['api', 'core_application']);
        $coreLock = $this->settings->__get(['api', 'core_resource_lock']);

        if ($account->getName() == $coreAccount && $application->getName() == $coreApplication && $coreLock) {
            throw new ApiException("Unauthorised: this is a core resource", 4, -1, 403);
        }
    }
}
