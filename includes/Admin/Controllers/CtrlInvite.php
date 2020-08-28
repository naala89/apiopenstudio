<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Hash;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Class CtrlInvite.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlInvite extends CtrlBase
{

    /**
     * {@inheritdoc}
     */
    protected $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
    ];

    /**
     * View all current invites.
     *
     * @param Request $request
     *   Slim request object.
     * @param Response $response
     *   Slim response object.
     * @param array $args
     *   Slim args array
     *
     * @return ResponseInterface|Response
     *
     * @throws GuzzleException
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Invites: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }
        $menu = $this->getMenus();

        $params = $request->getParams();

        $invites = [];
        try {
            $result = $this->apiCall('get', 'invite',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => $params,
                ]
            );
            $invites = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        // Pagination.
        $page = isset($params['page']) ? $params['page'] : 1;
        $pages = ceil(count($invites) / $this->settings['admin']['pagination_step']);
        $invites = array_slice($invites,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true);

        return $this->view->render($response, 'invites.twig', [
            'menu' => $menu,
            'invites' => $invites,
            'params' => $params,
            'page' => $page,
            'pages' => $pages,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Delete an invite.
     *
     * @param Request $request
     *   Slim request object.
     * @param Response $response
     *   Slim response object.
     * @param array $args
     *   Slim args array
     *
     * @return ResponseInterface|Response
     *
     * @throws GuzzleException
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Invites: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }
        $menu = $this->getMenus();

        if (empty($iid = $args['iid'])) {
            $this->flash->addMessageNow('error', 'Delete invite: invalid invite ID');
            return $response->withStatus(302)->withHeader('Location', '/invites');
        }

        try {
            $result = $this->apiCall('delete', "invite/$iid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            $this->flash->addMessageNow('info', $result);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        $invites = [];
        try {
            $result = $this->apiCall('get', 'invite',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $invites = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        // Pagination.
        $page = 1;
        $pages = ceil(count($invites) / $this->settings['admin']['pagination_step']);
        $invites = array_slice($invites,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true);

        return $this->view->render($response, 'invites.twig', [
            'menu' => $menu,
            'invites' => $invites,
            'params' => [],
            'page' => $page,
            'pages' => $pages,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Invite a single or multiple users to GaterData.
     *
     * @param Request $request
     *   Slim request object.
     * @param Response $response
     *   Slim response object.
     * @param array $args
     *   Slim args array
     *
     * @return ResponseInterface|Response
     *
     * @throws GuzzleException
     */
    public function invite(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($email = $allPostVars['invite-email'])) {
            $this->flash->addMessage('error', 'Invite user: email not specified');
            return $response->withRedirect('/invites');
        }

        try {
            $result = $this->apiCall('post', "user/invite",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'form_params' => ['email' => $email],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);

            $message = '';
            if (isset($result['resent'])) {
                $message .= "<p><b>Resent invites:</b><br/>";
                foreach ($result['resent'] as $email) {
                    $message .= "$email<br/>";
                }
                $message .= "</p>";
            }
            if (isset($result['success'])) {
                $message .= "<p><b>Sent invites:</b><br/>";
                foreach ($result['success'] as $email) {
                    $message .= "$email<br/>";
                }
                $message .= "</p>";
            }
            if (isset($result['fail'])) {
                $message .= "<p><b>Failed invites:</b><br/>";
                foreach ($result['fail'] as $email) {
                    $message .= "$email<br/>";
                }
                $message .= "</p>";
            }
            $this->flash->addMessage('info', $message);
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }

        return $response->withStatus(302)->withHeader('Location', '/invites');
    }
}
