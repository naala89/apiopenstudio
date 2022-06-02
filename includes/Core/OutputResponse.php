<?php

/**
 * Class OutputResponse.
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
 * Class OutputResponse
 *
 * Response outputs base class.
 */
abstract class OutputResponse extends OutputEntity
{
    /**
     * The output data.
     *
     * @var DataContainer The output data.
     */
    protected DataContainer $data;

    /**
     * Content-type header value.
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = '';

    /**
     * The HTTP output status.
     *
     * @var mixed The output status.
     */
    public $status;

    /**
     * Output constructor.
     *
     * @param mixed $data
     *   Output data.
     * @param integer $status
     *   HTTP output status.
     * @param MonologWrapper $logger
     *   Logger.
     * @param mixed|null $meta
     *   Output meta.
     */
    public function __construct($data, int $status, MonologWrapper $logger, $meta = null)
    {
        parent::__construct($data, $logger, $meta);
        $this->data = $data;
        $this->status = $status;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed Result of the processor.
     *
     * @throws ApiException Throw an exception if unable to precess the output.
     */
    public function process()
    {
        parent::process();
        http_response_code($this->status);
        header($this->header);
        return $this->data;
    }
}
