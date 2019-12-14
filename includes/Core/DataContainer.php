<?php

namespace Gaterdata\Core;

class DataContainer extends Entity
{
    /**
     * @var array Data types.
     */
    private $types = [
        'boolean',
        'integer',
        'float',
        'text',
        'array',
        'json',
        'xml',
        'image',
        'file',
    ];

    /**
     * @var string Data type
     */
    protected $type = '';

    /**
     * @var mixed Data
     */
    protected $data;

    /**
     * DataContainer constructor.
     *
     * @param mixed $data
     *   Data stored in the container.
     * @param string $dataType
     *   Data type.
     *
     * @throws ApiException
     */
    public function __construct($data, $dataType)
    {
        $this->setData($data);
        $this->setType($dataType);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if ($this->type == 'boolean') {
            return filter_var($this->data, FILTER_VALIDATE_BOOLEAN);
        }
        if ($this->type == 'integer') {
            return filter_var($this->data, FILTER_VALIDATE_INT);
        }
        if ($this->type == 'float') {
            return filter_var($this->data, FILTER_VALIDATE_FLOAT);
        }
        return $this->data;
    }

    /**
     * @param $val
     */
    public function setData($val)
    {
        $this->data = $val;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $val
     * @throws ApiException
     */
    public function setType($val)
    {
        if (!in_array($val, $this->types)) {
            throw new ApiException("trying to to set an invalid type: $val");
        }
        $this->type = $val;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
