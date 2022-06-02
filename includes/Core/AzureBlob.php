<?php

/**
 * Class AzureBlob.
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

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use stdClass;

/**
 * Class AzureBlob
 *
 * Class to perform file operations with an Azure Blob Storage.
 *
 * @see https://flysystem.thephpleague.com/docs/adapter/azure-blob-storage/
 */
class AzureBlob
{
    /**
     * @var array|string[] Required parameters.
     */
    protected array $requireParams = [
        'dsn',
        'container',
    ];

    /**
     * @var array|string[] Optional parameters and their defaults.
     */
    protected array $optionalParams = [
        'prefix' => '',
    ];

    /**
     * Upload a file to an Azure Blob Storage.
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

        $provider = BlobRestProxy::createBlobService($parameters->dsn);

        $adapter = new AzureBlobStorageAdapter(
            $provider,
            $parameters->container,
            $parameters->prefix
        );

        $filesystem = new Filesystem($adapter);

        try {
            $filesystem->write($filename, $data);
        } catch (FilesystemException $e) {
            throw new ApiException($e->getMessage(), 8, -1, 400);
        }
    }
}
