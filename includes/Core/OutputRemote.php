<?php

/**
 * Class OutputRemote.
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
 * Class OutputRemote
 *
 * Remote outputs base class.
 */
abstract class OutputRemote extends OutputEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Base remote',
        'machineName' => 'base_remote',
        'description' => 'Base class to output in the results of the resource to a remote server.',
        'menu' => '',
        'input' => [
            'filename' => [
                'description' => 'The output filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'transport' => [
                'description' => 'The Transport for uploading. example: s3, sftp, google_cloud, azure_blob.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'parameters' => [
                // phpcs:ignore
                'description' => 'Name/Value pairs for parameters required by the transport, e.g. username, password, etc.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
            ],
        ],
    ];

    /**
     * The output data.
     *
     * @var DataContainer The output data.
     */
    protected DataContainer $data;

    /**
     * @var mixed Transport class.
     */
    protected $transport;

    /**
     * Processor helper class.
     *
     * @var ProcessorHelper
     */
    protected ProcessorHelper $helper;

    /**
     * OutputRemote constructor.
     *
     * @param mixed|null $meta
     *   Output meta.
     * @param Request $request
     *   The full request object.
     * @param MonologWrapper $logger
     *   Logger.
     * @param mixed $data
     *   Output data.
     */
    public function __construct($meta, Request &$request, MonologWrapper $logger, $data)
    {
        parent::__construct($meta, $request, $logger, $data);
        $this->helper = new ProcessorHelper();
    }

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function process()
    {
        $data = parent::process();

        $filename = $this->val('filename', true);
        $transportName = $this->val('transport', true);
        $parameters = $this->val('parameters', true);

        $classString = $this->helper->getProcessorString($transportName, ['Output']);
        $transport = new $classString();

        try {
            $transport->uploadFile($parameters, $filename, $data->getData());
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
