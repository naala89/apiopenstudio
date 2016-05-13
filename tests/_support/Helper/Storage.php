<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Storage extends \Codeception\Module
{
  private $token='';

  public function storeToken()
  {
    $response = $this->getModule('REST')->response;
    $arr = \GuzzleHttp\json_decode(\GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response)), true);
    if (isset($arr['token'])) {
      $this->token = $arr['token'];
    }
  }

  public function getToken()
  {
    return $this->token;
  }

  public function compareToken()
  {
    $response = $this->getModule('REST')->response;
    $arr = \GuzzleHttp\json_decode(\GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response)), true);
    \PHPUnit_Framework_Assert::assertEquals($this->token, $arr['token']);
  }

}
