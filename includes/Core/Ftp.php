<?php

/**
 * Class Ftp.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use stdClass;

/**
 * Class Ftp
 *
 * Class to perform file operations over the FTP protocol.
 *
 * @see https://flysystem.thephpleague.com/docs/adapter/ftp/
 */
class Ftp
{
    /**
     * @var array|string[] Required parameters.
     */
    protected array $requireParams = [
        'host',
        'root',
        'username',
        'password',
    ];

    /**
     * @var array|string[] Optional parameters and their defaults.
     */
    protected array $optionalParams = [
        'port' => 21,
        'ssl' => false,
        'timeout' => 90,
        'utf8' => false,
        'passive' => true,
        'transferMode' => FTP_BINARY,
        'systemType' => null,
        'ignorePassiveAddress' => null,
        'timestampsOnUnixListingsEnabled' => false,
        'recurseManually' => true,
    ];

    /**
     * Upload a file via FTP.
     *
     * @param stdClass $parameters
     * @param string $filename
     * @param string $data
     *
     * @return void
     *
     * @throws ApiException
     */
    public function uploadFile(stdClass $parameters, string $filename, string $data)
    {
        foreach ($this->requireParams as $requireParam) {
            if (!isset($parameters->$requireParam)) {
                throw new ApiException("Missing parameter: $requireParam", 6, -1, 400);
            }
        }
        foreach ($this->optionalParams as $paramName => $paramDefault) {
            if (!isset($parameters->$paramName)) {
                $parameters->$paramName = $paramDefault;
            }
        }

        $connectionOptions = FtpConnectionOptions::fromArray([
            $parameters->host,
            $parameters->root,
            $parameters->username,
            $parameters->password,
            $parameters->port,
            $parameters->ssl,
            $parameters->timeout,
            $parameters->utf8,
            $parameters->passive,
            $parameters->transferMode,
            $parameters->systemType,
            $parameters->ignorePassiveAddress,
            $parameters->timestampsOnUnixListingsEnabled,
            $parameters->recurseManually,
        ]);

        $adapter = new FtpAdapter($connectionOptions);

        $filesystem = new Filesystem($adapter);

        try {
            $filesystem->write($filename, $data);
        } catch (FilesystemException $e) {
            throw new ApiException($e->getMessage(), 8, -1, 400);
        }
    }
}
