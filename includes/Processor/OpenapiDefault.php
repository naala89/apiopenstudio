<?php

/**
 * Class OpenapiDefault.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;

/**
 * Class OpenapiDefault
 *
 * Generate default OpenApi documentation for an application and all its resources.
 */
class OpenapiDefault extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'OpenApi Read',
        'machineName' => 'openapi_read',
        'description' => 'Fetch OpenApi documentation for resources in an application that the user has access to.',
        'menu' => 'Documentation',
        'input' => [
            'appid' => [
                'description' => 'The application ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

    /**
     * @var AccountMapper
     */
    protected AccountMapper $accountMapper;

    /**
     * @var ApplicationMapper
     */
    protected ApplicationMapper $applicationMapper;

    /**
     * @var ResourceMapper
     */
    protected ResourceMapper $resourceMapper;

    /**
     * OpenapiRead constructor.
     *
     * @param $meta
     * @param Request $request
     * @param ADOConnection|null $db
     * @param MonologWrapper|null $logger
     */
    public function __construct($meta, Request &$request, ADOConnection $db = null, MonologWrapper $logger = null)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $appid = $this->val('appid', true);
        $settings = new Config();

        // Only developers for an application can use this processor.
        $roles = Utilities::getRolesFromToken();
        $permitted = false;
        foreach ($roles as $role) {
            if ($role['appid'] == $appid && $role['role_name'] == 'Developer') {
                $permitted = true;
            }
        }
        if (!$permitted) {
            throw new ApiException('permission denied', 4, 403);
        }

        $application = $this->applicationMapper->findByAppId($appid);
        if (empty($application->getAppid())) {
            throw new ApiException('invalid appid', 6, $this->id, 400);
        }
        $account = $this->accountMapper->findByAccid($application->getAccid());
        if (empty($account->getAccid())) {
            throw new ApiException('application assigned to an invalid account', 6, $this->id, 400);
        }

        $openApiParentClassName = Utilities::getOpenApiParentClassPath($this->config);
        $openApiPathClassName = Utilities::getOpenApiPathClassPath($this->config);
        $openApiParentClass = new $openApiParentClassName();
        $openApiPathClass = new $openApiPathClassName();

        $openApiParentClass->setDefault($account->getName(), $application->getName());
        $schema = $openApiParentClass->export();

        $application->setOpenapi($schema);
        try {
            $this->applicationMapper->save(($application));
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), 6, $this->id, 400);
        }

        $schema = json_decode($schema, true);
        $schema['paths'] = [];

        $resources = $this->resourceMapper->findByAppId($appid);
        foreach ($resources as $resource) {
            $openApiPathClass->setDefault($resource, $account->getName(), $application->getName());
            $resourceSchema = $openApiPathClass->export();
            $resource->setOpenapi($resourceSchema);
            try {
                $this->resourceMapper->save($resource);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), 6, $this->id, 400);
            }
            $schema['paths'] = array_merge_recursive($schema['paths'], json_decode($resourceSchema, true));
        }

        return new DataContainer(json_encode($schema, JSON_UNESCAPED_SLASHES), 'json');
    }
}
