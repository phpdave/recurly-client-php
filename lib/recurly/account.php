<?php
/*
 * Class Recurly_Account
 * @property string $adjustments The URL of adjustments for the specified account.
 * @property string $account_balance The URL of the account balance for the specified account.
 * @property string $billing_info The URL of billing info for the specified account.
 * @property string $invoices The URL of invoices for the specified account.
 * @property string $redemption The URL of the coupon redemption for the specified account.
 * @property string $subscriptions The URL of subscriptions for the specified account.
 * @property string $transactions The URL of transactions for the specified account.
 * @property string $account_code The unique identifier of the account.
 * @property string $state The state of accounts to return: active or closed.
 * @property string $username The username of the account.
 * @property string $email The email address of the account.
 * @property array of strings $cc_emails Additional email address that should receive account correspondence. These should be separated only by commas. These CC emails will receive all emails that the email field also receives.
 * @property string $first_name The first name of the account.
 * @property string $last_name The last name of the account.
 * @property string $company_name The company name of the account.
 * @property string $vat_number The VAT number of the account (to avoid having the VAT applied).
 * @property boolean $tax_exempt The tax status of the account. true exempts tax on the account, false applies tax on the account.
 * @property object $address The nested address information of the account: address1, address2, city, state, zip, country, phone.
 * @property string $accept_language The ISO 639-1 language code from the user's browser, indicating their preferred language and locale.
 * @property string $hosted_login_token The unique token for automatically logging the account in to the hosted management pages. You may automatically log the user into their hosted management pages by directing the user to: https://:subdomain.recurly.com/account/:hosted_login_token.
 * @property datetime $created_at The date and time the account was created in Recurly.
 * @property datetime $updated_at The date and time the account or its billing info was last updated.
 * @property datetime $closed_at For closed accounts, the date and time it was closed.
 */
class Recurly_Account extends Recurly_Resource
{
  protected static $_writeableAttributes;
  protected static $_requiredAttributes;

  function __construct($accountCode = null, $client = null) {
    parent::__construct(null, $client);
    if (!is_null($accountCode))
      $this->account_code = $accountCode;
    $this->address = new Recurly_Address();
  }

  public static function init()
  {
    Recurly_Account::$_writeableAttributes = array(
      'account_code','username','first_name','last_name','vat_number',
      'email','company_name','accept_language','billing_info','address',
      'tax_exempt','entity_use_code','cc_emails'
    );
    Recurly_Account::$_requiredAttributes = array(
      'account_code'
    );

  }

  public function &__get($key)
  {
    if ($key == 'redemption' && parent::__isset('redemptions')) {
      return new Recurly_Stub('redemption', $this->_href . "/redemption", $this->_client);
    } else {
      return parent::__get($key);
    }
  }

  public static function get($accountCode, $client = null) {
    return Recurly_Base::_get(Recurly_Account::uriForAccount($accountCode), $client);
  }

  public function create() {
    $this->_save(Recurly_Client::POST, Recurly_Client::PATH_ACCOUNTS);
  }
  public function update() {
    $this->_save(Recurly_Client::PUT, $this->uri());
  }

  public function close() {
    Recurly_Base::_delete($this->uri(), $this->_client);
    $this->state = 'closed';
  }
  public static function closeAccount($accountCode, $client = null) {
    return Recurly_Base::_delete(Recurly_Account::uriForAccount($accountCode), $client);
  }

  public function reopen() {
    $this->_save(Recurly_Client::PUT, $this->uri() . '/reopen');
  }
  public static function reopenAccount($accountCode, $client = null) {
    return Recurly_Base::_put(Recurly_Account::uriForAccount($accountCode) . '/reopen', $client);
  }

  protected function uri() {
    if (!empty($this->_href))
      return $this->getHref();
    else
      return Recurly_Account::uriForAccount($this->account_code);
  }
  protected static function uriForAccount($accountCode) {
    return Recurly_Client::PATH_ACCOUNTS . '/' . rawurlencode($accountCode);
  }

  protected function getNodeName() {
    return 'account';
  }
  protected function getWriteableAttributes() {
    return Recurly_Account::$_writeableAttributes;
  }
  protected function getRequiredAttributes() {
    return Recurly_Account::$_requiredAttributes;
  }
}

Recurly_Account::init();
