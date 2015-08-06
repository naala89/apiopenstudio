<?php

/**
 * Endpoint for Twitter.
 *
 * Meta:
 * {
 *    type: 'Twitter',
 *    meta: {
 *      key: <processor|string>,
 *      method: <processor|'get'|'post'>,
 *      object: <processor|string>,
 *      action: <processor|string>,
 *      parameters: {[<processor|string>: <processor|string>]*}
 *    },
 * }
 *
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');
include_once(Config::$dirIncludes . 'class.Curl.php');

class ProcessorFacebook extends Processor {

  const URL = 'https://api.twitter.com/1.1/%s/%s/';

  protected $required = array(
    'key',
    'method',
    'object',
    'action',
    'parameters',
  );

  protected $details = array(
    'name' => 'Twitter',
    'description' => 'Facebook end-point.',
    'menu' => 'endpoint',
    'input' => array(
      'key' => array(
        'description' => 'The Twitter key.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'method' => array(
        'description' => 'GET or POST.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'get', 'post'),
      ),
      'object' => array(
        'description' => 'The query type, ie friends, lists, statuses.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'action' => array(
        'description' => 'The query action, eg list.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'parameters' => array(
        'description' => 'The query parameters, eg source_screen_name, target_id.',
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
    Debug::variable($this->meta, 'ProcessorTwitter');
    $this->validateRequired();

    $key = $this->getVar($this->meta->key);
    $method = $this->getVar($this->meta->method);
    if ($method != 'get' && $method != 'post') {
      throw new \Datagator\includes\ApiException('incorrect twitter method', 1, $this->id, 400);
    }
    $object = $this->getVar($this->meta->object);
    $action = $this->getVar($this->meta->action);
    $parameters = $this->getVar($this->meta->parameters); //json decode?

    $curl = new Curl();

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
    $url = sprintf($this->_oauthUrl, $appId, $appSecret);
    $raw = file_get_contents($url);

    if (!$raw) {
      throw new \Datagator\includes\ApiException('failed to fetch token from Facebook Graph', 1, $this->id, 400);
    }

    return $raw;
  }
}
