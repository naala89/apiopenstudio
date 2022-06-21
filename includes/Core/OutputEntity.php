<?php

/**
 * Class OutputEntity.
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
 * Class OutputEntity
 *
 * Outputs base class.
 */
abstract class OutputEntity extends Entity
{
    /**
     * The output data.
     *
     * @var DataContainer The output data.
     */
    protected DataContainer $data;

    /**
     * OutputEntity constructor.
     *
     * @param $meta
     *   Metadata for the processor.
     * @param Request $request
     *   The full request object.
     * @param MonologWrapper $logger
     *   Logger.
     * @param mixed $data
     *   HTTP output data.
     */
    public function __construct($meta, Request &$request, MonologWrapper $logger, $data)
    {
        parent::__construct($meta, $request, $logger);
        $this->data = $data;
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
        return $this->castData();
    }

    /**
     * Cast the data to the required Type.
     *
     * @throws ApiException Throw an exception if unable to convert the data.
     */
    abstract protected function castData();
}
