<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
  private $token='';

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
  public function seeTokenIsSameAsStoredToken()
  {
    $response = $this->getModule('REST')->response;
    $arr = \GuzzleHttp\json_decode(\GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response)), true);
    \PHPUnit_Framework_Assert::assertEquals($this->token, $arr['token']);
  }
}
