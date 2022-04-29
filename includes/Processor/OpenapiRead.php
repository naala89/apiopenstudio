<?php

/**
 * Class OpenapiRead.
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
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\ResourceMapper;
use stdClass;

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

        try {
            $roles = Utilities::getRolesFromToken();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $permitted = false;
        foreach ($roles as $role) {
            if ($role['appid'] == $appid) {
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
            throw new ApiException('invalid appid');
        }

        $schema = json_decode($application->getOpenapi(), true);
        if (empty($schema)) {
            return new DataContainer(null);
        }

        try {
            $resources = $this->resourceMapper->findByAppId($appid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $schema['paths'] = [];
        foreach ($resources as $resource) {
            $resourceOpenApi = $resource->getOpenapi();
            if (!empty($resourceOpenApi)) {
                $schema['paths'] = array_merge_recursive($schema['paths'], json_decode($resourceOpenApi, true));
            }
        }
        if (empty($schema['paths'])) {
            $schema['paths'] = new stdClass();
        }

        try {
            $result = new DataContainer(json_encode($schema, JSON_UNESCAPED_SLASHES), 'json');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}
