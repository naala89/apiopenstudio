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
     * @return string
     */
    public function getMyAccountName(): string
    {
        return getenv('TESTING_ACCOUNT_NAME');
    }

    /**
     * @return string
     */
    public function getMyApplicationName(): string
    {
        return getenv('TESTING_APPLICATION_NAME');
    }

    /**
     * @return string
     */
    public function getCoreBaseUri(): string
    {
        return '/' . getenv('CORE_ACCOUNT_NAME') . '/' . getenv('CORE_APPLICATION_NAME');
    }

    /**
     * @return string
     */
    public function getMyBaseUri(): string
    {
        return '/' . getenv('TESTING_ACCOUNT_NAME') . '/' . getenv('TESTING_APPLICATION_NAME');
    }

    /**
     * @param $yamlFilename
     */
    public function setYamlFilename($yamlFilename)
    {
        $this->yamlFilename = $yamlFilename;
    }

    /**
     * @return string
     */
    public function getYamlFilename(): string
    {
        return $this->yamlFilename;
    }

    /**
     * @param $name
     * @param $value
     * @throws ModuleException
     */
    public function haveHttpHeader($name, $value)
    {
        $this->getModule('REST')->haveHttpHeader($name, $value);
    }

    /**
     * @return false|string
     * @throws ModuleException
     */
    public function getBaseUrl()
    {
        $str = $this->getModule('PhpBrowser')->_getUrl();
        return substr($str, 0, strlen($str) - 4);
    }

    /**
     * @throws ModuleException
     */
    public function storeMyToken()
    {
        $response = $this->getModule('REST')->response;
        $arr = \GuzzleHttp\json_decode(
            \GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response)),
            true
        );
        if (isset($arr['token'])) {
            $this->token = $arr['token'];
        }
    }

    /**
     * @return string
     */
    public function getMyStoredToken(): string
    {
        return $this->token;
    }

    /**
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
            'token' => 'string',
            'uid' => 'integer',
            'expires' => 'string',
        ]);
        $this->storeMyToken();
        $this->haveHttpHeader('Authorization', 'Bearer ' . $this->getMyStoredToken());
    }

    /**
     * @param null $yamlFilename
     * @return array
     */
    public function getResourceFromYaml($yamlFilename = null): array
    {
        $yamlFilename = empty($yamlFilename) ? $this->yamlFilename : $yamlFilename;
        $yamlArr = file_get_contents(codecept_data_dir($yamlFilename));
        return Spyc::YAMLLoadString($yamlArr);
    }

    /**
     * @throws ModuleException
     */
    public function createResourceFromYaml($yamlFilename = null)
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
        $this->getModule('REST')->seeResponseContains('true');
    }

    /**
     * @param array $params
     * @throws ModuleException
     */
    public function callResourceFromYaml($params = array())
    {
        $yamlArr = $this->getResourceFromYaml($this->yamlFilename);
        $method = strtolower($yamlArr['method']);
        $params = array_merge($params, array('token' => $this->token, 'debug' => 4));
        $uri = '/' . $this->applicationName . '/' . $yamlArr['uri'];
        if ($method == 'get') {
            $this->getModule('REST')->sendGet($uri, $params);
        } else {
            $this->getModule('REST')->sendPost($uri, $params);
        }
    }

    /**
     * @throws ModuleException
     */
    public function tearDownTestFromYaml($yamlFilename = null)
    {
        $yamlFilename = empty($yamlFilename) ? $this->yamlFilename : $yamlFilename;
        $yamlArr = $this->getResourceFromYaml($yamlFilename);
        $this->haveHttpHeader('Accept', 'application/json');
        $this->haveHttpHeader('Authorization', 'Bearer ' . $this->getMyStoredToken());
        $this->getModule('REST')->sendGET(
            $this->getCoreBaseUri() . '/resource/',
            ['appid' => $yamlArr['appid']]
        );
        $resources = json_decode($this->getModule('REST')->response, true);
        $resid = 0;
        if (!isset($resources['error'])) {
            foreach ($resources as $resource) {
                if (
                    strtolower($resource['method']) == strtolower($yamlArr['method'])
                    && $resource['uri'] == $yamlArr['uri']
                ) {
                    $resid = $resource['resid'];
                }
            }
            $this->getModule('REST')->sendDELETE(
                $this->getCoreBaseUri() . '/resource/' . $resid,
                []
            );
        }
    }

    /**
     * @param $yamlFilename
     * @param $params
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
     * @param $length
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
     * @throws ModuleException
     */
    public function seeResult()
    {
        var_dump($this->getModule('REST')->response);
        exit;
    }

    /**
     * @return mixed
     * @throws ModuleException
     */
    public function getResponse()
    {
        return $this->getModule('REST')->response;
    }
}
