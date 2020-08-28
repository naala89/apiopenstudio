<?php

namespace Gaterdata\Admin\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
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
    protected $permittedRoles = [
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
     * @throws Exception
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View resources: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allParams = $request->getParams();
        if (!empty($allParams['filter_by_application'])) {
            $allParams['filter_by_account'] = '';
        }
        $allParams['order_by'] = empty($allParams['order_by']) ? 'name' : $allParams['order_by'];

        $query = [];
        if (!empty($allParams['filter_by_application'])) {
            $query['appid'] = $allParams['filter_by_application'];
        }
        if (!empty($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['order_by'])
                && $allParams['order_by'] != 'account'
                && $allParams['order_by'] != 'application') {
            $query['order_by'] = $allParams['order_by'];
        }
        if (!empty($allParams['direction'])) {
            $query['direction'] = $allParams['direction'];
        }

        $resources = [];
        try {
            $result = $this->apiCall('GET', 'resource', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                ],
                'query' => $query,
            ]);
            $resources = json_decode($result->getBody()->getContents(), true);
        } catch (Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        // Filter by account.
        if (!empty($allParams['filter_by_account'])) {
            $filterApps = [];
            foreach ($this->userAccounts as $userAccount) {
                if ($userAccount['accid'] == $allParams['filter_by_account']) {
                    foreach ($this->userApplications as $userApplication) {
                        if ($userApplication['accid'] == $userAccount['accid']) {
                            $filterApps[] = $userApplication['appid'];
                        }
                    }
                }
            }
            foreach ($resources as $index => $resource) {
                if (!in_array($resource['appid'], $filterApps)) {
                    unset($resources[$index]);
                }
            }
        }

        $sortedResources = [];
        if ($allParams['order_by'] == 'account') {
            // Sort by account name.
            foreach ($this->userAccounts as $userAccount) {
                foreach ($this->userApplications as $userApplication) {
                    foreach ($resources as $index => $resource) {
                        if ($userAccount['accid'] == $userApplication['accid']
                                && $userApplication['appid'] == $resource['appid']) {
                            $sortedResources[] = $resource;
                            unset($resources[$index]);
                        }
                    }
                }
            }
        } elseif ($allParams['order_by'] == 'application') {
            // Sort by application name.
            foreach ($this->userApplications as $userApplication) {
                foreach ($resources as $index => $resource) {
                    if ($userApplication['appid'] == $resource['appid']) {
                        $sortedResources[] = $resource;
                        unset($resources[$index]);
                    }
                }
            }
        } else {
            // All other sorts.
            $sortedResources = $resources;
        }

        // Pagination.
        $page = isset($allParams['page']) ? $allParams['page'] : 1;
        $pages = ceil(count($sortedResources) / $this->settings['admin']['pagination_step']);
        $sortedResources = array_slice(
            $sortedResources,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true
        );

        return $this->view->render($response, 'resources.twig', [
            'menu' => $menu,
            'params' => $allParams,
            'resources' => $sortedResources,
            'page' => $page,
            'pages' => $pages,
            'accounts' => $this->userAccounts,
            'applications' => $this->userApplications,
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
     * @throws Exception
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Create a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();

        try {
            $result = $this->apiCall('get', 'functions/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $functions = json_decode($result->getBody()->getContents(), true);
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
            'accounts' => $this->userAccounts,
            'applications' => $this->userApplications,
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
     * @throws Exception
     */
    public function edit(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Create a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $resid = $args['resid'];

        if (empty($args['resource'])) {
            try {
                $result = $this->apiCall('get', 'resource',
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'query' => ['resid' => $resid],
                    ]
                );
                $resource = json_decode($result->getBody()->getContents(), true);
                if (!empty($resource)) {
                    $resource = $resource[0];
                } else {
                    $this->flash->addMessageNow('error', 'resource not found.');
                }
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
            }
        } else {
            $resource = $args['resource'];
        }

        $accounts = $this->userAccounts;
        $applications = $this->userApplications;
        try {
            $result = $this->apiCall('get', 'functions/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $functions = json_decode($result->getBody()->getContents(), true);
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
     * @throws Exception
     */
    public function upload(Request $request, Response $response, array $args)
    {
        // Validate access.
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
                $meta = Yaml::dump($arr, 50);
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
                    ]
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
                    ]
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
     * @throws Exception
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Delete a resource: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        $resid = $allPostVars['resid'];

        try {
            $result = $this->apiCall(
                'delete',
                "resource/$resid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
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
     * @throws Exception
     */
    public function download(Request $request, Response $response, array $args)
    {
        // Validate access.
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
            $result = $this->apiCall('get', "resource/export/{$args['format']}/{$args['resid']}", [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token']
                ]
            ]);
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
     * @throws Exception
     */
    public function import(Request $request, Response $response, array $args)
    {
        // Validate access.
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
                    ]
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
    private function getResource($allPostVars)
    {
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
