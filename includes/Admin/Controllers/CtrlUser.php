<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;
use phpmailerException;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Hash;

/**
 * Class CtrlUser.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlUser extends CtrlBase
{

    /**
     * {@inheritdoc}
     */
    protected $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
        'Developer',
    ];

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
     * @throws \Exception
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();

        return $this->view->render($response, 'user.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * View a new user.
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
     * @throws \Exception
     */
    public function view(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $uid = $args['uid'];
        $user = [];

        try {
            $result = $this->apiCall(
                'get', 'user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'uid' => $uid,
                    ],
                ]
            );
            $user = json_decode($result->getBody()->getContents(), true);
            $user = isset($user[0]) ? $user[0] : [];
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->view->render($response, 'user-view.twig', [
            'user' => $user,
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Edit a user page.
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
     * @throws \Exception
     */
    public function edit(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $uid = $args['uid'];
        $user = [];

        try {
            $result = $this->apiCall(
                'get', 'user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'uid' => $uid,
                    ],
                ]
            );
            $user = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->view->render($response, 'user-edit.twig', [
            'menu' => $menu,
            'user' => $user[0],
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Upload a user.
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
     * @throws \Exception
     */
    public function upload(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allPostVars = $request->getParams();

        // Workaround for Authentication middleware that will think this is a login attempt.
        if (!empty($allPostVars['upload-username'])) {
            $allPostVars['username'] = $allPostVars['upload-username'];
        }
        if (!empty($allPostVars['upload-password'])) {
            $allPostVars['password'] = $allPostVars['upload-password'];
        }
        unset($allPostVars['upload-username']);
        unset($allPostVars['upload-password']);

        if (!empty($allPostVars['uid'])) {
            // Edit a user.
            $uid = $allPostVars['uid'];
            unset($allPostVars['uid']);
            try {
                $result = $this->apiCall('put', "user/$uid",
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'json' => $allPostVars,
                    ]
                );
                $this->flash->addMessageNow('info', 'User updated.');

                $user = json_decode($result->getBody()->getContents(), true);

                return $this->view->render($response, 'user-view.twig', [
                    'menu' => $menu,
                    'user' => $user,
                    'messages' => $this->flash->getMessages(),
                ]);
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
                $user = $allPostVars;
                $user['uid'] = $uid;
                return $this->view->render($response, 'user-view.twig', [
                    'menu' => $menu,
                    'user' => $user,
                    'messages' => $this->flash->getMessages(),
                ]);
            }
        } else {
            // Create a user.
            try {
                $result = $this->apiCall('post', 'user',
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'form_params' => $allPostVars,
                    ]
                );
                $this->flash->addMessageNow('info', 'User created.');
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
                $user = $allPostVars;
                return $this->view->render($response, 'user-edit.twig', [
                    'menu' => $menu,
                    'user' => $user,
                    'messages' => $this->flash->getMessages(),
                ]);
            }
        }
        try {
            $result = $this->apiCall('get','user',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $users = (array) json_decode($result->getBody()->getContents());
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
            $users = [];
        }

        return $this->view->render($response, 'users.twig', [
            'menu' => $menu,
            'users' => $users,
            'messages' => $this->flash->getMessages(),
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
     * @throws \Exception
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $uid = $args['uid'];

        try {
            $result = $this->apiCall('delete', "user/$uid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            if ($result == 'true') {
                $this->flash->addMessageNow('info', 'User successfully deleted.');
            } else {
                $this->flash->addMessageNow('error', 'User deletion failed, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
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
    public function invite(Request $request, Response $response, array $args)
    {
        // Validate access.
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
        $mail = new PHPMailer(true); // Passing `true` enables exceptions
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
     *
     * @throws \Exception
     */
    public function register(Request $request, Response $response, array $args)
    {
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
    private function getHost()
    {
        $possibleHostSources = ['HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR'];
        $sourceTransformations = [
            "HTTP_X_FORWARDED_HOST" => function ($value) {
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
