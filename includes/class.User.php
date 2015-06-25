<?php

/**
 * @TODO: this is not used yet.
 */

include_once(Config::$dirIncludes . 'class.Utilities.php');

class User
{
  private $db;

  /**
   * @param $db
   */
  public function User(&$db)
  {
    $this->db = $db;
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
    $users = $this->_dbGetUserByExternalId($client, $externalId);
    while (!$users->EOF) {
      $uid = $users->fields['uid'];
      $this->_dbDeleteUserRoles($uid);
      $this->_dbDeleteUser($uid);
      $users->MoveNext();
    }
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
    $uid = $this->_dbInsertUser($client, $externalId, $token, $sessionName, $sessionId, $staleTime);

    //insert the client's roles
    foreach ($roles as $roleId => $role) {
      $dbRole = $this->_dbGetRole($client, $role);
      //ensure role exists for this client
      if ($dbRole->RecordCount() < 1) {
        $rid = $this->_dbInsertRole($client, $role);
      } else {
        $rid = $dbRole->Fields('rid');
      }
      $this->_dbInsertUserRole($uid, $rid);
    }
  }

  /**
   * Insert a user for a client.
   *
   * @param $client
   * @param $externalId
   * @param $token
   * @param $sessionName
   * @param $sessionId
   * @param $staleTime
   * @return mixed
   */
  private function _dbInsertUser($client, $externalId, $token, $sessionName, $sessionId, $staleTime)
  {
    $sql = 'INSERT INTO users ("client", "external_id", "token", "session_name", "session_id", "stale_time") VALUES (?, ?, ?, ?, ?, ?)';
    $this->db->Execute($sql, array($client, $externalId, $token, $sessionName, $sessionId, Utilities::date_php2mysql(strtotime($staleTime))));
    return $this->db->Insert_ID();
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
    $sql = 'INSERT INTO roles ("client", "role") VALUES (?, ?)';
    $this->db->Execute($sql, array($client, $role));
    return $this->db->Insert_ID();
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
    $sql = 'INSERT INTO user_roles ("uid", "rid") VALUES (?, ?)';
    $this->db->Execute($sql, array($uid, $rid));
    return $this->db->Insert_ID();
  }

  /**
   * Delete a user, based on uid.
   *
   * @param $uid
   * @return mixed
   */
  private function _dbDeleteUser($uid)
  {
    $sql = 'DELETE FROM users WHERE uid=?';
    $this->db->Execute($sql, array($uid));
    return $this->db->Affected_Rows();
  }

  /**
   * Delete all roles for a user.
   *
   * @param $uid
   * @return mixed
   */
  private function _dbDeleteUserRoles($uid)
  {
    $sql = 'DELETE FROM user_roles WHERE uid=?';
    $this->db->Execute($sql, array($uid));
    return $this->db->Affected_Rows();
  }

  /**
   * Fetch user by client and uid.
   *
   * @param $client
   * @param $uid
   * @return mixed
   */
  private function _dbGetUser($client, $uid)
  {
    $sql = 'SELECT * FROM users WHERE client=? AND uid=?';
    $recordSet = $this->db->Execute($sql, array($client, $uid));
    return $recordSet;
  }

  /**
   * Fetch user by client and external_id.
   *
   * @param $client
   * @param $externalId
   * @return mixed
   */
  private function _dbGetUserByExternalId($client, $externalId)
  {
    $sql = 'SELECT * FROM users WHERE client=? AND external_id=?';
    $recordSet = $this->db->Execute($sql, array($client, $externalId));
    return $recordSet;
  }

  /**
   * Fetch a user by token.
   * @param $token
   * @return mixed
   */
  private function _dbGetUserByToken($token)
  {
    $sql = 'SELECT * FROM users WHERE token=?';
    $recordSet = $this->db->Execute($sql, array($token));
    return $recordSet;
  }

  /**
   * Fetch a role.
   *
   * @param $rid
   * @return mixed
   */
  private function _dbGetRole($rid)
  {
    $sql = 'SELECT * FROM roles WHERE rid=?';
    $recordSet = $this->db->Execute($sql, array($rid));
    return $recordSet;
  }

  /**
   * Fetch the roles for a user.
   *
   * @param $uid
   * @return mixed
   */
  private function _dbGetUserRoles($uid)
  {
    $sql = 'SELECT * FROM roles r INNER JOIN user_roles ur ON ur.rid = u.rid WHERE ur.uid=?';
    $recordSet = $this->db->Execute($sql, array($uid));
    return $recordSet;
  }
}