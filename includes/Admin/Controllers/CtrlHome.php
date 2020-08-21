<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlHome.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlHome extends CtrlBase
{

    /**
     * {@inheritdoc}
     */
    protected $permittedRoles = [];

    /**
     * Home page.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     *   Response.
     *
     * @throws \Exception
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access denied');
            return $response->withStatus(302)->withHeader('Location', '/logout');
        }

        $menu = $this->getMenus();

        return $this->view->render($response, 'home.twig', [
            'menu' => $menu,
            'accounts' => $this->userAccounts,
            'applications' => $this->userApplications,
            'roles' => $this->userRoles,
            'flash' => $this->flash,
        ]);
    }
}
