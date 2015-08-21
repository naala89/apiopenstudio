<?php

/**
 * Container for data for an user row.
 */

namespace Datagator\Db;
use Datagator\Core;

class User
{
  protected $uid;
  protected $active;
  protected $username;
  protected $salt;
  protected $hash;
  protected $token;
  protected $tokenTtl;
  protected $email;
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

  /**
   * @param null $uid
   * @param null $active
   * @param null $username
   * @param null $salt
   * @param null $hash
   * @param null $token
   * @param null $tokenTtl
   * @param null $email
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
   */
  public function __construct($uid=NULL, $active=NULL, $username=NULL, $salt=NULL, $hash=NULL, $token=NULL, $tokenTtl=NULL, $email=NULL, $honorific=NULL, $nameFirst=NULL, $nameLast=NULL, $company=NULL, $website=NULL, $addressStreet=NULL, $addressSuburb=NULL, $addressCity=NULL, $addressState=NULL, $addressPostcode=NULL, $phoneMobile=NULL, $phoneWork=NULL)
  {
    $this->uid = $uid;
    $this->active = $active;
    $this->username = $username;
    $this->salt = $salt;
    $this->hash = $hash;
    $this->token = $token;
    $this->tokenTtl = $tokenTtl;
    $this->email = $email;
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
   * @return int active
   */
  public function getActive()
  {
    return $this->active;
  }

  /**
   * @param $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }

  /**
   * @return mixed username
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @param $userName
   */
  public function setUsername($userName)
  {
    $this->username = $userName;
  }

  /**
   * @return mixed salt
   */
  public function getSalt()
  {
    return $this->salt;
  }

  /**
   * @param $salt
   */
  public function setSalt($salt)
  {
    $this->salt = $salt;
  }

  /**
   * @return mixed hash
   */
  public function getHash()
  {
    return $this->hash;
  }

  /**
   * @param $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }

  /**
   * @return mixed token
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * @param $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * @return mixed token
   */
  public function getTokenTtl()
  {
    return $this->tokenTtl;
  }

  /**
   * @param $tokenTtl
   */
  public function setTokenTtl($tokenTtl)
  {
    $this->tokenTtl = $tokenTtl;
  }

  /**
   * @return mixed email
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * @return int honorific
   */
  public function getHonorific()
  {
    return $this->honorific;
  }

  /**
   * @param $honorific
   */
  public function setHonorific($honorific)
  {
    $this->honorific = $honorific;
  }

  /**
   * @return int name_first
   */
  public function getNameFirst()
  {
    return $this->nameFirst;
  }

  /**
   * @param $nameFirst
   */
  public function setNameFirst($nameFirst)
  {
    $this->nameFirst = $nameFirst;
  }

  /**
   * @return int name_last
   */
  public function getNameLast()
  {
    return $this->nameLast;
  }

  /**
   * @param $nameLast
   */
  public function setNameLast($nameLast)
  {
    $this->nameLast = $nameLast;
  }

  /**
   * @return int company
   */
  public function getCompany()
  {
    return $this->company;
  }

  /**
   * @param $company
   */
  public function setCompany($company)
  {
    $this->company = $company;
  }

  /**
   * @return int website
   */
  public function getWebsite()
  {
    return $this->website;
  }

  /**
   * @param $website
   */
  public function setWebsite($website)
  {
    $this->website = $website;
  }

  /**
   * @return int address_street
   */
  public function getAddressStreet()
  {
    return $this->addressStreet;
  }

  /**
   * @param $addressStreet
   */
  public function setAddressStreet($addressStreet)
  {
    $this->addressStreet = $addressStreet;
  }

  /**
   * @return int address_suburb
   */
  public function getAddressSuburb()
  {
    return $this->addressSuburb;
  }

  /**
   * @param $addressSuburb
   */
  public function setAddressSuburb($addressSuburb)
  {
    $this->addressSuburb = $addressSuburb;
  }

  /**
   * @return int address_city
   */
  public function getAddressCity()
  {
    return $this->addressCity;
  }

  /**
   * @param $addressCity
   */
  public function setAddressCity($addressCity)
  {
    $this->addressCity = $addressCity;
  }

  /**
   * @return int address_state
   */
  public function getAddressState()
  {
    return $this->addressState;
  }

  /**
   * @param $addressState
   */
  public function setAddressState($addressState)
  {
    $this->addressState = $addressState;
  }

  /**
   * @return int address_postcode
   */
  public function getAddressPostcode()
  {
    return $this->addressPostcode;
  }

  /**
   * @param $addressPostcode
   */
  public function setAddressPostcode($addressPostcode)
  {
    $this->addressPostcode = $addressPostcode;
  }

  /**
   * @return int phone_mobile
   */
  public function getPhoneMobile()
  {
    return $this->phoneMobile;
  }

  /**
   * @param $phoneMobile
   */
  public function setPhoneMobile($phoneMobile)
  {
    $this->phoneMobile = $phoneMobile;
  }

  /**
   * @return int phone_work
   */
  public function getPhoneWork()
  {
    return $this->phoneWork;
  }

  /**
   * @param $phoneWork
   */
  public function setPhoneWork($phoneWork)
  {
    $this->phoneWork = $phoneWork;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->uid, 'uid');
    Core\Debug::variable($this->active, 'active');
    Core\Debug::variable($this->username, 'username');
    Core\Debug::variable($this->salt, 'salt');
    Core\Debug::variable($this->hash, 'hash');
    Core\Debug::variable($this->token, 'token');
    Core\Debug::variable($this->tokenTtl, 'token_ttl');
    Core\Debug::variable($this->email, 'email');
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
  }
}
