<?php

/**
 * Get data form the Facebook API.
 */

namespace Datagator\Endpoint;
use Datagator\Processor;
use Datagator\Core;
use Facebook\Authentication;
use Facebook\Exceptions;

class Facebook extends Processor\ProcessorBase
{
  private $endpoint = 'https://graph.facebook.com';

  protected $required = array('appId', 'appSecret', 'graphVersion', 'accessToken', 'query');
  protected $details = array(
    'name' => 'Facebook',
    'description' => 'Fetch results from facebook Graph API.',
    'menu' => 'Endpoint',
    'application' => 'All',
    'input' => array(
      'appId' => array(
        'description' => 'The app_id that you will be accessing facebook with.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'appSecret' => array(
        'description' => 'The app_secret that you will be accessing facebook with.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'graphVersion' => array(
        'description' => 'The version of graph to use (do not prefix with "v", e.g. use "2.4").',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'float'),
      ),/*
      'accessToken' => array(
        'description' => 'The Facebook access token (see https://developers.facebook.com/docs/graph-api/overview for how to get token).',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'query' => array(
        'description' => 'The Facebook root node that you want to access (see https://developers.facebook.com/docs/graph-api/reference for reference).',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),*/
    ),
  );

  /**
   * Retrieve data from an endpoint URL.
   *
   * @return mixed
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Endpoint Facebook', 4);
    $this->validateRequired();

    $appId = $this->getVar($this->meta->appId);
    $appSecret = $this->getVar($this->meta->appSecret);
    $graphVersion = $this->getVar($this->meta->graphVersion);
    $accessToken = $this->getVar($this->meta->accessToken);
    $query = $this->getVar($this->meta->query);

    $fb = new \Facebook\Facebook(array(
      'app_id' => $appId,
      'app_secret' => $appSecret,
      'default_graph_version' => "v$graphVersion",
    ));

    try {
      $response = $fb->get($query, $accessToken);
    } catch(Exceptions\FacebookResponseException $e) {
      throw new Core\ApiException('graph returned an error: ' . $e->getMessage(), 5, $this->id);
    } catch(Exceptions\FacebookSDKException $e) {
      throw new Core\ApiException('facebook SDK returned an error: ' . $e->getMessage(), 5, $this->id);
    }

    return $response;
  }

  private function _getToken($appId, $appSecret, $graphVersion)
  {
    $fb = new Facebook([
      'app_id' => $appId,
      'app_secret' => $appSecret,
      'default_graph_version' => "v$graphVersion",
    ]);

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

    if (! isset($accessToken)) {
      if ($helper->getError()) {
        $message = 'Error: ' . $helper->getError();
        $message .= '. Error Code: ' . $helper->getErrorCode();
        $message .= '. Error Reason: ' . $helper->getErrorReason();
        $message .= '. Error Description: ' . $helper->getErrorDescription();
        throw new Core\ApiException($message, 4, $this->id, 401);
      } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
      }
      exit;
    }
  }
}
