<?php

/**
 * Class GeoPoint.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;

/**
 * Class GeoPoint
 *
 * Processor class to define a geo-point.
 */
class GeoPoint extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Geo-Point',
        'machineName' => 'geoPoint',
        // phpcs:ignore
        'description' => 'GeoPoint processor is a container object that holds geographic point coordinates and attributes. Each entry of a coordinate pair and associated attributes, if any, represent a discrete element in the geopoint vector.',
        'menu' => 'Primitive',
        'input' => [
            'lat' => [
                'description' => 'The latitude of the geo point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['float', 'integer'],
                'limitValues' => [],
                'default' => 0
            ],
            'lon' => [
                'description' => 'The longitude of the geo point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['float', 'integer'],
                'limitValues' => [],
                'default' => 0
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $lat = $this->val('lat', true);
        $lon = $this->val('lon', true);

        return new Core\DataContainer(['lat' => $lat, 'lon' => $lon], 'array');
    }
}
