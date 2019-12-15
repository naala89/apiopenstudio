<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use phpDocumentor\Reflection\Types\Integer;
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
        $resources = array_slice($resources,
        ($page - 1) * $this->settings['admin']['paginationStep'],
        $this->settings['admin']['paginationStep'],
        true);

        return $this->view->render($response, 'resources.twig', [
            'menu' => $menu,
            'params' => $query,
            'resources' => $resources,
            'page' => $page,
            'pages' => $pages,
            'accounts' => $accounts,
            'applications' => (array) $applications,
            'messages' => $this->flash->getMessages(),
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

        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);

        try {
            $result = $client->request('GET', 'functions/all', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $functions = json_decode($result->getBody()->getContents(), true);
            $result = $client->request('GET', 'account/all', [
                'headers' => [
                  'Authorization' => "Bearer $token",
                ],
            ]);
            $accounts = json_decode($result->getBody()->getContents(), true);
            $result = $client->request('GET', 'application', [
                'headers' => [
                  'Authorization' => "Bearer $token",
                ],
            ]);
            $applications = json_decode($result->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                return $response->withStatus(302)->withHeader('Location', '/login');
                break;
                default:
                    $accounts = [];
                    $applications = [];
                    $functions = [];
                break;
            }
        }

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
            'resid' => !empty($args['resid']) ? $args['resid'] : '',
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
                'query' => ['resid' => $resid],
            ]);
            $resource = json_decode($result->getBody()->getContents(), true);
            $result = $client->request('GET', 'functions/all', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $functions = json_decode($result->getBody()->getContents(), true);
            $result = $client->request('GET', 'account/all', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $accounts = json_decode($result->getBody()->getContents(), true);
            $result = $client->request('GET', 'application', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $applications = json_decode($result->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                default:
                    return $response->withStatus(302)->withHeader('Location', '/resources');
                    break;
            }
        }

        $sortedFunctions = [];
        foreach ($functions as $function) {
            $sortedFunctions[$function['menu']][] = $function;
        }

        $obj = json_decode($resource['meta'], true);
        $resource['meta'] = [];
        if (isset($obj['security'])) {
            $resource['meta']['security'] = Yaml::dump($obj['security'], Yaml::PARSE_OBJECT);
        }
        if (isset($obj['process'])) {
            $resource['meta']['process'] = Yaml::dump($obj['process'], Yaml::PARSE_OBJECT);
        }
        return $this->view->render($response, 'resource.twig', [
            'operation' => 'edit',
            'menu' => $menu,
            'accounts' => $accounts,
            'applications' => $applications,
            'resource' => $resource,
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
            || !isset($allPostVars['ttl'])
        ) {
            $this->flash->addMessage('error', 'Cannot upload resource, not all information received');
            return $response->withStatus(302)->withHeader('Location', '/resource/create');
        }
        $method = intval($allPostVars['resid']) > 1 ? 'PUT' : 'POST';
        switch ($allPostVars['format']) {
            case 'yaml':
                $meta = '';
                $meta .= !empty($allPostVars['security']) ? ("security:\n  " . $allPostVars['security']) . "\n" : '';
                $meta .= !empty($allPostVars['process']) ? ("process:\n  " . $allPostVars['process']) . "\n" : '';
                break;
            case 'json':
                $meta = [];
                $meta['security'] = !empty($allPostVars['security']) ? json_decode($allPostVars['security']) : '';
                $meta['process'] = !empty($allPostVars['process']) ? json_decode($allPostVars['process']) : '';
                $meta = json_encode($meta);
                break;
            default:
                $meta = '';
                break;
        }

        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);
        try {
            $client->request($method, 'resource', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'form_params' => [
                    'resid' => !empty($allPostVars['resid']) ? $allPostVars['resid'] : null,
                    'name' => $allPostVars['name'],
                    'description' => $allPostVars['description'],
                    'appid' => $allPostVars['appid'],
                    'method' => $allPostVars['method'],
                    'uri' => $allPostVars['uri'],
                    'ttl' => $allPostVars['ttl'],
                    'format' => $allPostVars['format'],
                    'meta' => $meta,
                ],
            ]);
            $this->flash->addMessageNow('info', 'Resource successfully created.');
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessageNow('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                default:
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
     * Generate the array for Twig for the current resource to be created/edited.
     *
     * @param array $allPostVars
     *   Post vars in this request.
     * @return array
     *   Twig vars.
     */
    private function getResource($allPostVars) {
        return [
            'name' => $allPostVars['name'],
            'description' => $allPostVars['description'],
            'appid' => $allPostVars['appid'],
            'method' => $allPostVars['method'],
            'uri' => $allPostVars['uri'],
            'ttl' => $allPostVars['ttl'],
            'meta' => [
                'security' =>  $allPostVars['security'],
                'process' =>  $allPostVars['process'],
            ]
        ];
    }
}
