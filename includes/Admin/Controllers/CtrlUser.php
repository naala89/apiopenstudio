<?php
/**
 * Class CtrlUser.
 *
 * @package    ApiOpenStudio
 * @subpackage Admin\Controllers
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Admin\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlUser.
 *
 * Controller for the user view/edit pages.
 */
class CtrlUser extends CtrlBase
{
    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    protected $permittedRoles = [];

    /**
     * Create a new user.
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();

        return $this->view->render($response, 'user-edit.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * View a user.
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function view(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $uid = $args['uid'];
        $user = [];

        try {
            $result = $this->apiCall(
                'get', 'user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'uid' => $uid,
                    ],
                ]
            );
            $user = json_decode($result->getBody()->getContents(), true);
            $user = isset($user[0]) ? $user[0] : [];
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->view->render($response, 'user-view.twig', [
            'user' => $user,
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Edit a user page.
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function edit(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $uid = $args['uid'];
        $user = [];

        try {
            $result = $this->apiCall(
                'get', 'user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'uid' => $uid,
                    ],
                ]
            );
            $user = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->view->render($response, 'user-edit.twig', [
            'menu' => $menu,
            'user' => $user[0],
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Upload a user.
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function upload(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allPostVars = $request->getParams();

        // Workaround for Authentication middleware that will think this is a login attempt.
        if (!empty($allPostVars['upload-username'])) {
            $allPostVars['username'] = $allPostVars['upload-username'];
        }
        if (!empty($allPostVars['upload-password'])) {
            $allPostVars['password'] = $allPostVars['upload-password'];
        }
        unset($allPostVars['upload-username']);
        unset($allPostVars['upload-password']);

        if (!empty($allPostVars['uid'])) {
            // Edit a user.
            $uid = $allPostVars['uid'];
            unset($allPostVars['uid']);
            try {
                $result = $this->apiCall('put', "user/$uid",
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'json' => $allPostVars,
                    ]
                );
                $this->flash->addMessageNow('info', 'User updated.');

                $user = json_decode($result->getBody()->getContents(), true);

                return $this->view->render($response, 'user-view.twig', [
                    'menu' => $menu,
                    'user' => $user,
                    'messages' => $this->flash->getMessages(),
                ]);
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
                $user = $allPostVars;
                $user['uid'] = $uid;
                return $this->view->render($response, 'user-view.twig', [
                    'menu' => $menu,
                    'user' => $user,
                    'messages' => $this->flash->getMessages(),
                ]);
            }
        } else {
            // Create a user.
            try {
                $result = $this->apiCall('post', 'user',
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'form_params' => $allPostVars,
                    ]
                );
                $this->flash->addMessageNow('info', 'User created.');
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
                $user = $allPostVars;
                return $this->view->render($response, 'user-edit.twig', [
                    'menu' => $menu,
                    'user' => $user,
                    'messages' => $this->flash->getMessages(),
                ]);
            }
        }
        try {
            $result = $this->apiCall(
                'get',
                'user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $users = (array) json_decode($result->getBody()->getContents());
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
            $users = [];
        }

        return $this->view->render($response, 'users.twig', [
            'menu' => $menu,
            'users' => $users,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Delete a user account.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $uid = $args['uid'];

        try {
            $result = $this->apiCall('delete', "user/$uid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            if ($result == 'true') {
                $this->flash->addMessageNow('info', 'User successfully deleted.');
            } else {
                $this->flash->addMessageNow('error', 'User deletion failed, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $response->withStatus(302)->withHeader('Location', '/users');
    }
}
