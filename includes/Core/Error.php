<?php

/**
 * Class Error.
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
 * Class Error
 *
 * Handle errors in a standard way, so that they are returned through the API.
 */
class Error
{
    /**
     * Processor ID.
     *
     * @var string|integer Processor ID.
     */
    private $id;

    /**
     * Internal error code.
     *
     * @var integer Error code.
     */
    private int $code;

    /**
     * Error message.
     *
     * @var string Error message.
     */
    private string $message;

    /**
     * Error constructor.
     *
     * @param integer $code Error code.
     * @param string|integer $id Processor ID.
     * @param string $message Error message.
     */
    public function __construct(int $code, $id, string $message)
    {
        $this->code = $code;
        $this->message = $message;
        $this->id = $id;
    }

    /**
     * Construct and return the output error message
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException
     */
    public function process(): DataContainer
    {
        return new DataContainer(
            [
                'result' => 'error',
                'data' => [
                    'id' => !empty($this->id) ? $this->id : -1,
                    'code' => $this->code,
                    'message' => (!empty($this->message) ? (ucfirst($this->message) . '.') : 'Unidentified error.'),
                ],
            ],
            'array'
        );
    }
}
