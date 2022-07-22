<?php

/**
 * Class ResourceExport.
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
use ApiOpenStudio\Db\Resource;
use ApiOpenStudio\Db\ResourceMapper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ResourceExport
 *
 * Processor class to export a resource.
 */
class ResourceExport extends ProcessorEntity
{
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
                'default' => null,
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
     * Resource mapper class.
     *
     * @var ResourceMapper
     */
    private ResourceMapper $resourceMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
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
        try {
            $resource = $this->resourceMapper->findByResid($resid);
            $userRoles = Utilities::getRolesFromToken();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($resource->getResid())) {
            throw new ApiException('Invalid resource', 6, $this->id, 400);
        }

        // Validate user has Developer access to its application.
        $userHasAccess = false;
        foreach ($userRoles as $userRole) {
            if ($userRole['role_name'] == 'Developer' && $userRole['appid'] == $resource->getAppId()) {
                $userHasAccess = true;
            }
        }
        if (!$userHasAccess) {
            throw new ApiException('Permission denied', 6, $this->id, 400);
        }

        $resourceExport = $this->getExportArray($resource);

        try {
            switch ($format) {
                case 'yaml':
                    header('Content-Disposition: attachment; filename="resource.twig"');
                    $result = new  DataContainer($this->getYaml($resourceExport), 'text');
                    break;
                case 'json':
                    header('Content-Disposition: attachment; filename="resource.json"');
                    $result = new DataContainer($this->getJson($resourceExport), 'json');
                    break;
                default:
                    throw new ApiException("Invalid format: $format", 6, $this->id, 400);
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }

    /**
     * Construct an array of the resource for export.
     *
     * @param Resource $resource
     *
     * @return array
     */
    protected function getExportArray(Resource $resource): array
    {
        $result = $resource->dump();

        $meta = json_decode($result['meta'], true);
        unset($result['meta']);
        foreach ($meta as $key => $definition) {
            $result[$key] = $definition;
        }

        unset($result['openapi']);

        return $result;
    }

    /**
     * Create a YAML string from a resource array.
     *
     * @param array $resource The exportable resource array.
     *
     * @return string A YAML string.
     */
    protected function getYaml(array $resource): string
    {
        return  Yaml::dump($resource, 5000, 4, Yaml::PARSE_OBJECT);
    }

    /**
     * Create a JSON string from a resource array.
     *
     * @param array $resource The exportable resource array.
     *
     * @return string A JSON string.
     */
    protected function getJson(array $resource): string
    {
        return json_encode($resource, JSON_UNESCAPED_SLASHES);
    }
}
