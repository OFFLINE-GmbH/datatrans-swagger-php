<?php
/**
 * StatusResponse
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
 * StatusResponse Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class StatusResponse implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'StatusResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'transaction_id' => 'string',
        'merchant_id' => 'string',
        'type' => 'string',
        'status' => 'string',
        'currency' => 'string',
        'refno' => 'string',
        'refno2' => 'string',
        'payment_method' => 'string',
        'detail' => '\Swagger\Client\Model\Detail',
        'customer' => '\Swagger\Client\Model\Customer',
        'cdm' => '\Swagger\Client\Model\CDMResponse',
        'accertify' => '\Swagger\Client\Model\Accertify',
        'language' => 'string',
        'card' => '\Swagger\Client\Model\StatusCardDetail',
        'twi' => '\Swagger\Client\Model\TwintDetail',
        'pap' => '\Swagger\Client\Model\PayPalDetail',
        'rek' => '\Swagger\Client\Model\RekaDetail',
        'elv' => '\Swagger\Client\Model\ElvDetail',
        'kln' => '\Swagger\Client\Model\KlarnaDetail',
        'int' => '\Swagger\Client\Model\ByjunoDetail',
        'swp' => '\Swagger\Client\Model\SwissPassDetail',
        'mpg' => '\Swagger\Client\Model\MPGDetail',
        'mfx' => '\Swagger\Client\Model\MFXDetail',
        'mpx' => '\Swagger\Client\Model\MPXDetail',
        'mdp' => '\Swagger\Client\Model\MDPDetail',
        'esy' => '\Swagger\Client\Model\SwisscomPayDetail',
        'pfc' => '\Swagger\Client\Model\PostfinanceDetail',
        'wec' => '\Swagger\Client\Model\WeChatDetail',
        'scx' => '\Swagger\Client\Model\SuperCard',
        'history' => '\Swagger\Client\Model\Action[]',
        'ep2' => '\Swagger\Client\Model\Ep2',
        'dcc' => '\Swagger\Client\Model\Dcc',
        'multi_currency_processing' => '\Swagger\Client\Model\MultiCurrencyProcessing'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'transaction_id' => null,
        'merchant_id' => null,
        'type' => null,
        'status' => null,
        'currency' => null,
        'refno' => null,
        'refno2' => null,
        'payment_method' => null,
        'detail' => null,
        'customer' => null,
        'cdm' => null,
        'accertify' => null,
        'language' => null,
        'card' => null,
        'twi' => null,
        'pap' => null,
        'rek' => null,
        'elv' => null,
        'kln' => null,
        'int' => null,
        'swp' => null,
        'mpg' => null,
        'mfx' => null,
        'mpx' => null,
        'mdp' => null,
        'esy' => null,
        'pfc' => null,
        'wec' => null,
        'scx' => null,
        'history' => null,
        'ep2' => null,
        'dcc' => null,
        'multi_currency_processing' => null
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
        'transaction_id' => 'transactionId',
        'merchant_id' => 'merchantId',
        'type' => 'type',
        'status' => 'status',
        'currency' => 'currency',
        'refno' => 'refno',
        'refno2' => 'refno2',
        'payment_method' => 'paymentMethod',
        'detail' => 'detail',
        'customer' => 'customer',
        'cdm' => 'cdm',
        'accertify' => 'accertify',
        'language' => 'language',
        'card' => 'card',
        'twi' => 'TWI',
        'pap' => 'PAP',
        'rek' => 'REK',
        'elv' => 'ELV',
        'kln' => 'KLN',
        'int' => 'INT',
        'swp' => 'SWP',
        'mpg' => 'MPG',
        'mfx' => 'MFX',
        'mpx' => 'MPX',
        'mdp' => 'MDP',
        'esy' => 'ESY',
        'pfc' => 'PFC',
        'wec' => 'WEC',
        'scx' => 'SCX',
        'history' => 'history',
        'ep2' => 'ep2',
        'dcc' => 'dcc',
        'multi_currency_processing' => 'multiCurrencyProcessing'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'transaction_id' => 'setTransactionId',
        'merchant_id' => 'setMerchantId',
        'type' => 'setType',
        'status' => 'setStatus',
        'currency' => 'setCurrency',
        'refno' => 'setRefno',
        'refno2' => 'setRefno2',
        'payment_method' => 'setPaymentMethod',
        'detail' => 'setDetail',
        'customer' => 'setCustomer',
        'cdm' => 'setCdm',
        'accertify' => 'setAccertify',
        'language' => 'setLanguage',
        'card' => 'setCard',
        'twi' => 'setTwi',
        'pap' => 'setPap',
        'rek' => 'setRek',
        'elv' => 'setElv',
        'kln' => 'setKln',
        'int' => 'setInt',
        'swp' => 'setSwp',
        'mpg' => 'setMpg',
        'mfx' => 'setMfx',
        'mpx' => 'setMpx',
        'mdp' => 'setMdp',
        'esy' => 'setEsy',
        'pfc' => 'setPfc',
        'wec' => 'setWec',
        'scx' => 'setScx',
        'history' => 'setHistory',
        'ep2' => 'setEp2',
        'dcc' => 'setDcc',
        'multi_currency_processing' => 'setMultiCurrencyProcessing'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'transaction_id' => 'getTransactionId',
        'merchant_id' => 'getMerchantId',
        'type' => 'getType',
        'status' => 'getStatus',
        'currency' => 'getCurrency',
        'refno' => 'getRefno',
        'refno2' => 'getRefno2',
        'payment_method' => 'getPaymentMethod',
        'detail' => 'getDetail',
        'customer' => 'getCustomer',
        'cdm' => 'getCdm',
        'accertify' => 'getAccertify',
        'language' => 'getLanguage',
        'card' => 'getCard',
        'twi' => 'getTwi',
        'pap' => 'getPap',
        'rek' => 'getRek',
        'elv' => 'getElv',
        'kln' => 'getKln',
        'int' => 'getInt',
        'swp' => 'getSwp',
        'mpg' => 'getMpg',
        'mfx' => 'getMfx',
        'mpx' => 'getMpx',
        'mdp' => 'getMdp',
        'esy' => 'getEsy',
        'pfc' => 'getPfc',
        'wec' => 'getWec',
        'scx' => 'getScx',
        'history' => 'getHistory',
        'ep2' => 'getEp2',
        'dcc' => 'getDcc',
        'multi_currency_processing' => 'getMultiCurrencyProcessing'
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

    const TYPE_PAYMENT = 'payment';
    const TYPE_CREDIT = 'credit';
    const TYPE_CARD_CHECK = 'card_check';
    const STATUS_INITIALIZED = 'initialized';
    const STATUS_CHALLENGE_REQUIRED = 'challenge_required';
    const STATUS_CHALLENGE_ONGOING = 'challenge_ongoing';
    const STATUS_AUTHENTICATED = 'authenticated';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_SETTLED = 'settled';
    const STATUS_CANCELED = 'canceled';
    const STATUS_TRANSMITTED = 'transmitted';
    const STATUS_FAILED = 'failed';
    const PAYMENT_METHOD_ACC = 'ACC';
    const PAYMENT_METHOD_ALP = 'ALP';
    const PAYMENT_METHOD_APL = 'APL';
    const PAYMENT_METHOD_AMX = 'AMX';
    const PAYMENT_METHOD_AZP = 'AZP';
    const PAYMENT_METHOD_BAC = 'BAC';
    const PAYMENT_METHOD_BON = 'BON';
    const PAYMENT_METHOD_CBL = 'CBL';
    const PAYMENT_METHOD_CFY = 'CFY';
    const PAYMENT_METHOD_CSY = 'CSY';
    const PAYMENT_METHOD_CUP = 'CUP';
    const PAYMENT_METHOD_DEA = 'DEA';
    const PAYMENT_METHOD_DIN = 'DIN';
    const PAYMENT_METHOD_DII = 'DII';
    const PAYMENT_METHOD_DIB = 'DIB';
    const PAYMENT_METHOD_DIS = 'DIS';
    const PAYMENT_METHOD_DNK = 'DNK';
    const PAYMENT_METHOD_ECA = 'ECA';
    const PAYMENT_METHOD_ELV = 'ELV';
    const PAYMENT_METHOD_EPS = 'EPS';
    const PAYMENT_METHOD_ESY = 'ESY';
    const PAYMENT_METHOD_GFT = 'GFT';
    const PAYMENT_METHOD_GPA = 'GPA';
    const PAYMENT_METHOD_HPC = 'HPC';
    const PAYMENT_METHOD_INT = 'INT';
    const PAYMENT_METHOD_JCB = 'JCB';
    const PAYMENT_METHOD_JEL = 'JEL';
    const PAYMENT_METHOD_KLN = 'KLN';
    const PAYMENT_METHOD_MAU = 'MAU';
    const PAYMENT_METHOD_MDP = 'MDP';
    const PAYMENT_METHOD_MFA = 'MFA';
    const PAYMENT_METHOD_MFX = 'MFX';
    const PAYMENT_METHOD_MPA = 'MPA';
    const PAYMENT_METHOD_MFG = 'MFG';
    const PAYMENT_METHOD_MPG = 'MPG';
    const PAYMENT_METHOD_MPX = 'MPX';
    const PAYMENT_METHOD_MYO = 'MYO';
    const PAYMENT_METHOD_PAP = 'PAP';
    const PAYMENT_METHOD_PAY = 'PAY';
    const PAYMENT_METHOD_PEF = 'PEF';
    const PAYMENT_METHOD_PFC = 'PFC';
    const PAYMENT_METHOD_PSC = 'PSC';
    const PAYMENT_METHOD_REK = 'REK';
    const PAYMENT_METHOD_SAM = 'SAM';
    const PAYMENT_METHOD_SWB = 'SWB';
    const PAYMENT_METHOD_SCX = 'SCX';
    const PAYMENT_METHOD_SWP = 'SWP';
    const PAYMENT_METHOD_TWI = 'TWI';
    const PAYMENT_METHOD_UAP = 'UAP';
    const PAYMENT_METHOD_VIS = 'VIS';
    const PAYMENT_METHOD_WEC = 'WEC';
    const PAYMENT_METHOD_SWH = 'SWH';
    const PAYMENT_METHOD_VPS = 'VPS';
    const PAYMENT_METHOD_MBP = 'MBP';
    const PAYMENT_METHOD_GEP = 'GEP';
    const LANGUAGE_EN = 'en';
    const LANGUAGE_DE = 'de';
    const LANGUAGE_FR = 'fr';
    const LANGUAGE_IT = 'it';
    const LANGUAGE_ES = 'es';
    const LANGUAGE_EL = 'el';
    const LANGUAGE_FI = 'fi';
    const LANGUAGE_HU = 'hu';
    const LANGUAGE_KO = 'ko';
    const LANGUAGE_NL = 'nl';
    const LANGUAGE_NO = 'no';
    const LANGUAGE_DA = 'da';
    const LANGUAGE_PL = 'pl';
    const LANGUAGE_PT = 'pt';
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_JA = 'ja';
    const LANGUAGE_SK = 'sk';
    const LANGUAGE_SL = 'sl';
    const LANGUAGE_SV = 'sv';
    const LANGUAGE_TR = 'tr';
    const LANGUAGE_ZH = 'zh';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTypeAllowableValues()
    {
        return [
            self::TYPE_PAYMENT,
            self::TYPE_CREDIT,
            self::TYPE_CARD_CHECK,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatusAllowableValues()
    {
        return [
            self::STATUS_INITIALIZED,
            self::STATUS_CHALLENGE_REQUIRED,
            self::STATUS_CHALLENGE_ONGOING,
            self::STATUS_AUTHENTICATED,
            self::STATUS_AUTHORIZED,
            self::STATUS_SETTLED,
            self::STATUS_CANCELED,
            self::STATUS_TRANSMITTED,
            self::STATUS_FAILED,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPaymentMethodAllowableValues()
    {
        return [
            self::PAYMENT_METHOD_ACC,
            self::PAYMENT_METHOD_ALP,
            self::PAYMENT_METHOD_APL,
            self::PAYMENT_METHOD_AMX,
            self::PAYMENT_METHOD_AZP,
            self::PAYMENT_METHOD_BAC,
            self::PAYMENT_METHOD_BON,
            self::PAYMENT_METHOD_CBL,
            self::PAYMENT_METHOD_CFY,
            self::PAYMENT_METHOD_CSY,
            self::PAYMENT_METHOD_CUP,
            self::PAYMENT_METHOD_DEA,
            self::PAYMENT_METHOD_DIN,
            self::PAYMENT_METHOD_DII,
            self::PAYMENT_METHOD_DIB,
            self::PAYMENT_METHOD_DIS,
            self::PAYMENT_METHOD_DNK,
            self::PAYMENT_METHOD_ECA,
            self::PAYMENT_METHOD_ELV,
            self::PAYMENT_METHOD_EPS,
            self::PAYMENT_METHOD_ESY,
            self::PAYMENT_METHOD_GFT,
            self::PAYMENT_METHOD_GPA,
            self::PAYMENT_METHOD_HPC,
            self::PAYMENT_METHOD_INT,
            self::PAYMENT_METHOD_JCB,
            self::PAYMENT_METHOD_JEL,
            self::PAYMENT_METHOD_KLN,
            self::PAYMENT_METHOD_MAU,
            self::PAYMENT_METHOD_MDP,
            self::PAYMENT_METHOD_MFA,
            self::PAYMENT_METHOD_MFX,
            self::PAYMENT_METHOD_MPA,
            self::PAYMENT_METHOD_MFG,
            self::PAYMENT_METHOD_MPG,
            self::PAYMENT_METHOD_MPX,
            self::PAYMENT_METHOD_MYO,
            self::PAYMENT_METHOD_PAP,
            self::PAYMENT_METHOD_PAY,
            self::PAYMENT_METHOD_PEF,
            self::PAYMENT_METHOD_PFC,
            self::PAYMENT_METHOD_PSC,
            self::PAYMENT_METHOD_REK,
            self::PAYMENT_METHOD_SAM,
            self::PAYMENT_METHOD_SWB,
            self::PAYMENT_METHOD_SCX,
            self::PAYMENT_METHOD_SWP,
            self::PAYMENT_METHOD_TWI,
            self::PAYMENT_METHOD_UAP,
            self::PAYMENT_METHOD_VIS,
            self::PAYMENT_METHOD_WEC,
            self::PAYMENT_METHOD_SWH,
            self::PAYMENT_METHOD_VPS,
            self::PAYMENT_METHOD_MBP,
            self::PAYMENT_METHOD_GEP,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getLanguageAllowableValues()
    {
        return [
            self::LANGUAGE_EN,
            self::LANGUAGE_DE,
            self::LANGUAGE_FR,
            self::LANGUAGE_IT,
            self::LANGUAGE_ES,
            self::LANGUAGE_EL,
            self::LANGUAGE_FI,
            self::LANGUAGE_HU,
            self::LANGUAGE_KO,
            self::LANGUAGE_NL,
            self::LANGUAGE_NO,
            self::LANGUAGE_DA,
            self::LANGUAGE_PL,
            self::LANGUAGE_PT,
            self::LANGUAGE_RU,
            self::LANGUAGE_JA,
            self::LANGUAGE_SK,
            self::LANGUAGE_SL,
            self::LANGUAGE_SV,
            self::LANGUAGE_TR,
            self::LANGUAGE_ZH,
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
        $this->container['transaction_id'] = isset($data['transaction_id']) ? $data['transaction_id'] : null;
        $this->container['merchant_id'] = isset($data['merchant_id']) ? $data['merchant_id'] : null;
        $this->container['type'] = isset($data['type']) ? $data['type'] : null;
        $this->container['status'] = isset($data['status']) ? $data['status'] : null;
        $this->container['currency'] = isset($data['currency']) ? $data['currency'] : null;
        $this->container['refno'] = isset($data['refno']) ? $data['refno'] : null;
        $this->container['refno2'] = isset($data['refno2']) ? $data['refno2'] : null;
        $this->container['payment_method'] = isset($data['payment_method']) ? $data['payment_method'] : null;
        $this->container['detail'] = isset($data['detail']) ? $data['detail'] : null;
        $this->container['customer'] = isset($data['customer']) ? $data['customer'] : null;
        $this->container['cdm'] = isset($data['cdm']) ? $data['cdm'] : null;
        $this->container['accertify'] = isset($data['accertify']) ? $data['accertify'] : null;
        $this->container['language'] = isset($data['language']) ? $data['language'] : null;
        $this->container['card'] = isset($data['card']) ? $data['card'] : null;
        $this->container['twi'] = isset($data['twi']) ? $data['twi'] : null;
        $this->container['pap'] = isset($data['pap']) ? $data['pap'] : null;
        $this->container['rek'] = isset($data['rek']) ? $data['rek'] : null;
        $this->container['elv'] = isset($data['elv']) ? $data['elv'] : null;
        $this->container['kln'] = isset($data['kln']) ? $data['kln'] : null;
        $this->container['int'] = isset($data['int']) ? $data['int'] : null;
        $this->container['swp'] = isset($data['swp']) ? $data['swp'] : null;
        $this->container['mpg'] = isset($data['mpg']) ? $data['mpg'] : null;
        $this->container['mfx'] = isset($data['mfx']) ? $data['mfx'] : null;
        $this->container['mpx'] = isset($data['mpx']) ? $data['mpx'] : null;
        $this->container['mdp'] = isset($data['mdp']) ? $data['mdp'] : null;
        $this->container['esy'] = isset($data['esy']) ? $data['esy'] : null;
        $this->container['pfc'] = isset($data['pfc']) ? $data['pfc'] : null;
        $this->container['wec'] = isset($data['wec']) ? $data['wec'] : null;
        $this->container['scx'] = isset($data['scx']) ? $data['scx'] : null;
        $this->container['history'] = isset($data['history']) ? $data['history'] : null;
        $this->container['ep2'] = isset($data['ep2']) ? $data['ep2'] : null;
        $this->container['dcc'] = isset($data['dcc']) ? $data['dcc'] : null;
        $this->container['multi_currency_processing'] = isset($data['multi_currency_processing']) ? $data['multi_currency_processing'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getTypeAllowableValues();
        if (!is_null($this->container['type']) && !in_array($this->container['type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'type', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'status', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getPaymentMethodAllowableValues();
        if (!is_null($this->container['payment_method']) && !in_array($this->container['payment_method'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'payment_method', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getLanguageAllowableValues();
        if (!is_null($this->container['language']) && !in_array($this->container['language'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'language', must be one of '%s'",
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
     * Gets transaction_id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->container['transaction_id'];
    }

    /**
     * Sets transaction_id
     *
     * @param string $transaction_id The transactionId received after an authorization.
     *
     * @return $this
     */
    public function setTransactionId($transaction_id)
    {
        $this->container['transaction_id'] = $transaction_id;

        return $this;
    }

    /**
     * Gets merchant_id
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->container['merchant_id'];
    }

    /**
     * Sets merchant_id
     *
     * @param string $merchant_id The merchant id.
     *
     * @return $this
     */
    public function setMerchantId($merchant_id)
    {
        $this->container['merchant_id'] = $merchant_id;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param string $type type
     *
     * @return $this
     */
    public function setType($type)
    {
        $allowedValues = $this->getTypeAllowableValues();
        if (!is_null($type) && !in_array($type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'type', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string $status The transaction status  |Status|Description| |:---|:---| |initialized| When a transaction was initialized. A transaction is initialized after a successful init request. This status is only set for customer-initiated flows before consumers start their payment via our payment forms.| |authenticated| When a transaction was authenticated. This status is only set if you defer the authorization from the authentication.| |authorized| When a transaction was authorized. This status is only set if you defer the settlement from the authorization.| |settled| When a transaction was settled partially or fully.| |canceled| When a transaction was canceled by the user or automatically by the system after a time out occurred on our payment forms. |transmitted| When a transaction was transmitted to the acquirer for processing. This is automatically set by our system.| |failed| When a transaction failed.|
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($status) && !in_array($status, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'status', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string $currency 3 letter <a href='https://en.wikipedia.org/wiki/ISO_4217' target='_blank'>ISO-4217</a> character code. For example `CHF` or `USD`
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets refno
     *
     * @return string
     */
    public function getRefno()
    {
        return $this->container['refno'];
    }

    /**
     * Sets refno
     *
     * @param string $refno The merchant's reference number. It should be unique for each transaction.
     *
     * @return $this
     */
    public function setRefno($refno)
    {
        $this->container['refno'] = $refno;

        return $this;
    }

    /**
     * Gets refno2
     *
     * @return string
     */
    public function getRefno2()
    {
        return $this->container['refno2'];
    }

    /**
     * Sets refno2
     *
     * @param string $refno2 Optional customer's reference number. Supported by some payment methods or acquirers.
     *
     * @return $this
     */
    public function setRefno2($refno2)
    {
        $this->container['refno2'] = $refno2;

        return $this;
    }

    /**
     * Gets payment_method
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->container['payment_method'];
    }

    /**
     * Sets payment_method
     *
     * @param string $payment_method payment_method
     *
     * @return $this
     */
    public function setPaymentMethod($payment_method)
    {
        $allowedValues = $this->getPaymentMethodAllowableValues();
        if (!is_null($payment_method) && !in_array($payment_method, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'payment_method', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['payment_method'] = $payment_method;

        return $this;
    }

    /**
     * Gets detail
     *
     * @return \Swagger\Client\Model\Detail
     */
    public function getDetail()
    {
        return $this->container['detail'];
    }

    /**
     * Sets detail
     *
     * @param \Swagger\Client\Model\Detail $detail detail
     *
     * @return $this
     */
    public function setDetail($detail)
    {
        $this->container['detail'] = $detail;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return \Swagger\Client\Model\Customer
     */
    public function getCustomer()
    {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param \Swagger\Client\Model\Customer $customer customer
     *
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets cdm
     *
     * @return \Swagger\Client\Model\CDMResponse
     */
    public function getCdm()
    {
        return $this->container['cdm'];
    }

    /**
     * Sets cdm
     *
     * @param \Swagger\Client\Model\CDMResponse $cdm cdm
     *
     * @return $this
     */
    public function setCdm($cdm)
    {
        $this->container['cdm'] = $cdm;

        return $this;
    }

    /**
     * Gets accertify
     *
     * @return \Swagger\Client\Model\Accertify
     */
    public function getAccertify()
    {
        return $this->container['accertify'];
    }

    /**
     * Sets accertify
     *
     * @param \Swagger\Client\Model\Accertify $accertify accertify
     *
     * @return $this
     */
    public function setAccertify($accertify)
    {
        $this->container['accertify'] = $accertify;

        return $this;
    }

    /**
     * Gets language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->container['language'];
    }

    /**
     * Sets language
     *
     * @param string $language The language (language code) in which the payment was presented to the cardholder. The <a href='https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes' target='_blank'>ISO-639-1</a> two letter language codes listed above are supported
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $allowedValues = $this->getLanguageAllowableValues();
        if (!is_null($language) && !in_array($language, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'language', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['language'] = $language;

        return $this;
    }

    /**
     * Gets card
     *
     * @return \Swagger\Client\Model\StatusCardDetail
     */
    public function getCard()
    {
        return $this->container['card'];
    }

    /**
     * Sets card
     *
     * @param \Swagger\Client\Model\StatusCardDetail $card card
     *
     * @return $this
     */
    public function setCard($card)
    {
        $this->container['card'] = $card;

        return $this;
    }

    /**
     * Gets twi
     *
     * @return \Swagger\Client\Model\TwintDetail
     */
    public function getTwi()
    {
        return $this->container['twi'];
    }

    /**
     * Sets twi
     *
     * @param \Swagger\Client\Model\TwintDetail $twi twi
     *
     * @return $this
     */
    public function setTwi($twi)
    {
        $this->container['twi'] = $twi;

        return $this;
    }

    /**
     * Gets pap
     *
     * @return \Swagger\Client\Model\PayPalDetail
     */
    public function getPap()
    {
        return $this->container['pap'];
    }

    /**
     * Sets pap
     *
     * @param \Swagger\Client\Model\PayPalDetail $pap pap
     *
     * @return $this
     */
    public function setPap($pap)
    {
        $this->container['pap'] = $pap;

        return $this;
    }

    /**
     * Gets rek
     *
     * @return \Swagger\Client\Model\RekaDetail
     */
    public function getRek()
    {
        return $this->container['rek'];
    }

    /**
     * Sets rek
     *
     * @param \Swagger\Client\Model\RekaDetail $rek rek
     *
     * @return $this
     */
    public function setRek($rek)
    {
        $this->container['rek'] = $rek;

        return $this;
    }

    /**
     * Gets elv
     *
     * @return \Swagger\Client\Model\ElvDetail
     */
    public function getElv()
    {
        return $this->container['elv'];
    }

    /**
     * Sets elv
     *
     * @param \Swagger\Client\Model\ElvDetail $elv elv
     *
     * @return $this
     */
    public function setElv($elv)
    {
        $this->container['elv'] = $elv;

        return $this;
    }

    /**
     * Gets kln
     *
     * @return \Swagger\Client\Model\KlarnaDetail
     */
    public function getKln()
    {
        return $this->container['kln'];
    }

    /**
     * Sets kln
     *
     * @param \Swagger\Client\Model\KlarnaDetail $kln kln
     *
     * @return $this
     */
    public function setKln($kln)
    {
        $this->container['kln'] = $kln;

        return $this;
    }

    /**
     * Gets int
     *
     * @return \Swagger\Client\Model\ByjunoDetail
     */
    public function getInt()
    {
        return $this->container['int'];
    }

    /**
     * Sets int
     *
     * @param \Swagger\Client\Model\ByjunoDetail $int int
     *
     * @return $this
     */
    public function setInt($int)
    {
        $this->container['int'] = $int;

        return $this;
    }

    /**
     * Gets swp
     *
     * @return \Swagger\Client\Model\SwissPassDetail
     */
    public function getSwp()
    {
        return $this->container['swp'];
    }

    /**
     * Sets swp
     *
     * @param \Swagger\Client\Model\SwissPassDetail $swp swp
     *
     * @return $this
     */
    public function setSwp($swp)
    {
        $this->container['swp'] = $swp;

        return $this;
    }

    /**
     * Gets mpg
     *
     * @return \Swagger\Client\Model\MPGDetail
     */
    public function getMpg()
    {
        return $this->container['mpg'];
    }

    /**
     * Sets mpg
     *
     * @param \Swagger\Client\Model\MPGDetail $mpg mpg
     *
     * @return $this
     */
    public function setMpg($mpg)
    {
        $this->container['mpg'] = $mpg;

        return $this;
    }

    /**
     * Gets mfx
     *
     * @return \Swagger\Client\Model\MFXDetail
     */
    public function getMfx()
    {
        return $this->container['mfx'];
    }

    /**
     * Sets mfx
     *
     * @param \Swagger\Client\Model\MFXDetail $mfx mfx
     *
     * @return $this
     */
    public function setMfx($mfx)
    {
        $this->container['mfx'] = $mfx;

        return $this;
    }

    /**
     * Gets mpx
     *
     * @return \Swagger\Client\Model\MPXDetail
     */
    public function getMpx()
    {
        return $this->container['mpx'];
    }

    /**
     * Sets mpx
     *
     * @param \Swagger\Client\Model\MPXDetail $mpx mpx
     *
     * @return $this
     */
    public function setMpx($mpx)
    {
        $this->container['mpx'] = $mpx;

        return $this;
    }

    /**
     * Gets mdp
     *
     * @return \Swagger\Client\Model\MDPDetail
     */
    public function getMdp()
    {
        return $this->container['mdp'];
    }

    /**
     * Sets mdp
     *
     * @param \Swagger\Client\Model\MDPDetail $mdp mdp
     *
     * @return $this
     */
    public function setMdp($mdp)
    {
        $this->container['mdp'] = $mdp;

        return $this;
    }

    /**
     * Gets esy
     *
     * @return \Swagger\Client\Model\SwisscomPayDetail
     */
    public function getEsy()
    {
        return $this->container['esy'];
    }

    /**
     * Sets esy
     *
     * @param \Swagger\Client\Model\SwisscomPayDetail $esy esy
     *
     * @return $this
     */
    public function setEsy($esy)
    {
        $this->container['esy'] = $esy;

        return $this;
    }

    /**
     * Gets pfc
     *
     * @return \Swagger\Client\Model\PostfinanceDetail
     */
    public function getPfc()
    {
        return $this->container['pfc'];
    }

    /**
     * Sets pfc
     *
     * @param \Swagger\Client\Model\PostfinanceDetail $pfc pfc
     *
     * @return $this
     */
    public function setPfc($pfc)
    {
        $this->container['pfc'] = $pfc;

        return $this;
    }

    /**
     * Gets wec
     *
     * @return \Swagger\Client\Model\WeChatDetail
     */
    public function getWec()
    {
        return $this->container['wec'];
    }

    /**
     * Sets wec
     *
     * @param \Swagger\Client\Model\WeChatDetail $wec wec
     *
     * @return $this
     */
    public function setWec($wec)
    {
        $this->container['wec'] = $wec;

        return $this;
    }

    /**
     * Gets scx
     *
     * @return \Swagger\Client\Model\SuperCard
     */
    public function getScx()
    {
        return $this->container['scx'];
    }

    /**
     * Sets scx
     *
     * @param \Swagger\Client\Model\SuperCard $scx scx
     *
     * @return $this
     */
    public function setScx($scx)
    {
        $this->container['scx'] = $scx;

        return $this;
    }

    /**
     * Gets history
     *
     * @return \Swagger\Client\Model\Action[]
     */
    public function getHistory()
    {
        return $this->container['history'];
    }

    /**
     * Sets history
     *
     * @param \Swagger\Client\Model\Action[] $history history
     *
     * @return $this
     */
    public function setHistory($history)
    {
        $this->container['history'] = $history;

        return $this;
    }

    /**
     * Gets ep2
     *
     * @return \Swagger\Client\Model\Ep2
     */
    public function getEp2()
    {
        return $this->container['ep2'];
    }

    /**
     * Sets ep2
     *
     * @param \Swagger\Client\Model\Ep2 $ep2 ep2
     *
     * @return $this
     */
    public function setEp2($ep2)
    {
        $this->container['ep2'] = $ep2;

        return $this;
    }

    /**
     * Gets dcc
     *
     * @return \Swagger\Client\Model\Dcc
     */
    public function getDcc()
    {
        return $this->container['dcc'];
    }

    /**
     * Sets dcc
     *
     * @param \Swagger\Client\Model\Dcc $dcc dcc
     *
     * @return $this
     */
    public function setDcc($dcc)
    {
        $this->container['dcc'] = $dcc;

        return $this;
    }

    /**
     * Gets multi_currency_processing
     *
     * @return \Swagger\Client\Model\MultiCurrencyProcessing
     */
    public function getMultiCurrencyProcessing()
    {
        return $this->container['multi_currency_processing'];
    }

    /**
     * Sets multi_currency_processing
     *
     * @param \Swagger\Client\Model\MultiCurrencyProcessing $multi_currency_processing multi_currency_processing
     *
     * @return $this
     */
    public function setMultiCurrencyProcessing($multi_currency_processing)
    {
        $this->container['multi_currency_processing'] = $multi_currency_processing;

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
