<?php

namespace Gaterdata\Db;

/**
 * Class ExternalUser.
 *
 * @package Gaterdata\Db
 */
class ExternalUser {

  protected $id;
  protected $appId;
  protected $externalId;
  protected $externalEntity;
  protected $dataField1;
  protected $dataField2;
  protected $dataField3;

  /**
   * ExternalUser constructor.
   *
   * @param int $id
   *   External entity ID.
   * @param int $appId
   *   Application ID.
   * @param int $externalId
   *   External ID.
   * @param string $externalEntity
   *   External entity name.
   * @param string $dataField1
   *   Spare data field 1.
   * @param string $dataField2
   *   Spare data field 2.
   * @param string $dataField3
   *   Spare data field 3.
   */
  public function __construct($id = NULL, $appId = NULL, $externalId = NULL, $externalEntity = NULL, $dataField1 = NULL, $dataField2 = NULL, $dataField3 = NULL) {
    $this->id = $id;
    $this->appId = $appId;
    $this->externalId = $externalId;
    $this->externalEntity = $externalEntity;
    $this->dataField1 = $dataField1;
    $this->dataField2 = $dataField2;
    $this->dataField3 = $dataField3;
  }

  /**
   * Get External user ID.
   *
   * @return int
   *   External user ID.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set External user ID.
   *
   * @param int $id
   *   External user ID.
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get Application ID.
   *
   * @return int
   *   Application ID.
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Set application ID.
   *
   * @param int $appId
   *   Application ID.
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * Get External ID.
   *
   * @return mixed
   *   External ID.
   */
  public function getExternalId() {
    return $this->externalId;
  }

  /**
   * Set External ID.
   *
   * @param mixed $externalId
   *   External ID.
   */
  public function setExternalId($externalId) {
    $this->externalId = $externalId;
  }

  /**
   * Get External entity.
   *
   * @return mixed
   *   External entity.
   */
  public function getExternalEntity() {
    return $this->externalEntity;
  }

  /**
   * Set External entity.
   *
   * @param string $externalEntity
   *   External entity.
   */
  public function setExternalEntity($externalEntity) {
    $this->externalEntity = $externalEntity;
  }

  /**
   * Get data field 1.
   *
   * @return mixed
   *   Data field 1.
   */
  public function getDataField1() {
    return $this->dataField1;
  }

  /**
   * Set data field 1.
   *
   * @param mixed $dataField1
   *   Data field 1.
   */
  public function setDataField1($dataField1) {
    $this->dataField1 = $dataField1;
  }

  /**
   * Get data field 2.
   *
   * @return mixed
   *   Data field 2.
   */
  public function getDataField2() {
    return $this->dataField2;
  }

  /**
   * Set data field 2.
   *
   * @param mixed $dataField2
   *   Data field 2.
   */
  public function setDataField2($dataField2) {
    $this->dataField2 = $dataField2;
  }

  /**
   * Get data field 3.
   *
   * @return mixed
   *   Data field 3.
   */
  public function getDataField3() {
    return $this->dataField3;
  }

  /**
   * Set data field 3.
   *
   * @param mixed $dataField3
   *   Data field 3.
   */
  public function setDataField3($dataField3) {
    $this->dataField3 = $dataField3;
  }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   External user.
   */
  public function dump() {
    return [
      'uid' => $this->uid,
      'appId' => $this->appId,
      'externalId' => $this->externalId,
      'externalEntity' => $this->externalEntity,
      'dataField1' => $this->dataField1,
      'dataField2' => $this->dataField2,
      'dataField3' => $this->dataField3,
    ];
  }

}
