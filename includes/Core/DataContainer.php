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
    use DetectTypeTrait, ConvertToBooleanTrait, ConvertToIntegerTrait, ConvertToFloatTrait, ConvertToTextTrait, ConvertToArrayTrait, ConvertToJsonTrait, ConvertToXmlTrait, ConvertToHtmlTrait, ConvertToImageTrait {
        ConvertToJsonTrait::xml2json insteadof ConvertToArrayTrait;
    }

    /**
     * All data types.
     *
     * @var array Data types.
     */
    private array $types = [
        'boolean',
        'integer',
        'float',
        'text',
        'array',
        'json',
        'xml',
        'html',
        'image',
        'file',
        'empty',
    ];

    /**
     * Data type.
     *
     * @var string Default data type
     */
    private string $type = 'empty';

    /**
     * Data.
     *
     * @var mixed Data
     */
    private $data;

    /**
     * DataContainer constructor.
     *
     * @param mixed $data Data stored in the container.
     * @param string|null $dataType Data type.
     *
     * @throws ApiException
     */
    public function __construct($data, string $dataType = null)
    {
        if (empty($dataType)) {
            $this->setData($data);
        } else {
            if (!in_array($dataType, $this->types)) {
                throw new ApiException("invalid datatype, cannot set DataContainer to: $dataType");
            }
            $detectedType = $this->detectType($data);
            $method = 'from' . ucfirst(strtolower($detectedType)) . 'To' . ucfirst(strtolower($dataType));
            if (!method_exists(__CLASS__, $method)) {
                throw new ApiException("could not find method to cast: $method");
            }
            $data = $this->$method($data);
            $this->data = $data;
            $this->type = $dataType;
        }
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
     * Set the data.
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
     * Get the data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
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
        $detectedType = $this->detectType($this->data);
        if (!in_array($type, $this->types)) {
            throw new ApiException("invalid datatype, cannot set DataContainer to: $type");
        }
        $method = 'from' . ucfirst(strtolower($detectedType)) . 'To' . ucfirst(strtolower($type));
        if (!method_exists(__CLASS__, $method)) {
            throw new ApiException("could not find method to cast: $method");
        }
        $data = $this->$method($this->data);
        $this->data = $data;
        $this->type = $type;
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
}
