<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use stdClass;

/**
 * Class CtrlBase.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlBase {

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
   * @var array
   */
  protected $permittedRoles = [];

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
   * @param string $username
   *   Username.
   *
   * @return stdClass user access rights.
   */
  protected function getAccessRights($username) {
    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $response = $client->request('GET', 'userrole', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => [
          'username' => $username,
        ],
      ]);
      $result = json_decode($response->getBody()->getContents());
    } catch (ClientException $e) {
      // @TODO: This may not be the best way to trap unauthorized or timed out token.
      return false;
    } catch (RequestException $e) {
      // @TODO: This may not be the best way to trap unauthorized or timed out token.
      return false;
    }

    return $this->userAccessRights = $result;
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
        ];
      }
      if (in_array('Account manager', $roles)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Application manager', $roles)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
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
   * Get available accounts for user's roles.
   *
   * @return array
   *   Array of account names indexed by account ID.
   */
  protected function getAccounts() {
    $accounts = [];

    foreach($this->userAccessRights as $account) {
      $accounts[$account->account_id] = $account->account_name;
    }

    return $accounts;
  }

  /**
   * Get available applications for user's roles.
   *
   * @return array
   *   Array of application names indexed by application ID.
   */
  protected function getApplications() {
    $applications = [];

    foreach($this->userAccessRights as $account) {
      foreach($account as $application) {
        if (is_object($application) && isset($application->application_id) && isset($application->application_name)) {
          $applications[$application->application_id] = $application->application_name;
        }
      }
    }

    return $applications;
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

  /**
   * Validate user access by role.
   *
   * @return bool
   *   Access validated.
   */
  protected function checkAccess() {
    if (empty($this->permittedRoles)) {
      return TRUE;
    }
    $roles = $this->getRoles();
    foreach ($roles as $role) {
      if (in_array($role, $this->permittedRoles)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
