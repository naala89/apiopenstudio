<?php

namespace Gaterdata\Admin\Controllers;

use Gaterdata\Core\Debug;
use Slim\Flash\Messages;
use Slim\Views\Twig;
// use Gaterdata\Admin\User;
// use Gaterdata\Core\ApiException;
use Slim\Collection;
use GuzzleHttp\Client;
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
    $this->settings = $settings;
    $this->view = $view;
    $this->flash = $flash;
  }

  /**
   * Fetch the roles for a user ID.
   *
   * @param string $username
   *   Username.
   *
   * @return stdClass
   *    Raw decoded result form gaterdata.
   */
  protected function getRoles($username) {
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
      $this->catchApiError($e);
      $result = new stdClass();
    } catch (RequestException $e) {
      if ($response->getStatusCode() == 401) {
        $this->catchApiError($e);
      }
      $result = new stdClass();
    }
      
    return $result;
  }

  /**
   * Get available menu items for user's roles.
   *
   * @param stdClass $roles
   *   Raw decoded result form gaterdata.
   *
   * @return array
   *   Associative array of menu titles and links.
   */
  protected function getMenus(stdClass $roles) {
    $menus = [];
    $roleNames = [];

    foreach($roles as $account) {
      foreach($account as $application) {
        foreach($application as $role) {
          if (is_object($role) && isset($role->role_name) && isset($role->role_id)) {
            $roleNames[$role->role_id] = $role->role_name;
          }
        }
      }
    }

    if (empty($roles)) {
      $menus += [
        'Login' => '/login',
      ];
    }
    else {
      $menus += [
        'Home' => '/',
      ];
      if (in_array('Administrator', $roleNames)) {
        $menus += [
          'Accounts' => '/accounts',
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Account manager', $roleNames)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Application manager', $roleNames)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Developer', $roleNames)) {
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
   * Validate user access by role.
   *
   * @param array $roles
   *   Array of user roles (text).
   *
   * @return bool
   *   Access validated.
   */
  protected function checkAccess(array $roles) {
    if (empty($this->permittedRoles)) {
      return TRUE;
    }
    foreach ($this->permittedRoles as $permittedRole) {
      if (in_array($permittedRole, $roles)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  private function catchApiError($e) {
    if ($e->hasResponse()) {
      $responseObject = json_decode($e->getResponse()->getBody()->getContents());
      $message = $responseObject->error->message;
    } else {
      $message = $e->getMessage();
    }
    $this->container['flash']->addMessage('error', $message);
  }

}
