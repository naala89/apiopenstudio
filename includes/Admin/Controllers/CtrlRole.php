<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;
use Gaterdata\Admin\Account;
use Gaterdata\Admin\Application;
use Gaterdata\Admin\UserAccount;
use Gaterdata\Admin\UserRole;

/**
 * Class CtrlRole.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlRole extends CtrlBase
{

    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    const PERMITTED_ROLES = [
        'Administrator',
        'Account manager',
        'Application manager',
    ];

    /**
     * List user roles.
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
//        $params = $request->getQueryParams();

        try {
            $result = $client->request('GET', 'user/role', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'query' => $params,
            ]);
            $userRoles = (array)json_decode($result->getBody()->getContents());
            $result = $client->request('GET', 'account/all', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $accounts = (array)json_decode($result->getBody()->getContents());
            $result = $client->request('GET', 'application', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $applications = (array)json_decode($result->getBody()->getContents());
            $result = $client->request('GET', 'user', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
            $users = (array)json_decode($result->getBody()->getContents());
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                default:
                    return $this->view->render($response, 'user-roles.twig', [
                        'menu' => $menu,
                        'user_roles' => [],
                        'messages' => $this->flash->getMessages(),
                    ]);
                    break;
            }
        }

        return $this->view->render($response, 'user-roles.twig', [
            'menu' => $menu,
            'params' => $params,
            'user_roles' => $userRoles,
            'accounts' => $accounts,
            'applications' => $applications,
            'users' => $users,
            'roles' => $this->allRoles,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Create a user role.
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
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);

        try {
            $result = $client->request('POST', 'user/role', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'form_params' => [
                    'uid' => $allPostVars['uid'],
                    'accid' => $allPostVars['accid'],
                    'appid' => $allPostVars['appid'],
                    'rid' => $allPostVars['rid'],
                ],
            ]);
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                default:
                    return $response->withStatus(302)->withHeader('Location', '/user/roles');
                    break;
            }
        }

        $this->flash->addMessage('info', 'User role created.');
        return $response->withStatus(302)->withHeader('Location', '/user/roles');
    }

    /**
     * Delete a user role.
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
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        $urid = $allPostVars['urid'];
        if (empty($urid)) {
            $this->flash->addMessage('error', 'Cannot delete user role, user role unspecified.');
            return $response->withStatus(302)->withHeader('Location', '/user/roles');
        }

        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);

        try {
            $result = $client->request('DELETE', 'user/role/' . $urid, [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
        } catch (ClientException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                default:
                    return $response->withStatus(302)->withHeader('Location', '/user/roles');
                    break;
            }
        }

        $this->flash->addMessage('info', 'User role deleted.');
        return $response->withStatus(302)->withHeader('Location', '/user/roles');
    }
}
