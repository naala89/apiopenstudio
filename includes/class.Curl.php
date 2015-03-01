<?php

class Curl
{
  public $httpStatus;
  public $curlStatus;
  public $errorMsg;
  public $type;
  public $options = array();
  public $url;

  public function __construct()
  {
    $this->options[CURLOPT_RETURNTRANSFER] = TRUE;
  }

  /**
   * Send a GET request using cURL.
   *
   * @param string $url
   *  url for the curl call
   * @param array $options
   *  additional options
   *
   * @return string
   */
  public function get($url, array $options = array())
  {
    $options[CURLOPT_HTTPGET] = TRUE;
    return $this->_exec($url, $options);
  }

  /**
   * Send a POST request using cURL.
   *
   * @param string $url
   *  url for the curl call
   * @param array $post
   *  values to send
   * @param array $options
   *  additional options
   *
   * @return string
   */
  public function post($url, array $post = array(), array $options = array())
  {
    $options[CURLOPT_POST] = TRUE;
    if (!empty($post)) {
      $options[CURLOPT_POSTFIELDS] = $post;
    }

    return $this->_exec($url, $options);
  }

  /**
   * Utility function to get options after adding them to the default curl options.
   *
   * @param string $url
   *  url for the curl call
   * @param array $options
   *  additional options
   *
   * @return array
   *  array of options
   */
  private function getCurlOptions($url, $options)
  {
    return $this->options + $options + array(CURLOPT_URL => $url);
  }

  /**
   * Perform a cURL request.
   *
   * @param string $url
   *  url for the curl call
   * @param array $options
   *  additional options
   *
   * @return string
   */
  private function _exec($url, array $options = array())
  {
    $options = $this->getCurlOptions($url, $options);
    Debug::variable($options, 'Curl options', 4);

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $this->httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $this->type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $this->curlStatus = curl_errno($ch);
    $this->errorMsg = curl_error($ch);

    curl_close($ch);

    return $response;
  }
}
