<?php
/**
 * Class ResourceExport.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\AccountMapper;
use Gaterdata\Db\ApplicationMapper;
use Gaterdata\Db\ResourceMapper;
use Gaterdata\Db\UserMapper;
use Gaterdata\Db\UserRoleMapper;
use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;

/**
 * Class ResourceExport
 *
 * Processor class to export a resource.
 */
class ResourceExport extends Core\ProcessorEntity
{
    /**
     * @var Config
     */
    private $settings;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * @var ApplicationMapper
     */
    private $applicationMapper;

    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Resource export',
        'machineName' => 'resource_export',
        'description' => 'Export a resource file.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                // phpcs:ignore
                'description' => 'The token of the user making the call. This is used to validate the user permissions.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'resid' => [
                'description' => 'The Resource ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'format' => [
                'description' => 'The format to save the file as.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['yaml', 'json'],
                'default' => 'yaml',
            ],
        ],
    ];

    /**
     * ResourceExport constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db);
        $this->userRoleMapper = new UserRoleMapper($db);
        $this->accountMapper = new AccountMapper($db);
        $this->applicationMapper = new ApplicationMapper($db);
        $this->resourceMapper = new ResourceMapper($db);
        $this->settings = new Core\Config();
    }

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

        $token = $this->val('token', true);
        $currentUser = $this->userMapper->findBytoken($token);
        $resid = $this->val('resid', true);
        $format = $this->val('format', true);

        $resource = $this->resourceMapper->findByResid($resid);
        if (empty($resource->getResid())) {
            throw new Core\ApiException('Invalid resource', 6, $this->id, 400);
        }

        $role = $this->userRoleMapper->findByUidAppidRolename(
            $currentUser->getUid(),
            $resource->getAppid(),
            'Developer');
        if (empty($role->getUrid())) {
            throw new Core\ApiException("Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400);
        }

        switch ($format) {
            case 'yaml':
                header('Content-Disposition: attachment; filename="resource.twig"');
                return $this->getYaml($resource);
                break;
            case 'json':
                header('Content-Disposition: attachment; filename="resource.json"');
                return $this->getJson($resource);
                break;
        }
    }

    /**
     * Create a YAML string from a resource.
     *
     * @param mixed $resource The resource.
     *
     * @return string A YAML string.
     */
    private function getYaml($resource)
    {
        $obj = [];
        $obj['name'] = $resource->getName();
        $obj['description'] = $resource->getDescription();
        $obj['uri'] = $resource->getUri();
        $obj['method'] = $resource->getMethod();
        $obj['appid'] = $resource->getAppId();
        $obj['ttl'] = $resource->getTtl();
        $obj = array_merge($obj, json_decode($resource->getMeta(), true));
        return  Yaml::dump($obj, Yaml::PARSE_OBJECT);
    }

    /**
     * Create a JSON string from a resource.
     *
     * @param mixed $resource The resource.
     *
     * @return string A YAML string.
     */
    private function getJson($resource)
    {
        $obj = [];
        $obj['name'] = $resource->getName();
        $obj['description'] = $resource->getDescription();
        $obj['uri'] = $resource->getUri();
        $obj['method'] = $resource->getMethod();
        $obj['appid'] = $resource->getAppId();
        $obj['ttl'] = $resource->getTtl();
        $obj = array_merge($obj, json_decode($resource->getMeta(), true));
        return json_encode($obj);
    }
}
