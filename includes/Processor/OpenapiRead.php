<?php

/**
 * Class OpenapiRead.
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
 * Class OpenapiRead
 *
 * Processor class to fetch OpenApi documentation for resources in an application that the user has access to.
 */
class OpenapiRead extends ProcessorEntity
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

        $application = $this->applicationMapper->findByAppId($appid);
        if (empty($application->getAppid())) {
            throw new ApiException('invalid appid');
        }

        $openApi = $application->getOpenapi();
        if (empty($openApi)) {
            $settings = new Config();
            $account = $this->accountMapper->findByAccid($application->getAccid());
            $openApiClassName = "\\ApiOpenStudio\\Core\\OpenApi\\OpenApiParent" .
                substr($settings->__get(['api', 'openapi_version']), 0, 1);
            $openApiClass = new $openApiClassName();
            $openApiClass->setDefault($account->getName(), $application->getName());
            $openApi = $openApiClass->export();
            $application->setOpenapi($openApiClass->export());
            $this->applicationMapper->save(($application));
        }
        $openApi = json_decode($openApi, true);
        $openApi['paths'] = [];

        $resources = $this->resourceMapper->findByAppId($appid);
        foreach ($resources as $resource) {
            $resourceOpenApi = $resource->getOpenapi();
            if (empty($resourceOpenApi)) {
                $settings = new Config();
                $openApiClassName = "\\ApiOpenStudio\\Core\\OpenApi\\OpenApiPath" .
                    substr($settings->__get(['api', 'openapi_version']), 0, 1);
                $openApiClass = new $openApiClassName();
                $openApiClass->setDefault($resource, $account->getName(), $application->getName());
                $resourceOpenApi = $openApiClass->export();
                $resource->setOpenapi($resourceOpenApi);
                $this->resourceMapper->save(($resource));
            }

            $openApi['paths'] = array_merge_recursive($openApi['paths'], json_decode($resourceOpenApi, true));
        }

        return new DataContainer(json_encode($openApi, JSON_UNESCAPED_SLASHES), 'json');
    }
}
