<?php

/**
 * Class VarFile.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class VarFile
 *
 * Processor class to hold a file variable.
 */
class VarFile extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'File (NOT FULLY IMPLEMENTED YET)',
        'machineName' => 'var_file',
        'description' => 'NOTE: This is not fully implemented yet. Return the contents of a file or the file path.',
        'menu' => 'Primitive',
        'input' => [
            'location' => [
                // phpcs:ignore
                'description' => 'The location of the file. This can be a remote server address or "_FILES" to receive from a POST.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'filename' => [
                // phpcs:ignore
                'description' => 'The name of the file. This is the name of the file field in the post or the remote server filename.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the file does not exist.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'get_contents' => [
                // phpcs:ignore
                'description' => 'Return the contents of the file. If false, then the file path will be returned, this is used for large files to prevent out of memory errors.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
        ],
    ];

    /**
     * Config object.
     *
     * @var Config
     */
    private Config $settings;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Config();
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $location = $this->val('location', true);
        $filename = $this->val('filename', true);
        $nullable = $this->val('nullable', true);
        $getContents = $this->val('get_contents', true);

        if ($location == '_FILES') {
            $file = $this->getFileFiles($filename, $getContents, $nullable);
        } else {
            $file = $this->getFileRemote($location, $filename, $getContents, $nullable);
        }

        return $file;
    }

    /**
     * Get a file from $_FILES.
     *
     * @param string $filename The filename in the post.
     * @param boolean $getContents Return the contents or the filepath.
     * @param boolean $nullable Thrown an error if empty.
     *
     * @return DataContainer
     *
     * @throws ApiException Exception.
     */
    private function getFileFiles(string $filename, bool $getContents, bool $nullable): DataContainer
    {
        $this->validateFilesError($filename, $nullable);
        try {
            $dir = $this->settings->__get(['api', 'dir_tmp']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $extension = pathinfo($_FILES[$filename]['name'], PATHINFO_EXTENSION);
        try {
            $basename = bin2hex(random_bytes(8));
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), 5, $this->id, 417);
        }
        $basename = sprintf('%s.%0.8s', $basename, $extension);
        $dest = $dir . $basename;
        move_uploaded_file($_FILES[$filename]['tmp_name'], $dest);

        if ($getContents) {
            $fileContent = file_get_contents($dest);
            unlink($dest);
            return new DataContainer($fileContent, 'text');
        }

        return new DataContainer($dest, 'file');
    }

    /**
     * Get a file from a remote location.
     *
     * @param string $location Remote server URL and directory structure.
     * @param string $filename The file to fetch.
     * @param boolean $getContents Return file contents of path to locally stored file.
     * @param boolean $nullable Thrown an exception if the file contents are empty.
     *
     * @return DataContainer
     *
     * @throws ApiException Exception.
     */
    private function getFileRemote(
        string $location,
        string $filename,
        bool $getContents,
        bool $nullable
    ): DataContainer {
        try {
            $dir = $this->settings->__get(['api', 'base_path']) . $this->settings->__get(['api', 'dirFileStorage']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        $name = md5($filename . time());
        $extension = '.' . pathinfo($_FILES[$filename]['tmp_name'], PATHINFO_EXTENSION);
        $fileString = "$dir/$name.$extension";
        $client = new Client();

        if (!$getContents) {
            try {
                $client->get("$location/$filename", ['sink', $fileString]);
                return new DataContainer($fileString, 'file');
            } catch (Exception | GuzzleException $e) {
                throw new ApiException($e->getMessage(), 5, $this->id, 417);
            }
        } else {
            try {
                $client->get("$location/$filename", ['sink', $fileString]);
                $fileContent = file_get_contents($fileString);
                unlink($fileString);
                if (empty($fileContent) && !$nullable) {
                    throw new ApiException('file contents are empty', 5, $this->id, 417);
                }
                // todo Add a detectType function.
                return new DataContainer($fileContent);
            } catch (Exception $e) {
                throw new ApiException($e->getMessage(), 5, $this->id, 417);
            }
        }
    }

    /**
     * Validate file in $_FILES.
     *
     * @param string $filename The filename.
     * @param boolean $nullable Empty file allowed.
     *
     * @return void Valid file received.
     *
     * @throws ApiException Exception.
     *
     * @see https://www.php.net/manual/en/features.file-upload.php
     */
    private function validateFilesError(string $filename, bool $nullable): void
    {
        if (
            !isset($_FILES[$filename]['error']) || is_array($_FILES[$filename]['error'])
        ) {
            throw new ApiException('Undefined, multiple files or file corrupt', 5, $this->id, 417);
        }

        switch ($_FILES[$filename]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                if (!$nullable) {
                    throw new ApiException('No file sent', 5, $this->id, 417);
                }
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new ApiException('Exceeded filesize limit', 5, $this->id, 417);
            default:
                throw new ApiException('Unknown errors, please check the logs', 5, $this->id, 417);
        }
    }
}
