<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use stdClass;
use Slim\Http\Response;

/**
 * Class CtrlBase.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlBase {

  /**
   * Roles allowed to visit the page.
   * 
   * @var array
   */
  const PERMITTED_ROLES = [];
  /**
   * @var Slim\Collection
   */
  protected $settings;
  /**
   * @var Twig
   */
  protected $view;
  /**
   * @var Messages.
   */
  protected $flash;
  /**
   * @var array.
   */
  protected $menu;
  /**
   * @var stdClass,
   */
  protected $userAccessRights;

  /**
   * Base constructor.
   *
   * @param \Slim\Collection $settings
   *   Settings array.
   * @param \Slim\Views\Twig $view
   *   View container.
   * @param \Slim\Flash\Messages $flash
   *   Flash messages container.
   */
  public function __construct(Collection $settings, Twig $view, Messages $flash) {
    $this->userAccessRights = new stdClass();
    $this->settings = $settings;
    $this->view = $view;
    $this->flash = $flash;
  }

  /**
   * Fetch the access rights for a user.
   *
   * @param \Slim\Http\Response
   *   Response object.
   * @param string $username
   *   Username.
   *
   * @return stdClass user access rights.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function getAccessRights($response, $username) {
    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('GET', 'userrole', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => [
          'username' => $username,
        ],
      ]);
      $result = json_decode($result->getBody()->getContents());
    } 
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401: 
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          break;
      }
    }

    return $this->userAccessRights = $result;
  }

  /**
   * Get available roles for user's roles.
   *
   * @return array
   *   Array of role names indexed by role ID.
   */
  protected function getRoles() {
    $roles = [];

    foreach($this->userAccessRights as $account) {
      foreach($account as $application) {
        foreach($application as $role) {
          if (is_object($role) && isset($role->role_name) && isset($role->role_id)) {
            $roles[$role->role_id] = $role->role_name;
          }
        }
      }
    }

    return $roles;
  }

  /**\
   * Get accounts for the user.
   *
   * @param \Slim\Http\Response
   *   Response object.
   * @param array $params
   *   Sort and filter params.
   *
   * @return array
   *   Array of account names, indexed by accid.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */

  protected function getAccounts(Response $response, array $params = []) {
    $roles = $this->getRoles();
    $accounts = [];

    if (in_array('Administrator', $roles)) {
      // Fetch all accounts from the API.
      try {
        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);
        foreach($params as $key => $value) {
          $query[$key] = $value;
        }

        $result = $client->request('GET', 'account/all', [
          'headers' => [
            'Authorization' => "Bearer $token",
          ],
          'query' => $query,
        ]);
        $result = json_decode($result->getBody()->getContents());

        foreach ((array) $result as $accid => $name) {
          $accounts[$accid] = $name;
        }
      } catch (ClientException $e) {
        $result = $e->getResponse();
        switch ($result->getStatusCode()) {
          case 401: 
            return $response->withStatus(302)->withHeader('Location', '/login');
            break;
          default:
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            $accounts = [];
            break;
        }
      }
    }
    else {
      // Not admin, so take accounts from user access rights.
      foreach((array) $this->userAccessRights as $accid => $account) {
        $accounts[$accid] = $account->account_name;
      }
    }

    return $accounts;
  }

  /**
   * Get applications for the user.
   *
   * @param \Slim\Http\Response
   *   Response object.
   * @param array $params
   *   Sort and filter params.
   *
   * @return array
   *   Array of applications and the account they belong to:
   *     [accid => [appid => name]]
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function getApplications(Response $response, array $params = []) {
    $roles = $this->getRoles();
    $applications = [];

    if (in_array('Administrator', $roles)) {
      // Fetch all accounts from the API.
      try {
        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $token = $_SESSION['token'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);
        $query = ['account_name' => 'all'];
        $query = ['application_name' => 'all'];
        foreach($params as $key => $value) {
          $query[$key] = $value;
        }

        $result = $client->request('GET', 'application', [
          'headers' => [
            'Authorization' => "Bearer $token",
          ],
          'query' => $query,
        ]);
        $applications = json_decode($result->getBody()->getContents());
      } catch (ClientException $e) {
        $result = $e->getResponse();
        switch ($result->getStatusCode()) {
          case 401: 
            return $response->withStatus(302)->withHeader('Location', '/login');
            break;
          default:
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            $applications = [];
            break;
        }
      }
    }
    else {
      // Not admin, so take accounts from user access rights.
      foreach((array) $this->userAccessRights as $accid => $accounts) {
        var_dump($accounts);die();
      }
    }

    return $applications;
  }

  /**
   * Validate user access by role.
   *
   * @return bool
   *   Access validated.
   */
  protected function checkAccess() {
    if (empty(self::PERMITTED_ROLES)) {
      return TRUE;
    }

    $roles = $this->getRoles();
    foreach ($roles as $role) {
      if (in_array($role, self::PERMITTED_ROLES)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Get available menu items for user's roles.
   *
   * @return array
   *   Associative array of menu titles and links.
   */
  protected function getMenus() {
    $menus = [];
    $roles = $this->getRoles();

    if (empty($roles)) {
      $menus += [
        'Login' => '/login',
      ];
    }
    else {
      $menus += [
        'Home' => '/',
      ];
      if (in_array('Administrator', $roles)) {
        $menus += [
          'Accounts' => '/accounts',
          'Applications' => '/applications',
          'Users' => '/users',
          'User Roles' => '/user_roles',
          'Roles' => '/roles',
        ];
      }
      if (in_array('Account manager', $roles)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
          'User Roles' => '/user_roles',
          'Roles' => '/roles',
        ];
      }
      if (in_array('Application manager', $roles)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
          'User Roles' => '/user_roles',
        ];
      }
      if (in_array('Developer', $roles)) {
        $menus += [
          'Resources' => '/resources',
        ];
      }
      $menus += [
        'Logout' => '/logout',
      ];
    }

    return $menus;
  }
  
  /**
   * Get an error message from a API call exception.
   * 
   * @param  mixed $e
   *
   * @return string
   */
  protected function getErrorMessage($e) {
    if ($e->hasResponse()) {
      $responseObject = json_decode($e->getResponse()->getBody()->getContents());
      $message = $responseObject->error->message;
    } else {
      $message = $e->getMessage();
    }
    return $message;
  }

}
