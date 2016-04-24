<?php

/**
 * Get data form the Facebook API.
 */

namespace Datagator\Endpoint;
use Datagator\Db\ExternalUserMapper;
use Datagator\Processor;
use Datagator\Core;

class Twitter extends Processor\ProcessorBase
{
  private $apiUrl = 'https://api.twitter.com/';
  private $externalEntity = 'twitter';
  public $details = array(
    'name' => 'Twitter',
    'description' => 'Fetch results from the Twitter API.',
    'menu' => 'Endpoint',
    'application' => 'All',
    'input' => array(
      'key' => array(
        'description' => 'The consumer key to attach with.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'secret' => array(
        'description' => 'The consumer secret to attach with.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'twitterId' => array(
        'description' => 'The ID of the twitter account used for authentication - use this if an application has more than one twitter account that it uses (if omitted it will default to "twitter").',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'method' => array(
        'description' => 'The API call method (get or past).',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"get"', '"post"'),
      ),
      'uri' => array(
        'description' => 'The call you want to make to the Twitter API (e.g. "statuses/user_timeline.json", "friends/list.json", etc).',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'options' => array(
        'description' => 'The options (an array of Processor Field) allowed by Twitter for the call (e.g. cursor: -1, screen_name: twitterapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor Field'),
      ),
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
    Core\Debug::variable($this->meta, 'Endpoint Twitter', 4);

    $key = $this->val($this->meta->key);
    $secret = $this->val($this->meta->secret);
    $appId = $this->request->appId;
    $twitterId = $this->val($this->meta->twitterId);
    $twitterId = empty($twitterId) ? 'twitter' : $twitterId;

    // get existing token if it exists and if not then fetch a new one
    $mapper = new ExternalUserMapper($this->request->db);
    $externalUser = $mapper->findByAppIdEntityExternalId($appId, 'twitter', $twitterId);
    $token = empty($externalUser->getDataField1()) ? $this->_getToken($key, $secret, $appId, $twitterId) : $externalUser->getDataField1();

    // make call
    $method = $this->val($this->meta->method);
    if ($method != 'get' && $method != 'post') {
      throw new Core\ApiException('incorrect method', 6, $this->id);
    }
    $uri = $this->val($this->meta->uri);
    $url = $this->apiUrl . $uri;
    $options = $this->val($this->meta->options);
    $parameters = array();
    foreach ($options as $option) {
      $parameters[] = $option;
    }
    $curl = new Core\Curl();
    $result = $curl->$method($url, array(
      'CURLOPT_POSTFIELDS' => $parameters
    ));
    $normalise = new Core\Normalise($result, $curl->type);
    $result = $normalise->normalise();

    if ($curl->httpStatus != 200) {
      throw new Core\ApiException($result, 5, $this->id, $curl->httpStatus);
    }

    return $result;
  }

  /**
   * @param $key
   * @param $secret
   * @param $appId
   * @param $twitterId
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _getToken($key, $secret, $appId, $twitterId)
  {
    // fetch token from twitter
    $url = $this->apiUrl . 'oauth2/token';
    $credentials = $this->_getCredentials($key, $secret);
    $options = array(
      'CURLOPT_HTTPHEADER' => array(
        "Authorization: Basic $credentials",
        'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
      ),
      'CURLOPT_POSTFIELDS' => 'grant_type=client_credentials'
    );
    $curl = new Core\Curl();
    $response = $curl->post($url, $options);
    $normalise = new Core\Normalise($response, $curl->type);
    $response = $normalise->normalise();
    if (empty($response['token_type']) || empty($response['access_token'])) {
      throw new Core\ApiException($response, 4, $this->id, 403);
    }
    $token = $response['access_token'];

    // save token to db
    $mapper = new ExternalUserMapper($this->request->db);
    $externalUser = $mapper->findByAppIdEntityExternalId($appId, $this->externalEntity, $twitterId);
    if (empty($externalUser->getId())) {
      $externalUser->setAppId($appId);
      $externalUser->setExternalId($twitterId);
      $externalUser->setExternalEntity($this->externalEntity);
    }
    $externalUser->setDataField1($token);
    $mapper->save($externalUser);

    return $token;
  }

  /**
   * @param $key
   * @param $secret
   * @return string
   */
  private function _getCredentials($key, $secret)
  {
    return base64_encode(rawurlencode($key) . ':' . rawurlencode($secret));
  }
}
