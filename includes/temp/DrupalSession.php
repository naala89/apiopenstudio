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

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db\UserMapper;

class DrupalSession extends ProcessorBase
{
  public function process()
  {
    Debug::variable($this->meta, 'ProcessorDrupalSession');
    $token = $this->val($this->meta->token);
    $externalId = $this->val($this->meta->externalId);
    $db = $this->getDb();
    $userMapper = new UserMapper($db);

    if (!empty($token)) {
      $sql = 'SELECT * FROM user WHERE token=?';
      $bindParams = array($token);
    } elseif (!empty($externalId)) {
      $sql = 'SELECT * FROM user WHERE external_id=?';
      $bindParams = array($externalId);
    } else {
      throw new Core\ApiException('externalId or token not defined', 3, $this->id, 417);
    }
    $recordSet = $this->request->db->Execute($sql, $bindParams);
    if ($recordSet->RecordCount() < 1) {
      throw new Core\ApiException('Invalid token or external ID', 3, $this->id, 417);
    }

    return $recordSet->fields['session_name'] . '=' . $recordSet->fields['session_id'];
  }
}
