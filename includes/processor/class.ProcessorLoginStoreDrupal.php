<?php

/**
 * Store the Drupal login token
 *
 * Takes the input of a login event and stores the token if successful
 *
 * METADATA
 * {
 *  "type":"tokenStoreDrupal",
 *  "meta": {
 *    "id":<integer>,
 *    "source":<processor>
 *    "staleTime":<string> (defaults to "+1 day")
 *  }
 * }
 *
 * @TODO: Separate user DB stuff into new User class
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorLoginStoreDrupal extends Processor
{
  public function process()
  {
    Debug::variable($this->meta, 'ProcessorLoginStoreDrupal', 4);

    $source = $this->getVar($this->meta->source);
    if ($this->status != 200) {
      return $source;
    }
    $source = json_decode($source);
    if (empty($source->token) || empty($source->user) || empty($source->user->uid)) {
      $this->status = 419;
      return new Error(3, $this->id, 'login failed, no token received');
    }

    $token = $this->request->db->escape($source->token);
    $sessionName = $this->request->db->escape($source->session_name);
    $sessionId = $this->request->db->escape($source->sessid);
    $externalId = $this->request->db->escape($source->user->uid);
    $roles = $source->user->roles;
    $staleTime = !empty($source->staleTime) ? $this->request->db->escape($source->staleTime) : Config::$tokenLife;
    $client = $this->request->db->escape($this->request->client);

    $result = $this->_deleteUser($client, $externalId);
    if (!$result) {
      $this->status = 400;
      return new Error(2, $this->id, 'an error occurred while deleting a user login');
    }
    $result = $this->_insertUser($client, $externalId, $roles, $token, $sessionName, $sessionId, $staleTime);
    if (!$result) {
      $this->status = 400;
      return new Error(2, $this->id, 'DB error occurred storing login');
    }

    return $source;
  }

  /**
   * Delete all rows of user by client ID and external ID.
   *
   * @param $client
   * @param $externalId
   * @return mixed
   */
  private function _deleteUser($client, $externalId)
  {
    $result = $this->_dbGetUserIds($client, $externalId);

    while($row = mysqli_fetch_object($result)) {
      $uid = $row->uid;

      $deleteUserRoles = $this->_dbDeleteUserRoles($uid);
      if (!$deleteUserRoles) {
        $this->status = 400;
        return new Error(2, $this->id, 'an error occurred while deleting a users roles');
      }

      $deleteUser = $this->_dbDeleteUser($uid);
      if (!$deleteUser) {
        $this->status = 400;
        return new Error(2, $this->id, 'an error occurred while deleting a users login');
      }
    }

    return $result;
  }

  /**
   * Insert a user and their roles.
   *
   * @param $client
   * @param $externalId
   * @param $roles
   * @param $token
   * @param $sessionName
   * @param $sessionId
   * @param $staleTime
   * @return mixed
   */
  private function _insertUser($client, $externalId, $roles, $token, $sessionName, $sessionId, $staleTime)
  {
    //insert user
    $result = $this->_dbInsertUser($client, $externalId, $token, $sessionName, $sessionId, $staleTime);
    $uid = $this->request->db->insertId();

    //insert the client's roles
    Debug::variable($roles);
    foreach ($roles as $roleId => $role) {
      $dbRole = $this->_dbGetRole($client, $role);
      //ensure role exists for this client
      if ($dbRole->num_rows < 1) {
        $this->_dbInsertRole($client, $role);
        $rid = $this->request->db->insertId();
      } else {
        $row = mysqli_fetch_object($dbRole);
        $rid = $row->rid;
      }
      $result = $this->_dbInsertUserRole($uid, $rid);
    }

    return $result;
  }

  /**
   * Fetch all rows for client and external_id.
   *
   * @param $client
   * @param $externalId
   * @return mixed
   */
  private function _dbGetUserIds($client, $externalId)
  {
    return $this->request->db
        ->select()
        ->from('users')
        ->where(array('client', $client))
        ->where(array('external_id', $externalId))
        ->execute();
  }

  /**
   * Delete all roles for a user.
   *
   * @param $uid
   * @return mixed
   */
  private function _dbDeleteUserRoles($uid)
  {
    return $this->request->db
        ->delete('user_roles')
        ->where(array('uid', $uid))
        ->execute();
  }

  /**
   * Delete a user, based on uid.
   *
   * @param $client
   * @param $uid
   * @return mixed
   */
  private function _dbDeleteUser($uid)
  {
    return $this->request->db
        ->delete('users')
        ->where(array('uid', $uid))
        ->execute();
  }


  /**
   * Get all role rows for a client and role.
   *
   * @param $client
   * @param $role
   * @return mixed
   */
  private function _dbGetRole($client, $role)
  {
    return $this->request->db
        ->select()
        ->from('roles')
        ->where(array('client', $client))
        ->where(array('role', $role))
        ->execute();
  }

  private function _dbInsertUser($client, $externalId, $token, $sessionName, $sessionId, $staleTime)
  {
    return $this->request->db
        ->insert('users')
        ->set(array(
            array('client', $client),
            array('external_id', $externalId),
            array('token', $token),
            array('session_name', $sessionName),
            array('session_id', $sessionId),
            array('stale_time', Utilities::date_php2mysql(strtotime($staleTime)))))
        ->execute();
  }

  /**
   * Insert a role for a client.
   *
   * @param $client
   * @param $role
   * @return mixed
   */
  private function _dbInsertRole($client, $role)
  {
    return $this->request->db
        ->insert('roles')
        ->set(array(
            array('client', $client),
            array('role', $role)
        ))
        ->execute();
  }

  /**
   * Insert a user role.
   *
   * @param $client
   * @param $role
   * @return mixed
   */
  private function _dbInsertUserRole($uid, $rid)
  {
    return $this->request->db
        ->insert('user_roles')
        ->set(array(
            array('uid', $uid),
            array('rid', $rid)
        ))
        ->execute();
  }
}
