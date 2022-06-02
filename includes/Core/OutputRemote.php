<?php

/**
 * Class OutputRemote.
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

/**
 * Class OutputRemote
 *
 * Remote outputs base class.
 */
abstract class OutputRemote extends OutputEntity
{
    /**
     * The output data.
     *
     * @var DataContainer The output data.
     */
    protected DataContainer $data;

    /**
     * @var Ftp SFTP client.
     */
    protected Ftp $sftp;

    /**
     * @var S3 S3 client.
     */
    protected S3 $s3;

    /**
     * @var AzureBlob Azure Blob Storage client.
     */
    protected AzureBlob $azureBlob;

    /**
     * @var Ftp $ftp FTP client.
     */
    protected Ftp $ftp;

    /**
     * @var GoogleCloud $googleCloud Google Cloud client.
     */
    private GoogleCloud $googleCloud;

    /**
     * Output constructor.
     *
     * @param mixed $data
     *   Output data.
     * @param MonologWrapper $logger
     *   Logger.
     * @param mixed|null $meta
     *   Output meta.
     */
    public function __construct($data, MonologWrapper $logger, $meta = null)
    {
        parent::__construct($data, $logger, $meta);
        $this->sftp = new Ftp();
        $this->s3 = new S3();
        $this->azureBlob = new AzureBlob();
        $this->googleCloud = new GoogleCloud();
        $this->ftp = new Ftp();
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function process()
    {
        $filename = $this->val('filename', true);
        $method = $this->val('method', true);
        $parameters = $this->val('parameters', true);

        try {
            switch ($method) {
                case 'sftp':
                    $this->sftp->uploadFile($parameters, $filename, $this->data->getData());
                    break;
                case 's3':
                    $this->s3->uploadFile($parameters, $filename, $this->data->getData());
                    break;
                case 'azure_blob':
                    $this->azureBlob->uploadFile($parameters, $filename, $this->data->getData());
                    break;
                case 'google_loud':
                    $this->googleCloud->uploadFile($parameters, $filename, $this->data->getData());
                    break;
                case 'ftp':
                    $this->ftp->uploadFile($parameters, $filename, $this->data->getData());
                    break;
                default:
                    throw new ApiException('Unsupported upload method', 6, $this->id, 400);
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
