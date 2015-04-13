<?php

/**
 * Generate the Drupal session cookie string
 *
 * Either uid or token can be given for authentication
 *
 * METADATA
 * {
 *    "type":"drupalSession",
 *    "meta":{
 *      "id": <integer>
 *      "token":<processor|string>,
 *      "externalId":<processor|string>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorDrupalSession extends Processor
{
  public function process()
  {
    Debug::variable($this->meta, 'ProcessorDrupalSession');
    if (!isset($this->meta->token) && !isset($this->meta->externalId)) {
      $this->status = 417;
      return new Error(3, $this->id, 'externalId or token not defined');
    }

    $this->status = 200;

    if (!empty($this->meta->token)) {

      $token = $this->getVar($this->meta->token);
      if ($this->status != 200) {
        return $token;
      }

      $token = $this->request->db->escape($token);
      $result = $this->request->db
          ->select()
          ->from('user')
          ->where('token = "' . $token . '"')
          ->execute();

    } elseif (!empty($this->meta->externalId)) {

      $externalId = $this->getVar($this->meta->externalId);
      if ($this->status != 200) {
        return $externalId;
      }

      $result = $this->request->db
          ->select()
          ->from('user')
          ->where('external_id = "' . $this->request->db->escape($externalId) . '"')
          ->execute();
    } else {
      throw new ApiException('empty externalId or token found', 3, $this->id, 417);
    }

    $dbObj = $result->fetch_object();

    return $dbObj->session_name . '=' . $dbObj->session_id;
  }
}
