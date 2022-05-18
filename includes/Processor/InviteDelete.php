<?php

/**
 * Class InviteDelete.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db;

/**
 * Class InviteDelete
 *
 * Processor class delete an invite.
 */
class InviteDelete extends Core\ProcessorEntity
{
    /**
     * Invite mapper class.
     *
     * @var Db\InviteMapper
     */
    private Db\InviteMapper $inviteMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Invite delete',
        'machineName' => 'invite_delete',
        'description' => 'Delete an invite by ID.',
        'menu' => 'Admin',
        'input' => [
            'iid' => [
                'description' => 'Invite ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
    ];

    /**
     * InviteDelete constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->inviteMapper = new Db\InviteMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();
        $iid = $this->val('iid', true);

        try {
            $invite = $this->inviteMapper->findByIid($iid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($invite->getIid())) {
            throw new Core\ApiException('Invalid iid: ' . $iid);
        }

        try {
            $this->inviteMapper->delete($invite);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new Core\DataContainer('Deleted user invite for ' . $invite->getEmail(), 'text');
    }
}
