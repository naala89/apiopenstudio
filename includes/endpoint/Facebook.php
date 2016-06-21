<?php

/**
 * Get data form the Facebook API.
 */

namespace Datagator\Endpoint;
use Datagator\Processor;
use Datagator\Core;
use Facebook\Authentication;
use Facebook\Exceptions;

class Facebook extends Processor\ProcessorEntity
{
  protected $details = array(
    'name' => 'Facebook',
    'description' => 'Fetch results from facebook Graph API.',
    'menu' => 'Endpoint',
    'application' => 'Common',
    'input' => array(
      'appId' => array(
        'description' => 'The app_id that you will be accessing facebook with.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'appSecret' => array(
        'description' => 'The app_secret that you will be accessing facebook with.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'graphVersion' => array(
        'description' => 'The version of graph to use (do not prefix with "v", e.g. use "2.4").',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'float'),
      ),
      'node' => array(
        'description' => 'The node or edge that you want to fetch or post to facebook.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'fields' => array(
        'description' => 'The fields that you want to fetch from the node.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'fields' => array(
        'description' => 'An array of the fields that you want to fetch from the node.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'data' => array(
        'description' => 'An array of the data that you want to send from the node.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'objectType' => array(
        'description' => 'The object type that you want to send from the node, i.e. photos.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal'),
      ),
    ),
  );

  /**
   * Retrieve data from an endpoint URL.
   *
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Endpoint Facebook', 4);

    $appId = $this->val($this->meta->appId);
    $appSecret = $this->val($this->meta->appSecret);
    $graphVersion = $this->val($this->meta->graphVersion);
    $accessToken = $this->_getToken($appId, $appSecret, $graphVersion);
    $method = strtolower($this->val($this->meta->method));
    if ($method != 'get' && $method != 'post') {
      throw new Core\ApiException('invalid method', 6, $this->id);
    }

    $fb = new \Facebook\Facebook(array(
      'app_id' => $appId,
      'app_secret' => $appSecret,
      'default_graph_version' => "v$graphVersion",
    ));

    $node = $this->val($this->meta->node);

    return $this->{"_$method"}($fb, $node, $accessToken);
  }

  /**
   * Generate an FB access token/
   *
   * @param $fb
   * @param bool|TRUE $longLived
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _getToken($fb, $longLived=TRUE)
  {
    $helper = $fb->getRedirectLoginHelper();

    try {
      $accessToken = $helper->getAccessToken();
    } catch(Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      throw new Core\ApiException('Graph returned an error: ' . $e->getMessage(), 5, $this->id);
    } catch(Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      throw new Core\ApiException('Graph SDK returned an error: ' . $e->getMessage(), 5, $this->id);
    }

    if (!isset($accessToken)) {
      if ($helper->getError()) {
        $message = 'Error: ' . $helper->getError();
        $message .= '. Error Code: ' . $helper->getErrorCode();
        $message .= '. Error Reason: ' . $helper->getErrorReason();
        $message .= '. Error Description: ' . $helper->getErrorDescription();
        throw new Core\ApiException($message, 4, $this->id, 401);
      } else {
        throw new Core\ApiException('Bad request', 5, $this->id, 400);
      }
    }

    $oAuth2Client = $fb->getOAuth2Client();
    if ($longLived && !$accessToken->isLongLived()) {
      try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
      } catch (\Facebook\Exceptions\FacebookSDKException $e) {
        throw new Core\ApiException('Error getting long-lived access token: ' . $e->getMessage(), 4, $this->id, 401);
      }
    }

    return $accessToken;
  }

  /**
   * Get data from FB.
   *
   * @param $fb
   * @param $node
   * @param $accessToken
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _get($fb, $node, $accessToken)
  {
    $fields = $this->val($this->meta->fields);

    try {
      $response = $fb->get("/$node?fields=" . implode(',', $fields), $accessToken);
    } catch(Exceptions\FacebookResponseException $e) {
      throw new Core\ApiException('graph returned an error: ' . $e->getMessage(), 5, $this->id);
    } catch(Exceptions\FacebookSDKException $e) {
      throw new Core\ApiException('facebook SDK returned an error: ' . $e->getMessage(), 5, $this->id);
    }

    return $response->getDecodedBody();
  }

  /**
   * Post data to FB.
   * @param $fb
   * @param $node
   * @param $accessToken
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _post($fb, $node, $accessToken)
  {
    $objectType = $this->val($this->meta->objectType);
    $data = $this->val($this->meta->data);


    try {
      $response = $fb->get("/$node?fields=" . implode(',', $fields), $accessToken);
    } catch(Exceptions\FacebookResponseException $e) {
      throw new Core\ApiException('graph returned an error: ' . $e->getMessage(), 5, $this->id);
    } catch(Exceptions\FacebookSDKException $e) {
      throw new Core\ApiException('facebook SDK returned an error: ' . $e->getMessage(), 5, $this->id);
    }

    return $response->getDecodedBody();
  }
}
