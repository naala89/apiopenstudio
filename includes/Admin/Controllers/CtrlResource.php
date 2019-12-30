<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Yaml\Yaml;
use Exception;

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
        $pages = ceil(count($resources) / $this->settings['admin']['pagination_step']);
        $resources = array_slice(
            $resources,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
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

        try {
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
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
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
            try {
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
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
            }
        } else {
            $resource = $args['resource'];
        }

        try {
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
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        $sortedFunctions = [];
        foreach ($functions as $function) {
            $sortedFunctions[$function['menu']][] = $function;
        }

        $obj = json_decode($resource['meta'], true);
        $resource['meta'] = [];
        foreach (self::META_SECTIONS as $item) {
            if (isset($obj[$item])) {
                $resource['meta'][$item] = Yaml::dump($obj[$item], 500, 2, Yaml::PARSE_OBJECT);
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
            try {
//                var_dump([
//                    'resid' => $allPostVars['resid'],
//                    'name' => $allPostVars['name'],
//                    'description' => $allPostVars['description'],
//                    'appid' => $allPostVars['appid'],
//                    'method' => $allPostVars['method'],
//                    'uri' => $allPostVars['uri'],
//                    'ttl' => $allPostVars['ttl'],
//                    'format' => $allPostVars['format'],
//                    'meta' => $meta,
//                    ]);die();
                $result = $this->apiCall('put', 'resource',
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
                $this->flash->addMessageNow('info', 'Resource successfully edited.');
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
            }
        } else {
            try {
                $result = $this->apiCall('post', 'resource',
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'form_params' => [
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
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
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
        $resid = $allPostVars['resid'];

        try {
            $result = $this->apiCall('delete',"resource/$resid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ],
                $response
            );
            $result = json_decode($result->getBody()->getContents(), true);
            if ($result == 'true') {
                $this->flash->addMessage('info', 'Resource successfully deleted.');
            } else {
                $this->flash->addMessage('error', 'Resource failed to delete, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            return $response->withStatus(302)->withHeader('Location', '/resources');
        }

        return $response->withStatus(302)->withHeader('Location', '/resources');
    }

    /**
     * Download a resource.
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
    public function download(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Delete a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        if (empty($args['resid'])) {
            $this->flash->addMessage('error', 'Missing resource ID argument');
            return $response->withStatus(302)->withHeader('Location', '/resources');
        }
        if (empty($args['format']) || !in_array($args['format'], ['yaml', 'json'])) {
            $this->flash->addMessage('error', 'Invalid resource format requested.');
            return $response->withStatus(302)->withHeader('Location', '/resources');
        }

        try {
            $result = $this->apiCall('get', "resource/export/{$args['format']}/{$args['resid']}",
                ['headers' => ['Authorization' => "Bearer " . $_SESSION['token']]],
                $response
            );
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            return $response->withStatus(302)->withHeader('Location', '/resources');
        }

        echo trim((string) $result->getBody(), '"');
        return $response->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment;filename="gaterdata.' . $args['format'] . '"');
    }

    /**
     * Import a resource.
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
    public function import(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Delete a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $directory = $this->settings['api']['base_path'] . $this->settings['api']['dir_tmp'];
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['resource_file'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            try {
                $this->apiCall('post', 'resource/import',
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'multipart' => [
                            [
                                'name' => 'resource_file',
                                'contents' => fopen($directory . $filename, 'r'),
                            ],
                        ],
                    ],
                    $response
                );
            } catch (\Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
            }
        } else {
            $this->flash->addMessage('error', 'Error in uploading file');
        }
        unlink($directory . $filename);

        return $response->withStatus(302)->withHeader('Location', '/resources');
    }

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory
     *   directory to which the file is moved
     * @param $uploadedFile
     *   uploaded file to move
     *
     * @return string
     *   filename of moved file
     *
     * @throws Exception
     */
    private function moveUploadedFile($directory, $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . $filename);

        return $filename;
    }

    /**
     * Generate the array for Twig for the current resource to be created/edited.
     *
     * @param array $allPostVars
     *   Post vars in this request.
     *
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
