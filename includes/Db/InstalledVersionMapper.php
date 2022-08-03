<?php

/**
 * Class InstalledMapper.
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

/**
 * Class InstalledVersionMapper.
 *
 * Mapper class for DB calls used for the installed_version table.
 */
class InstalledVersionMapper extends Mapper
{
    /**
     * Save an InstalledVersion object.
     *
     * @param InstalledVersion $installedVersion The InstalledVersion object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(InstalledVersion $installedVersion): bool
    {
        if ($installedVersion->getMid() === null) {
            $sql = 'INSERT INTO `installed_version` (`module`, `version`, `update`) VALUES (?, ?, ?)';
            $bindParams = [
                $installedVersion->getModule(),
                $installedVersion->getVersion(),
                $installedVersion->getUpdate(),
            ];
        } else {
            $sql = 'UPDATE `installed_version` SET `module` = ?, `version` = ?, `update` = ? WHERE `mid` = ?';
            $bindParams = [
                $installedVersion->getModule(),
                $installedVersion->getVersion(),
                $installedVersion->getUpdate(),
                $installedVersion->getMid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete an installed_version.
     *
     * @param InstalledVersion $installedVersion The InstalledVersion object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(InstalledVersion $installedVersion): bool
    {
        $sql = 'DELETE FROM `installed_version` WHERE `mid` = ?';
        $bindParams = [$installedVersion->getMid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find all installed_version's.
     *
     * @param array $params Filter params.
     *
     * @return array Array of Application objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params = []): array
    {
        $sql = 'SELECT * FROM `installed_version`';
        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Find all installed_version's.
     *
     * @param string $machineName Module machine_name.
     *
     * @return InstalledVersion.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByModule(string $machineName): InstalledVersion
    {
        $sql = 'SELECT * FROM `installed_version` WHERE `module`=?';
        $bindParams = [$machineName];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Map a DB row into an InstalledVersion object.
     *
     * @param array $row DB row object.
     *
     * @return InstalledVersion
     */
    protected function mapArray(array $row): InstalledVersion
    {
        $installedVersion = new InstalledVersion();
        $installedVersion->setMid($row['mid'] ?? null);
        $installedVersion->setModule($row['module'] ?? null);
        $installedVersion->setVersion($row['version'] ?? null);
        $installedVersion->setUpdate($row['update'] ?? null);

        return $installedVersion;
    }
}
