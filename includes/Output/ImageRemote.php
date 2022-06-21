<?php

/**
 * Class ImageRemote.
 *
 * @package    ApiOpenStudio\Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Output;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\ConvertToImageTrait;
use ApiOpenStudio\Core\DetectTypeTrait;
use ApiOpenStudio\Core\OutputRemote;
use RuntimeException;
use Selective\ImageType\ImageTypeDetector;
use Selective\ImageType\ImageTypeDetectorException;
use Selective\ImageType\Provider\RasterProvider;
use SplTempFileObject;

/**
 * Class ImageRemote
 *
 * Outputs the results as image to a remote location.
 */
class ImageRemote extends OutputRemote
{
    use ConvertToImageTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Image remote',
        'machineName' => 'image_remote',
        'description' => 'Output in the results of the resource in image format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'apiopenstudio.jpeg',
            ],
            'transport' => [
                'description' => 'The Transport for uploading. example: ApiOpenStudio\Plugins\TransportS3.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'parameters' => [
                // phpcs:ignore
                'description' => 'Name/Value pairs for parameters required by the uploader, e.g. username, password, etc.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
            ],
        ],
    ];

    /**
     * Cast the data to JSON.
     *
     * @throws ApiException
     *   Throw an exception if unable to convert the data.
     */
    protected function castData(): void
    {
        $currentType = $this->data->getType();
        $method = 'from' . ucfirst(strtolower($currentType)) . 'ToImage';

        try {
            $data = $this->$method($this->data->getData());
            if (substr($data, 0, 11) != 'data:image/') {
                $imageType = $this->detectImageType($data);
                $data = "data:image/$imageType;base64,$data";
            }
            $this->data->setData($data);
            $this->data->setType('text');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }

    /**
     * Detect the image-type of an image.
     *
     * @param string $base64
     *
     * @return string
     *
     * @throws ApiException
     */
    protected function detectImageType(string $base64): string
    {
        $raw = base64_decode($base64);
        try {
            $image = new SplTempFileObject();
            $image->fwrite($raw);
            $detector = new ImageTypeDetector();
            $detector->addProvider(new RasterProvider());
            $result = $detector->getImageTypeFromFile($image)->getFormat();
        } catch (RuntimeException | ImageTypeDetectorException $e) {
            throw new ApiException($e->getMessage(), 6, $this->id, 500);
        }

        return $result;
    }
}
