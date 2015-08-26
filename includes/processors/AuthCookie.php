<?php

/**
 * Provide cookie authentication
 *
 * This class is to be used by ProcessorInput.
 *
 * Meta:
 *    {
 *      "type": "cookie",
 *      "meta": {
 *        "id": <integer>,
 *        "cookie": <processor|string>
 *      }
 *    }
 */

namespace Datagator\Processors;
use Datagator\Core;

class AuthCookie extends Processor
{
  protected $required = array('cookie');
  protected $details = array(
    'name' => 'Auth (Cookie)',
    'description' => 'Authentication for remote server, using a cookie.',
    'menu' => 'authentication',
    'application' => 'All',
    'input' => array(
      'cookie' => array(
        'description' => 'The cookie.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Auth Cookie', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $cookie = $this->getVar($this->meta->cookie);

    return array(CURLOPT_COOKIE => $cookie);
  }
}
