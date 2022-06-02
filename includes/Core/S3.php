<?php

/**
 * Class S3.
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

use Aws\S3\S3Client;
use Aws\S3\S3MultiRegionClient;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use stdClass;

/**
 * Class S3
 *
 * Class to perform file operations over with AWS S3 buckets.
 *
 * @see https://github.com/thephpleague/flysystem-aws-s3-v3
 */
class S3
{
    /**
     * @var array|string[] Required parameters.
     */
    protected array $requireParams = [
        'key',
        'secret',
        'bucket',
        'version',
    ];

    /**
     * @var array|string[] Optional parameters and their defaults.
     */
    protected array $optionalParams = [
        'region' => null,
    ];

    /**
     * Upload a file to an S3 bucket.
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

        if (!empty($parameters->region)) {
            $provider = new S3Client([
                'credentials' => [
                    'key'    => $parameters->key,
                    'secret' => $parameters->secret,
                ],
                'region' => $parameters->region,
                'version' => $parameters->version,
            ]);
        } else {
            $provider = new S3MultiRegionClient([
                'credentials' => [
                    'key'    => $parameters->key,
                    'secret' => $parameters->secret,
                ],
                'version' => $parameters->version,
            ]);
        }

        $adapter = new AwsS3V3Adapter($provider, $parameters->bucket);

        $filesystem = new Filesystem($adapter);

        try {
            $filesystem->write($filename, $data);
        } catch (FilesystemException $e) {
            throw new ApiException($e->getMessage(), 8, -1, 400);
        }
    }
}
