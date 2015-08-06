<?php

/**
 * Utility class with all User functions
 */

namespace Datagator\Core;
use Datagator\Adodb;


class User
{
  const STALE_TIME = "+12 hour";
  private $dbUser;
  private $dbRole;

  /**
   * Constructor.
   *
   * @param $db
   */
  public function User($db)
  {
    $this->dbUser = new DB\create($db, 'Users');
    $this->dbRole = new DB\create($db, 'Roles');
  }

  /**
   * Create a user for an existing client.
   *
   * @param $email
   * @param $password
   * @param $clientId
   * @param $roles
   * @return bool
   * @throws \Datagator\includes\ApiException
   */
  public function create($email, $password, $clientId, $roles)
  {
    $this->db->StartTrans();
    // check user doesn't already exist
    $result = $this->_dbGetUserByEmail($email);
    if ($result->RecordCount() > 0) {
      $this->db->FailTrans();
      throw new \Datagator\includes\ApiException('user already exists', -1, $this->id, 417);
    }
    //check client exists
    $result = $this->_dbGetClientById($clientId);
    if ($result->RecordCount() != 1) {
      $this->db->FailTrans();
      throw new \Datagator\includes\ApiException("client ($clientId) does not exist", -1, $this->id, 417);
    }
    //check roles exist
    foreach ($roles as $role) {
      $result = $this->_dbGetRoleByRid($role);
      if ($result->RecordCount() != 1) {
        $this->db->FailTrans();
        throw new \Datagator\includes\ApiException("role ($role) does not exist", -1, $this->id, 417);
      }
    }

    //insert user
    $result = $this->_dbInsertUser($email, $password, $clientId);
    if ($result->affectedRows() < 1) {
      $this->db->FailTrans();
      throw new \Datagator\includes\ApiException('failed to insert user', -1, $this->id, 500);
    }
    $user = $this->_dbGetUserByEmail($email);
    // insert user roles
    foreach ($roles as $role) {
      $result = $this->_dbInsertUserRole($user->fields['uid'], $role);
      if ($result->affectedRows() < 1) {
        $this->db->FailTrans();
        throw new \Datagator\includes\ApiException("failed to insert user role ($role)", -1, $this->id, 500);
      }
    }
    $this->db->CompleteTrans();

    return TRUE;
  }

  /**
   * Activate a user.
   *
   * @param $uid
   * @return bool
   */
  public function activate($uid)
  {
    $sql = 'UPDATE users SET `active` = 1 WHERE `uid` = ?';
    $bindParams = array($uid);
    $result = $this->db->Execute($sql, $bindParams);
    return $result->affectedRows() > 0;
  }

  /**
   * Deactivate a user.
   *
   * @param $uid
   * @return bool
   */
  public function deactivate($uid)
  {
    $sql = 'UPDATE users SET `active` = 0 WHERE `uid` = ?';
    $bindParams = array($uid);
    $result = $this->db->Execute($sql, $bindParams);
    return $result->affectedRows() > 0;
  }

  /**
   * Delete a user and its roles bu UID.
   * @param $uid
   * @throws \ApiException
   */
  public function deleteByUid($uid)
  {
    $result = $this->_dbGetUserByUid($uid);
    if ($result->RecordCount() < 1) {
      throw new \Datagator\includes\ApiException("no such user", -1, $this->id, 417);
    }
    $this->_delete($uid);
  }

  /**
   * Delete a user and its roles bu UID.
   *
   * @param $email
   * @throws \ApiException
   */
  public function deleteByEmail($email)
  {
    $result = $this->_dbGetClientByEmail($email);
    if ($result->RecordCount() < 1) {
      throw new \Datagator\includes\ApiException("no such user", -1, $this->id, 417);
    }
    $this->_delete($result->fields['uid']);
  }

  /**
   * Utility function to delete a user.
   *
   * @param $uid
   * @throws \ApiException
   */
  private function _delete($uid)
  {
    $this->db->StartTrans();
    $roles = $this->_dbGetRoleByUid($uid);
    if (!$roles) {
      $this->db->FailTrans();
      throw new \Datagator\includes\ApiException("error fetching roles", -1, $this->id, 417);
    }
    while (!$roles->EOF) {
      $result = $this->deleteRole($uid, $roles->fields['rid']);
      if ($result->affectedRows() < 1) {
        $this->db->FailTrans();
        throw new \Datagator\includes\ApiException("error deleting roles", -1, $this->id, 417);
      }
      $roles->MoveNext();
    }
    $this->_dbDeleteUser($uid);
    $this->db->CompleteTrans();
  }

  /**
   * Delete a role for a user.
   * @param $uid
   * @param $rid
   * @return mixed
   */
  public function deleteRole($uid, $rid)
  {
    return $this->_dbDeleteUserRole($uid, $rid);
  }

  /**
   * Perform login process.
   *
   * @param $email
   * @param $password
   * @return string
   * @throws \ApiException
   */
  public function login($email, $password)
  {
    $result = $this->_dbGetUserByEmailPassword($email, $password);
    if ($result->RecordCount() != 1) {
      throw new \Datagator\includes\ApiException("access denied", -1, $this->id, 401);
    }

    $uid = $result->fields['uid'];
    $token = $this->_generateToken();

    $this->db->StartTrans();
    $result = $this->_dbInsertToken($uid, $token);
    if ($result->affectedRows() < 1) {
      $this->db->FailTrans();
      throw new \Datagator\includes\ApiException('failed to generate token', -1, $this->id, 417);
    }
    $this->db->CompleteTrans();

    return $token;
  }

