<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
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
   * @var array,
   */
  protected $allRoles = [
    1 => 'Administrator',
    2 => 'Account manager',
    3 => 'Application manager',
    4 => 'Developer',
  ];

  /**
   * Base constructor.
   *
   * @param Collection $settings
   *   Settings array.
   * @param Twig $view
   *   View container.
   * @param Messages $flash
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
   * @param Response
   *   Response object.
   * @param string $uid
   *   User ID.
   *
   * @return stdClass user access rights.
   *
   * @throws GuzzleException
   */
  protected function getAccessRights($response, $uid) {
    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('GET', 'user/role', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => [
          'uid' => $uid,
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

    $this->userAccessRights = [];
    foreach ($result as $userRole) {
      if ($userRole->accid == NULL && $userRole->appid == NULL) {
        $this->userAccessRights[0][0][] = $userRole->rid;
      }
      else {
        $this->userAccessRights[$userRole->accid][$userRole->appid][] = $userRole->rid;
      }
    }

    return $this->userAccessRights;
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
        foreach($application as $rid) {
          $roles[$rid] = $this->allRoles[$rid];
        }
      }
    }

    return $roles;
  }

  /**\
   * Get accounts for the user.
   *
   * @param Response $response
   *   Response object.
   * @param array $params
   *   Sort and filter params.
   *
   * @return array
   *   Array of account names, indexed by accid.
   *
   * @throws GuzzleException
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
   * @param Response $response
   *   Response object.
   * @param array $params
   *   Sort and filter params.
   *
   * @return array
   *   Array of applications and the account they belong to:
   *     [accid => [appid => name]]
   *
   * @throws GuzzleException
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
    foreach ($roles as $rid => $name) {
      if (in_array($name, self::PERMITTED_ROLES)) {
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
