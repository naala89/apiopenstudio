<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;
use Codeception\Module;
use PHPUnit_Framework_Assert;
use Spyc;

class Api extends Module
{
    private string $token = '';
    private string $yamlFilename = '';

    /**
     * Get the test acc name.
     *
     * @return string
     */
    public function getMyAccountName(): string
    {
        return getenv('TESTING_ACCOUNT_NAME');
    }

    /**
     * Get the test app name.
     *
     * @return string
     */
    public function getMyApplicationName(): string
    {
        return getenv('TESTING_APPLICATION_NAME');
    }

    /**
     * Get the base URI (domain/core_acc_name/core_app_name).
     *
     * @return string
     */
    public function getCoreBaseUri(): string
    {
        return '/' . getenv('CORE_ACCOUNT_NAME') . '/' . getenv('CORE_APPLICATION_NAME');
    }

    /**
     * Get the base URI (domain/test_acc_name/test_app_name).
     *
     * @return string
     */
    public function getMyBaseUri(): string
    {
        return '/' . getenv('TESTING_ACCOUNT_NAME') . '/' . getenv('TESTING_APPLICATION_NAME');
    }

    /**
     * Set the store YAML filename.
     *
     * @param string $yamlFilename
     */
    public function setYamlFilename(string $yamlFilename)
    {
        $this->yamlFilename = $yamlFilename;
    }

    /**
     * Return the store YAML filename.
     *
     * @return string
     */
    public function getYamlFilename(): string
    {
        return $this->yamlFilename;
    }

    /**
     * Set a header & value.
     *
     * @param string $name
     * @param string $value
     *
     * @throws ModuleException
     */
    public function haveHttpHeader(string $name, string $value)
    {
        $this->getModule('REST')->haveHttpHeader($name, $value);
    }

    /**
     * Return the base URL for the test API server
     *
     * @return false|string
     *
     * @throws ModuleException
     */
    public function getBaseUrl()
    {
        $str = $this->getModule('PhpBrowser')->_getUrl();
        return substr($str, 0, strlen($str) - 4);
    }

    /**
     * Store a login token for future use.
     *
     * @throws ModuleException
     */
    public function storeMyToken()
    {
        $response = $this->getModule('REST')->response;
        $arr = json_decode(json_encode(json_decode($response)), true);
        if (isset($arr['data']['token'])) {
            $this->token = $arr['data']['token'];
        }
    }

    /**
     * Return the stored user access token.
     *
     * @return string
     */
    public function getMyStoredToken(): string
    {
        return $this->token;
    }

    /**
     * Login a user and store the token.
     *
     * @param string $username
     * @param string $password
     *
     * @throws ModuleException
     */
    public function performLogin(string $username, string $password)
    {
        $this->getModule('REST')->sendPost(
            $this->getCoreBaseUri() . '/auth/token',
            ['username' => $username, 'password' => $password]
        );
        $this->getModule('REST')->seeResponseCodeIs(200);
        $this->getModule('REST')->seeResponseIsJson();
        $this->getModule('REST')->seeResponseMatchesJsonType([
            'result' => 'string',
            'data' => [
                'uid' => 'integer',
                'token' => 'string',
                'token_expiry' => 'string',
                'refresh_token' => 'string',
                'refresh_expiry' => 'string',
            ],
        ]);
        $this->storeMyToken();
        $this->haveHttpHeader('Authorization', 'Bearer ' . $this->getMyStoredToken());
    }

    /**
     * Get a resource from a YAML file.
     *
     * @param ?string $yamlFilename
     *
     * @return array
     */
    public function getResourceFromYaml(?string $yamlFilename = null): array
    {
        $yamlFilename = empty($yamlFilename) ? $this->yamlFilename : $yamlFilename;
        $yamlArr = file_get_contents(codecept_data_dir($yamlFilename));
        return Spyc::YAMLLoadString($yamlArr);
    }

    /**
     * Create a resource from a YAML file.
     *
     * @param ?string $yamlFilename
     *
     * @throws ModuleException
     */
    public function createResourceFromYaml(?string $yamlFilename = null)
    {
        $yamlFilename = empty($yamlFilename) ? $this->yamlFilename : $yamlFilename;
        $this->getModule('REST')->sendPost(
            $this->getCoreBaseUri() . '/resource/import',
            [],
            [
                'resource_file' => [
                    'name' => $yamlFilename,
                    'type' => 'file',
                    'error' => UPLOAD_ERR_OK,
                    'size' => filesize(codecept_data_dir($yamlFilename)),
                    'tmp_name' => codecept_data_dir($yamlFilename),
                ],
            ]
        );
        $this->getModule('REST')->seeResponseCodeIs(200);
        $this->getModule('REST')->seeResponseIsJson();
    }

