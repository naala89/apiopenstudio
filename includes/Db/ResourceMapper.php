<?php

/**
 * Class ResourceMapper.
 *
 * @package    ApiOpenStudio\Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Db;

/**
 * Class ResourceMapper.
 *
 * Mapper class for DB calls used for the resource table.
 */
class ResourceMapper extends Mapper
{
    /**
     * Save an API Resource.
     *
     * @param Db\Resource $resource The API Resource.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(Db\Resource $resource): bool
    {
        if ($resource->getResid() == null) {
            $sql = <<<'TAG'
INSERT INTO resource (appid, name, description, method, uri, meta, openapi, ttl) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
TAG;
            $bindParams = [
                $resource->getAppId(),
                $resource->getName(),
                $resource->getDescription(),
                $resource->getMethod(),
                $resource->getUri(),
                $resource->getMeta(),
                $resource->getOpenapi(),
                $resource->getTtl(),
            ];
        } else {
            // phpcs:ignore
            $sql = 'UPDATE resource SET appid = ?, name = ?, description = ?, method = ?, uri = ?, meta = ?, openapi = ?, ttl = ? WHERE resid = ?';
            $bindParams = [
                $resource->getAppId(),
                $resource->getName(),
                $resource->getDescription(),
                $resource->getMethod(),
                $resource->getUri(),
                $resource->getMeta(),
                $resource->getOpenapi(),
                $resource->getTtl(),
                $resource->getResid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete an API resource.
     *
     * @param Db\Resource $resource Resource object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(Db\Resource $resource): bool
    {
        $sql = 'DELETE FROM resource WHERE resid = ?';
        $bindParams = [$resource->getResid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find all resources.
     *
     * @param array $params Filter and order params.
     *
     * @return array Resource object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function all(array $params = []): array
    {
        $sql = 'SELECT * FROM resource';
        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Find a resource by its ID.
     *
     * @param integer $resid Resource ID.
     *
     * @return Db\Resource Resource object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByResid(int $resid): Db\Resource
    {
        $sql = 'SELECT * FROM resource WHERE resid = ?';
        $bindParams = [$resid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a resource by application ID, method and URI.
     *
     * @param integer $appid Application ID.
     * @param string $method API resource method.
     * @param string $uri API resource URI.
     *
     * @return Db\Resource Resource object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppIdMethodUri(int $appid, string $method, string $uri): Db\Resource
    {
        $sql = 'SELECT * FROM resource WHERE appid = ? AND method = ? AND uri = ?';
        $bindParams = [$appid, $method, $uri];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a resource by application name/s, method and uri.
     *
     * @param array|string $appNames Application name or array of application names.
     * @param string $method Resource method.
     * @param string $uri Resource uri.
     *
     * @return array Array of Resource objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppNamesMethodUri($appNames, string $method, string $uri): array
    {
        $sql = 'SELECT r.* FROM resource AS r INNER JOIN application AS a ON r.appid=a.appid';
        $sql .= ' WHERE';
        $bindParams = [];
        if (is_array($appNames)) {
            $q = [];
            for ($i = 0; $i < count($appNames); $i++) {
                $q[] = '?';
                $bindParams[] = $appNames[$i];
            }
            $sql .= ' a.name in (' . implode(',', $q) . ')';
        } else {
            $sql .= ' a.name=?';
            $bindParams[] = $appNames;
        }
        $sql .= ' AND r.method = ? AND r.uri = ?';
        $bindParams[] = $method;
        $bindParams[] = $uri;

        $recordSet = $this->db->Execute($sql, $bindParams);
        if (!$recordSet) {
            $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            $this->logger->error('db', $message);
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
     * @param integer|array $appids Application ID or an array of appid's.
     * @param array $params Filter and order params.
     *
     * @return array Array of Resource objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAppId($appids, array $params = []): array
    {
        if (!is_array($appids)) {
            $sql = 'SELECT * FROM resource WHERE appid = ?';
            $bindParams = [$appids];
        } else {
            $placeholders = $bindParams = [];
            foreach ($appids as $appid) {
                if (!is_numeric($appid)) {
                    throw new ApiException("invalid appid: $appid", 6, -1, 401);
                }
                $placeholders[] = '?';
                $bindParams[] = (int) $appid;
            }
            $sql = 'SELECT * FROM resource WHERE appid IN (' . implode(', ', $placeholders) . ')';
        }

        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find Resources by user ID.
     *
     * @param integer $uid Application ID or an array of appid's.
     * @param array $params Filter and order params.
     *
     * @return array Array of Resource objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid, array $params = []): array
    {
        $privilegedRoles = ['Developer'];

        $sql = 'SELECT *';
        $sql .= ' FROM resource';
        $sql .= ' WHERE appid IN (';
        $sql .= ' SELECT ur.appid';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND r.name in ("' . implode('", "', $privilegedRoles) . '"))';

        $bindParams = [$uid];

        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find resources for a user, based on uid and role, then filter by accid, appid & resid.
     *
     * @param integer $uid User ID.
     * @param integer $rid Role ID.
     * @param integer $accid Account ID.
     * @param integer $appid Application ID.
     * @param integer $resid Resource ID.
     * @param array $params Filter params (keyword, order by, direction and limit).
     *
     * @return array of Resource objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUidRidAccidAppidResid(
        int $uid,
        int $rid,
        int $accid,
        int $appid,
        int $resid,
        array $params
    ): array {
        // Find Applications for the user role.
        $userRoleMapper = new UserRoleMapper($this->db, $this->logger);
        $result = $userRoleMapper->findByFilter(['col' => ['uid' => $uid, 'rid' => $rid]]);
        $appids = [];
        foreach ($result as $item) {
            $app_id = $item->getAppid();
            if (!in_array($app_id, $appids)) {
                $appids[] = $app_id;
            }
        }
        // Find all resources for the applications the user has rights for.
        $result = $this->findByAppId($appids, $params);
        // No further filters, so return the results.
        if (empty($accid) && empty($appid) && empty($resid)) {
            return $result;
        }
        // If accid is filter, find all applications for the accid.
        $appid = empty($appid) ? [] : [$appid];
        if (!empty($accid)) {
            $applicationMapper = new ApplicationMapper($this->db, $this->logger);
            $applications = $applicationMapper->findByAccid($accid);
            foreach ($applications as $application) {
                $appid[] = $application->getAppid();
            }
        }
        // Filter by resid.
        if (!empty($resid)) {
            foreach ($result as $item) {
                if ($resid == $item->getResid()) {
                    return [$item];
                }
            }
            return [];
        }
        // Filter by appid.
        $resources = [];
        if (!empty($appid)) {
            foreach ($result as $item) {
                if (in_array($item->getAppid(), $appid)) {
                    $resources[] = $item;
                }
            }
        }

        return $resources;
    }

    /**
     * Map a DB row to this object.
     *
     * @param array $row DB row object.
     *
     * @return Resource Resource object.
     */
    protected function mapArray(array $row): Resource
    {
        $resource = new Resource();

        $resource->setResid($row['resid'] ?? 0);
        $resource->setAppId($row['appid'] ?? 0);
        $resource->setName($row['name'] ?? '');
        $resource->setDescription($row['description'] ?? '');
        $resource->setMethod($row['method'] ?? '');
        $resource->setUri($row['uri'] ?? '');
        $resource->setMeta($row['meta'] ?? '');
        $resource->setOpenapi($row['openapi'] ?? '');
        $resource->setTtl($row['ttl'] ?? 0);

        return $resource;
    }
}
