<?php

/**
 * Store the Drupal login token
 *
 * Takes the input of a login event and stores the token if successful
 *
 *
 * METADATA
 * {
 *  "type":"tokenStoreDrupal",
 *  "meta": {
 *    "id":<integer>,
 *    "source":<processor>
 *  }
 * }
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class LoginStoreDrupal extends Core\ProcessorEntity
{
    private $user;
    private $defaultEntity = 'drupal';
  /**
   * {@inheritDoc}
   */
    protected $details = [
    'name' => 'Login Store Drupal',
    'machineName' => 'loginStoreDrupal',
    'description' => 'Login the user Stores the access details from a users login to a remote drupal site for future \
    use.',
    'menu' => 'Process',
    'input' => [
      'source' => [
        'description' => 'The results of a login attempt to the remote site. i.e. Processor InputUrl.',
        'cardinality' => [1, 1],
        'literalAllowed' => false,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'externalEntity' => [
        'description' => 'The name of the external entity this user is tied to (use custom names if you access more \
        than one drupal site).',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => 'drupal',
      ],
    ],
    ];

  /**
   * {@inheritDoc}
   */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $source = $this->val('source');
        $source = json_decode($source);
        if (empty($source->token) || empty($source->user) || empty($source->user->uid)) {
            throw new Core\ApiException('login failed, no token received', 4, $this->id, 419);
        }
        $externalEntity = !empty($this->meta->externalEntity) ? $this->val('externalEntity') : $this->defaultEntity;
        $externalId = $source->user->uid;
        $appid = $this->request->appId;
        $db = $this->getDb();

        $userMapper = new Db\ExternalUserMapper($db);
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

        return $source;
    }
}
