<?php

/**
 * Base class for all resource based processors.
 *
 * METADATA
 * {
 *    "type":"resource",
 *    "meta":{
 *      "id":<mixed>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorResource extends Processor
{
  public function process()
  {
    Debug::message('ProcessorResource');
    return;
  }

  protected function insertResource($clientId, $method, $resource, $meta, $ttl)
  {
    $sql = 'INSERT INTO resource ("client", "method", "resopurce", "meta", "ttl") VALUES (?, ?, ?, ?, ?)';
    $recordSet = $this->request->db->Execute($sql, array($clientId, $method, $resource, $meta, $ttl));
    if ($recordSet->Affected_Rows() == 0) {
      throw new ApiException('there was an error inserting resource', 2, $this->id, 417);
    }
    return TRUE;
  }
}
