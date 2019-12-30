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
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allParams = $request->getParams();

        $query = [];
        if (!empty($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['direction'])) {
            $query['order_by'] = 'name';
            $query['direction'] = $allParams['direction'];
        }
//        var_dump($query);die;

        try {
            $result = $this->apiCall('get', 'role/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => $query,
                ],
                $response
            );
            $roles = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow($e->getMessage());
            $roles = [];
        }

        return $this->view->render($response, 'roles.twig', [
            'menu' => $menu,
            'roles' => $roles,
            'messages' => $this->flash->getMessages(),
        ]);
    }
}
