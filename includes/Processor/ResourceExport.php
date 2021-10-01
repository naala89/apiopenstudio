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
use ApiOpenStudio\Core;
use ApiOpenStudio\Db\ResourceMapper;
use ApiOpenStudio\Db\UserRoleMapper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ResourceExport
 *
 * Processor class to export a resource.
 */
class ResourceExport extends Core\ProcessorEntity
{
    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    private UserRoleMapper $userRoleMapper;

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
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
        $this->resourceMapper = new ResourceMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $uid = Core\Utilities::getUidFromToken();
        $resid = $this->val('resid', true);
        $format = $this->val('format', true);

        $resource = $this->resourceMapper->findByResid($resid);
        if (empty($resource->getResid())) {
            throw new Core\ApiException('Invalid resource', 6, $this->id, 400);
        }

        $role = $this->userRoleMapper->findByUidAppidRolename(
            $uid,
            $resource->getAppid(),
            'Developer'
        );
        if (empty($role->getUrid())) {
            throw new Core\ApiException(
                "Unauthorised: you do not have permissions for this application",
                6,
                $this->id,
                400
            );
        }

        switch ($format) {
            case 'yaml':
                header('Content-Disposition: attachment; filename="resource.twig"');
                return new Core\DataContainer($this->getYaml($resource), 'text');
                break;
            case 'json':
                header('Content-Disposition: attachment; filename="resource.json"');
                return new Core\DataContainer($this->getJson($resource), 'text');
                break;
        }

        throw new Core\ApiException("Invalid format: $format", 6, $this->id, 400);
    }

    /**
     * Create a YAML string from a resource.
     *
     * @param mixed $resource The resource.
     *
     * @return string A YAML string.
     */
    private function getYaml($resource): string
    {
        $obj = [
            'name' => $resource->getName(),
            'description' => $resource->getDescription(),
            'uri' => $resource->getUri(),
            'method' => $resource->getMethod(),
            'appid' => $resource->getAppId(),
            'ttl' => $resource->getTtl(),
        ];
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
    private function getJson($resource): string
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
