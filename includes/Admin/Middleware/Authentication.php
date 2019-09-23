<?php

namespace Gaterdata\Admin\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Gaterdata\Core\ApiException;
use GuzzleHttp\Client;


/**
 * Class Authentication.
 *
 * @package Gaterdata\Admin\Middleware
 */
class Authentication {

  /**
   * @var \Slim\Container
   */
  private $settings;
  /**
   * @var string
   */
  private $loginPath;
  /**
   * @var Container
   */
  private $container;

  /**
   * Authentication constructor.
   *
   * @param \Slim\Container $container
   *   Container.
   * @param array $settings
   *   Application settings.
   * @param string $loginPath
   *   Login URI.
   */
  public function __construct(Container $container, array $settings, $loginPath) {
    $this->container = $container;
    $this->settings = $settings;
    $this->loginPath = $loginPath;
  }

  /**
   * Middleware invocation.
   *
   * @param \Psr\Http\Message\ServerRequestInterface $request
   *   PSR7 request.
   * @param \Psr\Http\Message\ResponseInterface $response
   *   PSR7 Response.
   * @param callable $next
   *   Next middleware.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response Interface.
   */
  public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    // If login post, get login the values.
    $data = $request->getParsedBody();
    $username = isset($data['username']) ? $data['username'] : '';
    $password = isset($data['password']) ? $data['password'] : '';
    $uri = $request->getUri()->withPath($this->loginPath);

    if (!empty($username) || !empty($password)) {
      // This is a login attempt.
      try {
        $client = new Client([
          'base_uri'        => $this->settings['api']['url'],
          'timeout'         => 0,
        ]);
        $api_url = $this->settings['api']['common_account'] . '/' . $this->settings['api']['common_application'] . '/login';
        $response = $client->request('POST', $api_url, [
          'form_params' => [
            'username' => $username, 
            'password' => $password
          ]
        ]);
        if ($response->getStatusCode() != 200) {
          throw new ApiException('Access Denied');
        }
        $result = json_decode($response->getBody());
        $_SESSION['token'] = $result->token;
        $_SESSION['uid'] = $result->uid;
      } catch (ApiException $e) {
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        $this->container['flash']->addMessage('error', $e->getMessage());
      }
    }

    // Validate token and uid are set (valid login).
    if (!isset($_SESSION['token']) || !isset($_SESSION['uid'])) {
      return $response = $response->withRedirect($uri);
    }
    return $next($request, $response);
  }

}
