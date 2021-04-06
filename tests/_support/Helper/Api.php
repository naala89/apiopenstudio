<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    private $token = '';
    private $accountName = 'apiopenstudio';
    private $applicationName = 'testing';
    private $username = 'tester';
    private $password = 'tester_pass';
    private $yamlFilename = '';

    /**
     * @return string
     */
    public function getMyUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getMyPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getMyAccountName()
    {
        return $this->accountName;
    }

    /**
     * @return string
     */
    public function getMyApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * @return string
     */
    public function getCoreBaseUri()
    {
        return '/apiopenstudio/core';
    }

    /**
     * @return string
     */
    public function getMyBaseUri()
    {
        return '/' . $this->getMyAccountName() . '/' . $this->getMyApplicationName();
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
    public function getYamlFilename()
    {
        return $this->yamlFilename;
    }

    /**
     * @param $name
     * @param $value
     * @throws \Codeception\Exception\ModuleException
     */
    public function haveHttpHeader($name, $value)
    {
        $this->getModule('REST')->haveHttpHeader($name, $value);
    }

    /**
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function getBaseUrl()
    {
        $str = $this->getModule('PhpBrowser')->_getUrl();
        return substr($str, 0, strlen($str) - 4);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
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
    public function getMyStoredToken()
    {
        return $this->token;
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function performLogin()
    {
        $this->getModule('REST')->sendPost(
            $this->getCoreBaseUri() . '/login',
            ['username' => $this->username, 'password' => $this->password]
        );
        $this->getModule('REST')->seeResponseCodeIs(200);
        $this->getModule('REST')->seeResponseIsJson();
        $this->getModule('REST')->seeResponseMatchesJsonType(array('token' => 'string'));
        $this->storeMyToken();
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function seeTokenIsSameAsStoredToken()
    {
        $response = $this->getModule('REST')->response;
        $arr = \GuzzleHttp\json_decode(\GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response)), true);
        \PHPUnit_Framework_Assert::assertEquals($this->token, $arr['token']);
    }

    /**
     * @return array
     */
    public function getResourceFromYaml($yamlFilename = null)
    {
        $yamlFilename = empty($yamlFilename) ? $this->yamlFilename : $yamlFilename;
        $yamlArr = file_get_contents(codecept_data_dir($yamlFilename));
        return \Spyc::YAMLLoadString($yamlArr);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
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
     * @throws \Codeception\Exception\ModuleException
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
     * @throws \Codeception\Exception\ModuleException
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
     * @throws \Codeception\Exception\ModuleException
     */
    public function seeReponseHasLength($length)
    {
        if (strlen(trim($this->getModule('REST')->response, '"')) != $length) {
            \PHPUnit_Framework_Assert::assertTrue(
                false,
                'string ' . $this->getModule('REST')->response . " does not have length $length"
            );
        }
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function seeResult()
    {
        var_dump($this->getModule('REST')->response);
        exit;
    }

    /**
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function getResponse()
    {
        return $this->getModule('REST')->response;
    }
}
