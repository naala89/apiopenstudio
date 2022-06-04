<?php

/**
 * Class Image.
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
use ApiOpenStudio\Core\OutputResponse;
use RuntimeException;
use Selective\ImageType\ImageTypeDetector;
use Selective\ImageType\ImageTypeDetectorException;
use Selective\ImageType\Provider\RasterProvider;
use SplTempFileObject;

/**
 * Class Image
 *
 * Outputs the results as an image.
 */
class Image extends OutputResponse
{
    use ConvertToImageTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Image',
        'machineName' => 'image',
        // phpcs:ignore
        'description' => 'Output the results of the resource as an image in the response. This does not need to be added to the resource - it will be automatically detected by the Accept header.',
        'menu' => 'Output',
        'input' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * We currently do not define image type, so image if subtype wildcard is returned.
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type: image/*';

    /**
     * Cast the data to image.
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
            $imageType = $this->detectImageType($data);
            $mimeSubType = $this->request->getOutFormat()['mimeSubType'];
            if ($mimeSubType != '*' && $mimeSubType != $imageType) {
                throw new ApiException(
                    "Invalid image type requested ($mimeSubType), actual: $imageType",
                    3,
                    $this->id,
                    400
                );
            }
            if (substr($data, 0, 11) != 'data:image/') {
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
