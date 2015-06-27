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
      throw new ApiException('externalId or token not defined', 3, $this->id, 417);
    }

    if (!empty($this->meta->token)) {
      $token = $this->getVar($this->meta->token);
      $sql = 'SELECT * FROM user WHERE token=?';
      $recordSet = $this->request->db->Execute($sql, array($token));
    } elseif (!empty($this->meta->externalId)) {
      $externalId = $this->getVar($this->meta->externalId);
      $sql = 'SELECT * FROM user WHERE external_id=?';
      $recordSet = $this->request->db->Execute($sql, array($externalId));
    }
    if ($recordSet->RecordCount() < 1) {
      throw new ApiException('Invalid token', 3, $this->id, 417);
    }
    return $recordSet->fields['session_name'] . '=' . $recordSet->fields['session_id'];
  }
}
