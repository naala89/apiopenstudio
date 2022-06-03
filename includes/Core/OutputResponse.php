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
     * OutputResponse constructor.
     *
     * @param mixed|null $meta
     *   Output meta.
     * @param Request $request
     *   The full request object.
     * @param MonologWrapper $logger
     *   Logger.
     * @param mixed $data
     *   Output data.
     * @param integer $status
     *   HTTP output status.
     */
    public function __construct($meta, Request &$request, MonologWrapper $logger, $data, int $status)
    {
        parent::__construct($meta,$request, $logger, $data);
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
        $this->setHeader();
        $this->setResponseCode();
        return $this->data;
    }

    /**
     * Set the response headers.
     *
     * @return void
     */
    public function setHeader()
    {
        header($this->header);
    }

    /**
     * Set the HTML response code.
     *
     * @return void
     */
    public function setResponseCode()
    {
        http_response_code($this->status);
    }
}
