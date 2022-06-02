<?php

/**
 * Class ProcessorEntity.
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

use ADOConnection;

/**
 * Class ProcessorEntity
 *
 * Base class for all entities.
 */
abstract class ProcessorEntity extends Entity
{
    /**
     * All the request details.
     *
     * @var Request Request.
     */
    protected Request $request;

    /**
     * DB connections.
     *
     * @var ADOConnection $dbLayer
     */
    protected ADOConnection $db;

    /**
     * Constructor. Store processor metadata and request data in object.
     *
     * @param $meta
     *   Metadata for the processor.
     * @param Request $request
     *   The full request object.
     * @param ADOConnection $db
     *   The DB connection object.
     * @param MonologWrapper|null $logger
     *   The logger.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger = null)
    {
        parent::__construct($meta, $logger);
        $this->request = $request;
        $this->db = $db;
    }

    /**
     * Generate the params array for the sql search.
     *
     * @param string|null $keyword Search keyword.
     * @param array|null $keywordCols Columns to search for the keyword.
     * @param string|null $orderBy Order by column.
     * @param string|null $direction Order direction.
     *
     * @return array
     */
    protected function generateParams(
        ?string $keyword,
        ?array $keywordCols,
        ?string $orderBy,
        ?string $direction
    ): array {
        $params = [];
        if (!empty($keyword) && !empty($keywordCols)) {
            foreach ($keywordCols as $keywordCol) {
                $params['filter'][] = ['keyword' => "%$keyword%", 'column' => $keywordCol];
            }
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }
        return $params;
    }
}
