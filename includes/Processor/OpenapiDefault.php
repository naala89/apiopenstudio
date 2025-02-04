<?php

/**
 * Class OpenapiDefault.
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
        'name' => 'OpenApi Default',
        'machineName' => 'openapi_default',
        'description' => 'Generate default OpenApi documentation for an application and all its resources.',
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
     * @var Config
     */
    protected Config $settings;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
        $this->settings = new Config();
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

        // Only developers for an application can use this processor.
        try {
            $roles = Utilities::getClaimFromToken('roles');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $permitted = false;
        foreach ($roles as $role) {
            if ($role['appid'] == $appid && $role['role_name'] == 'Developer') {
                $permitted = true;
            }
        }
        if (!$permitted) {
            throw new ApiException('permission denied', 4, 403);
        }

        try {
            $application = $this->applicationMapper->findByAppId($appid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($application->getAppid())) {
            throw new ApiException('invalid appid', 6, $this->id, 400);
        }

        try {
            $account = $this->accountMapper->findByAccid($application->getAccid());
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($account->getAccid())) {
            throw new ApiException('application assigned to an invalid account', 6, $this->id, 400);
        }

        try {
            $openApiParentClassName = Utilities::getOpenApiParentClassPath($this->settings);
            $openApiPathClassName = Utilities::getOpenApiPathClassPath($this->settings);
            $openApiParentClass = new $openApiParentClassName();
            $openApiPathClass = new $openApiPathClassName();
            $openApiParentClass->setDefault($account->getName(), $application->getName());
            $schema = $openApiParentClass->export();
            $application->setOpenapi($schema);

            $this->applicationMapper->save(($application));

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
                    throw new ApiException($e->getMessage(), 2, $this->id, 500);
                }
                $schema['paths'] = array_merge_recursive($schema['paths'], json_decode($resourceSchema, true));
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getHtmlCode(), $this->id, $e->getHtmlCode());
        }

        try {
            $result = new DataContainer(json_encode($schema, JSON_UNESCAPED_SLASHES), 'json');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}
