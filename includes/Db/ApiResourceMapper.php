<?php

namespace Datagator\Db;

use Datagator\Core\ApiException;
use ADOConnection;
use Cascade\Cascade;

/**
 * Class ApiResourceMapper.
 *
 * @package Datagator\Db
 */
class ApiResourceMapper {

  protected $db;

  /**
   * ResourceMapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
    $this->db = $dbLayer;
  }

  /**
   * Save an API Resource.
   *
   * @param \Datagator\Db\ApiResource $resource
   *   The API Resource.
   *
   * @return bool
   *   Success.
   *
   * @throws \Datagator\Core\ApiException
   */
  public function save(ApiResource $resource) {
    if ($resource->getId() == NULL) {
      $sql = 'INSERT INTO resource (appid, name, description, method, identifier, meta, ttl) VALUES (?, ?, ?, ?, ?, ?, ?)';
      $bindParams = array(
        $resource->getAppId(),
        $resource->getName(),
        $resource->getDescription(),
        $resource->getMethod(),
        $resource->getIdentifier(),
        $resource->getMeta(),
        $resource->getTtl(),
      );
    }
    else {
      $sql = 'UPDATE resource SET appid = ?, name = ?, description = ?, method = ?, identifier = ?, meta = ?, ttl = ? WHERE id = ?';
      $bindParams = array(
        $resource->getAppId(),
        $resource->getName(),
        $resource->getDescription(),
        $resource->getMethod(),
        $resource->getIdentifier(),
        $resource->getMeta(),
        $resource->getTtl(),
        $resource->getId(),
      );
    }
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Delete an API resource.
   *
   * @param \Datagator\Db\ApiResource $resource
   *   API resoure object.
   *
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
  public function delete(ApiResource $resource) {
    if ($resource->getId() == NULL) {
      throw new ApiException('could not delete resource, not found', 2);
    }
    $sql = 'DELETE FROM resource WHERE id = ?';
    $bindParams = array($resource->getId());
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
    throw new ApiException($message, 2);
  }

  /**
   * Find an API resource by its ID.
   *
   * @param int $id
   *   API  resource ID.
   *
   * @return \Datagator\Db\ApiResource
   *   ApiResource object.
   *
   * @throws ApiException
   */
  public function findId($id) {
    $sql = 'SELECT * FROM resource WHERE id = ?';
    $bindParams = array($id);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
  }

  /**
   * Find an API resopurce by application ID, method and identifier.
   *
   * @param int $appid
   *   Application ID.
   * @param string $method
   *   API resource method.
   * @param string $identifier
   *   API resource identifier.
   *
   * @return \Datagator\Db\ApiResource
   *   ApiResource object.
   *
   * @throws ApiException
   */
  public function findByAppIdMethodIdentifier($appid, $method, $identifier) {
    $sql = 'SELECT r.* FROM resource AS r WHERE r.appid = ? AND r.method = ? AND r.identifier = ?';
    $bindParams = array($appid, $method, $identifier);
    $row = $this->db->GetRow($sql, $bindParams);
    if (!$row) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }
    return $this->mapArray($row);
  }

  /**
   * Find a resource by application name/s, method and identifier.
   *
   * @param array|string $appNames
   *   Application name or array of application names.
   * @param string $method
   *   Resource method.
   * @param string $identifier
   *   Resource identifier.
   *
   * @return array
   *   Array of ApiResource objects.
   *
   * @throws ApiException
   */
  public function findByAppNamesMethodIdentifier($appNames, $method, $identifier) {
    $sql = 'SELECT r.* FROM resource AS r INNER JOIN application AS a ON r.appid=a.appid WHERE';
    $bindParams = array();
    if (is_array($appNames)) {
      $q = array();
      for ($i = 0; $i < count($appNames); $i++) {
        $q[] = '?';
        $bindParams[] = $appNames[$i];
      }
      $sql .= ' a.name in (' . implode(',', $q) . ')';
    }
    else {
      $sql .= ' a.name=?';
      $bindParams[] = $appNames;
    }
    $sql .= ' AND r.method = ? AND r.identifier = ?';
    $bindParams[] = $method;
    $bindParams[] = $identifier;

    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Find API Resources by an application ID.
   *
   * @param int $appid
   *   Application ID.
   *
   * @return array
   *   Array of ApiResource objects.
   *
   * @throws ApiException
   */
  public function findByAppId($appid) {
    $sql = 'SELECT * FROM resource WHERE appid = ?';
    $bindParams = array($appid);

    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = array();
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Map a DB row to this object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return \Datagator\Db\ApiResource
   *   ApiResource object.
   */
  protected function mapArray(array $row) {
    $resource = new ApiResource();

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
