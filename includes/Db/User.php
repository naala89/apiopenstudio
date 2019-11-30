<?php

namespace Gaterdata\Db;

use Gaterdata\Core\Hash;

/**
 * Class User.
 *
 * @package Gaterdata\Db
 */
class User {

    protected $uid;
    protected $active;
    protected $username;
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
    protected $addressCountry;
    protected $addressPostcode;
    protected $phoneMobile;
    protected $phoneWork;

  /**
   * User constructor.
   *
   * @param int $uid
   *   User ID.
   * @param int $active
   *   Active status.
   * @param string $username
   *   Username.
   * @param string $hash
   *   Password hash.
   * @param string $token
   *   Access token.
   * @param string $tokenTtl
   *   Access token stale date.
   * @param string $email
   *   User email.
   * @param string $honorific
   *   User honorific.
   * @param string $nameFirst
   *   First name.
   * @param string $nameLast
   *   Last name.
   * @param string $company
   *   Company.
   * @param string $website
   *   Website.
   * @param string $addressStreet
   *   Street address.
   * @param string $addressSuburb
   *   Suburb.
   * @param string $addressCity
   *   City.
   * @param string $addressState
   *   State.
   * @param string $addressCountry
   *   Country.
   * @param string $addressPostcode
   *   Postcode.
   * @param string $phoneMobile
   *   Mobile number.
   * @param string $phoneWork
   *   Business number.
   */
    public function __construct(
        $uid = null,
        $active = null,
        $username = null,
        $hash = null,
        $token = null,
        $tokenTtl = null,
        $email = null,
        $honorific = null,
        $nameFirst = null,
        $nameLast = null,
        $company = null,
        $website = null,
        $addressStreet = null,
        $addressSuburb = null,
        $addressCity = null,
        $addressState = null,
        $addressCountry = null,
        $addressPostcode = null,
        $phoneMobile = null,
        $phoneWork = null
    ) {
        $this->uid = $uid;
        $this->active = $active;
        $this->username = $username;
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
        $this->addressCountry = $addressCountry;
        $this->addressPostcode = $addressPostcode;
        $this->phoneMobile = $phoneMobile;
        $this->phoneWork = $phoneWork;
    }

  /**
   * Get user ID.
   *
   * @return int
   *   User ID.
   */
    public function getUid()
    {
        return $this->uid;
    }

  /**
   * Set the user ID.
   *
   * @param int $uid
   *   User ID.
   */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

  /**
   * Get the active status.
   *
   * @return int
   *   Active status.
   */
    public function getActive()
    {
        return $this->active;
    }

  /**
   * Set the active status.
   *
   * @param int $active
   *   Active status.
   */
    public function setActive($active)
    {
        $this->active = $active;
    }

  /**
   * Get the username.
   *
   * @return string
   *   Username.
   */
    public function getUsername()
    {
        return $this->username;
    }

  /**
   * Set the username.
   *
   * @param string $userName
   *   Username.
   */
    public function setUsername($userName)
    {
        $this->username = $userName;
    }

  /**
   * Set the password. This will also create the hash.
   *
   * @param string $password
   *   Password.
   */
    public function setPassword($password)
    {
      // Generate hash.
        $this->hash = Hash::generateHash($password);
    }

  /**
   * Get the hash.
   *
   * @return string
   *   Hash.
   */
    public function getHash()
    {
        return $this->hash;
    }

  /**
   * Set the hash.
   *
   * @param string $hash
   *   Hash.
   */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

  /**
   * Get the token.
   *
   * @return string
   *   Token.
   */
    public function getToken()
    {
        return $this->token;
    }

  /**
   * Set the token.
   *
   * @param string $token
   *   Token.
   */
    public function setToken($token)
    {
        $this->token = $token;
    }

  /**
   * Get the token stale date.
   *
   * @return string
   *   token stale date.
   */
    public function getTokenTtl()
    {
        return $this->tokenTtl;
    }

  /**
   * Set the token stale date.
   *
   * @param string $tokenTtl
   *   Token stale date.
   */
    public function setTokenTtl($tokenTtl)
    {
        $this->tokenTtl = $tokenTtl;
    }

  /**
   * Get the user email.
   *
   * @return string
   *   Email.
   */
    public function getEmail()
    {
        return $this->email;
    }

  /**
   * Set the email.
   *
   * @param string $email
   *   Email.
   */
    public function setEmail($email)
    {
        $this->email = $email;
    }

  /**
   * Get the honorific.
   *
   * @return string
   *   Honorific.
   */
    public function getHonorific()
    {
        return $this->honorific;
    }

