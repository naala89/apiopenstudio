<?php

/**
 * Container for data for an user row.
 */

namespace Datagator\Db;
use Datagator\Core;

class User
{
  protected $uid;
  protected $honorific;
  protected $nameFirst;
  protected $nameLast;
  protected $company;
  protected $website;
  protected $addressStreet;
  protected $addressSuburb;
  protected $addressCity;
  protected $addressState;
  protected $addressPostcode;
  protected $phoneMobile;
  protected $phoneWork;
  protected $email;
  protected $password;

  /**
   * @param null $uid
   * @param null $active
   * @param null $honorific
   * @param null $nameFirst
   * @param null $nameLast
   * @param null $company
   * @param null $website
   * @param null $addressStreet
   * @param null $addressSuburb
   * @param null $addressCity
   * @param null $addressState
   * @param null $addressPostcode
   * @param null $phoneMobile
   * @param null $phoneWork
   * @param null $email
   * @param $password
   */
  public function __construct($uid=NULL, $active=NULL, $honorific=NULL, $nameFirst=NULL, $nameLast=NULL, $company=NULL, $website=NULL, $addressStreet=NULL, $addressSuburb=NULL, $addressCity=NULL, $addressState=NULL, $addressPostcode=NULL, $phoneMobile=NULL, $phoneWork=NULL, $email=NULL, $password)
  {
    $this->uid = $uid;
    $this->active = $active;
    $this->honorific = $honorific;
    $this->nameFirst = $nameFirst;
    $this->nameLast = $nameLast;
    $this->company = $company;
    $this->website = $website;
    $this->addressStreet = $addressStreet;
    $this->addressSuburb = $addressSuburb;
    $this->addressCity = $addressCity;
    $this->addressState = $addressState;
    $this->addressPostcode = $addressPostcode;
    $this->phoneMobile = $phoneMobile;
    $this->phoneWork = $phoneWork;
    $this->email = $email;
    $this->password = $password;
  }

  /**
   * @return int uid
   */
  public function getUid()
  {
    return $this->uid;
  }

  /**
   * @param $val
   */
  public function setUid($val)
  {
    $this->uid = $val;
  }

  /**
   * @return int active
   */
  public function getActive()
  {
    return $this->active;
  }

  /**
   * @param $val
   */
  public function setActive($val)
  {
    $this->active = $val;
  }

  /**
   * @return int honorific
   */
  public function getHonorific()
  {
    return $this->honorific;
  }

  /**
   * @param $val
   */
  public function setHonorific($val)
  {
    $this->honorific = $val;
  }

  /**
   * @return int name_first
   */
  public function getNameFirst()
  {
    return $this->nameFirst;
  }

  /**
   * @param $val
   */
  public function setNameFirst($val)
  {
    $this->active = $val;
  }

  /**
   * @return int name_last
   */
  public function getNameLast()
  {
    return $this->nameLast;
  }

  /**
   * @param $val
   */
  public function setNameLst($val)
  {
    $this->nameLast = $val;
  }

  /**
   * @return int company
   */
  public function getCompany()
  {
    return $this->company;
  }

  /**
   * @param $val
   */
  public function setCompany($val)
  {
    $this->company = $val;
  }

  /**
   * @param $val
   */
  public function setWebsite($val)
  {
    $this->website = $val;
  }

  /**
   * @return int website
   */
  public function getWebsite()
  {
    return $this->website;
  }

  /**
   * @param $val
   */
  public function setAddressStreet($val)
  {
    $this->addressStreet = $val;
  }

  /**
   * @return int address_street
   */
  public function getAddressStreet()
  {
    return $this->addressStreet;
  }

  /**
   * @param $val
   */
  public function setAddressSuburb($val)
  {
    $this->addressSuburb = $val;
  }

  /**
   * @return int address_suburb
   */
  public function getAddressSuburb()
  {
    return $this->getAddressSuburb();
  }

  /**
   * @param $val
   */
  public function setAddressCity($val)
  {
    $this->addressCity = $val;
  }

  /**
   * @return int address_city
   */
  public function getAddressCity()
  {
    return $this->addressCity;
  }

  /**
   * @param $val
   */
  public function setAddressState($val)
  {
    $this->addressState = $val;
  }

  /**
   * @return int address_state
   */
  public function getAddressState()
  {
    return $this->addressState;
  }

  /**
   * @param $val
   */
  public function setAddressPostcode($val)
  {
    $this->addressPostcode = $val;
  }

  /**
   * @return int address_postcode
   */
  public function getAddressPostcode()
  {
    return $this->addressPostcode;
  }

  /**
   * @param $val
   */
  public function setPhoneMobile($val)
  {
    $this->phoneMobile = $val;
  }

  /**
   * @return int phone_mobile
   */
  public function getPhoneMobile()
  {
    return $this->phoneMobile;
  }

  /**
   * @param $val
   */
  public function setPhoneWork($val)
  {
    $this->phoneWork = $val;
  }

  /**
   * @return int phone_work
   */
  public function getPhoneWork()
  {
    return $this->phoneWork;
  }

  /**
   * @return mixed email
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param $val
   */
  public function setEmail($val)
  {
    $this->email = $val;
  }

  /**
   * @return mixed password
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @param $val
   */
  public function setPassword($val)
  {
    $this->password = $val;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->uid, 'uid');
    Core\Debug::variable($this->active, 'active');
    Core\Debug::variable($this->honorific, 'honorific');
    Core\Debug::variable($this->nameFirst, 'name_first');
    Core\Debug::variable($this->nameLast, 'name_last');
    Core\Debug::variable($this->company, 'company');
    Core\Debug::variable($this->website, 'website');
    Core\Debug::variable($this->addressStreet, 'address_street');
    Core\Debug::variable($this->addressSuburb, 'address_suburb');
    Core\Debug::variable($this->addressCity, 'address_city');
    Core\Debug::variable($this->addressState, 'address_state');
    Core\Debug::variable($this->addressPostcode, 'address_postcode');
    Core\Debug::variable($this->phoneMobile, 'phone_mobile');
    Core\Debug::variable($this->phoneWork, 'phone_work');
    Core\Debug::variable($this->email, 'email');
    Core\Debug::variable($this->password, 'password');
  }
}
