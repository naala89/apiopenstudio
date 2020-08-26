<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\BadResponseException;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Collection;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CtrlBase.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlBase
{

    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    protected $permittedRoles = [];

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
    protected $allRoles = [];

    /**
     * @var array,
     */
    protected $userAccessRights = [];

    /**
     * @var array
     */
    protected $userRoles = [];

    /**
     * @var array
     */
    protected $userAccounts = [];

    /**
     * @var array
     */
    protected $userApplications = [];

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
    public function __construct(Collection $settings, Twig $view, Messages $flash)
    {
        $this->settings = $settings;
        $this->view = $view;
        $this->flash = $flash;
    }

    /**
     * Make an API call.
     *
     * @param string $method
     *   Resource method.
     * @param string $uri
     *   Resource URI. This is the string after <domain>/<account>/<application>/.
     * @param array $requestOptions
     *   Request optionsL query params, header, etc.
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function apiCall($method, $uri, $requestOptions = []) {
        try {
            $requestOptions['protocols'] = $this->settings['api']['protocols'];
            $domain = $this->settings['api']['url'];
            $account = $this->settings['api']['core_account'];
            $application = $this->settings['api']['core_application'];
            $client = new Client(['base_uri' => "$domain/$account/$application/"]);
            return $client->request($method, $uri, $requestOptions);
        } catch (BadResponseException $e) {
            $result = $e->getResponse();
            switch ($result->getStatusCode()) {
                case 401:
                    throw new \Exception('Unauthorised');
                    break;
                default:
                    throw new \Exception($this->getErrorMessage($e));
                    break;
            }
        }
    }

    /**
     * Fetch all user roles for a user.
     *
     * @param integer $uid
     *   User ID.
     *
     * @return array|mixed
     */
    protected function apiCallUserRoles($uid) {
        $userRoles = [];
        try {
            $result = $this->apiCall('GET', 'user/role', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ], 'query' => ['uid' => $uid],
            ]);
            $userRoles = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }
        return $userRoles;
    }

    /**
     * Fetch all roles.
     *
     * @return array|mixed
     */
    protected function apiCallRolesAll() {
        $allRoles = [];
        try {
            $result = $this->apiCall('GET', 'role/all', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ]);
            $result = json_decode($result->getBody()->getContents(), true);
            foreach ($result as $role) {
                $allRoles[$role['rid']] = $role['name'];
            }
        } catch (\Exception $e) {
        }
        return $allRoles;
    }

    /**
     * Fetch all Accounts.
     *
     * @param array $params
     *   Sort params.
     *
     * @return array|mixed
     */
    protected function apiCallAccountAll(array $params = []) {
        $allAccounts = $query = [];
        foreach ($params as $key => $value) {
            $query[$key] = $value;
        }
        $query['order_by'] = empty($query['order_by']) ? 'name' : $query['order_by'];

        try {
            $result = $this->apiCall('GET', 'account', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ]);
            $allAccounts = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $allAccounts;
    }

    /**
     * Fetch all applications from the API.
     *
     * @param array $params
     *   Sort params.
     *
     * @return array|mixed
     */
    protected function apiCallApplicationAll(array $params = []) {
        foreach ($params as $key => $value) {
            $query[$key] = $value;
        }
        $query['order_by'] = empty($query['order_by']) ? 'name' : $query['order_by'];

        $allApplications = [];
        try {
            $result = $this->apiCall('GET', 'application', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ]);
            $allApplications = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
        }

        return $allApplications;
    }

    /**
     * Fetch the access rights for a user.
     *
     * @param integer $uid
     *    User ID.
     *
     * @return array user access rights.
     *   [
     *     <accid>> => [
     *       <appid> => [
     *         <rid>,
     *       ],
     *     ],
     *   ]
     *
     * @throws \Exception
     */
    private function getAccessRights($uid = 0)
    {
        if ($uid == 0) {
            $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        }
        $userRoles = $this->apiCallUserRoles($uid);

        $userAccessRights = [];

        foreach ($userRoles as $userRole) {
            $accid = $userRole['accid'] == null ? 0 : $userRole['accid'];
            $appid = $userRole['appid'] == null ? 0 : $userRole['appid'];
            $userAccessRights[$accid][$appid][] = $userRole['rid'];
        }

        return $userAccessRights;
    }

    /**
     * Get all roles for user.
     *
     * @return array
     *   [<rid> => <rolename>]
     *
     * @throws \Exception
     */
    private function getRoles()
    {
        $roles = [];

        foreach ($this->userAccessRights as $accid => $appids) {
            foreach ($appids as $appid => $rids) {
                foreach ($rids as $rid) {
                    $roles[$rid] = $this->allRoles[$rid];
                }
            }
        }

        return $roles;
    }

    /**
     * Get accounts for the user.
     *
     * @param array $params
     *   Sort and filter params.
     *
     * @return array
     *   Array of account names that the user has permissions for
     *   [<accid> => <account_name>]
     *
     * @throws \Exception
     */
    private function getAccounts(array $params = [])
    {
        $allAccounts = $query = [];
        foreach ($params as $key => $value) {
            $query[$key] = $value;
        }

        try {
            $result = $this->apiCall('GET', 'account', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ]);
            $allAccounts = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
        }

        return $allAccounts;
    }

    /**
     * Get applications for the user.
     *
     * @param array $params
     *   Sort and filter params.
     *
     * @return array
     *   Array of applications and the account they belong to:
     *     [
     *       appid => [
     *         'name' => <app_name>,
     *         'accid' => <accid>,
     *       ],
     *     ]
     *
     * @throws \Exception
     */
    protected function getApplications(array $params = [])
    {
        $allApplications = $this->apiCallApplicationAll($params);

        if (isset($this->userAccessRights[0])) {
            // User has access to all accounts, so all applications.
            return $allApplications;
        }

        $applications = [];
        foreach ($this->userAccessRights as $accid => $apps) {
            if (isset($apps[0])) {
                // User has access to all applications in the account.
                foreach ($allApplications as $appid => $application) {
                    if ($accid == $application['accid']) {
                        $applications[$appid] = $application;
                    }
                }
            } else {
                foreach ($apps as $appid => $rids) {
                    $applications[$appid] = $allApplications[$appid];
                }
            }
        }

        return $applications;
    }

    /**
     * Validate user access by role.
     *
     * @return bool
     *   Access validated.
     *
     * @throws \Exception
     */
    protected function checkAccess()
    {
        if (empty($this->userAccessRights) || empty($this->allRoles)) {
            $this->allRoles = $this->apiCallRolesAll();
            $this->userAccessRights = $this->getAccessRights();
            $this->userAccounts = $this->getAccounts();
            $this->userApplications = $this->getApplications();
            $this->userRoles = $this->getRoles();
        }
        if (empty($this->permittedRoles)) {
            return true;
        }

        foreach ($this->userRoles as $rid => $name) {
            if (in_array($name, $this->permittedRoles)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get available menu items for user's roles.
     *
     * @return array
     *   Associative array of menu titles and links.
     *
     * @throws \Exception
     */
    protected function getMenus()
    {
        $menus = [];

        if (empty($_SESSION['uid'])) {
            $menus += [
                'Login' => '/login',
            ];
        } else {
            $menus += [
                'Home' => '/',
            ];
            if (in_array('Administrator', $this->userRoles)) {
                $menus += [
                    'Accounts' => '/accounts',
                    'Applications' => '/applications',
                    'Users' => '/users',
                    'Invites' => '/invites',
                    'User Roles' => '/user/roles',
                    'Roles' => '/roles',
                ];
            }
            if (in_array('Account manager', $this->userRoles)) {
                $menus += [
                    'Accounts' => '/accounts',
                    'Applications' => '/applications',
                    'Users' => '/users',
                    'Invites' => '/invites',
                    'User Roles' => '/user/roles',
                    'Roles' => '/roles',
                ];
            }
            if (in_array('Application manager', $this->userRoles)) {
                $menus += [
                    'Accounts' => '/accounts',
                    'Applications' => '/applications',
                    'Users' => '/users',
                    'Invites' => '/invites',
                    'User Roles' => '/user/roles',
                ];
            }
            if (in_array('Developer', $this->userRoles)) {
                $menus += [
                    'Accounts' => '/accounts',
                    'Applications' => '/applications',
                    'Resources' => '/resources',
                    'Vars' => '/vars',
                ];
            }
            $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
            $menus += [
                'My account' => "/user/view/$uid",
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
    protected function getErrorMessage($e)
    {
        if ($e->hasResponse()) {
            $responseObject = json_decode($e->getResponse()->getBody()->getContents());
            $message = $responseObject->error->message;
        }
        if (empty($message)) {
            $message = $e->getMessage();
        }
        return $message;
    }
}