  /**
   * Set the honorific.
   *
   * @param string $honorific
   *   Honorific.
   */
    public function setHonorific($honorific)
    {
        $this->honorific = $honorific;
    }

  /**
   * Get the first name.
   *
   * @return string
   *   First name.
   */
    public function getNameFirst()
    {
        return $this->nameFirst;
    }

  /**
   * Set the first name.
   *
   * @param string $nameFirst
   *   First name.
   */
    public function setNameFirst($nameFirst)
    {
        $this->nameFirst = $nameFirst;
    }

  /**
   * Get the last name.
   *
   * @return string
   *   Last name.
   */
    public function getNameLast()
    {
        return $this->nameLast;
    }

  /**
   * Set the last name.
   *
   * @param string $nameLast
   *   Last name.
   */
    public function setNameLast($nameLast)
    {
        $this->nameLast = $nameLast;
    }

  /**
   * Get the company.
   *
   * @return string
   *   Company.
   */
    public function getCompany()
    {
        return $this->company;
    }

  /**
   * Set the company.
   *
   * @param string $company
   *   Company.
   */
    public function setCompany($company)
    {
        $this->company = $company;
    }

  /**
   * Get the website.
   *
   * @return string
   *   Website.
   */
    public function getWebsite()
    {
        return $this->website;
    }

  /**
   * Set the website.
   *
   * @param string $website
   *   Website.
   */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

  /**
   * Get the street address.
   *
   * @return string
   *   Street address.
   */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

  /**
   * Set the street address.
   *
   * @param string $addressStreet
   *   Street address.
   */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

  /**
   * Get the suburb.
   *
   * @return string
   *   Suburb.
   */
    public function getAddressSuburb()
    {
        return $this->addressSuburb;
    }

  /**
   * Set the suburb.
   *
   * @param string $addressSuburb
   *   Suburb.
   */
    public function setAddressSuburb($addressSuburb)
    {
        $this->addressSuburb = $addressSuburb;
    }

  /**
   * Get the city.
   *
   * @return string
   *   City.
   */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

  /**
   * Set the city.
   *
   * @param string $addressCity
   *   City.
   */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

  /**
   * Get the state.
   *
   * @return string
   *   State.
   */
    public function getAddressState()
    {
        return $this->addressState;
    }

  /**
   * Set the state.
   *
   * @param string $addressState
   *   State.
   */
    public function setAddressState($addressState)
    {
        $this->addressState = $addressState;
    }

  /**
   * Get the country.
   *
   * @return string
   *   Country.
   */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

  /**
   * Set the country.
   *
   * @param string $addressCountry
   *   Country.
   */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;
    }

  /**
   * Get the postcode.
   *
   * @return string
   *   Postcode.
   */
    public function getAddressPostcode()
    {
        return $this->addressPostcode;
    }

  /**
   * Set the postcode.
   *
   * @param string $addressPostcode
   *   Postcode.
   */
    public function setAddressPostcode($addressPostcode)
    {
        $this->addressPostcode = $addressPostcode;
    }

  /**
   * Get the mobile phone number.
   *
   * @return string
   *   Mobile phone number.
   */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

  /**
   * Set the mobile phone number.
   *
   * @param string $phoneMobile
   *   Mobile phone number.
   */
    public function setPhoneMobile($phoneMobile)
    {
        $this->phoneMobile = $phoneMobile;
    }

  /**
   * Get the work phone number.
   *
   * @return string
   *   Work phone number.
   */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

  /**
   * Set the work phone number.
   *
   * @param string $phoneWork
   *   Work phone number.
   */
    public function setPhoneWork($phoneWork)
    {
        $this->phoneWork = $phoneWork;
    }

  /**
   * Return the values as an associative array.
   *
   * @return array
   *   User.
   */
    public function dump()
    {
        return [
        'uid' => $this->uid,
        'active' => $this->active,
        'username' => $this->username,
        'hash' => $this->hash,
        'token' => $this->token,
        'tokenTtl' => $this->tokenTtl,
        'email' => $this->email,
        'honorific' => $this->honorific,
        'nameFirst' => $this->nameFirst,
        'nameLast' => $this->nameLast,
        'company' => $this->company,
        'website' => $this->website,
        'addressStreet' => $this->addressStreet,
        'addressSuburb' => $this->addressSuburb,
        'addressCity' => $this->addressCity,
        'addressState' => $this->addressState,
        'addressCountry' => $this->addressCountry,
        'addressPostcode' => $this->addressPostcode,
        'phoneMobile' => $this->phoneMobile,
        'phoneWork' => $this->phoneWork,
        ];
    }

}
