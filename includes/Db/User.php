<?php
/**
 * Class User.
 *
 * @package    ApiOpenStudio
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

use ApiOpenStudio\Core\Hash;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class User.
 *
 * DB class for for storing user row data.
 */
class User
{
    /**
     * User ID.
     *
     * @var integer User ID.
     */
    protected $uid;

    /**
     * User active status.
     *
     * @var boolean User active status.
     */
    protected $active;

    /**
     * User username.
     *
     * @var string Username.
     */
    protected $username;

    /**
     * User hashed password.
     *
     * @var string  User password hash.
     */
    protected $hash;

    /**
     * User auth token.
     *
     * @var string User auth token.
     */
    protected $token;

    /**
     * User token TTL.
     *
     * @var string User auth token TTL.
     */
    protected $tokenTtl;

    /**
     * User email.
     *
     * @var string User email.
     */
    protected $email;

    /**
     * User honorific.
     *
     * @var string User honorific.
     */
    protected $honorific;

    /**
     * User first name.
     *
     * @var string User first name.
     */
    protected $nameFirst;

    /**
     * User last name.
     *
     * @var string User last name.
     */
    protected $nameLast;

    /**
     * User company.
     *
     * @var string User's company.
     */
    protected $company;

    /**
     * User website.
     *
     * @var string User's website.
     */
    protected $website;

    /**
     * User street.
     *
     * @var string User street address.
     */
    protected $addressStreet;

    /**
     * User suburb.
     *
     * @var string User suburb address.
     */
    protected $addressSuburb;

    /**
     * User city.
     *
     * @var string User city address.
     */
    protected $addressCity;

    /**
     * User county/state.
     *
     * @var string User state/county address.
     */
    protected $addressState;

    /**
     * User country.
     *
     * @var string User country address.
     */
    protected $addressCountry;

    /**
     * User postcode.
     *
     * @var string User postcode address.
     */
    protected $addressPostcode;

    /**
     * User mobile phone.
     *
     * @var string User mobile number.
     */
    protected $phoneMobile;

    /**
     * User work phone.
     *
     * @var string User work number.
     */
    protected $phoneWork;

    /**
     * User reset token.
     *
     * @var string Password reset token.
     */
    protected $passwordReset;

    /**
     * User reset token TTL.
     *
     * @var string Password reset TTL.
     */
    protected $passwordResetTtl;


    /**
     * User constructor.
     *
     * @param integer $uid User ID.
     * @param integer $active Active status.
     * @param string $username Username.
     * @param string $hash Password hash.
     * @param string $token Access token.
     * @param string $tokenTtl Access token stale date.
     * @param string $email User email.
     * @param string $honorific User honorific.
     * @param string $nameFirst First name.
     * @param string $nameLast Last name.
     * @param string $company Company.
     * @param string $website Website.
     * @param string $addressStreet Street address.
     * @param string $addressSuburb Suburb.
     * @param string $addressCity City.
     * @param string $addressState State.
     * @param string $addressCountry Country.
     * @param string $addressPostcode Postcode.
     * @param string $phoneMobile Mobile number.
     * @param string $phoneWork Business number.
     * @param string $passwordReset Password reset token.
     * @param string $passwordResetTtl Password reset token TTL.
     */
    public function __construct(
        int $uid = null,
        int $active = null,
        string $username = null,
        string $hash = null,
        string $token = null,
        string $tokenTtl = null,
        string $email = null,
        string $honorific = null,
        string $nameFirst = null,
        string $nameLast = null,
        string $company = null,
        string $website = null,
        string $addressStreet = null,
        string $addressSuburb = null,
        string $addressCity = null,
        string $addressState = null,
        string $addressCountry = null,
        string $addressPostcode = null,
        string $phoneMobile = null,
        string $phoneWork = null,
        string $passwordReset = null,
        string $passwordResetTtl = null
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
        $this->passwordReset = $passwordReset;
        $this->passwordResetTtl = $passwordResetTtl;
    }

    /**
     * Get user ID.
     *
     * @return integer User ID.
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set the user ID.
     *
     * @param integer $uid User ID.
     *
     * @return void
     */
    public function setUid(int $uid)
    {
        $this->uid = $uid;
    }

    /**
     * Get the active status.
     *
     * @return integer Active status.
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the active status.
     *
     * @param integer $active Active status.
     *
     * @return void
     */
    public function setActive(int $active)
    {
        $this->active = $active;
    }

    /**
     * Get the username.
     *
     * @return string Username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the username.
     *
     * @param string $userName Username.
     *
     * @return void
     */
    public function setUsername(string $userName)
    {
        $this->username = $userName;
    }

    /**
     * Set the password. This will also create the hash.
     *
     * @param string|null $password Password.
     *
     * @return void
     */
    public function setPassword(string $password = null)
    {
        // Generate hash.
        $this->hash = Hash::generateHash($password);
    }

    /**
     * Get the hash.
     *
     * @return string Hash.
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set the hash.
     *
     * @param string|null $hash Hash.
     *
     * @return void
     */
    public function setHash(string $hash = null)
    {
        $this->hash = $hash;
    }

    /**
     * Get the token.
     *
     * @return string Token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the token.
     *
     * @param string|null $token Token.
     *
     * @return void
     */
    public function setToken($token = null)
    {
        $this->token = $token;
    }

