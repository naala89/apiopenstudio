<?php

/**
 * Wrapper of the Curl function.
 */

namespace Gaterdata\Core;

class Curl
{
    public $httpStatus;
    public $curlStatus;
    public $errorMsg;
    public $type;
    public $options = array(CURLOPT_RETURNTRANSFER => true);
    public $url;
  /**
   * Curl constants
   *
   * 13     CURLOPT_TIMEOUT
   * 47     CURLOPT_POST
   * 52     CURLOPT_FOLLOWLOCATION
   * 64     CURLOPT_SSL_VERIFYPEER
   * 78     CURLOPT_CONNECTTIMEOUT
   * 80     CURLOPT_HTTPGET
   * 10001  CURLOPT_FILE
   * 10002  CURLOPT_URL
   * 10005  CURLOPT_USERPWD
   * 10015  CURLOPT_POSTFIELDS
   * 10022  CURLOPT_COOKIE
   * 19913  CURLOPT_RETURNTRANSFER
   */

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
        $options[CURLOPT_HTTPGET] = true;
        return $this->_exec($url, $options);
    }

  /**
   * Send a POST request using cURL.
   *
   * @param $url
   *  url for the curl call
   * @param array $options
   *  additional options. This includes the post vars.
   *
   * @return string
   */
    public function post($url, array $options = array())
    {
        $options[CURLOPT_POST] = true;
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
    private function _getCurlOptions($url, array $options = array())
    {
        return $this->options + array(CURLOPT_URL => $url) + $options;
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
        $options = $this->_getCurlOptions($url, $options);

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
