<?php

/**
 * Class GoogleCloud.
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

use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\Filesystem;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\FilesystemException;
use stdClass;

/**
 * Class GoogleCloud
 *
 * Class to perform file operations with Google Cloud.
 *
 * @see https://flysystem.thephpleague.com/docs/adapter/google-cloud-storage/
 */
class GoogleCloud
{
    /**
     * @var array|string[] Required parameters.
     */
    protected array $requireParams = [
        'bucket',
    ];

    /**
     * @var array|string[] Optional parameters and their defaults.
     */
    protected array $optionalParams = [
        'prefix' => '',
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

        $storageClient = new StorageClient([]);
        $bucket = $storageClient->bucket($parameters->bucket);

        $adapter = new GoogleCloudStorageAdapter($bucket, $parameters->prefix);

        $filesystem = new Filesystem($adapter);

        try {
            $filesystem->write($filename, $data);
        } catch (FilesystemException $e) {
            throw new ApiException($e->getMessage(), 8, -1, 400);
        }
    }
}
