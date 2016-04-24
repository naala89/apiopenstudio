<?php

/**
 * Endpoint for Facebook.
 *
 * Meta:
 * {
 *    type: 'ePFacebook',
 *    meta: {
 *      graphId: <processor|string>,
 *      nodetype: <processor|string>,
 *      appId: <processor|string>,
 *      appSecret: <processor|string>,
 *      limit: <processor|integer>,
 *    },
 * }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');
include_once(Config::$dirIncludes . 'class.Curl.php');

class ProcessorFacebook extends Processor {

  const GRAPH_URL = 'https://graph.facebook.com/%s/%s?%s&%s';
  const OAUTH_URL = 'https://graph.facebook.com/oauth/access_token?client_id=%s&client_secret=%s&grant_type=client_credentials';

  public $details = array(
    'name' => 'Facebook',
    'description' => 'Facebook end-point.',
    'menu' => 'endpoint',
    'input' => array(
      'graphId' => array(
        'description' => 'The graph ID.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
      'nodeType' => array(
        'description' => 'The node or edge type.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
      'appId' => array(
        'description' => 'The facebook app ID.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
      'appSecret' => array(
        'description' => 'The app secret.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
      'limit' => array(
        'description' => 'The number of objects to fetch.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'var', 'literal'),
      ),
    ),
  );

  /**
   * @return array|bool|\Error
   * @throws \ApiException
   */
  public function process () {
    Debug::variable($this->meta, 'ProcessorFacebook');

    $graphid = $this->val($this->meta->graphId);
    $nodetype = $this->val($this->meta->nodeType);
    $appId = $this->val($this->meta->appId);
    $appSecret = $this->val($this->meta->appSecret);
    $limit = $this->val($this->meta->limit);

    $options = array('limit' => $limit);
    $qs = http_build_query($options, '', '&');
    $token = $this->_fetchToken($appId, $appSecret);

    $url = sprintf($this::GRAPH_URL, $graphid, $nodetype, $token, $qs);
    $curl = new Curl();
    $raw = $curl->get($url);
    $data = json_decode($raw);

    if (isset($data->error)) {
      throw new \Datagator\includes\ApiException($data->error->message, $data->error->code, $this->id, 400);
    } elseif (!isset($data->data)) {
      throw new \Datagator\includes\ApiException('failed to fetch data from Facebook Graph', 1, $this->id, 400);
    }

    return $data->data;
  }

  /**
   * Fetch Facebook authentication token.
   *
   * @param $appId
   * @param $appSecret
   * @return string
   * @throws \ApiException
   */
  private function _fetchToken ($appId, $appSecret) {
    $url = sprintf($this::OAUTH_URL, $appId, $appSecret);
    $raw = file_get_contents($url);

    if (!$raw) {
      throw new \Datagator\includes\ApiException('failed to fetch token from Facebook Graph', 1, $this->id, 400);
    }

    return $raw;
  }
}
