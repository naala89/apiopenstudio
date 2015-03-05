<?php

/**
 * Provide token authentication based on token in DB
 *
 * Meta:
 *    {
 *      "type": "tokenValidate",
 *      "meta": {
 *        "id":<integer>,
 *        "token": <processor|string>
 *      }
 *    }
 *
 * @TODO: Can we set ValidateToken so that it can
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorValidateToken extends Processor
{
  protected $required = array('token');

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorValidateToken');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $token = $this->getVar($this->meta->token);
    if ($this->status != 200) {
      return $token;
    }

    $result = $this->request->db
        ->select()
        ->from('users', 'stale_time')
        ->where(array('token', $this->request->db->escape($token)))
        ->where('(now() < stale_time OR stale_time IS NULL)')
        ->execute();

    if ($result->num_rows < 1) {
      // TODO: we need to turn off caching here.
      $this->status = 403;
      return new Error(4, $this->id, 'invalid token');
    }

    return TRUE;
  }
}
