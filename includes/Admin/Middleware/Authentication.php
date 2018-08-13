<?php

namespace Datagator\Admin\Middleware;

use Datagator\Admin\User;

class Authentication
{
  private $settings;
  private $uri;

  /**
   * Authentication constructor.
   *
   * @param $settings
   * @param $loginPath
   */
  public function __construct($settings, $loginPath)
  {
    $this->settings = $settings;
    $this->loginPath = $loginPath;
  }

  /**
   *
   * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
   * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
   * @param  callable                                 $next     Next middleware
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function __invoke($request, $response, $next)
  {
    $data = $request->getParsedBody();
    $account = isset($data['account']) ? $data['account'] : '';
    $username = isset($data['username']) ? $data['username'] : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if (!empty($account) || !empty($username) || !empty($password)) {
      $user = new User($this->settings);
      $token = $user->adminLogin($account, $username, $password);
      if (!$token) {
        unset($_SESSION['token']);
        $uri = $request->getUri()->withPath($this->loginPath);
        return $response = $response->withRedirect($uri);
      }
      $_SESSION['token'] = $token;
    }

    if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
      $uri = $request->getUri()->withPath($this->loginPath);
      return $response = $response->withRedirect($uri);
    }
    return $next($request, $response);
  }
}
