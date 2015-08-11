<?php

/**
 * Container for data for an external_user row.
 */

namespace Datagator\Db;
use Datagator\Core;

class ExternalUser
{
  protected $uid;
  protected $cid;
  protected $externalId;
  protected $externalEntity;
  protected $dataField1;
  protected $dataField2;
  protected $dataField3;

  /**
   * @param null $uid
   * @param null $cid
   * @param null $externalId
   * @param null $externalEntity
   * @param null $dataField1
   * @param null $dataField2
   * @param null $dataField3
   */
  public function __construct($uid=NULL, $cid=NULL, $externalId=NULL, $externalEntity=NULL, $dataField1=NULL, $dataField2=NULL, $dataField3=NULL)
  {
    $this->uid = $uid;
    $this->cid = $cid;
    $this->externalId = $externalId;
    $this->externalEntity = $externalEntity;
    $this->dataField1 = $dataField1;
    $this->dataField2 = $dataField2;
    $this->dataField3 = $dataField3;
  }

  /**
   * @return int uid
   */
  public function getUid()
  {
    return $this->uid;
  }

  /**
   * @param $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }

  /**
   * @return int cid
   */
  public function getCid()
  {
    return $this->cid;
  }

  /**
   * @param $cid
   */
  public function setCid($cid)
  {
    $this->cid = $cid;
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
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->uid, 'uid');
    Core\Debug::variable($this->cid, 'cid');
    Core\Debug::variable($this->externalId, 'externalId');
    Core\Debug::variable($this->externalEntity, 'externalEntity');
    Core\Debug::variable($this->dataField1, '$dataField1');
    Core\Debug::variable($this->dataField2, '$dataField2');
    Core\Debug::variable($this->dataField3, '$dataField3');
  }
}
