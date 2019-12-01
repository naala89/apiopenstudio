<?php

namespace Gaterdata\Db;

/**
 * Class VarsMapper.
 *
 * @package Gaterdata\Db
 */
class VarsMapper extends Mapper
{
    /**
     * @var ADOConnection DB connection instance.
     */
    protected $db;

    /**
     * VarsMapper constructor.
     *
     * @param ADOConnection $dbLayer
     *   DB connection object.
     */
    public function __construct(ADOConnection $dbLayer)
    {
        $this->db = $dbLayer;
    }

    /**
     * Save the var.
     *
     * @param \Gaterdata\Db\Vars $vars
     *   Vars object.
     *
     * @return bool
     *   Success.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function save(Vars $vars)
    {
        if ($vars->getId() == null) {
            $sql = 'INSERT INTO vars (appid, name, val) VALUES (?, ?, ?)';
            $bindParams = [
            $vars->getAppId(),
            $vars->getName(),
            $vars->getval(),
            ];
        } else {
            $sql = 'UPDATE vars SET appid=?, name=?, val=? WHERE id = ?';
            $bindParams = [
            $vars->getAppId(),
            $vars->getName(),
            $vars->getVal(),
            $vars->getId(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete the vars.
     *
     * @param \Gaterdata\Db\Vars $vars
     *   Vars object.
     *
     * @return bool
     *   Success.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function delete(Vars $vars)
    {
        if ($vars->getId() === null) {
            throw new ApiException('cannot delete var - empty ID', 2);
        }
        $sql = 'DELETE FROM vars WHERE id = ?';
        $bindParams = [$vars->getId()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find a var by its ID.
     *
     * @param int $id
     *   Var ID.
     *
     * @return \Gaterdata\Db\Vars
     *   Vars object.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findById($id)
    {
        $sql = 'SELECT * FROM vars WHERE id = ?';
        $bindParams = [$id];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a var by application ID and var name.
     *
     * @param int $appId
     *   Application ID.
     * @param string $name
     *   Var name.
     *
     * @return \Gaterdata\Db\Vars
     *   Vars object.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByAppIdName($appId, $name)
    {
        $sql = 'SELECT * FROM vars WHERE appid = ? AND name = ?';
        $bindParams = [$appId, $name];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find the vars belonging to an application.
     *
     * @param int $appId
     *   Application ID.
     *
     * @return array
     *   Array of Vars objects.
     *
     * @throws \Gaterdata\Core\ApiException
     */
    public function findByAppId($appId)
    {
        $sql = 'SELECT * FROM vars WHERE appid = ?';
        $bindParams = [$appId];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a results row to attributes.
     *
     * @param array $row
     *   DB results row.
     *
     * @return \Gaterdata\Db\Vars
     *   Vars object.
     */
    protected function mapArray(array $row)
    {
        $vars = new Vars();

        $vars->setId(!empty($row['id']) ? $row['id'] : null);
        $vars->setAppId(!empty($row['appid']) ? $row['appid'] : null);
        $vars->setName(!empty($row['name']) ? $row['name'] : null);
        $vars->setVal(!empty($row['val']) ? $row['val'] : null);

        return $vars;
    }
}
