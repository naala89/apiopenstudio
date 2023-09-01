<?php

/**
 * Class User.
 *
 * @package    ApiOpenStudio\Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

use ApiOpenStudio\Core\Hash;

/**
 * Class User.
 *
 * DB class for storing user row data.
 */
class User
{
    /**
     * User ID.
     *
     * @var ?int User ID.
     */
    protected ?int $uid;

    /**
     * User active status.
     *
     * @var ?int User active status.
     */
    protected ?int $active;

    /**
     * User username.
     *
     * @var ?string Username.
     */
    protected ?string $username;

    /**
     * User hashed password.
     *
     * @var ?string User password hash.
     */
    protected ?string $hash;

    /**
     * User refresh token.
     *
     * @var ?string User refresh token..
     */
    protected ?string $refreshToken;

    /**
     * User email.
     *
     * @var ?string User email.
     */
    protected ?string $email;

    /**
     * User honorific.
     *
     * @var ?string User honorific.
     */
    protected ?string $honorific;

    /**
     * User first name.
     *
     * @var ?string User first name.
     */
    protected ?string $nameFirst;

    /**
     * User last name.
     *
     * @var ?string User last name.
     */
    protected ?string $nameLast;

    /**
     * User company.
     *
     * @var ?string User's company.
     */
    protected ?string $company;

    /**
     * User website.
     *
     * @var ?string User's website.
     */
    protected ?string $website;

    /**
     * User street.
     *
     * @var ?string User street address.
     */
    protected ?string $addressStreet;

    /**
     * User suburb.
     *
     * @var ?string User suburb address.
     */
    protected ?string $addressSuburb;

    /**
     * User city.
     *
     * @var ?string User city address.
     */
    protected ?string $addressCity;

    /**
     * User county/state.
     *
     * @var ?string User state/county address.
     */
    protected ?string $addressState;

    /**
     * User country.
     *
     * @var ?string User country address.
     */
    protected ?string $addressCountry;

    /**
     * User postcode.
     *
     * @var ?string User postcode address.
     */
    protected ?string $addressPostcode;

    /**
     * User mobile phone.
     *
     * @var ?string User mobile number.
     */
    protected ?string $phoneMobile;

    /**
     * User work phone.
     *
     * @var ?string User work number.
     */
    protected ?string $phoneWork;

    /**
     * User reset token.
     *
     * @var ?string Password reset token.
     */
    protected ?string $passwordReset;

    /**
     * User reset token TTL.
     *
     * @var ?string Password reset TTL.
     */
    protected ?string $passwordResetTtl;


    /**
     * User constructor.
     *
     * @param int|null $uid User ID.
     * @param int|null $active Active status.
     * @param string|null $username Username.
     * @param string|null $hash Password hash.
     * @param string|null $email User email.
     * @param string|null $honorific User honorific.
     * @param string|null $nameFirst First name.
     * @param string|null $nameLast Last name.
     * @param string|null $company Company.
     * @param string|null $website Website.
     * @param string|null $addressStreet Street address.
     * @param string|null $addressSuburb Suburb.
     * @param string|null $addressCity City.
     * @param string|null $addressState State.
     * @param string|null $addressCountry Country.
     * @param string|null $addressPostcode Postcode.
     * @param string|null $phoneMobile Mobile number.
     * @param string|null $phoneWork Business number.
     * @param string|null $passwordReset Password reset token.
     * @param string|null $passwordResetTtl Password reset token TTL.
     * @param string|null $refreshToken Refresh token.
     */
    public function __construct(
        int $uid = null,
        int $active = null,
        string $username = null,
        string $hash = null,
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
        string $passwordResetTtl = null,
        string $refreshToken = null
    ) {
        $this->uid = $uid;
        $this->active = $active;
        $this->username = $username;
        $this->hash = $hash;
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
        $this->refreshToken = $refreshToken;
    }

    /**
     * Get user ID.
     *
     * @return int User ID.
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * Set the user ID.
     *
     * @param int $uid User ID.
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
     * @return int Active status.
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * Set the active status.
     *
     * @param int $active Active status.
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
    public function getUsername(): ?string
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
    public function getHash(): ?string
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
     * Get the refresh token.
     *
     * @return string refresh token.
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * Set the refresh token.
     *
     * @param string|null $refreshToken refresh token.
     *
     * @return void
     */
    public function setRefreshToken(string $refreshToken = null)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Get the user email.
     *
     * @return string Email.
     */
    public function getEmail(): ?string
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
    public function getHonorific(): ?string
    {
        return $this->honorific;
    }

    /**
     * Set the honorific.
     *
     * @param string|null $honorific Honorific.
     *
     * @return void
     */
    public function setHonorific(string $honorific = null)
    {
        $this->honorific = $honorific;
    }

