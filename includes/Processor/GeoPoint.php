<?php
/**
 * Class GeoPoint.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class GeoPoint
 *
 * Processor class to define a geo-point.
 */
class GeoPoint extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Geo-Point',
        'machineName' => 'geoPoint',
        // phpcs:ignore
        'description' => 'GeoPoint function is a container object that holds geographic point coordinates and attributes. Each entry of a coordinate pair and associated attributes, if any, represent a discrete element in the geopoint vector.',
        'menu' => 'Primitive',
        'input' => [
            'lat' => [
                'description' => 'The latitude of the geo point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['float', 'integer'],
                'limitValues' => [],
                'default' => 0
            ],
            'lon' => [
                'description' => 'The longitude of the geo point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
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
