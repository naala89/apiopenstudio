<?php

/**
 * Class OpenapiUpdate.
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
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;
use mysql_xdevapi\Exception;

/**
 * Class OpenapiUpdate
 *
 * Processor class to update OpenApi documentation for resources in an application that the user has access to.
 */
class OpenapiUpdate extends ProcessorEntity
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
            'open_api' => [
                'description' => 'The new OpenApi schema.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['json'],
                'limitValues' => [],
                'default' => '',
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
        $openApi = json_decode($this->val('open_api', true), true);
        $appid = $this->val('appid', true);

        $settings = new Config();
        $openApiParentClassName = "\\ApiOpenStudio\\Core\\OpenApi\\OpenApiParent" .
            substr($settings->__get(['api', 'openapi_version']), 0, 1);
        $openApiParentClass = new $openApiParentClassName();

        $paths = $openApi['paths'];
        $openApi['paths'] = [];
        $openApiParentClass->import($openApi);

        // Extract the accid and appid from the schema.
        try {
            $applicationName = $openApiParentClass->getApplication();
            $accountName = $openApiParentClass->getAccount();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), 7, $this->id, 400);
        }
        $account = $this->accountMapper->findByName($accountName);
        $accid = $account->getAccid();
        if (empty($accid)) {
            throw new ApiException(
                "invalid account name in the schema: $accountName",
                7,
                $this->id,
                400
            );
        }
        $application = $this->applicationMapper->findByAccidAppname($account->getAccid(), $applicationName);
        if (empty($appid) || $appid != $application->getAppid()) {
            throw new ApiException(
                "invalid application name in the schema: $applicationName",
                7,
                $this->id,
                400
            );
        }

        // Validate user has permissions.
        $roles = Utilities::getRolesFromToken();
        $permitted = false;
        foreach ($roles as $role) {
            if ($role['appid'] == $appid || $role['role_name'] == 'Developer') {
                $permitted = true;
            }
        }
        if (!$permitted) {
            throw new ApiException('permission denied', 4, 403);
        }

        $application->setOpenapi(json_encode($openApi, JSON_UNESCAPED_SLASHES));
        try {
            $this->applicationMapper->save($application);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), 6, $this->id, 400);
        }

        foreach ($paths as $uri => $path) {
            foreach ($path as $method => $body) {
                $resource = $this->resourceMapper->findByAppIdMethodUri($appid, $method, trim($uri, '/'));
                if (empty($resource->getResid())) {
                    throw new ApiException(
                        "invalid resource in the schema: $applicationName, $method, $uri",
                        7,
                        $this->id,
                        400
                    );
                }
                $resource->setOpenapi(json_encode([$uri => [$method => $body]], JSON_UNESCAPED_SLASHES));
                try {
                    $this->resourceMapper->save($resource);
                } catch (ApiException $e) {
                    throw new ApiException($e->getMessage(), 6, $this->id, 400);
                }
            }
        }

        return $this->val('open_api');
    }
}
