<?php

/**
 * @TODO: this needs some serious tidying
 */

include_once(Config::$dirIncludes . 'class.DB.php');

class User
{
  private $db;

  public function User($db = NULL)
  {
    if ($db === NULL) {
      $this->db = new DB(Config::$debugDb);
    } else {
      $this->db = $db;
    }
  }
  /**
   * Delete all rows of user by client ID and external ID.
   *
   * @param $client
   * @param $externalId
   * @return mixed
   */
  public function deleteUser($client, $externalId)
  {
    $result = $this->_dbGetUserId($client, $externalId);

    while($row = mysqli_fetch_object($result)) {
      $uid = $row->uid;

      $deleteUserRoles = $this->_dbDeleteUserRoles($uid);
      if (!$deleteUserRoles) {
        throw new ApiException('an error occurred while deleting a users roles', 2, $this->id, 400);
      }

      $deleteUser = $this->_dbDeleteUser($uid);
      if (!$deleteUser) {
        throw new ApiException('an error occurred while deleting a users login', 2, $this->id, 400);
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
  public function insertUser($client, $externalId, $roles, $token, $sessionName, $sessionId, $staleTime)
  {
    //insert user
    $result = $this->_dbInsertUser($client, $externalId, $token, $sessionName, $sessionId, $staleTime);
    $uid = $this->db->insertId();

    //insert the client's roles
    foreach ($roles as $roleId => $role) {
      $dbRole = $this->_dbGetRole($client, $role);
      //ensure role exists for this client
      if ($dbRole->num_rows < 1) {
        $this->_dbInsertRole($client, $role);
        $rid = $this->db->insertId();
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
  private function _dbGetUserId($client, $externalId)
  {
    return $this->db
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
    return $this->db
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
    return $this->db
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
    return $this->db
        ->select()
        ->from('roles')
        ->where(array('client', $client))
        ->where(array('role', $role))
        ->execute();
  }

  private function _dbInsertUser($client, $externalId, $token, $sessionName, $sessionId, $staleTime)
  {
    return $this->db
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
    return $this->db
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
   * @param $uid
   * @param $rid
   * @return mixed
   */
  private function _dbInsertUserRole($uid, $rid)
  {
    return $this->db
        ->insert('user_roles')
        ->set(array(
            array('uid', $uid),
            array('rid', $rid)))
        ->execute();
  }

  public function getUserRoles($uid)
  {
    return $this->db
        ->select()
        ->from(array('user_roles' => 'ur'))
        ->joins('inner join roles r on (ur.rid = r.rid)')
        ->where(array('ur.uid', $uid))
        ->execute();
  }

  public function getUserByToken($token)
  {
    return $this->db
        ->select()
        ->from('users')
        ->where(array('token', $token))
        ->execute();
  }
}