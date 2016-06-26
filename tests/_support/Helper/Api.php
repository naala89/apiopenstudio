<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
  private $token = '';
  private $accountName = 'Datagator';
  private $applicationName = 'Testing';
  private $username = 'tester';
  private $password = 'tester_pass';

  private $yamlFilename= '';

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
   * @throws \Codeception\Exception\ModuleException
   */
  public function storeMyToken()
  {
    $response = $this->getModule('REST')->response;
    $arr = \GuzzleHttp\json_decode(\GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response)), true);
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
    $this->getModule('REST')->sendPost('/common/user/login', ['username' => $this->username, 'password' => $this->password]);
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
  public function getResourceFromYaml()
  {
    $yamlArr = file_get_contents(codecept_data_dir($this->yamlFilename));
    return \Spyc::YAMLLoadString($yamlArr);
  }

  /**
   * @throws \Codeception\Exception\ModuleException
   */
  public function createResourceFromYaml()
  {
    $this->getModule('REST')->sendPost('/' . $this->applicationName . '/resource/yaml', ['token' => $this->token], ['resource' => codecept_data_dir($this->yamlFilename)]);
    $this->getModule('REST')->seeResponseCodeIs(200);
    $this->getModule('REST')->seeResponseIsJson();
    $this->getModule('REST')->seeResponseContains('true');
  }

  /**
   * @param array $params
   * @throws \Codeception\Exception\ModuleException
   */
  public function callResourceFromYaml($params=array())
  {
    $yamlArr = $this->getResourceFromYaml($this->yamlFilename);
    $method = strtolower($yamlArr['method']);
    $params = array_merge($params, array('token' => $this->token, 'debug'=> 4));
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
  public function tearDownTestFromYaml($code=200, $respone='true')
  {
    $yamlArr = $this->getResourceFromYaml($this->yamlFilename);
    $params = array();
    $params[] = 'token=' . $this->token;
    $params[] = 'method=' . $yamlArr['method'];
    $params[] = 'uri=' . urlencode($yamlArr['uri']);
    $uri = '/' . $this->applicationName . '/resource?' . implode('&', $params);
    $this->getModule('REST')->sendDELETE($uri);
    $this->getModule('REST')->seeResponseIsJson();
    $this->getModule('REST')->seeResponseCodeIs($code);
    if (is_array($respone)) {
      $this->getModule('REST')->seeResponseContainsJson($respone);
    } else {
      $this->getModule('REST')->seeResponseContains($respone);
    }
  }

  /**
   * @param $yamlFilename
   * @param $params
   */
  public function doTestFromYaml($yamlFilename, $params=array())
  {
    $this->performLogin();
    $this->setYamlFilename($yamlFilename);
    $this->createResourceFromYaml();
    $this->callResourceFromYaml($params);
  }

  public function seeReponseHasLength($length)
  {
    if (strlen(trim($this->getModule('REST')->response, '"')) != $length) {
      \PHPUnit_Framework_Assert::assertTrue(false, 'string ' . $this->getModule('REST')->response . " does not have length $length");
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

  public function getResponse()
  {
    return $this->getModule('REST')->response;
  }
}