    /**
     * Get the token stale date.
     *
     * @return string token stale date.
     */
    public function getTokenTtl()
    {
        return $this->tokenTtl;
    }

    /**
     * Set the token stale date.
     *
     * @param string|null $tokenTtl Token stale date.
     *
     * @return void
     */
    public function setTokenTtl(string $tokenTtl = null)
    {
        $this->tokenTtl = $tokenTtl;
    }

    /**
     * Get the user email.
     *
     * @return string Email.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email.
     *
     * @param string $email Email.
     *
     * @return void
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Get the honorific.
     *
     * @return string Honorific.
     */
    public function getHonorific()
    {
        return $this->honorific;
    }

    /**
     * Set the honorific.
     *
     * @param string $honorific Honorific.
     *
     * @return void
     */
    public function setHonorific(string $honorific)
    {
        $this->honorific = $honorific;
    }

    /**
     * Get the first name.
     *
     * @return string First name.
     */
    public function getNameFirst()
    {
        return $this->nameFirst;
    }

    /**
     * Set the first name.
     *
     * @param string $nameFirst First name.
     *
     * @return void
     */
    public function setNameFirst(string $nameFirst)
    {
        $this->nameFirst = $nameFirst;
    }

    /**
     * Get the last name.
     *
     * @return string Last name.
     */
    public function getNameLast()
    {
        return $this->nameLast;
    }

    /**
     * Set the last name.
     *
     * @param string $nameLast Last name.
     *
     * @return void
     */
    public function setNameLast(string $nameLast)
    {
        $this->nameLast = $nameLast;
    }

    /**
     * Get the company.
     *
     * @return string Company.
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the company.
     *
     * @param string $company Company.
     *
     * @return void
     */
    public function setCompany(string $company)
    {
        $this->company = $company;
    }

    /**
     * Get the website.
     *
     * @return string Website.
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set the website.
     *
     * @param string $website Website.
     *
     * @return void
     */
    public function setWebsite(string $website)
    {
        $this->website = $website;
    }

    /**
     * Get the street address.
     *
     * @return string Street address.
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set the street address.
     *
     * @param string $addressStreet Street address.
     *
     * @return void
     */
    public function setAddressStreet(string $addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    /**
     * Get the suburb.
     *
     * @return string Suburb.
     */
    public function getAddressSuburb()
    {
        return $this->addressSuburb;
    }

    /**
     * Set the suburb.
     *
     * @param string $addressSuburb Suburb.
     *
     * @return void
     */
    public function setAddressSuburb(string $addressSuburb)
    {
        $this->addressSuburb = $addressSuburb;
    }

    /**
     * Get the city.
     *
     * @return string City.
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set the city.
     *
     * @param string $addressCity City.
     *
     * @return void
     */
    public function setAddressCity(string $addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * Get the state.
     *
     * @return string State.
     */
    public function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * Set the state.
     *
     * @param string $addressState State.
     *
     * @return void
     */
    public function setAddressState(string $addressState)
    {
        $this->addressState = $addressState;
    }

    /**
     * Get the country.
     *
     * @return string Country.
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * Set the country.
     *
     * @param string $addressCountry Country.
     *
     * @return void
     */
    public function setAddressCountry(string $addressCountry)
    {
        $this->addressCountry = $addressCountry;
    }

    /**
     * Get the postcode.
     *
     * @return string Postcode.
     */
    public function getAddressPostcode()
    {
        return $this->addressPostcode;
    }

    /**
     * Set the postcode.
     *
     * @param string $addressPostcode Postcode.
     *
     * @return void
     */
    public function setAddressPostcode(string $addressPostcode)
    {
        $this->addressPostcode = $addressPostcode;
    }

    /**
     * Get the mobile phone number.
     *
     * @return string Mobile phone number.
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

    /**
     * Set the mobile phone number.
     *
     * @param string $phoneMobile Mobile phone number.
     *
     * @return void
     */
    public function setPhoneMobile(string $phoneMobile)
    {
        $this->phoneMobile = $phoneMobile;
    }

    /**
     * Get the work phone number.
     *
     * @return string Work phone number.
     */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

    /**
     * Set the work phone number.
     *
     * @param string $phoneWork Work phone number.
     *
     * @return void
     */
    public function setPhoneWork(string $phoneWork)
    {
        $this->phoneWork = $phoneWork;
    }

    /**
     * Get the password reset token.
     *
     * @return string Password reset token.
     */
    public function getPasswordReset()
    {
        return $this->passwordReset;
    }

    /**
     * Set the password reset token.
     *
     * @param string|null $passwordReset Password reset token.
     *
     * @return void
     */
    public function setPasswordReset($passwordReset = null)
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * Get the password reset token TTL.
     *
     * @return string Password reset token TTL.
     */
    public function getPasswordResetTtl()
    {
        return $this->passwordResetTtl;
    }

    /**
     * Set the password reset token TTL.
     *
     * @param string|null $passwordResetTtl Password reset token TTL.
     *
     * @return void
     */
    public function setPasswordResetTtl($passwordResetTtl = null)
    {
        $this->passwordResetTtl = $passwordResetTtl;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array User.
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
            'passwordReset' => $this->passwordReset,
            'passwordResetTtl' => $this->passwordResetTtl,
        ];
    }
}
