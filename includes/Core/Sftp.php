<?php

/**
 * Class Sftp.
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
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use stdClass;

/**
 * Class Sftp
 *
 * Class to perform file operations over the SFTP protocol.
 *
 * @see https://flysystem.thephpleague.com/docs/adapter/sftp-v3/
 */
class Sftp
{
    /**
     * @var array|string[] Required parameters.
     */
    protected array $requireParams = [
        'host',
        'username',
        'root_path',
    ];

    /**
     * @var array|string[] Optional parameters and their defaults.
     */
    protected array $optionalParams = [
        'password' => null,
        'private_key' => null,
        'passphrase' => null,
        'port' => 22,
        'user_agent' => false,
        'timeout' => 30,
        'max_tries' => 4,
        'fingerprint_string' => null,
    ];

    /**
     * @var array|int[][] Uploaded file permissions.
     */
    protected array $permissions = [
        'file' => [
            'public' => 0640,
            'private' => 0604,
        ],
        'dir' => [
            'public' => 0740,
            'private' => 7604,
        ],
    ];

    /**
     * Upload a file via SFTP.
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

        $provider = new SftpConnectionProvider(
            $parameters->host,
            $parameters->username,
            $parameters->password,
            $parameters->private_key,
            $parameters->passphrase,
            $parameters->port,
            $parameters->user_agent,
            $parameters->timeout,
            $parameters->max_tries,
            $parameters->fingerprint_string,
        );

        $adapter = new SftpAdapter(
            $provider,
            $parameters->root_path,
            PortableVisibilityConverter::fromArray($this->permissions)
        );

        $filesystem = new Filesystem($adapter);

        try {
            $filesystem->write($filename, $data);
        } catch (FilesystemException $e) {
            throw new ApiException($e->getMessage(), 8, -1, 400);
        }
    }
}
