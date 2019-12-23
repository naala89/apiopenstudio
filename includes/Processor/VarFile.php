<?php

/**
 * Post variable
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use GuzzleHttp\Client;

class VarFile extends Core\ProcessorEntity
{
    /**
     * @var Core\Config
     */
    private $settings;

    /**
     * {@inheritDoc}
     */
    protected $details = [
    'name' => 'Var (File)',
        'machineName' => 'var_file',
        'description' => 'Return the contents of a file or the file path.',
        'menu' => 'Primitive',
        'input' => [
            'location' => [
                'description' => 'The location of the file. This can be a remote server address or "_FILES" to receive from a POST.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'filename' => [
                'description' => 'The name of the file. This is the name of the file field in the post or the remote server filename.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string'],
                'limitValues' => [],
                'default' => '',
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the file does not exist.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'get_contents' => [
                'description' => 'Return the contents of the file. If false, then the file path will be returned, this is used for large files to prevent out of memory errors.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db) {
        parent::__construct($meta, $request, $db);
        $this->settings = new Core\Config();
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

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
     * @param string $filename
     *   The filename in the post.
     * @param bool $getContents
     *   Return the contents or the filepath.
     * @param $nullable
     *   Thrown an error if empty.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException
     */
    private function getFileFiles($filename, $getContents, $nullable)
    {
        $this->validateFilesError($filename, $nullable);
        if ($getContents) {
            $dir = $this->settings->__get(['api', 'base_path']) . $this->settings->__get(['api', 'dirTmp']);
            $extension = pathinfo($_FILES[$filename]['name'], PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8));
            $basename = sprintf('%s.%0.8s', $basename, $extension);
            $dest = $dir . $basename;
            move_uploaded_file($_FILES[$filename]['tmp_name'], $dest);
            $fileContent = file_get_contents($dest);
            unlink($dest);
            return new Core\DataContainer($fileContent, $this->detectType($fileContent));
        } else {
            $dir = $this->settings->__get(['api', 'base_path']) . $this->settings->__get(['api', 'dirTmp']);
            $extension = pathinfo($_FILES[$filename]['name'], PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8));
            $basename = sprintf('%s.%0.8s', $basename, $extension);
            $dest = $dir . $basename;
            move_uploaded_file($_FILES[$filename]['tmp_name'], $dest);
            return new Core\DataContainer($dest, 'file');
        }
    }

    /**
     * Get a file from a remote location.
     * @param string $location
     *   Remote server URL and directory structure.
     * @param string $filename
     *   The file to fetch.
     * @param $getContents
     *   Return file contents of path to locally stored file.
     * @param $nullable
     *   Thrown an exceoption if the file contents are empty.
     *
     * @return Core\DataContainer
     *
     * @throws Core\ApiException
     */
    private function getFileRemote($location, $filename, $getContents, $nullable) {
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
                return new Core\DataContainer($fileContent, $this->detectType($fileContent));
            } catch (Exception $e) {
                throw new Core\ApiException($e->getMessage(), 5, $this->id, 417);
            }
        }
    }

    /**
     * Validate file in $_FILES.
     *
     * @param $filename
     *   The filename
     * @param $nullable
     *   Empty file allowed.
     *
     * @return bool
     *   Valid file received.
     *
     * @throws Core\ApiException
     *
     * @see https://www.php.net/manual/en/features.file-upload.php
     */
    private function validateFilesError($filename, $nullable) {
        if (
            !isset($_FILES[$filename]['error']) || is_array($_FILES[$filename]['error'])) {
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
        return true;
    }
}
