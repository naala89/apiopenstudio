<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;
use phpmailerException;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Hash;
use GuzzleHttp\Client;

/**
 * Class CtrlUsers.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlUsers extends CtrlBase
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
   * Display the users page.
   *
   * @param Request $request
   *   Request object.
   * @param Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();

        // Filter params.
        $query = [];
        $allParams = $request->getParams();
        if (!empty($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['order_by'])) {
            $query['order_by'] = $allParams['order_by'];
        }
        if (!empty($allParams['direction'])) {
            $query['direction'] = $allParams['direction'];
        }

        try {
            $result = $this->apiCall('get','user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => $query
                ],
                $response
            );
            $users = (array) json_decode($result->getBody()->getContents());
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
            $users = [];
        }

        // Pagination.
        $page = isset($params['page']) ? $allParams['page'] : 1;
        $pages = ceil(count($users) / $this->settings['admin']['paginationStep']);
        $users = array_slice($users,
        ($page - 1) * $this->settings['admin']['paginationStep'],
        $this->settings['admin']['paginationStep'],
        true);

        return $this->view->render($response, 'users.twig', [
            'menu' => $menu,
            'users' => $users,
            'page' => $page,
            'pages' => $pages,
            'params' => $allParams,
            'messages' => $this->flash->getMessages(),
        ]);
    }
}
