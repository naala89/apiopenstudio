<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlLogin.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlLogin extends CtrlBase
{

    /**
     * Login page.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \Exception
     */
    public function login(Request $request, Response $response, array $args)
    {
        $menu = $this->getMenus();
        return $this->view->render($response, 'login.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Logout page.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *   Response.
     *
     * @throws \Exception
     */
    public function logout(Request $request, Response $response, array $args)
    {
        $menu = $this->getMenus();
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        return $this->view->render($response, 'login.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }
}
