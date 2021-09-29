<?php

/**
 * Class VarFile.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core;
use Exception;
use GuzzleHttp\Client;

/**
 * Class VarFile
 *
 * Processor class to hold a file variable.
 */
class VarFile extends Core\ProcessorEntity
{
    /**
     * Config object.
     *
     * @var Core\Config
     */
    private Core\Config $settings;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var (File)',
        'machineName' => 'var_file',
        'description' => 'Return the contents of a file or the file path.',
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
                'default' => '',
            ],
            'filename' => [
                // phpcs:ignore
                'description' => 'The name of the file. This is the name of the file field in the post or the remote server filename.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
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
     * VarFile constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\StreamLogger $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\StreamLogger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->settings = new Core\Config();
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
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
     * @return Core\DataContainer
     *
     * @throws Core\ApiException Exception.
     */
    private function getFileFiles(string $filename, bool $getContents, bool $nullable): Core\DataContainer
    {
        $this->validateFilesError($filename, $nullable);
        $dir = $this->settings->__get(['api', 'dir_tmp']);
        $extension = pathinfo($_FILES[$filename]['name'], PATHINFO_EXTENSION);
        try {
            $basename = bin2hex(random_bytes(8));
        } catch (Exception $e) {
            throw new Core\ApiException($e->getMessage(), 5, $this->id, 417);
        }
        $basename = sprintf('%s.%0.8s', $basename, $extension);
        $dest = $dir . $basename;
        move_uploaded_file($_FILES[$filename]['tmp_name'], $dest);

        if ($getContents) {
            $fileContent = file_get_contents($dest);
            unlink($dest);
            return new Core\DataContainer($fileContent, 'text');
        }

        return new Core\DataContainer($dest, 'file');
    }

    /**
     * Get a file from a remote location.
     *
     * @param string $location Remote server URL and directory structure.
     * @param string $filename The file to fetch.
     * @param boolean $getContents Return file contents of path to locally stored file.
     * @param boolean $nullable Thrown an exceoption if the file contents are empty.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException Exception.
     */
    private function getFileRemote(
        string $location,
        string $filename,
        bool $getContents,
        bool $nullable
    ): Core\DataContainer {
        $dir = $this->settings->__get(['api', 'base_path']) . $this->settings->__get(['api', 'dirFileStorage']);
        $name = md5($filename . time());
        $extension = '.' . pathinfo($_FILES[$filename]['tmp_name'], PATHINFO_EXTENSION);
        $fileString = "$dir/$name.$extension";
        $client = new Client();

        if (!$getContents) {
            try {
                $client->get("$location/$filename", ['sink', $fileString]);
                return new Core\DataContainer($fileString, 'file');
            } catch (Exception $e) {
                throw new Core\ApiException($e->getMessage(), 5, $this->id, 417);
            }
        } else {
            try {
                $client->get("$location/$filename", ['sink', $fileString]);
                $fileContent = file_get_contents($fileString);
                unlink($fileString);
                if (empty($fileContent) && !$nullable) {
                    throw new Core\ApiException('file contents are empty', 5, $this->id, 417);
                }
                // todo Add a detectType function.
                return new Core\DataContainer($fileContent, $this->detectType($fileContent));
            } catch (Exception $e) {
                throw new Core\ApiException($e->getMessage(), 5, $this->id, 417);
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
     * @throws Core\ApiException Exception.
     *
     * @see https://www.php.net/manual/en/features.file-upload.php
     */
    private function validateFilesError(string $filename, bool $nullable): void
    {
        if (
            !isset($_FILES[$filename]['error']) || is_array($_FILES[$filename]['error'])
        ) {
            throw new Core\ApiException('Undefined, multiple files or file corrupt', 5, $this->id, 417);
        }

        switch ($_FILES[$filename]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                if (!$nullable) {
                    throw new Core\ApiException('No file sent', 5, $this->id, 417);
                }
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Core\ApiException('Exceeded filesize limit', 5, $this->id, 417);
                break;
            default:
                throw new Core\ApiException('Unknown errors, please check the logs', 5, $this->id, 417);
                break;
        }
    }
}
