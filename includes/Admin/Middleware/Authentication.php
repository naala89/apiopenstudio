<?php
/**
 * Class Authentication.
 *
 * @package    ApiOpenStudio
 * @subpackage Admin\Middleware
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Admin\Middleware;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Authentication.
 *
 * A Slim PHP middle class to validate a user's logged in status and redirect if unauthenticated.
 */
class Authentication
{
    /**
     * Settings container.
     *
     * @var Container
     */
    private $settings;

    /**
     * Login URI.
     *
     * @var string
     */
    private $loginPath;

    /**
     * Middleware container.
     *
     * @var Container
     */
    private $container;

    /**
     * Authentication constructor.
     *
     * @param Container $container Container.
     * @param array $settings Application settings.
     * @param string $loginPath Login URI.
     */
    public function __construct(Container $container, array $settings, string $loginPath)
    {
        $this->container = $container;
        $this->settings = $settings;
        $this->loginPath = $loginPath;
    }

    /**
     * Middleware invocation.
     *
     * @param ServerRequestInterface $request PSR7 request.
     * @param ResponseInterface $response PSR7 Response.
     * @param callable $next Next middleware.
     *
     * @return ResponseInterface Response Interface.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $data = $request->getParsedBody();
        $username = isset($data['username']) ? $data['username'] : '';
        $password = isset($data['password']) ? $data['password'] : '';

        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);

        if (!empty($username) || !empty($password)) {
            // This is a login attempt.
            try {
                $result = $client->request('POST', "login", [
                    'form_params' => [
                        'username' => $username,
                        'password' => $password,
                    ]
                ]);
                $result = json_decode($result->getBody()->getContents());
                if (!isset($result->token) || !isset($result->uid)) {
                    return $response->withStatus(302)->withHeader('Location', '/login');
                }
                $_SESSION['token'] = $result->token;
                $_SESSION['uid'] = $result->uid;
                $_SESSION['username'] = $username;
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents(), true);
                $this->container['flash']->addMessage('error', $json['error']['message']);
            } catch (ClientException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents(), true);
                $this->container['flash']->addMessage('error', $json['error']['message']);
            } catch (ServerException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents(), true);
                $this->container['flash']->addMessage('error', $json['error']['message']);
            } catch (GuzzleException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents(), true);
                $this->container['flash']->addMessage('error', $json['error']['message']);
            }
        } else {
            // Validate the token and username.
            try {
                $token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
                $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
                $client->request('GET', "user", [
                    'headers' => ['Authorization' => "Bearer " . $token],
                    'query' => ['username' => $username],
                ]);
            } catch (BadResponseException $e) {
                $this->container['flash']->addMessage('error', $e->getMessage());
                unset($_SESSION['token']);
                unset($_SESSION['username']);
                unset($_SESSION['uid']);
            } catch (ClientException $e) {
                $this->container['flash']->addMessage('error', 'Permission denied.');
                unset($_SESSION['token']);
                unset($_SESSION['username']);
                unset($_SESSION['uid']);
            } catch (RequestException $e) {
                $this->container['flash']->addMessage('error', $e->getMessage());
                unset($_SESSION['token']);
                unset($_SESSION['username']);
                unset($_SESSION['uid']);
            } catch (ServerException $e) {
                $this->container['flash']->addMessageNow('error', 'Internal server error');
                unset($_SESSION['token']);
                unset($_SESSION['username']);
                unset($_SESSION['uid']);
            } catch (GuzzleException $e) {
                $this->container['flash']->addMessageNow('error', 'Internal server error');
                unset($_SESSION['token']);
                unset($_SESSION['username']);
                unset($_SESSION['uid']);
            }
        }

        // Validate token and uid are set (valid login).
        if (!isset($_SESSION['token']) || !isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
            $loginPath = $request->getUri()->withPath($this->loginPath);
            return $response->withStatus(302)->withHeader('Location', $loginPath);
        }
        return $next($request, $response);
    }
}
