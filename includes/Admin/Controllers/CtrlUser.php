<?php

namespace Gaterdata\Admin\Controllers;

use Gaterdata\Core\Debug;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;
use phpmailerException;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Hash;
use GuzzleHttp\Client;

/**
 * Class CtrlUser.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlUser extends CtrlBase {

  /**
   * Roles allowed to visit the page.
   * 
   * @var array
   */
  const PERMITTED_ROLES = [
    'Administrator',
    'Account manager',
    'Application manager',
  ];

  /**
   * Display the user page.
   *
   * @param Request $request
   *   Request object.
   * @param Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return ResponseInterface
   *   Response.
   *
   * @throws GuzzleException
   */
  public function index(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();
    $uid = $args['uid'];
    $mode = strpos($request->getUri()->getPath(), 'edit') !== FALSE ? 'edit' : 'view';

    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('GET', 'user', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => [
          'uid' => $uid,
        ],
      ]);
      $user = (array) json_decode($result->getBody()->getContents());
    } 
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401: 
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $user = [];
          break;
      }
    }

    return $this->view->render($response, 'user.twig', [
      'menu' => $menu,
      'user' => $user,
      'mode' => $mode,
    ]);
  }

  /**
   * Create a new user.
   *
   * @param Request $request
   *   Request object.
   * @param Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return ResponseInterface
   *   Response.
   *
   * @throws GuzzleException
   */
  public function create(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();

    if (strtolower($request->getMethod()) == 'get') {
      return $this->view->render($response, 'user-create.twig', [
        'menu' => $menu,
      ]);
    }

    $allPostVars = $request->getParams();

    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('POST', 'user/create', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'form_params' => $allPostVars,
      ]);
      $user = (array) json_decode($result->getBody()->getContents());
    }
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401:
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $user = [];
          break;
      }
    }

    if (empty($user['uid'])) {
      return $this->view->render($response, 'user-create.twig', [
        'menu' => $menu,
      ]);
    }
    return $this->view->render($response, 'user.twig', [
      'menu' => $menu,
      'user' => $user,
      'mode' => 'edit',
    ]);
  }

  /**
   * Update a user.
   *
   * @param Request $request
   *   Request object.
   * @param Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return ResponseInterface
   *   Response.
   *
   * @throws GuzzleException
   */
  public function update(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();
    $allPostVars = $request->getParams();
    $uid = $args['uid'];

    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('PUT', "user/$uid", [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => $allPostVars,
      ]);
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

    try {
      $result = $client->request('GET', 'user', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => [
          'uid' => $uid,
        ],
      ]);
      $user = (array) json_decode($result->getBody()->getContents());
    }
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401:
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $user = [];
          break;
      }
    }

    return $this->view->render($response, 'user.twig', [
      'menu' => $menu,
      'user' => $user,
      'mode' => 'edit',
    ]);
  }

  /**
   * Delete a user account and its associated roles.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return ResponseInterface
   *   Response.
   *
   * @throws GuzzleException
   */
  public function delete(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();
    $uid = $args['uid'];

    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('DELETE', "user/$uid", [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
      ]);
      $this->flash->addMessage('info', 'User successfully deleted.');
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

    return $response->withStatus(302)->withHeader('Location', '/users');
  }

  /**
   * Send a user an email with a token to register.
   *
   * The email templates are in /includes/Admin/templates/invite-user.email.twig.
   *
   * @param Request $request
   *   Slim request object.
   * @param Response $response
   *   Slim response object.
   * @param array $args
   *   Slim args array
   *
   * @return ResponseInterface|Response
   *
   * @throws GuzzleException
   * @throws \PHPMailer\PHPMailer\Exception
   */
  public function invite(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($email = $allPostVars['invite-email'])) {
      $this->flash->addMessage('error', 'Invite user: email not specified');
      return $response->withRedirect('/users');
    }

    // Check if user already exists.
    try {
      $userHlp = new User($this->dbSettings);
      $user = $userHlp->findByEmail($email);
      if (!empty($user['uid'])) {
        return $this->view->render($response, 'users.twig', [
          'menu' => $menu,
          'message' => [
            'type' => 'error',
            'text' => 'A user already exists with this email: ' . $email,
          ]
        ]);
      }
    } catch (ApiException $e) {
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => [
          $message['type'] = 'error',
          $message['text'] = $e->getMessage(),
        ]
      ]);
    }

    // Generate vars for the email.
    $token = Hash::generateToken($email);
    $host = $this->getHost();
    $scheme = $request->getUri()->getScheme();
    $link = "$scheme://$host/user/register/$token";

    // Add invite to DB.
    try {
      $accountHlp = new Account($this->dbSettings);
      $account = $accountHlp->findByUaid($uaid);
      $inviteHlp = new Invite($this->dbSettings);
      // Remove any old invites for this email.
      $inviteHlp->deleteByEmail($email);
      // Add new invite.
      $inviteHlp->create($account['accid'], $email, $token);
    } catch (ApiException $e) {
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => [
          'type' => 'error',
          'text' => $e->getMessage(),
        ]
      ]);
    }

    // Send the email.
    $mail = new PHPMailer(TRUE); // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->SMTPDebug = $this->mailSettings['debug'];
      $mail->isSMTP($this->mailSettings['smtp']);
      $mail->Host = $this->mailSettings['host'];
      $mail->SMTPAuth = $this->mailSettings['auth'];
      $mail->Username = $this->mailSettings['username'];
      $mail->Password = $this->mailSettings['password'];
      $mail->SMTPSecure = $this->mailSettings['smtpSecure'];
      $mail->Port = $this->mailSettings['port'];

      //Recipients
      $mail->addAddress($email);
      $mail->setFrom($this->mailSettings['from']['email'], $this->mailSettings['from']['name']);
      $mail->addReplyTo($this->mailSettings['from']['email'], $this->mailSettings['from']['name']);

      //Content
      $mail->Subject = $this->view->fetchBlock('invite-user.email.twig', 'subject');
      $mail->Body = $this->view->fetchBlock('invite-user.email.twig', 'body_html', [
        'link' => $link,
      ]);
      $mail->AltBody = $this->view->fetchBlock('invite-user.email.twig', 'body_text', [
        'link' => $link,
      ]);

      $mail->send();

      $message['text'] = 'Invite has been sent to ' . $email;
      $message['type'] = 'info';
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => $message,
      ]);
    } catch (phpmailerException $e) {
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => [
          'type' => 'error',
          'text' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo,
        ]
      ]);
    }
  }

  /**
   * Allow a user with a valid token to register.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return ResponseInterface
   *   Response.
   */
  public function register(Request $request, Response $response, array $args) {
    $menu = $this->getMenus([]);
    if ($request->isPost()) {
      $allVars = $request->getParsedBody();
    } else {
      $allVars = $args;
    }

    // Token not received.
    if (empty($allVars['token'])) {
      return $response->withRedirect('/login');
    }

    $token = $allVars['token'];

    // Invalid token.
    $inviteHlp = new Invite($this->dbSettings);
    $invite = $inviteHlp->findByToken($token);
    if (empty($invite['iid'])) {
      return $response->withRedirect('/login');
    }

    // Validate User is not already in the system.
    $userHlp = new User($this->dbSettings);
    $user = $userHlp->findByEmail($invite['email']);
    if (!empty($user['uid'])) {
      $inviteHlp->deleteByEmail($invite['email']);
      $message['text'] = 'Your user already exists: ' . $invite['email'];
      $message['type'] = 'error';
      return $this->view->render($response, 'home.twig', [
        'menu' => $menu,
        'message' => $message,
      ]);
    }

    if ($request->isGet()) {
      // Display the register form.
      return $this->view->render($response, 'register.twig', [
        'menu' => $menu,
        'token' => $token,
      ]);
    }

    // Fall through to new user register post form submission.
    return $this->createUser($allVars, $menu, $response);
  }

  /**
   * Get Local domain name.
   *
   * @return string
   *   Host name.
   */
  private function getHost() {
    $possibleHostSources = ['HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR'];
    $sourceTransformations = [
      "HTTP_X_FORWARDED_HOST" => function($value) {
        $elements = explode(',', $value);
        return trim(end($elements));
      }
    ];
    $host = '';
    foreach ($possibleHostSources as $source) {
      if (!empty($host)) {
        break;
      }
      if (empty($_SERVER[$source])) {
        continue;
      }
      $host = $_SERVER[$source];
      if (array_key_exists($source, $sourceTransformations)) {
        $host = $sourceTransformations[$source]($host);
      }
    }

    // Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);

    return trim($host);
  }

}
