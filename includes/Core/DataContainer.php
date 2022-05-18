<?php

/**
 * Class DataContainer.
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
 * Class DataContainer
 *
 * Provide s container for data to be passed between processors in a clean manner.
 */
class DataContainer extends Entity
{
    // phpcs:ignore
    use DetectTypeTrait,
        ConvertToArrayTrait,
        ConvertToBooleanTrait,
        ConvertToFileTrait,
        ConvertToFloatTrait,
        ConvertToHtmlTrait,
        ConvertToImageTrait,
        ConvertToIntegerTrait,
        ConvertToJsonTrait,
        ConvertToTextTrait,
        ConvertToUndefinedTrait,
        ConvertToXmlTrait;

    /**
     * All data types.
     *
     * @var array Data types.
     */
    private array $types = [
        'array',
        'boolean',
        'file',
        'float',
        'html',
        'image',
        'integer',
        'json',
        'text',
        'undefined',
        'xml',
    ];

    /**
     * Data type.
     *
     * @var string Default data type
     */
    private string $type = 'undefined';

    /**
     * Data.
     *
     * @var mixed Data
     */
    private $data;

    /**
     * DataContainer constructor.
     *
     * If the data type is specified, the data will automatically be cast to this type.
     *
     * @param mixed $data Data stored in the container.
     * @param string|null $type Data type.
     *
     * @throws ApiException
     */
    public function __construct($data, string $type = null)
    {
        if (empty($type)) {
            $type = $this->detectType($data);
        } else {
            $method = $this->getCastMethod($data, $type);
            $data = $this->$method($data);
        }
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Fetch all possible data types.
     *
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Get the data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the data type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the data.
     *
     * Data type is automatically detected and set.
     *
     * @param mixed $data
     *   Data.
     */
    public function setData($data)
    {
        $this->data = $data;
        $this->type = $this->detectType($data);
    }

    /**
     * Set the data type.
     *
     * This will also cast the data to the new type.
     *
     * @param string $type
     *   Data type.
     *
     * @throws ApiException
     */
    public function setType(string $type)
    {
        $method = $this->getCastMethod($this->data, $type);
        $this->data = $this->$method($this->data);
        $this->type = $type;
    }

    /**
     * Calculate the required cast method.
     *
     * @param $data
     * @param string $type
     *
     * @return string
     *
     * @throws ApiException
     */
    protected function getCastMethod($data, string $type): string
    {
        if (!in_array($type, $this->types)) {
            $message = "invalid datatype, cannot set DataContainer to: $type";
            throw new ApiException($message, 0, -1, 500);
        }

        $detectedType = $this->detectType($data);
        $method = 'from' . ucfirst(strtolower($detectedType)) . 'To' . ucfirst(strtolower($type));

        if (!method_exists(__CLASS__, $method)) {
            $message = "could not find method to cast: $method";
            throw new ApiException($message, 0, -1, 500);
        }

        return $method;
    }
}