    /**
     * Get the first name.
     *
     * @return string First name.
     */
    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    /**
     * Set the first name.
     *
     * @param string|null $nameFirst First name.
     *
     * @return void
     */
    public function setNameFirst(string $nameFirst = null)
    {
        $this->nameFirst = $nameFirst;
    }

    /**
     * Get the last name.
     *
     * @return string Last name.
     */
    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    /**
     * Set the last name.
     *
     * @param string|null $nameLast Last name.
     *
     * @return void
     */
    public function setNameLast(string $nameLast = null)
    {
        $this->nameLast = $nameLast;
    }

    /**
     * Get the company.
     *
     * @return string Company.
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * Set the company.
     *
     * @param string|null $company Company.
     *
     * @return void
     */
    public function setCompany(string $company = null)
    {
        $this->company = $company;
    }

    /**
     * Get the website.
     *
     * @return string Website.
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * Set the website.
     *
     * @param string|null $website Website.
     *
     * @return void
     */
    public function setWebsite(string $website = null)
    {
        $this->website = $website;
    }

    /**
     * Get the street address.
     *
     * @return string Street address.
     */
    public function getAddressStreet(): ?string
    {
        return $this->addressStreet;
    }

    /**
     * Set the street address.
     *
     * @param string|null $addressStreet Street address.
     *
     * @return void
     */
    public function setAddressStreet(string $addressStreet = null)
    {
        $this->addressStreet = $addressStreet;
    }

    /**
     * Get the suburb.
     *
     * @return string Suburb.
     */
    public function getAddressSuburb(): ?string
    {
        return $this->addressSuburb;
    }

    /**
     * Set the suburb.
     *
     * @param string|null $addressSuburb Suburb.
     *
     * @return void
     */
    public function setAddressSuburb(string $addressSuburb = null)
    {
        $this->addressSuburb = $addressSuburb;
    }

    /**
     * Get the city.
     *
     * @return string City.
     */
    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    /**
     * Set the city.
     *
     * @param string|null $addressCity City.
     *
     * @return void
     */
    public function setAddressCity(string $addressCity = null)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * Get the state.
     *
     * @return string State.
     */
    public function getAddressState(): ?string
    {
        return $this->addressState;
    }

    /**
     * Set the state.
     *
     * @param string|null $addressState State.
     *
     * @return void
     */
    public function setAddressState(string $addressState = null)
    {
        $this->addressState = $addressState;
    }

    /**
     * Get the country.
     *
     * @return string Country.
     */
    public function getAddressCountry(): ?string
    {
        return $this->addressCountry;
    }

    /**
     * Set the country.
     *
     * @param string|null $addressCountry Country.
     *
     * @return void
     */
    public function setAddressCountry(string $addressCountry = null)
    {
        $this->addressCountry = $addressCountry;
    }

    /**
     * Get the postcode.
     *
     * @return string Postcode.
     */
    public function getAddressPostcode(): ?string
    {
        return $this->addressPostcode;
    }

    /**
     * Set the postcode.
     *
     * @param string|null $addressPostcode Postcode.
     *
     * @return void
     */
    public function setAddressPostcode(string $addressPostcode = null)
    {
        $this->addressPostcode = $addressPostcode;
    }

    /**
     * Get the mobile phone number.
     *
     * @return string Mobile phone number.
     */
    public function getPhoneMobile(): ?string
    {
        return $this->phoneMobile;
    }

    /**
     * Set the mobile phone number.
     *
     * @param string|null $phoneMobile Mobile phone number.
     *
     * @return void
     */
    public function setPhoneMobile(string $phoneMobile = null)
    {
        $this->phoneMobile = $phoneMobile;
    }

    /**
     * Get the work phone number.
     *
     * @return string Work phone number.
     */
    public function getPhoneWork(): ?string
    {
        return $this->phoneWork;
    }

    /**
     * Set the work phone number.
     *
     * @param string|null $phoneWork Work phone number.
     *
     * @return void
     */
    public function setPhoneWork(string $phoneWork = null)
    {
        $this->phoneWork = $phoneWork;
    }

    /**
     * Get the password reset token.
     *
     * @return string Password reset token.
     */
    public function getPasswordReset(): ?string
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
    public function setPasswordReset(string $passwordReset = null)
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * Get the password reset token TTL.
     *
     * @return ?string Password reset token TTL.
     */
    public function getPasswordResetTtl(): ?string
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
    public function setPasswordResetTtl(string $passwordResetTtl = null)
    {
        $this->passwordResetTtl = $passwordResetTtl;
    }

    /**
     * Return the values as an associative array.
     *
     * @return array User.
     */
    public function dump(): array
    {
        return [
            'uid' => $this->uid,
            'active' => $this->active,
            'username' => $this->username,
            'hash' => $this->hash,
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
            'refreshToken' => $this->refreshToken,
        ];
    }
}
