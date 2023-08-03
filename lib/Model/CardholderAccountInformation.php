<?php
/**
 * CardholderAccountInformation
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Datatrans API Reference
 *
 * Welcome to the Datatrans API reference. This document is meant to be used in combination with https://docs.datatrans.ch. All the parameters used in the curl and web samples are described here. Reach out to support@datatrans.ch if something is missing or unclear.  Last updated: 19.07.23 - 08:42 UTC  # Payment Process The following steps describe how transactions are processed with Datatrans. We separate payments in three categories: Customer-initiated payments, merchant-initiated payments and after the payment.  ## Customer Initiated Payments We have three integrations available: [Redirect](https://docs.datatrans.ch/docs/redirect-lightbox), [Lightbox](https://docs.datatrans.ch/docs/redirect-lightbox) and [Secure Fields](https://docs.datatrans.ch/docs/secure-fields).  ### Redirect & Lightbox - Send the required parameters to initialize a `transactionId` to the [init](#operation/init) endpoint. - Let the customer proceed with the payment by redirecting them to the correct link - or showing them your payment form.   - Redirect: Redirect the browser to the following URL structure     ```     https://pay.sandbox.datatrans.com/v1/start/transactionId     ```   - Lightbox: Load the JavaScript library and initialize the payment form:     ```js     <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/datatrans-2.0.0.js\">     ```     ```js     payButton.onclick = function() {       Datatrans.startPayment({         transactionId:  \"transactionId\"       });     };     ``` - Your customer proceeds with entering their payment information and finally hits the pay or continue button. - For card payments, we check the payment information with your acquirers. The acquirers check the payment information with the issuing parties. The customer proceeds with 3D Secure whenever required. - Once the transaction is completed, we return all relevant information to you (check our [Webhook section](#section/Webhook) for more details). The browser will be redirected to the success, cancel or error URL with our `datatransTrxId` in the response.  ### Secure Fields - Send the required parameters to initialize a transactionId to our [secureFieldsInit](#operation/secureFieldsInit) endpoint. - Load the Secure Fields JavaScript libarary and initialize Secure Fields:   ```js   <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/secure-fields-2.0.0.js\">   ```   ```js   var secureFields = new SecureFields();   secureFields.init(     {{transactionId}}, {         cardNumber: \"cardNumberPlaceholder\",         cvv: \"cvvPlaceholder\",     });   ``` - Handle the success event of the secureFields.submit() call. - If 3D authentication is required for a specific transaction, the `redirect` property inside the `data` object will indicate the URL that the customer needs to be redirected to. - Use the [Authorize an authenticated transaction](#operation/authorize-split)endpoint to fully authorize the Secure Fields transaction. This is required to finalize the authorization process with Secure Fields.  ## Merchant Initiated Payments Once you have processed a customer-initiated payment or registration you can call our API to process recurring payments. Check our [authorize](#operation/authorize) endpoint to see how to create a recurring payment or our [validate](#operation/validate) endpoint to validate your customers’ saved payment details.  ## After the payment Use the `transactionId` to check the [status](#operation/status) and to [settle](#operation/settle), [cancel](#operation/cancel) or [refund](#operation/credit) a transaction.  # Idempotency  To retry identical requests with the same effect without accidentally performing the same operation more than needed, you can add the header `Idempotency-Key` to your requests. This is useful when API calls are disrupted or you did not receive a response. In other words, retrying identical requests with our idempotency key will not have any side effects. We will return the same response for any identical request that includes the same idempotency key.  If your request failed to reach our servers, no idempotent result is saved because no API endpoint processed your request. In such cases, you can simply retry your operation safely. Idempotency keys remain stored for 60 minutes. After 60 minutes have passed, sending the same request together with the previous idempotency key will create a new operation.  Please note that the idempotency key has to be unique for each request and has to be defined by yourself. We recommend assigning a random value as your idempotency key and using UUID v4. Idempotency is only available for `POST` requests.  Idempotency was implemented according to the [\"The Idempotency HTTP Header Field\" Internet-Draft](https://tools.ietf.org/id/draft-idempotency-header-01.html)  |Scenario|Condition|Expectation| |:---|:---|:---| |First time request|Idempotency key has not been seen during the past 60 minutes.|The request is processed normally.| |Repeated request|The request was retried after the first time request completed.| The response from the first time request will be returned.| |Repeated request|The request was retried before the first time request completed.| 409 Conflict. It is recommended that clients time their retries using an exponential backoff algorithm.| |Repeated request|The request body is different than the one from the first time request.| 422 Unprocessable Entity.|  Example: ```sh curl -i 'https://api.sandbox.datatrans.com/v1/transactions' \\     -H 'Authorization: Basic MTEwMDAwNzI4MzpobDJST1NScUN2am5EVlJL' \\     -H 'Content-Type: application/json; charset=UTF-8' \\     -H 'Idempotency-Key: e75d621b-0e56-4b71-b889-1acec3e9d870' \\     -d '{     \"refno\" : \"58b389331dad\",     \"amount\" : 1000,     \"currency\" : \"CHF\",     \"paymentMethods\" : [ \"VIS\", \"ECA\", \"PAP\" ],     \"option\" : {        \"createAlias\" : true     } }' ```  # Authentication Authentication to the APIs is performed with HTTP basic authentication. Your `merchantId` acts as the username. To get the password, login to the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a> and navigate to the security settings under `UPP Administration > Security`.  Create a base64 encoded value consisting of merchantId and password (most HTTP clients are able to handle the base64 encoding automatically) and submit the Authorization header with your requests. Here’s an example:  ``` base64(merchantId:password) = MTAwMDAxMTAxMTpYMWVXNmkjJA== ```  ``` Authorization: Basic MTAwMDAxMTAxMTpYMWVXNmkjJA== ````  All API requests must be done over HTTPS with TLS >= 1.2.  # Errors Datatrans uses HTTP response codes to indicate if an API call was successful or resulted in a failure. HTTP `2xx` status codes indicate a successful API call whereas HTTP `4xx` status codes indicate client errors or if something with the transaction went wrong - for example a decline. In rare cases HTTP `5xx` status codes are returned. Those indicate errors on Datatrans side.  Here’s the payload of a sample HTTP `400` error, showing that your request has wrong values in it ``` {   \"error\" : {     \"code\" : \"INVALID_PROPERTY\",     \"message\" : \"init.initRequest.currency The given currency does not have the right format\"   } } ```  # Webhook After each authorization Datatrans tries to call the configured Webhook (POST) URL. The Webhook URL can be configured within the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a>. It is also possible to overwrite the configured webhook URL with the `init.webhook` property. The Webhook payload contains the same information as the response of a [Status API](#operation/status) call.  ## Webhook signing If you want your webhook requests to be signed, setup a HMAC key in your merchant configuration. To get your HMAC key, login to our dashboard and navigate to the Security settings in your merchant configuration to view your server to server security settings. Select the radio button `Important parameters will be digitally signed (HMAC-SHA256) and sent with payment messages`. Datatrans will use this key to sign the webhook payload and will add a `Datatrans-Signature` HTTP request header:  ```sh Datatrans-Signature: t=1559303131511,s0=33819a1220fd8e38fc5bad3f57ef31095fac0deb38c001ba347e694f48ffe2fc ```  On your server, calculate the signature of the webhook payload and finally compare it to `s0`. `timestamp` is the `t` value from the Datatrans-Signature header, `payload` represents all UTF-8 bytes from the body of the payload and finally `key` is the HMAC key you configured within the dashboard. If the value of `sign` is equal to `s0` from the `Datatrans-Signature` header, the webhook payload is valid and was not tampered.  **Java**  ```java // hex bytes of the key byte[] key = Hex.decodeHex(key);  // Create sign with timestamp and payload String algorithm = \"HmacSha256\"; SecretKeySpec macKey = new SecretKeySpec(key, algorithm); Mac mac = Mac.getInstance(algorithm); mac.init(macKey); mac.update(String.valueOf(timestamp).getBytes()); byte[] result = mac.doFinal(payload.getBytes()); String sign = Hex.encodeHexString(result); ```  **Python**  ```python # hex bytes of the key key_hex_bytes = bytes.fromhex(key)  # Create sign with timestamp and payload sign = hmac.new(key_hex_bytes, bytes(str(timestamp) + payload, 'utf-8'), hashlib.sha256) ```  # Release notes <details>   <summary>Details</summary>    ### 2.0.37 - 19.07.2023 - added `MPX` paycard number to the status API - added `airlineData` to the Authorize Split API - added wallet indicator in Alias Status response - added Alipay+ support - added documentation for Twint+ parameters - added support for ferry reservations for Klarna - added 3D2.2 feature `3RI` - added support for `MPA` and `MPG` - fixed bug in MCP handling - fixed the handling of `authorize.card.3D.threeDSTransactionId` - fixed Klarna subtype documentation for the Status API  ### 2.0.36 - 16.03.2023 - added `MBP` (MobilePay) payment method - added `uniqueRefno` handling to the `init` API   - if the unique refno feature is enabled the init does not accept duplicated refnos anymore. even if the redirect never happens. - added proper error mappings for various errors with code `UNKNOWN_ERROR`  ### 2.0.35 - 08.02.2023 - added `VPS` (Vipps) payment method - added `SWP` to the authorize API - added `imageUrl` to the `article` property for `KLN` - fixed wrong validation for the `marketplace` property - added proper error mappings for various errors with code `UNKNOWN_ERROR`  ### 2.0.34 - 12.12.2022 * added support for `accertify` * increase the maximum length of `refno` to 40 characters * refactor of `MCP` properties to support static MCP  ### 2.0.33 - 08.11.2022 * fixed the openapi specification   * renamed the models   * removed illegal characters from the specification * added validation to some 3D properties  ### 2.0.32 - 12.10.2022 * added different `card` types `PlainCard`, `AliasCard` and `NetworkTokenCard` for the `authorize` and `init` endpoint   * the old card type is still supported * fixed `webhook.url` for mobile flows * improved the API docs for `statusResponse.status`  ### 2.0.31 - 06.10.2022 * update API docs for `status.language` in the status response  ### 2.0.30 - 23.09.2022 * added `qrData` to `MPX` and `MFX` in the status API response * added support for `KLN` train reservations * added additional `airPlus` properties * added the `ELV` request properties to the API docs (init and authorize API) * fix `MCP` sample request/response examples in the api docs * fix date format issues for `airPlus` properties  ### 2.0.29 - 17.08.2022 * added `merchantId` to the status API response * added `SWH` (Swish) payment method * added `messageExtensions` to `init.card.3d` * added `authorizeResponse.card` to the API docs * added `GFT` (MFG Gift Card) payment method * added `CBL` (Cartes Bancaires) payment method * added `HPC` (Hipercard) payment method * added `airPlus` to the init API request * added more languages to the `init.language` API docs * cleaned up `order.article` property * extended the init flow to work also with tokenization mode * improved the api docs for the `credit` api * no `card` object is returned in the `alias` info response if the content is empty * fix the status api now also returns the `externalCode` for `INT` transactions * fix enrollment check in `init` api if `init.number` is set with plain card number * fix handle `airlineData` date format issues  ### 2.0.28 - 23.05.2022 * Added support to send a webhook URL along the init request. If set, it overwrites the POST URL configured in the dashboard.   * See `init.webhook` for more information.  ### 2.0.27 - 13.04.2022 * Added MCP support (Multi Currency Processing)   * Added new `GET /v1/multicurrency/rates` API to fetch the MCP rates.   * Added `init.mcp` property   * Added `authorize.mcp` property   * Added `mcp` property in the `status` response if available for the transaction  ### 2.0.26 - 16.03.2022 * Added the OpenAPI description for the `GET /v1/aliases/{alias}` response.  ### 2.0.25 - 02.03.2022 * New API `/v1/transactions/{transactionId}/increase` to increase the amount for an authorized transaction (credit cards only).  ### 2.0.24 - 15.12.2021 🎄 * Added full support for `invoiceOnDelivery` when using `MFX` or `MPX` as payment method. * The Status API now returns the ESR data for `MFX` and `MPX` when `invoiceOnDelivery=true` was used.  ### 2.0.23 - 20.10.2021 * Added support for Klarna `KLN` hotel extended merchant data (EMD)  ### 2.0.22 - 21.07.2021 * Added full support for Swisscom Pay `ESY` * The `marketplace` object now accepts an array of splits.  ### 2.0.21 - 21.05.2021 * Updated idempotency handling. See the details here https://api-reference.datatrans.ch/#section/Idempotency  ### 2.0.20 - 18.05.2021 * In addition to `debit` and `credit` the Status API now also returns `prepaid` in the `card.info.type` property. * paysafecard - Added support for `merchantClientId`   ### 2.0.19 - 03.05.2021 * Fixed `PAP.orderTransactionId` to be a string * Added support for `PAP.fraudSessionId` (PayPal FraudNet)  ### 2.0.18 - 21.04.2021 * Added new `POST /v1/transactions/screen` API to check a customer's credit score before sending an actual authorization request. Currently only `INT` (Byjuno) is supported.  ### 2.0.17 - 20.04.2021 * Added new `GET /v1/aliases` API to receive more information about a particular alias.  ### 2.0.16 - 13.04.2021 * Added support for Migros Bank E-Pay <code>MDP</code>  ### 2.0.15 - 24.03.2021 * Byjuno - renamed `subPaymentMethod` to `subtype` (`subPaymentMethod` still works) * Klarna - Returning the `subtype` (`pay_now`, `pay_later`, `pay_over_time`, `direct_debit`, `direct_bank_transfer`) from the Status API  ### 2.0.14 - 09.03.2021 * Byjuno - Added support for `customData` and `firstRateAmount` * Returning the `transactionId` (if available) for a failed Refund API call.  ### 2.0.13 - 15.02.2021 * The Status and Webhook payloads now include the `language` property * Fixed a bug where `card.3D.transStatusReason` and `card.3D.cardholderInfo` was not returned  ### 2.0.12 - 04.02.2021 * Added support for PayPal transaction context (STC) * Fixed a bug where the transaction status did not switch to `failed` after it timed out * Fixed a bug with `option.rememberMe` not returning the Alias from the Status API  ### 2.0.11 - 01.02.2021 * Returning `card.3D.transStatusReason` (if available) from the Status API  ### 2.0.10 - 18.01.2021 * Returning `card.3D.cardholderInfo` (if available) from the Status API  ### 2.0.9 - 21.12.2020 * Added support for Alipay <code>ALP</code>  ### 2.0.8 - 21.12.2020 * Added full support for Klarna <code>KLN</code> * Added support for swissbilling <code>SWB</code>  </details>
 *
 * OpenAPI spec version: 2.0.37
 * Contact: support@datatrans.ch
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.46
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * CardholderAccountInformation Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CardholderAccountInformation implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CardholderAccountInformation';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'ch_acc_date' => '\DateTime',
        'ch_acc_change_ind' => 'string',
        'ch_acc_change' => '\DateTime',
        'ch_acc_pw_change_ind' => 'string',
        'ch_acc_pw_change' => '\DateTime',
        'ship_address_usage_ind' => 'string',
        'ship_address_usage' => '\DateTime',
        'txn_activity_day' => 'int',
        'txn_activity_year' => 'int',
        'provision_attempts_day' => 'int',
        'nb_purchase_account' => 'int',
        'suspicious_acc_activity' => 'string',
        'ship_name_indicator' => 'string',
        'payment_acc_ind' => 'string',
        'payment_acc_age' => '\DateTime'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'ch_acc_date' => 'date-time',
        'ch_acc_change_ind' => null,
        'ch_acc_change' => 'date-time',
        'ch_acc_pw_change_ind' => null,
        'ch_acc_pw_change' => 'date-time',
        'ship_address_usage_ind' => null,
        'ship_address_usage' => 'date-time',
        'txn_activity_day' => 'int32',
        'txn_activity_year' => 'int32',
        'provision_attempts_day' => 'int32',
        'nb_purchase_account' => 'int32',
        'suspicious_acc_activity' => null,
        'ship_name_indicator' => null,
        'payment_acc_ind' => null,
        'payment_acc_age' => 'date-time'
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'ch_acc_date' => 'chAccDate',
        'ch_acc_change_ind' => 'chAccChangeInd',
        'ch_acc_change' => 'chAccChange',
        'ch_acc_pw_change_ind' => 'chAccPwChangeInd',
        'ch_acc_pw_change' => 'chAccPwChange',
        'ship_address_usage_ind' => 'shipAddressUsageInd',
        'ship_address_usage' => 'shipAddressUsage',
        'txn_activity_day' => 'txnActivityDay',
        'txn_activity_year' => 'txnActivityYear',
        'provision_attempts_day' => 'provisionAttemptsDay',
        'nb_purchase_account' => 'nbPurchaseAccount',
        'suspicious_acc_activity' => 'suspiciousAccActivity',
        'ship_name_indicator' => 'shipNameIndicator',
        'payment_acc_ind' => 'paymentAccInd',
        'payment_acc_age' => 'paymentAccAge'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'ch_acc_date' => 'setChAccDate',
        'ch_acc_change_ind' => 'setChAccChangeInd',
        'ch_acc_change' => 'setChAccChange',
        'ch_acc_pw_change_ind' => 'setChAccPwChangeInd',
        'ch_acc_pw_change' => 'setChAccPwChange',
        'ship_address_usage_ind' => 'setShipAddressUsageInd',
        'ship_address_usage' => 'setShipAddressUsage',
        'txn_activity_day' => 'setTxnActivityDay',
        'txn_activity_year' => 'setTxnActivityYear',
        'provision_attempts_day' => 'setProvisionAttemptsDay',
        'nb_purchase_account' => 'setNbPurchaseAccount',
        'suspicious_acc_activity' => 'setSuspiciousAccActivity',
        'ship_name_indicator' => 'setShipNameIndicator',
        'payment_acc_ind' => 'setPaymentAccInd',
        'payment_acc_age' => 'setPaymentAccAge'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'ch_acc_date' => 'getChAccDate',
        'ch_acc_change_ind' => 'getChAccChangeInd',
        'ch_acc_change' => 'getChAccChange',
        'ch_acc_pw_change_ind' => 'getChAccPwChangeInd',
        'ch_acc_pw_change' => 'getChAccPwChange',
        'ship_address_usage_ind' => 'getShipAddressUsageInd',
        'ship_address_usage' => 'getShipAddressUsage',
        'txn_activity_day' => 'getTxnActivityDay',
        'txn_activity_year' => 'getTxnActivityYear',
        'provision_attempts_day' => 'getProvisionAttemptsDay',
        'nb_purchase_account' => 'getNbPurchaseAccount',
        'suspicious_acc_activity' => 'getSuspiciousAccActivity',
        'ship_name_indicator' => 'getShipNameIndicator',
        'payment_acc_ind' => 'getPaymentAccInd',
        'payment_acc_age' => 'getPaymentAccAge'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    const CH_ACC_CHANGE_IND__01 = '01';
    const CH_ACC_CHANGE_IND__02 = '02';
    const CH_ACC_CHANGE_IND__03 = '03';
    const CH_ACC_CHANGE_IND__04 = '04';
    const CH_ACC_PW_CHANGE_IND__01 = '01';
    const CH_ACC_PW_CHANGE_IND__02 = '02';
    const CH_ACC_PW_CHANGE_IND__03 = '03';
    const CH_ACC_PW_CHANGE_IND__04 = '04';
    const CH_ACC_PW_CHANGE_IND__05 = '05';
    const SHIP_ADDRESS_USAGE_IND__01 = '01';
    const SHIP_ADDRESS_USAGE_IND__02 = '02';
    const SHIP_ADDRESS_USAGE_IND__03 = '03';
    const SHIP_ADDRESS_USAGE_IND__04 = '04';
    const SUSPICIOUS_ACC_ACTIVITY__01 = '01';
    const SUSPICIOUS_ACC_ACTIVITY__02 = '02';
    const SHIP_NAME_INDICATOR__01 = '01';
    const SHIP_NAME_INDICATOR__02 = '02';
    const PAYMENT_ACC_IND__01 = '01';
    const PAYMENT_ACC_IND__02 = '02';
    const PAYMENT_ACC_IND__03 = '03';
    const PAYMENT_ACC_IND__04 = '04';
    const PAYMENT_ACC_IND__05 = '05';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getChAccChangeIndAllowableValues()
    {
        return [
            self::CH_ACC_CHANGE_IND__01,
            self::CH_ACC_CHANGE_IND__02,
            self::CH_ACC_CHANGE_IND__03,
            self::CH_ACC_CHANGE_IND__04,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getChAccPwChangeIndAllowableValues()
    {
        return [
            self::CH_ACC_PW_CHANGE_IND__01,
            self::CH_ACC_PW_CHANGE_IND__02,
            self::CH_ACC_PW_CHANGE_IND__03,
            self::CH_ACC_PW_CHANGE_IND__04,
            self::CH_ACC_PW_CHANGE_IND__05,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getShipAddressUsageIndAllowableValues()
    {
        return [
            self::SHIP_ADDRESS_USAGE_IND__01,
            self::SHIP_ADDRESS_USAGE_IND__02,
            self::SHIP_ADDRESS_USAGE_IND__03,
            self::SHIP_ADDRESS_USAGE_IND__04,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSuspiciousAccActivityAllowableValues()
    {
        return [
            self::SUSPICIOUS_ACC_ACTIVITY__01,
            self::SUSPICIOUS_ACC_ACTIVITY__02,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getShipNameIndicatorAllowableValues()
    {
        return [
            self::SHIP_NAME_INDICATOR__01,
            self::SHIP_NAME_INDICATOR__02,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPaymentAccIndAllowableValues()
    {
        return [
            self::PAYMENT_ACC_IND__01,
            self::PAYMENT_ACC_IND__02,
            self::PAYMENT_ACC_IND__03,
            self::PAYMENT_ACC_IND__04,
            self::PAYMENT_ACC_IND__05,
        ];
    }

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['ch_acc_date'] = isset($data['ch_acc_date']) ? $data['ch_acc_date'] : null;
        $this->container['ch_acc_change_ind'] = isset($data['ch_acc_change_ind']) ? $data['ch_acc_change_ind'] : null;
        $this->container['ch_acc_change'] = isset($data['ch_acc_change']) ? $data['ch_acc_change'] : null;
        $this->container['ch_acc_pw_change_ind'] = isset($data['ch_acc_pw_change_ind']) ? $data['ch_acc_pw_change_ind'] : null;
        $this->container['ch_acc_pw_change'] = isset($data['ch_acc_pw_change']) ? $data['ch_acc_pw_change'] : null;
        $this->container['ship_address_usage_ind'] = isset($data['ship_address_usage_ind']) ? $data['ship_address_usage_ind'] : null;
        $this->container['ship_address_usage'] = isset($data['ship_address_usage']) ? $data['ship_address_usage'] : null;
        $this->container['txn_activity_day'] = isset($data['txn_activity_day']) ? $data['txn_activity_day'] : null;
        $this->container['txn_activity_year'] = isset($data['txn_activity_year']) ? $data['txn_activity_year'] : null;
        $this->container['provision_attempts_day'] = isset($data['provision_attempts_day']) ? $data['provision_attempts_day'] : null;
        $this->container['nb_purchase_account'] = isset($data['nb_purchase_account']) ? $data['nb_purchase_account'] : null;
        $this->container['suspicious_acc_activity'] = isset($data['suspicious_acc_activity']) ? $data['suspicious_acc_activity'] : null;
        $this->container['ship_name_indicator'] = isset($data['ship_name_indicator']) ? $data['ship_name_indicator'] : null;
        $this->container['payment_acc_ind'] = isset($data['payment_acc_ind']) ? $data['payment_acc_ind'] : null;
        $this->container['payment_acc_age'] = isset($data['payment_acc_age']) ? $data['payment_acc_age'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getChAccChangeIndAllowableValues();
        if (!is_null($this->container['ch_acc_change_ind']) && !in_array($this->container['ch_acc_change_ind'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'ch_acc_change_ind', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getChAccPwChangeIndAllowableValues();
        if (!is_null($this->container['ch_acc_pw_change_ind']) && !in_array($this->container['ch_acc_pw_change_ind'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'ch_acc_pw_change_ind', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getShipAddressUsageIndAllowableValues();
        if (!is_null($this->container['ship_address_usage_ind']) && !in_array($this->container['ship_address_usage_ind'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'ship_address_usage_ind', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getSuspiciousAccActivityAllowableValues();
        if (!is_null($this->container['suspicious_acc_activity']) && !in_array($this->container['suspicious_acc_activity'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'suspicious_acc_activity', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getShipNameIndicatorAllowableValues();
        if (!is_null($this->container['ship_name_indicator']) && !in_array($this->container['ship_name_indicator'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'ship_name_indicator', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getPaymentAccIndAllowableValues();
        if (!is_null($this->container['payment_acc_ind']) && !in_array($this->container['payment_acc_ind'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'payment_acc_ind', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets ch_acc_date
     *
     * @return \DateTime
     */
    public function getChAccDate()
    {
        return $this->container['ch_acc_date'];
    }

    /**
     * Sets ch_acc_date
     *
     * @param \DateTime $ch_acc_date ch_acc_date
     *
     * @return $this
     */
    public function setChAccDate($ch_acc_date)
    {
        $this->container['ch_acc_date'] = $ch_acc_date;

        return $this;
    }

    /**
     * Gets ch_acc_change_ind
     *
     * @return string
     */
    public function getChAccChangeInd()
    {
        return $this->container['ch_acc_change_ind'];
    }

    /**
     * Sets ch_acc_change_ind
     *
     * @param string $ch_acc_change_ind ch_acc_change_ind
     *
     * @return $this
     */
    public function setChAccChangeInd($ch_acc_change_ind)
    {
        $allowedValues = $this->getChAccChangeIndAllowableValues();
        if (!is_null($ch_acc_change_ind) && !in_array($ch_acc_change_ind, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'ch_acc_change_ind', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['ch_acc_change_ind'] = $ch_acc_change_ind;

        return $this;
    }

    /**
     * Gets ch_acc_change
     *
     * @return \DateTime
     */
    public function getChAccChange()
    {
        return $this->container['ch_acc_change'];
    }

    /**
     * Sets ch_acc_change
     *
     * @param \DateTime $ch_acc_change ch_acc_change
     *
     * @return $this
     */
    public function setChAccChange($ch_acc_change)
    {
        $this->container['ch_acc_change'] = $ch_acc_change;

        return $this;
    }

    /**
     * Gets ch_acc_pw_change_ind
     *
     * @return string
     */
    public function getChAccPwChangeInd()
    {
        return $this->container['ch_acc_pw_change_ind'];
    }

    /**
     * Sets ch_acc_pw_change_ind
     *
     * @param string $ch_acc_pw_change_ind ch_acc_pw_change_ind
     *
     * @return $this
     */
    public function setChAccPwChangeInd($ch_acc_pw_change_ind)
    {
        $allowedValues = $this->getChAccPwChangeIndAllowableValues();
        if (!is_null($ch_acc_pw_change_ind) && !in_array($ch_acc_pw_change_ind, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'ch_acc_pw_change_ind', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['ch_acc_pw_change_ind'] = $ch_acc_pw_change_ind;

        return $this;
    }

    /**
     * Gets ch_acc_pw_change
     *
     * @return \DateTime
     */
    public function getChAccPwChange()
    {
        return $this->container['ch_acc_pw_change'];
    }

    /**
     * Sets ch_acc_pw_change
     *
     * @param \DateTime $ch_acc_pw_change ch_acc_pw_change
     *
     * @return $this
     */
    public function setChAccPwChange($ch_acc_pw_change)
    {
        $this->container['ch_acc_pw_change'] = $ch_acc_pw_change;

        return $this;
    }

    /**
     * Gets ship_address_usage_ind
     *
     * @return string
     */
    public function getShipAddressUsageInd()
    {
        return $this->container['ship_address_usage_ind'];
    }

    /**
     * Sets ship_address_usage_ind
     *
     * @param string $ship_address_usage_ind ship_address_usage_ind
     *
     * @return $this
     */
    public function setShipAddressUsageInd($ship_address_usage_ind)
    {
        $allowedValues = $this->getShipAddressUsageIndAllowableValues();
        if (!is_null($ship_address_usage_ind) && !in_array($ship_address_usage_ind, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'ship_address_usage_ind', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['ship_address_usage_ind'] = $ship_address_usage_ind;

        return $this;
    }

    /**
     * Gets ship_address_usage
     *
     * @return \DateTime
     */
    public function getShipAddressUsage()
    {
        return $this->container['ship_address_usage'];
    }

    /**
     * Sets ship_address_usage
     *
     * @param \DateTime $ship_address_usage ship_address_usage
     *
     * @return $this
     */
    public function setShipAddressUsage($ship_address_usage)
    {
        $this->container['ship_address_usage'] = $ship_address_usage;

        return $this;
    }

    /**
     * Gets txn_activity_day
     *
     * @return int
     */
    public function getTxnActivityDay()
    {
        return $this->container['txn_activity_day'];
    }

    /**
     * Sets txn_activity_day
     *
     * @param int $txn_activity_day txn_activity_day
     *
     * @return $this
     */
    public function setTxnActivityDay($txn_activity_day)
    {
        $this->container['txn_activity_day'] = $txn_activity_day;

        return $this;
    }

    /**
     * Gets txn_activity_year
     *
     * @return int
     */
    public function getTxnActivityYear()
    {
        return $this->container['txn_activity_year'];
    }

    /**
     * Sets txn_activity_year
     *
     * @param int $txn_activity_year txn_activity_year
     *
     * @return $this
     */
    public function setTxnActivityYear($txn_activity_year)
    {
        $this->container['txn_activity_year'] = $txn_activity_year;

        return $this;
    }

    /**
     * Gets provision_attempts_day
     *
     * @return int
     */
    public function getProvisionAttemptsDay()
    {
        return $this->container['provision_attempts_day'];
    }

    /**
     * Sets provision_attempts_day
     *
     * @param int $provision_attempts_day provision_attempts_day
     *
     * @return $this
     */
    public function setProvisionAttemptsDay($provision_attempts_day)
    {
        $this->container['provision_attempts_day'] = $provision_attempts_day;

        return $this;
    }

    /**
     * Gets nb_purchase_account
     *
     * @return int
     */
    public function getNbPurchaseAccount()
    {
        return $this->container['nb_purchase_account'];
    }

    /**
     * Sets nb_purchase_account
     *
     * @param int $nb_purchase_account nb_purchase_account
     *
     * @return $this
     */
    public function setNbPurchaseAccount($nb_purchase_account)
    {
        $this->container['nb_purchase_account'] = $nb_purchase_account;

        return $this;
    }

    /**
     * Gets suspicious_acc_activity
     *
     * @return string
     */
    public function getSuspiciousAccActivity()
    {
        return $this->container['suspicious_acc_activity'];
    }

    /**
     * Sets suspicious_acc_activity
     *
     * @param string $suspicious_acc_activity suspicious_acc_activity
     *
     * @return $this
     */
    public function setSuspiciousAccActivity($suspicious_acc_activity)
    {
        $allowedValues = $this->getSuspiciousAccActivityAllowableValues();
        if (!is_null($suspicious_acc_activity) && !in_array($suspicious_acc_activity, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'suspicious_acc_activity', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['suspicious_acc_activity'] = $suspicious_acc_activity;

        return $this;
    }

    /**
     * Gets ship_name_indicator
     *
     * @return string
     */
    public function getShipNameIndicator()
    {
        return $this->container['ship_name_indicator'];
    }

    /**
     * Sets ship_name_indicator
     *
     * @param string $ship_name_indicator ship_name_indicator
     *
     * @return $this
     */
    public function setShipNameIndicator($ship_name_indicator)
    {
        $allowedValues = $this->getShipNameIndicatorAllowableValues();
        if (!is_null($ship_name_indicator) && !in_array($ship_name_indicator, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'ship_name_indicator', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['ship_name_indicator'] = $ship_name_indicator;

        return $this;
    }

    /**
     * Gets payment_acc_ind
     *
     * @return string
     */
    public function getPaymentAccInd()
    {
        return $this->container['payment_acc_ind'];
    }

    /**
     * Sets payment_acc_ind
     *
     * @param string $payment_acc_ind payment_acc_ind
     *
     * @return $this
     */
    public function setPaymentAccInd($payment_acc_ind)
    {
        $allowedValues = $this->getPaymentAccIndAllowableValues();
        if (!is_null($payment_acc_ind) && !in_array($payment_acc_ind, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'payment_acc_ind', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['payment_acc_ind'] = $payment_acc_ind;

        return $this;
    }

    /**
     * Gets payment_acc_age
     *
     * @return \DateTime
     */
    public function getPaymentAccAge()
    {
        return $this->container['payment_acc_age'];
    }

    /**
     * Sets payment_acc_age
     *
     * @param \DateTime $payment_acc_age payment_acc_age
     *
     * @return $this
     */
    public function setPaymentAccAge($payment_acc_age)
    {
        $this->container['payment_acc_age'] = $payment_acc_age;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
