<?php

/**
 * Class LoginStoreDrupal.
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

use ApiOpenStudio\Core;
use ApiOpenStudio\Db;

/**
 * Class LoginStoreDrupal
 *
 * Processor class te login with auth from drupal.
 */
class LoginStoreDrupal extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Login Store Drupal',
        'machineName' => 'loginStoreDrupal',
        // phpcs:ignore
        'description' => 'Login the user Stores the access details from a users login to a remote drupal site for future use.',
        'menu' => 'Endpoint',
        'input' => [
            'source' => [
                'description' => 'The results of a login attempt to the remote site. i.e. Processor InputUrl.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'externalEntity' => [
                // phpcs:ignore
                'description' => 'The name of the external entity this user is tied to (use custom names if you access more than one drupal site).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'drupal',
            ],
        ],
    ];

    /**
     * Default external entity type.
     *
     * @var string
     */
    private string $defaultEntity = 'drupal';

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

        $source = $this->val('source');
        $source = json_decode($source);
        if (empty($source->token) || empty($source->user) || empty($source->user->uid)) {
            throw new Core\ApiException('login failed, no token received', 4, $this->id, 419);
        }
        $externalEntity = !empty($this->meta['externalEntity']) ? $this->val('externalEntity') : $this->defaultEntity;
        $externalId = $source->user->uid;
        $appid = $this->request->getAppId();

        $userMapper = new Db\ExternalUserMapper($this->db, $this->logger);
        $user = $userMapper->findByAppIdEntityExternalId($appid, $externalEntity, $externalId);
        if ($user->getId() == null) {
            $user->setAppId($appid);
            $user->setExternalEntity($externalEntity);
            $user->setExternalId($externalId);
        }
        $user->setDataField1($source->token);
        $user->setDataField2($source->session_name);
        $user->setDataField3($source->sessid);

        $userMapper->save($user);

        return new Core\DataContainer($source, 'text');
    }
}
