<?php

/**
 * Class CtrlHome.
 *
 * Controller for the home page.
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

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlHome.
 *
 * Controller for the home page.
 */
class CtrlHome extends CtrlBase
{
    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    protected $permittedRoles = [];

    /**
     * Home page.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface|Response Response.
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
