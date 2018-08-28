<?php

namespace Datagator\Admin\Controllers;

use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Datagator\Core\Hash;
use Datagator\Admin\User;
use Datagator\Admin\UserRole;
use Datagator\Admin\Role;
use Datagator\Admin\Application;
use Datagator\Admin\Invite;

/**
 * Class User.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlUser extends CtrlBase {

  protected $permittedRoles = ['Owner'];
  protected $mailSettings;

  /**
   * CtrlUser constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param array $mailSettings
   *   Mail settings array.
   * @param \Slim\Views\Twig $view
   *   View container.
   */
  public function __construct(array $dbSettings, array $mailSettings, Twig $view) {
    $this->mailSettings = $mailSettings;
    parent::__construct($dbSettings, $view);
  }

  /**
   * Display the users page.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function index(Request $request, Response $response, array $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);
    $accId = $_SESSION['accountId'];

    // Fetch all applications for the account.
    $applicationHlp = new Application($this->dbSettings);
    $applications = $applicationHlp->getByAccount($accId);

    // fetch all roles.
    $roleHlp = new Role($this->dbSettings);
    $roles = $roleHlp->findAll();

    // Fetch all user roles for the account
    $userRoleHlp = new UserRole($this->dbSettings);
    $userRoles = $userRoleHlp->findByAccId($accId);

    // Fetch all user roles for each application.
    foreach ($applications as $appId => $application) {
      $results = $userRoleHlp->findByAppId($appId);
      foreach ($results as $result) {
        $userRoles[] = $result;
      }
    }

    // Fetch distinct users for each user role.
    $userHlp = new User($this->dbSettings);
    $users = [];
    foreach ($userRoles as $userRole) {
      $uid = $userRole['uid'];
      if (!isset($user[$uid])) {
        $users[$uid] = $userHlp->findByUid($uid);
      }
    }

    // Add applications => roles to users array.
    foreach ($users as $uid => $user) {
      $user['applications'] = [];
      // Find all user roles for this user.
      foreach ($userRoles as $userRole) {
        if ($userRole['uid'] == $uid) {
          // Add application if not exists.
          $application = $applications[$userRole['appId']];
          $appId = $application['appId'];
          if (!isset($user['applications'][$appId])) {
            $user['applications'][$appId] = $application;
          }
          // Add role.
          $roleId = $userRole['rid'];
          $user['applications'][$appId]['roles'][$roleId] = $roles[$roleId];
        }
      }
    }

    return $this->view->render($response, 'users.twig', [
      'menu' => $menu,
      'applications' => $applications,
      'users' => $users,
      'roles' => $roles,
    ]);
  }

  /**
   * Send a user an email with a token to register.
   *
   * The email templates are in /includes/Admin/templates/invite-user.email.twig.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function invite(Request $request, Response $response, array $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      return $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $allPostVars = $request->getParsedBody();
    if (!isset($allPostVars['invite-email']) || empty($allPostVars['invite-email'])) {
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
      ]);
    }

    // Generate vars for the email.
    $email = $allPostVars['invite-email'];
    $token = Hash::generateToken($email);
    $host = $this->getHost();
    $link = $host . '/user/register/' . $token;

    // Check if user already exists.
    $userHlp = new User($this->dbSettings);
    $user = $userHlp->findByEmail($email);
    if (!empty($user['uid'])) {
      $message['text'] = 'A user already exists with this email: ' . $email;
      $message['type'] = 'error';
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => $message,
      ]);
    }

    // Add invite to DB.
    $inviteHlp = new Invite($this->dbSettings);
    // Remove any old invites for this email.
    $inviteHlp->deleteByEmail($email);
    // Add new invite.
    $inviteHlp->create($email, $token);

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
      $mail->setFrom($this->mailSettings['from']['email'], $this->mailSettings['email']['name']);
      $mail->addReplyTo($this->mailSettings['from']['email'], $this->mailSettings['email']['name']);

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
    } catch (Exception $e) {
      $message['text'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
      $message['type'] = 'info';
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => $message,
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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function register(Request $request, Response $response, array $args) {
    $menu = $this->getMenus([]);

    if ($request->isGet()) {
      // Initial form display.

      // Validate token is good.
      if (empty($args['token'])) {
        return $response->withRedirect('/login');
      }
      $token = $args['token'];
      $inviteHlp = new Invite($this->dbSettings);
      $invite = $inviteHlp->findByToken($token);
      if (empty($invite['id'])) {
        return $response->withRedirect('/login');
      }

      $userHlp = new User($this->dbSettings);
      $user = $userHlp->findByEmail($invite['email']);

      if (!empty($user['uid'])) {
        // User is already in the system.
        $inviteHlp->deleteByEmail($invite['email']);
        $message['text'] = 'Your user already exists: ' . $invite['email'];
        $message['type'] = 'error';
        return $this->view->render($response, 'home.twig', [
          'menu' => $menu,
          'message' => $message,
        ]);
      }

      // This is a new user, display the register form.
      return $this->view->render($response, 'register.twig', [
        'menu' => $menu,
        'token' => $token,
      ]);
    }

    // Fall through to new user register post form submission.
    $allPostVars = $request->getParsedBody();
    return $this->createUser($allPostVars, 'No role', $menu, $response);
  }

  /**
   * Create a new user form form submission.
   *
   * @param array $user
   *   Post vars.
   * @param array $role
   *   User role.
   * @param array $menu
   *   Menu items.
   * @param \Slim\Http\Response $response
   *   Response object.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  private function createUser($user, $role, $menu, $response) {
    if (empty($user['token']) ||
      empty($user['username']) ||
      empty($user['password']) ||
      empty($user['honorific']) ||
      empty($user['email']) ||
      empty($user['name_first']) ||
      empty($user['name_last'])) {
      // Missing mandatory fields.
      $message['text'] = "Required fields not entered.";
      $message['type'] = 'error';
      return $this->view->render($response, 'register.twig', [
        'menu' => $menu,
        'message' => $message,
        'token' => $user['token'],
      ]);
    }

    // Ensure email matches the invite token.
    $inviteHlp = new Invite($this->dbSettings);
    $invite = $inviteHlp->findByEmailToken($user['email'], $user['token']);
    if (empty($invite['id'])) {
      // Delete the invite.
      $inviteHlp->deleteByToken($user['token']);
      // Redirect to login.
      $message['text'] = "Your email does not match the invite email. Please resend the invite.";
      $message['type'] = 'error';
      return $this->view->render($response, 'login.twig', [
        'menu' => $menu,
        'message' => $message,
      ]);
    }

    // Validate username and email does not exist.
    $userHlp = new User($this->dbSettings);
    $user = $userHlp->findByEmail($user['email']);
    if (!empty($user['uid'])) {
      $message['text'] = 'A user already exists with this email: ' . $user['email'] . '.';
      $message['text'] .= 'Please use a different address.';
      $message['type'] = 'error';
      return $this->view->render($response, 'register.twig', [
        'menu' => $menu,
        'message' => $message,
        'token' => $user['token'],
      ]);
    }
    $user = $userHlp->findByUsername($user['username']);
    if (!empty($user['uid'])) {
      $message['text'] = 'A user already exists with this username: ' . $user['username'] . '.';
      $message['text'] .= 'Please use a different username.';
      $message['type'] = 'error';
      return $this->view->render($response, 'register.twig', [
        'menu' => $menu,
        'message' => $message,
        'token' => $user['token'],
      ]);
    }

    $uid = $userHlp->create(
      !empty($user['username']) ? $user['username'] : '',
      !empty($user['password']) ? $user['password'] : '',
      !empty($user['email']) ? $user['email'] : '',
      !empty($user['honorific']) ? $user['honorific'] : '',
      !empty($user['name_first']) ? $user['name_first'] : '',
      !empty($user['name_last']) ? $user['name_last'] : '',
      !empty($user['company']) ? $user['company'] : '',
      !empty($user['website']) ? $user['website'] : '',
      !empty($user['address_street']) ? $user['address_street'] : '',
      !empty($user['address_suburb']) ? $user['address_suburb'] : '',
      !empty($user['address_city']) ? $user['address_city'] : '',
      !empty($user['address_state']) ? $user['address_state'] : '',
      !empty($user['address_country']) ? $user['address_country'] : '',
      !empty($user['address_postcode']) ? $user['address_postcode'] : '',
      !empty($user['phone_mobile']) ? $user['phone_mobile'] : '',
      !empty($user['phone_work']) ? $user['phone_work'] : ''
    );
    if (!$uid) {
      $message['text'] = "Sorry, there was an error creating your user. Please speak to your administrator.";
      $message['type'] = 'error';
      return $this->view->render($response, 'login.twig', [
        'menu' => $menu,
        'message' => $message,
      ]);
    }

    // Delete the invite.
    $inviteHlp->deleteByToken($user['token']);

    // Redirect to login.
    $message['text'] = "Congratulations, you are registered. Please login.";
    $message['type'] = 'info';
    return $this->view->render($response, 'login.twig', [
      'menu' => $menu,
      'message' => $message,
    ]);
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
