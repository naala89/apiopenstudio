<?php

namespace Datagator\Admin\Middleware;

use Datagator\Admin\User;
use Datagator\Core\ApiException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Authentication.
 *
 * @package Datagator\Admin\Middleware
 */
class Authentication {
  private $settings;
  private $loginPath;

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
  public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    // If login post, get login the values.
    $data = $request->getParsedBody();
    $accountName = isset($data['account']) ? $data['account'] : '';
    $username = isset($data['username']) ? $data['username'] : '';
    $password = isset($data['password']) ? $data['password'] : '';
    $uri = $request->getUri()->withPath($this->loginPath);

    // This is a login attempt.
    if (!empty($accountName) || !empty($username) || !empty($password)) {
      try {
        $userHelper = new User($this->settings['db']);
      } catch (ApiException $e) {
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        return $next($request, $response);
      }

      $loginResult = $userHelper->adminLogin($accountName, $username, $password, $this->settings['user']['token_life']);
      if (!$loginResult) {
        // Login failed.
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
      } else {
        $_SESSION['token'] = $loginResult['token'];
        $_SESSION['uid'] = $loginResult['uid'];
      }
    }

    // Validate token.
    if (!isset($_SESSION['token']) || !isset($_SESSION['uid'])) {
      return $response = $response->withRedirect($uri);
    }
    return $next($request, $response);
  }

}
