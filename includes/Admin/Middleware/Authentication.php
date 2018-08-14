<?php

namespace Datagator\Admin\Middleware;

use Datagator\Admin\User;

/**
 * Class Authentication.
 *
 * @package Datagator\Admin\Middleware
 */
class Authentication {
  private $settings;
  private $uri;

  /**
   * Authentication constructor.
   *
   * @param array $settings
   *   Application settings.
   * @param string $loginPath
   *   Login URI.
   */
  public function __construct(array $settings, $loginPath) {
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
  public function __invoke($request, $response, callable $next) {
    $data = $request->getParsedBody();
    $account = isset($data['account']) ? $data['account'] : '';
    $username = isset($data['username']) ? $data['username'] : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if (!empty($account) || !empty($username) || !empty($password)) {
      $user = new User($this->settings);
      $result = $user->adminLogin($account, $username, $password);
      if (!$result) {
        unset($_SESSION['token']);
        unset($_SESSION['accountId']);
        unset($_SESSION['account']);
        $uri = $request->getUri()->withPath($this->loginPath);
        return $response = $response->withRedirect($uri);
      }
      $_SESSION['token'] = $result['token'];
      $_SESSION['account'] = $result['account'];
      $_SESSION['accountId'] = $result['accountId'];
    }

    if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
      $uri = $request->getUri()->withPath($this->loginPath);
      return $response = $response->withRedirect($uri);
    }
    return $next($request, $response);
  }

}
