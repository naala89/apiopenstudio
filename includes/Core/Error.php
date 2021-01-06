<?php

/**
 * Class Error.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
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
    private $code;

    /**
     * Error message.
     *
     * @var string Error message.
     */
    private $message;

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
     */
    public function process()
    {
        return new DataContainer(
            [
                'error' => [
                    'id' => !empty($this->id) ? $this->id : -1,
                    'code' => $this->code,
                    'message' => (!empty($this->message) ? (ucfirst($this->message) . '.') : 'Unidentified error.'),
                ],
            ],
            'array'
        );
    }
}
