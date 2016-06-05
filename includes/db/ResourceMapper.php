<?php

/**
 * Fetch and save resource data.
 */

namespace Datagator\Db;
use Datagator\Core;

class ResourceMapper
{
  protected $db;

  /**
   * @param $dbLayer
   */
  public function __construct($dbLayer)
  {
    $this->db = $dbLayer;
  }

  /**
   * @param \Datagator\Db\Resource $resource
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(Resource $resource)
  {
    if ($resource->getId() == NULL) {
      $sql = 'INSERT INTO resource (appid, name, description, method, identifier, meta, ttl) VALUES (?, ?, ?, ?, ?, ?, ?)';
      $bindParams = array(
        $resource->getAppId(),
        $resource->getName(),
        $resource->getDescription(),
        $resource->getMethod(),
        $resource->getIdentifier(),
        $resource->getMeta(),
        $resource->getTtl()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE resource SET appid = ?, name = ?, description = ?, method = ?, identifier = ?, meta = ?, ttl = ? WHERE id = ?';
      $bindParams = array(
        $resource->getAppId(),
        $resource->getName(),
        $resource->getDescription(),
        $resource->getMethod(),
        $resource->getIdentifier(),
        $resource->getMeta(),
        $resource->getTtl(),
        $resource->getId()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param \Datagator\Db\Resource $resource
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function delete(Resource $resource)
  {
    if ($resource->getId() == NULL) {
      throw new Core\ApiException('could not delete resource, not found', 2);
    }
    $sql = 'DELETE FROM resource WHERE id = ?';
    $bindParams = array($resource->getId());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param $id
   * @return \Datagator\Db\Resource
   */
  public function findId($id)
  {
    $sql = 'SELECT * FROM resource WHERE id = ?';
    $bindParams = array($id);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $appId
   * @param $method
   * @param $identifier
   * @return \Datagator\Db\Resource
   */
  public function findByAppIdMethodIdentifier($appId, $method, $identifier)
  {
    $sql = 'SELECT r.* FROM resource AS r INNER JOIN application AS a ON r.appid=a.appid WHERE (r.appid = ? OR a.name = "All") AND r.method = ? AND r.identifier = ?';
    $bindParams = array($appId, $method, $identifier);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $apps
   * @param $method
   * @param $identifier
   * @return array
   */
  public function findByAppsMethodIdentifier($apps, $method, $identifier)
  {
    $sql = 'SELECT r.* FROM resource AS r INNER JOIN application AS a ON r.appid=a.appid WHERE';
    $bindParams = array();
    if (is_array($apps)) {
      $q = array();
      for ($i = 0; $i < sizeof($apps); $i++) {
        $q[] = '?';
        $bindParams[] = $apps[$i];
      }
      $sql .= ' a.name in (' . implode(',', $q) . ')';
    } else {
      $sql .= ' a.name=?';
      $bindParams[] = $apps;
    }
    $sql .= ' AND r.method = ? AND r.identifier = ?';
    $bindParams[] = $method;
    $bindParams[] = $identifier;

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * @param $appId
   * @return array
   */
  public function findByAppId($appId)
  {
    $sql = 'SELECT * FROM resource WHERE appid = ?';
    $bindParams = array($appId);

    $recordSet = $this->db->Execute($sql, $bindParams);

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * @param array $row
   * @return \Datagator\Db\Resource
   */
  protected function mapArray(array $row)
  {
    $resource = new Resource();

    $resource->setId(!empty($row['id']) ? $row['id'] : NULL);
    $resource->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $resource->setName(!empty($row['name']) ? $row['name'] : NULL);
    $resource->setDescription(!empty($row['description']) ? $row['description'] : NULL);
    $resource->setMethod(!empty($row['method']) ? $row['method'] : NULL);
    $resource->setIdentifier(!empty($row['identifier']) ? $row['identifier'] : NULL);
    $resource->setMeta(!empty($row['meta']) ? $row['meta'] : NULL);
    $resource->setTtl(!empty($row['ttl']) ? $row['ttl'] : NULL);

    return $resource;
  }
}
