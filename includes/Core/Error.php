<?php
/**
 * Class Error.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

/**
 * Class Error
 *
 * Handle errors in a srandard way, so that they are returned through the API.
 */
class Error
{
    /**
     * @var string|integer Processor ID.
     */
    private $id;

    /**
     * @var integer Error code.
     */
    private $code;

    /**
     * @var string Error message.
     */
    private $message;

    /**
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
     * @return Core\DataContainer Result of the processor.
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
