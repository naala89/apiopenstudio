<?php

/**
 * Container for data for an external_user row.
 */

namespace Datagator\Db;
use Datagator\Core;

class ExternalUser
{
  protected $id;
  protected $appId;
  protected $externalId;
  protected $externalEntity;
  protected $dataField1;
  protected $dataField2;
  protected $dataField3;

  /**
   * @param null $id
   * @param null $appid
   * @param null $externalId
   * @param null $externalEntity
   * @param null $dataField1
   * @param null $dataField2
   * @param null $dataField3
   */
  public function __construct($id=NULL, $appId=NULL, $externalId=NULL, $externalEntity=NULL, $dataField1=NULL, $dataField2=NULL, $dataField3=NULL)
  {
    $this->id = $id;
    $this->appId = $appId;
    $this->externalId = $externalId;
    $this->externalEntity = $externalEntity;
    $this->dataField1 = $dataField1;
    $this->dataField2 = $dataField2;
    $this->dataField3 = $dataField3;
  }

  /**
   * @return int id
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int appid
   */
  public function getAppId()
  {
    return $this->appId;
  }

  /**
   * @param $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }

  /**
   * @return mixed external_id
   */
  public function getExternalId()
  {
    return $this->externalId;
  }

  /**
   * @param $externalId
   */
  public function setExternalId($externalId)
  {
    $this->externalId = $externalId;
  }

  /**
   * @return mixed external_entity
   */
  public function getExternalEntity()
  {
    return $this->externalEntity;
  }

  /**
   * @param $externalEntity
   */
  public function setExternalEntity($externalEntity)
  {
    $this->externalEntity = $externalEntity;
  }

  /**
   * @return mixed data_field_1
   */
  public function getDataField1()
  {
    return $this->dataField1;
  }

  /**
   * @param $dataField1
   */
  public function setDataField1($dataField1)
  {
    $this->dataField1 = $dataField1;
  }

  /**
   * @return mixed data_field_2
   */
  public function getDataField2()
  {
    return $this->dataField2;
  }

  /**
   * @param $dataField2
   */
  public function setDataField2($dataField2)
  {
    $this->dataField2 = $dataField2;
  }

  /**
   * @return mixed data_field_3
   */
  public function getDataField3()
  {
    return $this->dataField3;
  }

  /**
   * @param $dataField3
   */
  public function setDataField3($dataField3)
  {
    $this->dataField3 = $dataField3;
  }

  /**
   * Return the values as an associative array.
   * @return array
   */
  public function dump()
  {
    return array(
      'uid' => $this->uid,
      'appId' => $this->appId,
      'externalId' => $this->externalId,
      'externalEntity' => $this->externalEntity,
      'dataField1' => $this->dataField1,
      'dataField2' => $this->dataField2,
      'dataField3' => $this->dataField3,
    );
  }
}
