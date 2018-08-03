<?php

namespace Datagator\Admin;
use GuzzleHttp;

class User
{
    public function __construct() {
        $_SESSION['token'] = '';
    }

    public function login($username, $password) {
        $payload = ['form_params' => [
            'username' => $username,
            'password' => $password
        ]];
        $url = '/api/user/login';
        $client = new GuzzleHttp\Client();
        try {
            $result = json_decode($client->request('POST', $url, $payload));
        } catch (\Exception $e) {
            return FALSE;
        }
        if (is_array($result) && isset($result['token'])) {
            $_SESSION['token'] = $result['token'];
            return $result;
        }
        return FALSE;
    }

    public function logout() {
        $_SESSION['token'] = '';
        return TRUE;
    }

    public function isLoggedIn() {
        return $_SESSION['token'] != '';
    }
}