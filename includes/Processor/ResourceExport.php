<?php

/**
 * Class ResourceExport.
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
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\Resource;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ResourceExport
 *
 * Processor class to export a resource.
 */
class ResourceExport extends ProcessorEntity
{
    /**
     * Resource mapper class.
     *
     * @var ResourceMapper
     */
    private ResourceMapper $resourceMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Resource export',
        'machineName' => 'resource_export',
        'description' => 'Export a resource file.',
        'menu' => 'Admin',
        'input' => [
            'resid' => [
                'description' => 'The Resource ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'format' => [
                'description' => 'The format to save the file as.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
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
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
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

        $resid = $this->val('resid', true);
        $format = $this->val('format', true);

        // Validate resource exists.
        $resource = $this->resourceMapper->findByResid($resid);
        if (empty($resource->getResid())) {
            throw new ApiException('Invalid resource', 6, $this->id, 400);
        }

        // Validate user has Developer access to its application.
        $userRoles = Utilities::getRolesFromToken();
        $userHasAccess = false;
        foreach ($userRoles as $userRole) {
            if ($userRole['role_name'] == 'Developer' && $userRole['appid'] == $resource->getAppId()) {
                $userHasAccess = true;
            }
        }
        if (!$userHasAccess) {
            throw new ApiException('Permission denied', 6, $this->id, 400);
        }

        switch ($format) {
            case 'yaml':
                header('Content-Disposition: attachment; filename="resource.twig"');
                return new DataContainer($this->getYaml($resource), 'text');
                break;
            case 'json':
                header('Content-Disposition: attachment; filename="resource.json"');
                return new DataContainer($this->getJson($resource), 'json');
                break;
            default:
                throw new ApiException("Invalid format: $format", 6, $this->id, 400);
                break;
        }
    }

    /**
     * Create a YAML string from a resource.
     *
     * @param Resource $resource The resource.
     *
     * @return string A YAML string.
     */
    private function getYaml(Resource $resource): string
    {
        $obj = $resource->dump();
        $obj['meta'] = json_decode($obj['meta'], true);
        $obj['openapi'] = json_decode($obj['openapi'], true);
        return  Yaml::dump($obj, Yaml::PARSE_OBJECT);
    }

    /**
     * Create a JSON string from a resource.
     *
     * @param Resource $resource The resource.
     *
     * @return string A YAML string.
     */
    private function getJson(Resource $resource): string
    {
        $obj = $resource->dump();
        $obj['meta'] = json_decode($obj['meta'], true);
        $obj['openapi'] = json_decode($obj['openapi'], true);
        return json_encode($obj);
    }
}
