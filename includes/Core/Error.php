<?php

namespace Gaterdata\Core;

class Error
{
    private $id;
    private $code;
    private $message;

    /**
     * @param $code
     * @param $id
     * @param $message
     */
    public function __construct($code, $id, $message)
    {
        $this->code = $code;
        $this->message = $message;
        $this->id = $id;
    }

    /**
     * Construct and return the output error message
     *
     * @return array
     *
     * @throws ApiException
     */
    public function process()
    {
        return [
            'error' => [
                'id' => !empty($this->id) ? $this->id : -1,
                'code' => $this->code,
                'message' => (!empty($this->message) ? (ucfirst($this->message) . '.') : 'Unidentified error.'),
            ],
        ];
    }
}
