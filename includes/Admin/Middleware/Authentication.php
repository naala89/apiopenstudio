<?php

namespace Gaterdata\Admin\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Gaterdata\Core\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
   * 
   * @TODO: Validate token resource.
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
        $domain = $this->settings['api']['url'];
        $account = $this->settings['api']['core_account'];
        $application = $this->settings['api']['core_application'];
        $client = new Client(['base_uri' => "$domain/$account/$application/"]);
        $result = $client->request('POST', "login", [
          'form_params' => [
            "username" => $username,
            "password" => $password,
          ]
        ]);
        $result = json_decode($result->getBody()->getContents());
        if (!isset($result->token) || !isset($result->uid)) {
          throw new ApiException('Invalid response from api login');
        }
        $_SESSION['token'] = $result->token;
        $_SESSION['uid'] = $result->uid;
        $_SESSION['username'] = $username;
      } catch (ApiException $e) {
        $this->loginError($e);
      } catch (ClientException $e) {
        $this->loginError($e);
      } catch (RequestException $e) {
        $this->loginError($e);
      }
    }

    // Validate token and uid are set (valid login).
    if (!isset($_SESSION['token']) || !isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
      return $response->withStatus(302)->withHeader('Location', $uri);
    }
    return $next($request, $response);
  }

  private function loginError($e) {
    if ($e->hasResponse()) {
      $responseObject = json_decode($e->getResponse()->getBody()->getContents());
      $message = $responseObject->error->message;
    } else {
      $message = $e->getMessage();
    }
    unset($_SESSION['token']);
    unset($_SESSION['uid']);
    unset($_SESSION['username']);
    $this->container['flash']->addMessage('error', $message);
  }

}
