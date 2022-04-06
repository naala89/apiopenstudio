<?php

/**
 * Class Output.
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

use ApiOpenStudio\Core;
use ApiOpenStudio\Config;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Output
 *
 * Outputs base class.
 */
abstract class Output extends Core\ProcessorEntity
{
    /**
     * Config object.
     *
     * @var Core\Config
     */
    protected Core\Config $settings;

    /**
     * The output data.
     *
     * @var Core\DataContainer The output data.
     */
    protected Core\DataContainer $data;

    /**
     * The output metadata.
     *
     * @var mixed the output meta.
     */
    protected $meta;

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
     * @param Core\MonologWrapper $logger
     *   Logger.
     * @param mixed|null $meta
     *   Output meta.
     */
    public function __construct($data, int $status, Core\MonologWrapper $logger, $meta = null)
    {
        $this->settings = new Core\Config();
        $this->data = $data;
        $this->status = $status;
        $this->logger = $logger;
        $this->meta = $meta;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed Result of the processor.
     *
     * @throws Core\ApiException Throw an exception if unable to precess the output.
     */
    public function process()
    {
        $this->logger->debug('api', 'Output: ' . $this->details()['machineName']);
        $this->setStatus();

        if (empty($this->meta)) {
            $this->setHeader();
        } else {
            if (empty($this->meta->destination)) {
                $this->logger->alert('api', 'no destinations defined for output');
                throw new Core\ApiException('no destinations defined for output', 1, $this->id);
            }
            $method = $this->val('method');
            foreach ($this->meta->destination as $destination) {
                $url = $this->val($destination);
                $data = ['data' => $data];
                $curlOpts = [];
                if ($method == 'post') {
                    $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($data);
                } elseif ($method == 'get') {
                    $url .= http_build_query($data, '?', '&');
                }

                $curl = new Core\Curl($this->logger);
                $result = $curl->{$method}($url, $curlOpts);
                if ($result === false) {
                    $message = 'could not get response from remote server: ' . $curl->errorMsg;
                    $this->logger->alert('api', $message);
                    throw new Core\ApiException($message, 5, $this->id, $curl->httpStatus);
                }
                if ($curl->httpStatus != 200) {
                    $this->logger->alert('api', 'Failed to send data: ' . json_encode($result));
                    throw new Core\ApiException(json_encode($result), 5, $this->id, $curl->httpStatus);
                }
            }
        }

        $this->castData();

        return $this->data;
    }

    /**
     * Set the Content-Type header.
     *
     * @return void
     */
    public function setHeader()
    {
        header($this->header);
    }

    /**
     * Set the response status code.
     *
     * @return void
     */
    public function setStatus()
    {
        http_response_code($this->status);
    }

    /**
     * Cast the data to the required Type.
     *
     * @throws Core\ApiException Throw an exception if unable to convert the data.
     */
    abstract protected function castData();
}
