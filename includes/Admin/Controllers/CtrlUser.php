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
    $title = 'Users';
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
      'title' => $title,
      'applications' => $applications,
      'users' => $users,
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
      $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (!isset($allPostVars['invite-email']) || empty($allPostVars['invite-email'])) {
      return $response = $response->withRedirect('/users');
    }

    // Generate vars for the email.
    $email = $allPostVars['invite-email'];
    $token = Hash::generateToken($email);
    $host = $this->getHost();
    $link = $host . '/user/register?token=' . $token;

    // Add invite to DB.
    $invite = new Invite($this->dbSettings);
    // Remove any old invites.
    $invite->deleteByEmail($email);
    // Add new invite.
    $invite->create($email, $token);

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
      echo 'Message has been sent';
    } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }

    // TODO: render/redirect? setup post form in popup.
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