  /**
   * Check a token is still valid.
   *
   * @param $token
   * @return bool
   */
  public function validateToken($token)
  {
    $result = $this->_dbValidateToken($token);
    if ($result->RecordCount() < 1) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Generate a token.
   * @return string
   */
  private function _generateToken()
  {
    return Utilities::random_string(32);
  }

  /******************************************
   * DB User
  *******************************************/

  /**
   * Insert a user row in the DB.
   *
   * @param $email
   * @param $password
   * @param $clientId
   * @return mixed
   */
  private function _dbInsertUser($email, $password, $clientId)
  {
    $sql = 'INSERT INTO roles (`email`, `password`, `cid`) VALUES (?, ?, ?)';
    $bindParams = array($email, md5($password), $clientId);
    return $this->db->Execute($sql, $bindParams);
  }

  /**
   * Delete a user row from the DB.
   *
   * @param $uid
   * @return mixed
   */
  private function _dbDeleteUser($uid)
  {
    $sql = 'DELETE FROM users WHERE uid = ?';
    $bindParams = array($uid);
    return $this->db->Execute($sql, $bindParams);
  }

  /**
   * Fetch a User DB row by email.
   *
   * @param $email
   * @return mixed
   */
  private function _dbGetUserByEmail($email)
  {
    $sql = 'SELECT * FROM users WHERE `email` = ?';
    $bindParams = array($email);
    return $this->db->Execute($sql, $bindParams);
  }

  /**
   * Fetch a User DB row by email and password.
   *
   * @param $email
   * @param $password
   * @return mixed
   */
  private function _dbGetUserByEmailPassword($email, $password)
  {
    $sql = 'SELECT * FROM users WHERE `email` = ? AND `password` = ?';
    $bindParams = array($email, md5($password));
    return $this->db->Execute($sql, $bindParams);
  }

  /**
   * Fetch a User DB row by UID.
   *
   * @param $uid
   * @return mixed
   */
  private function _dbGetUserByUid($uid)
  {
    $sql = 'SELECT * FROM users WHERE `uid` = ?';
    $bindParams = array($uid);
    return $this->db->Execute($sql, $bindParams);
  }

  /******************************************
   * DB Token
   *******************************************/

  private function _dbValidateToken($token)
  {
    $sql = 'SELECT * FROM users WHERE `token`=? AND (`stale_time` > now() OR `stale_time` IS NULL)';
    $bindParams = array($token);
    return $this->db->Execute($sql, $bindParams);
  }

  private function _dbInsertToken($uid, $token=NULL, $staleTime=NULL)
  {
    $token = ($token === NULL ? $this->_generateToken() : $token);
    $staleTime = ($staleTime === NULL ? Utilities::date_php2mysql(strtotime(STALE_TIME)) : $staleTime);
    $sql = 'UPDATE users SET `token` = ?, `stale_time` = ? WHERE `uid` = ?';
    $bindParams = array($token, $staleTime, $uid);
    return $this->db->Execute($sql, $bindParams);
  }

  /******************************************
   * DB Client
   *******************************************/

  private function _dbGetClientById($cid)
  {
    $sql = 'SELECT * FROM clients WHERE `cid` = ?';
    $bindParams = array($cid);
    return $this->db->Execute($sql, $bindParams);
  }

  /******************************************
   * DB Roles
   *******************************************/

  private function _dbGetRoleByRid($rid)
  {
    $sql = 'SELECT * FROM roles WHERE `rid` = ?';
    $bindParams = array($rid);
    return $this->db->Execute($sql, $bindParams);
  }

  private function _dbGetRoleByUid($uid)
  {
    $sql = 'SELECT * FROM roles WHERE `uid` = ?';
    $bindParams = array($uid);
    return $this->db->Execute($sql, $bindParams);
  }

  private function _dbDeleteUserRole($uid, $rid)
  {
    $sql = 'SELECT * FROM roles WHERE `uid` = ? AND rid = ?';
    $bindParams = array($uid, $rid);
    return $this->db->Execute($sql, $bindParams);
  }

  private function _dbInsertUserRole($uid, $rid)
  {
    $sql = 'INSERT INTO user_roles (`uid`, `rid`) VALUES (?, ?)';
    $bindParams = array($uid, $rid);
    return $this->db->Execute($sql, $bindParams);
  }

  /**
   * Delete all rows of user by client ID and external ID.
   *
   * @param $client
   * @param $externalId
   * @return mixed
   *
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
   *
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
   *
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
   *
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
   *
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
   *
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
   *
  private function _dbDeleteUserRoles($uid)
  {
    $sql = 'DELETE FROM user_roles WHERE uid=?';
    $this->db->Execute($sql, array($uid));
    return $this->db->Affected_Rows();
  }

  /**
   * Fetch user by uid.
   *
   * @param $uid
   * @return mixed
   *
  private function _dbGetUser($uid)
  {
    $sql = 'SELECT * FROM users WHERE uid=?';
    $recordSet = $this->db->Execute($sql, array($uid));
    return $recordSet;
  }

  /**
   * Fetch user by client and external_id.
   *
   * @param $client
   * @param $externalId
   * @return mixed
   *
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
   *
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
   *
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
   *
  private function _dbGetUserRoles($uid)
  {
    $sql = 'SELECT * FROM roles r INNER JOIN user_roles ur ON ur.rid = u.rid WHERE ur.uid=?';
    $recordSet = $this->db->Execute($sql, array($uid));
    return $recordSet;
  }
   */
}
