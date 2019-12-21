<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Self_;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CtrlResource.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlResource extends CtrlBase
{
    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    const PERMITTED_ROLES = [
        'Developer',
    ];

    /**
     * Sections possible in a neta string.
     *
     * @var array
     */
    const META_SECTIONS = [
        'security',
        'process',
        'output',
    ];

    /**
     * Resources page.
     *
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View resources: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $applications = $this->getApplications($response, []);
        $appids = implode(',', array_keys((array) $applications));
        $allParams = $request->getParams();

        $query = [];
        if (!empty($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['order_by'])) {
            $query['order_by'] = $allParams['order_by'];
        }
        if (!empty($allParams['direction'])) {
            $query['direction'] = $allParams['direction'];
        }
        if (!empty($appids)) {
            $query['app_id'] = $appids;
        }

        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);

        try {
            $result = $client->request('GET', 'resource', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'query' => ['all' => 'true'],
            ]);
            $resources = (array) json_decode($result->getBody()->getContents());

            $result = $client->request('GET', 'account/all', [
                'headers' => [
                  'Authorization' => "Bearer $token",
                ],
            ]);
            $accounts = (array) json_decode($result->getBody()->getContents());
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                return $response->withStatus(302)->withHeader('Location', '/login');
                break;
                default:
                    $resources = [];
                    $accounts = [];
                    $applications = [];
                break;
            }
        }

        // Pagination.
        $page = isset($allParams['page']) ? $allParams['page'] : 1;
        $pages = ceil(count($resources) / $this->settings['admin']['paginationStep']);
        $resources = array_slice(
            $resources,
            ($page - 1) * $this->settings['admin']['paginationStep'],
            $this->settings['admin']['paginationStep'],
            true
        );
        return $this->view->render($response, 'resources.twig', [
            'menu' => $menu,
            'params' => $query,
            'resources' => $resources,
            'page' => $page,
            'pages' => $pages,
            'accounts' => $accounts,
            'applications' => (array) $applications,
            'messages' => $this->flash->getMessages(),
            'api_url' => $this->settings['api']['url'] . '/' . $this->settings['api']['core_account'] . '/' . $this->settings['api']['core_application'] . '/resource/file/',
        ]);
    }

    /**
     * Create a resource page.
     *
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Create a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();

        $result = $this->apiCall(
            'get',
            'functions/all',
            [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ],
            $response
        );
        $functions = json_decode($result->getBody()->getContents(), true);
        $result = $this->apiCall(
            'get',
            'account/all',
            [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ],
            $response
        );
        $accounts = json_decode($result->getBody()->getContents(), true);
        $result = $this->apiCall(
            'get',
            'application',
            [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ],
            $response
        );
        $applications = json_decode($result->getBody()->getContents(), true);

        $sortedFunctions = [];
        foreach ($functions as $function) {
            $sortedFunctions[$function['menu']][] = $function;
        }

        return $this->view->render($response, 'resource.twig', [
            'operation' => 'create',
            'menu' => $menu,
            'accounts' => $accounts,
            'applications' => $applications,
            'format' => $args['format'],
            'resource' => !empty($args['resource']) ? $args['resource'] : '',
            'resid' => '',
            'functions' => $sortedFunctions,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Edit a resource.
     *
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function edit(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Create a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $resid = $args['resid'];

        if (empty($args['resource'])) {
            $result = $this->apiCall(
                'get',
                'resource',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => ['resid' => $resid],
                ],
                $response
            );
            $resource = json_decode($result->getBody()->getContents(), true);
        } else {
            $resource = $args['resource'];
        }

        $result = $this->apiCall(
            'get',
            'functions/all',
            [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ],
            $response
        );
        $functions = json_decode($result->getBody()->getContents(), true);
        $result = $this->apiCall(
            'get',
            'account/all',
            [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ],
            $response
        );
        $accounts = json_decode($result->getBody()->getContents(), true);
        $result = $this->apiCall(
            'get',
            'application',
            [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ],
            $response
        );
        $applications = json_decode($result->getBody()->getContents(), true);

        $sortedFunctions = [];
        foreach ($functions as $function) {
            $sortedFunctions[$function['menu']][] = $function;
        }

        $obj = json_decode($resource['meta'], true);
        $resource['meta'] = [];
        foreach (self::META_SECTIONS as $item) {
            if (isset($obj[$item])) {
                $resource['meta'][$item] = Yaml::dump($obj[$item], Yaml::PARSE_OBJECT);
            }
        }
        return $this->view->render($response, 'resource.twig', [
            'operation' => 'edit',
            'menu' => $menu,
            'accounts' => $accounts,
            'applications' => $applications,
            'resource' => !empty($args['resource']) ? $args['resource'] : $resource,
            'functions' => $sortedFunctions,
            'messages' => $this->flash->getMessages(),
            'resid' => $resid,
        ]);
    }

    /**
     * Upload a resource.
     *
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function upload(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Upload a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (
            empty($allPostVars['format'])
            || empty($allPostVars['name'])
            || empty($allPostVars['description'])
            || empty($allPostVars['appid'])
            || empty($allPostVars['method'])
            || empty($allPostVars['uri'])
            || empty($allPostVars['process'])
            || !isset($allPostVars['ttl'])
        ) {
            $this->flash->addMessage('error', 'Cannot upload resource, not all information received');
            return $response->withStatus(302)->withHeader('Location', '/resource/create');
        }
        switch ($allPostVars['format']) {
            case 'yaml':
                $arr = [];
                foreach (self::META_SECTIONS as $item) {
                    if (!empty($allPostVars[$item])) {
                        $arr[$item] = Yaml::parse($allPostVars[$item]);
                    }
                }
                $meta = Yaml::dump($arr,50);
                break;
            case 'json':
                $meta = [];
                foreach (self::META_SECTIONS as $item) {
                    $meta[$item] = !empty($allPostVars[$item]) ? json_decode($allPostVars[$item], true) : '';
                }
                $meta = json_encode($meta);
                break;
            default:
                $meta = '';
                break;
        }

        if (!empty($allPostVars['resid'])) {
            $result = $this->apiCall(
                'put',
                'resource',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'resid' => $allPostVars['resid'],
                        'name' => $allPostVars['name'],
                        'description' => $allPostVars['description'],
                        'appid' => $allPostVars['appid'],
                        'method' => $allPostVars['method'],
                        'uri' => $allPostVars['uri'],
                        'ttl' => $allPostVars['ttl'],
                        'format' => $allPostVars['format'],
                        'meta' => $meta,
                    ],
                ],
                $response
            );
            $this->flash->addMessageNow('info', 'Resource successfully updated.');
        } else {
            $result = $this->apiCall(
                'post',
                'resource',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'name' => $allPostVars['name'],
                        'description' => $allPostVars['description'],
                        'appid' => $allPostVars['appid'],
                        'method' => $allPostVars['method'],
                        'uri' => $allPostVars['uri'],
                        'ttl' => $allPostVars['ttl'],
                        'format' => $allPostVars['format'],
                        'meta' => $meta,
                    ],
                ],
                $response
            );
            $this->flash->addMessageNow('info', 'Resource successfully created.');
        }

        $resource = $this->getResource($allPostVars);
        return !empty($allPostVars['resid']) ?
            $this->edit($request, $response, [
                'format' => $allPostVars['format'],
                'resource' => $resource,
                'resid' => $allPostVars['resid'],
            ]) :
            $this->create($request, $response, [
                'format' => $allPostVars['format'],
                'resource' => $resource,
            ]);
    }

    /**
     * Delete a resource.
     *
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Delete a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        $resid = $args['resid'];

        if (empty($resid)) {
            $result = $this->apiCall(
                'delete',
                'resource',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'resid' => $allPostVars['resid'],
                    ],
                ],
                $response
            );
            $result = json_decode($result->getBody()->getContents(), true);
        } else {
            $this->flash->addMessage('error', 'Cannot delete resource, no resid received.');
        }
        if ($result == 'true') {
            $this->flash->addMessage('info', 'Resource successfully deleted.');
        } else {
            $this->flash->addMessage('error', 'Resource failed to delete, please check the logs.');
        }
        return $response->withStatus(302)->withHeader('Location', '/resources');
    }

    /**
     * Generate the array for Twig for the current resource to be created/edited.
     *
     * @param array $allPostVars
     *   Post vars in this request.
     * @return array
     *   Twig vars.
     */
    private function getResource($allPostVars) {
        $arr = [
            'name' => $allPostVars['name'],
            'description' => $allPostVars['description'],
            'appid' => $allPostVars['appid'],
            'method' => $allPostVars['method'],
            'uri' => $allPostVars['uri'],
            'ttl' => $allPostVars['ttl'],
            'meta' => [
                'security' =>  $allPostVars['security'],
                'process' =>  $allPostVars['process'],
                'output' =>  $allPostVars['output'],
            ]
        ];
        if (isset($allPostVars['resid'])) {
            $arr['resid'] = $allPostVars['resid'];
        }
        return $arr;
    }
}
