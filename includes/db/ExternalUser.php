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
  public function __construct($id=NULL, $appid=NULL, $externalId=NULL, $externalEntity=NULL, $dataField1=NULL, $dataField2=NULL, $dataField3=NULL)
  {
    $this->id = $id;
    $this->appid = $appid;
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
   * @param $val
   */
  public function setId($val)
  {
    $this->id = $val;
  }

  /**
   * @return int appid
   */
  public function getAppId()
  {
    return $this->appid;
  }

  /**
   * @param $val
   */
  public function setAppId($val)
  {
    $this->appid = $val;
  }

  /**
   * @return mixed external_id
   */
  public function getExternalId()
  {
    return $this->externalId;
  }

  /**
   * @param $val
   */
  public function setExternalId($val)
  {
    $this->externalId = $val;
  }

  /**
   * @return mixed external_entity
   */
  public function getExternalEntity()
  {
    return $this->externalEntity;
  }

  /**
   * @param $val
   */
  public function setExternalEntity($val)
  {
    $this->externalEntity = $val;
  }

  /**
   * @return mixed data_field_1
   */
  public function getDataField1()
  {
    return $this->dataField1;
  }

  /**
   * @param $val
   */
  public function setDataField1($val)
  {
    $this->dataField1 = $val;
  }

  /**
   * @return mixed data_field_2
   */
  public function getDataField2()
  {
    return $this->dataField2;
  }

  /**
   * @param $val
   */
  public function setDataField2($val)
  {
    $this->dataField2 = $val;
  }

  /**
   * @return mixed data_field_3
   */
  public function getDataField3()
  {
    return $this->dataField3;
  }

  /**
   * @param $val
   */
  public function setDataField3($val)
  {
    $this->dataField3 = $val;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->uid, 'uid');
    Core\Debug::variable($this->appid, 'appId');
    Core\Debug::variable($this->externalId, 'externalId');
    Core\Debug::variable($this->externalEntity, 'externalEntity');
    Core\Debug::variable($this->dataField1, '$dataField1');
    Core\Debug::variable($this->dataField2, '$dataField2');
    Core\Debug::variable($this->dataField3, '$dataField3');
  }
}