    /**
     * Call a resource from a YAML file.
     *
     * @param array $params
     *
     * @throws ModuleException
     */
    public function callResourceFromYaml(array $params = [])
    {
        $yamlArr = $this->getResourceFromYaml($this->yamlFilename);
        $method = strtolower($yamlArr['method']);
        $params = array_merge($params, array('token' => $this->token, 'debug' => 4));
        $uri = '/' . $this->applicationName . '/' . $yamlArr['uri'];
        if ($method == 'get') {
            $this->getModule('REST')->sendGet($uri, $params);
        } elseif ($method == 'post') {
            $this->getModule('REST')->sendPost($uri, $params);
        } elseif ($method == 'delete') {
            $this->getModule('REST')->sendDelete($uri, $params);
        } elseif ($method == 'put') {
            $this->getModule('REST')->sendPut($uri, $params);
        }
    }

    /**
     * Delete a resource from an input YAML file.
     *
     * @param ?string $yamlFilename
     *
     * @throws ModuleException
     */
    public function tearDownTestFromYaml(?string $yamlFilename = null)
    {
        $yamlFilename = empty($yamlFilename) ? $this->yamlFilename : $yamlFilename;
        $yamlArr = $this->getResourceFromYaml($yamlFilename);
        $this->haveHttpHeader('Accept', 'application/json');
        $this->haveHttpHeader('Authorization', 'Bearer ' . $this->getMyStoredToken());

        $filter = [];
        if (isset($yamlArr['name'])) {
            $filter['name'] = $yamlArr['name'];
        }
        if (isset($yamlArr['appid'])) {
            $filter['appid'] = $yamlArr['appid'];
        }
        if (isset($yamlArr['method'])) {
            $filter['method'] = $yamlArr['method'];
        }
        if (isset($yamlArr['uri'])) {
            $filter['uri'] = $yamlArr['uri'];
        }
        $this->getModule('REST')->sendGET($this->getCoreBaseUri() . '/resource/', $filter);
        $resources = json_decode($this->getModule('REST')->response, true);
        if (isset($resources['result']) && $resources['result'] == 'ok') {
            foreach ($resources['data'] as $resource) {
                $resid = $resource['resid'];
                $this->getModule('REST')->sendDELETE(
                    $this->getCoreBaseUri() . '/resource/' . $resid,
                    []
                );
            }
        }
    }

    /**
     * Delete a resource by its appid, method & uri.
     *
     * @param int $appid
     * @param string $method
     * @param string $uri
     *
     * @throws ModuleException
     */
    public function deleteResource(int $appid, string $method, string $uri)
    {
        $this->haveHttpHeader('Accept', 'application/json');
        $this->haveHttpHeader('Authorization', 'Bearer ' . $this->getMyStoredToken());
        $this->getModule('REST')->sendGET(
            $this->getCoreBaseUri() . '/resource/',
            [
                'appid' => $appid,
                'method' => $method,
                'uri' => $uri,
            ]
        );
        $resources = json_decode($this->getModule('REST')->response, true);
        if ($resources['result'] != 'error') {
            foreach ($resources['data'] as $resource) {
                if (
                    strtolower($resource['method']) == strtolower($method)
                    && $resource['uri'] == $uri
                ) {
                    $this->getModule('REST')->sendDELETE(
                        $this->getCoreBaseUri() . '/resource/' . $resource['resid'],
                        []
                    );
                }
            }
        }
    }

    /**
     * Perform a test from a YAML file.
     *
     * @param $yamlFilename
     * @param array $params
     *
     * @throws ModuleException
     */
    public function doTestFromYaml($yamlFilename, $params = array())
    {
        $this->performLogin();
        $this->setYamlFilename($yamlFilename);
        $this->createResourceFromYaml();
        $this->callResourceFromYaml($params);
    }

    /**
     * Test the response length.
     *
     * @param $length
     *
     * @throws ModuleException
     */
    public function seeReponseHasLength($length)
    {
        if (strlen(trim($this->getModule('REST')->response, '"')) != $length) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'string ' . $this->getModule('REST')->response . " does not have length $length"
            );
        }
    }

    /**
     * Debugger method to output a response and stop processing.
     *
     * @throws ModuleException
     */
    public function seeResult()
    {
        var_dump($this->getModule('REST')->response);
        exit;
    }

    /**
     * Get the API response.
     *
     * @return mixed
     *
     * @throws ModuleException
     */
    public function getResponse()
    {
        return $this->getModule('REST')->response;
    }
}
