<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;

/**
 * Class ResourceMapper.
 *
 * @package Gaterdata\Db
 */
class ResourceMapper extends Mapper {

  /**
   * Save an API Resource.
   *
   * @param Resource $resource
   *   The API Resource.
   *
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
  public function save(Resource $resource) {
    if ($resource->getResid() == NULL) {
      $sql = 'INSERT INTO resource (appid, name, description, method, uri, meta, ttl) VALUES (?, ?, ?, ?, ?, ?, ?)';
      $bindParams = [
        $resource->getAppId(),
        $resource->getName(),
        $resource->getDescription(),
        $resource->getMethod(),
        $resource->getUri(),
        $resource->getMeta(),
        $resource->getTtl(),
      ];
    }
    else {
      $sql = 'UPDATE resource SET appid = ?, name = ?, description = ?, method = ?, uri = ?, meta = ?, ttl = ? WHERE resid = ?';
      $bindParams = [
        $resource->getAppId(),
        $resource->getName(),
        $resource->getDescription(),
        $resource->getMethod(),
        $resource->getUri(),
        $resource->getMeta(),
        $resource->getTtl(),
        $resource->getResid(),
      ];
    }
    return $this->saveDelete($sql, $bindParams);
  }

  /**
   * Delete an API resource.
   *
   * @param ApiResource $resource
   *   Resource object.
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
  public function delete(ApiResource $resource) {
    $sql = 'DELETE FROM resource WHERE resid = ?';
    $bindParams = [$resource->getResid()];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find all resources.
   *
   * @return Resource
   *   Resource object.
   *
   * @throws ApiException
   */
  public function all() {
    $sql = 'SELECT * FROM resource';
    return $this->fetchRows($sql);
  }

  /**
   * Find a resource by its ID.
   *
   * @param int $resid
   *   Resource ID.
   * @return mixed
   *
   * @return Resource
   *   Resource object.
   *
   * @throws ApiException
   */
  public function findId($resid) {
    $sql = 'SELECT * FROM resource WHERE resid = ?';
    $bindParams = [$resid];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find a resource by application ID, method and URI.
   *
   * @param int $appid
   *   Application ID.
   * @param string $method
   *   API resource method.
   * @param string $uri
   *   API resource URI.
   *
   * @return Resource
   *   Resource object.
   *
   * @throws ApiException
   */
  public function findByAppIdMethodUri($appid, $method, $uri) {
    $sql = 'SELECT * FROM resource WHERE appid = ? AND method = ? AND uri = ?';
    $bindParams = [$appid, $method, $uri];
    return $this->fetchRow($sql, $bindParams);
  }

  /**
   * Find a resource by application name/s, method and uri.
   *
   * @param array|string $appNames
   *   Application name or array of application names.
   * @param string $method
   *   Resource method.
   * @param string $uri
   *   Resource uri.
   *
   * @return array
   *   Array of Resource objects.
   *
   * @throws ApiException
   */
  public function findByAppNamesMethodUri($appNames, $method, $uri) {
    $sql = 'SELECT r.* FROM resource AS r INNER JOIN application AS a ON r.appid=a.appid WHERE';
    $bindParams = [];
    if (is_array($appNames)) {
      $q = [];
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
    $sql .= ' AND r.method = ? AND r.uri = ?';
    $bindParams[] = $method;
    $bindParams[] = $uri;

    $recordSet = $this->db->Execute($sql, $bindParams);
    if (!$recordSet) {
      $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
      Cascade::getLogger('gaterdata')->error($message);
      throw new ApiException($message, 2);
    }

    $entries = [];
    while (!$recordSet->EOF) {
      $entries[] = $this->mapArray($recordSet->fields);
      $recordSet->moveNext();
    }

    return $entries;
  }

  /**
   * Find Resources by an application ID.
   *
   * @param int|array $appids
   *   Application ID or an array of appid's.
   * @param array $params
   *   Filter and order params.
   *
   * @return array
   *   Array of Resource objects.
   *
   * @throws ApiException
   */
  public function findByAppId($appids, $params = []) {
    if (!is_array($appids)) {
      $sql = 'SELECT * FROM resource WHERE appid = ?';
      $bindParams = [$appids];
    }
    else {
      $placeholders = $bindParams = [];
      foreach ($appids as $appid) {
        if (!is_numeric($appid)) {
          throw new ApiException("invalid appid: $appid", 6, $this->id, 401);
        }
        $placeholders[] = '?';
        $bindParams[] = (Integer) $appid;
      }
      $sql = 'SELECT * FROM resource WHERE appid IN (' . implode(', ', $placeholders) . ')';
    }
    
    return $this->fetchRows($sql, $bindParams, $params);
  }

  /**
   * Map a DB row to this object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return Resource ApiResource object.
   *   ApiResource object.
   */
  protected function mapArray(array $row) {
    $resource = new Resource();

    $resource->setResid(!empty($row['resid']) ? $row['resid'] : NULL);
    $resource->setAppId(!empty($row['appid']) ? $row['appid'] : NULL);
    $resource->setName(!empty($row['name']) ? $row['name'] : NULL);
    $resource->setDescription(!empty($row['description']) ? $row['description'] : NULL);
    $resource->setMethod(!empty($row['method']) ? $row['method'] : NULL);
    $resource->setUri(!empty($row['uri']) ? $row['uri'] : NULL);
    $resource->setMeta(!empty($row['meta']) ? $row['meta'] : NULL);
    $resource->setTtl(!empty($row['ttl']) ? $row['ttl'] : 0);

    return $resource;
  }

}
