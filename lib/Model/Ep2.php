<?php
/**
 * Ep2
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
 * Ep2 Class Doc Comment
 *
 * @category Class
 * @description EP2 data if available.
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Ep2 implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Ep2';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'trm_id' => 'string',
        'trx_seq_cnt' => 'string',
        'aid' => 'string',
        'amt_auth' => 'string',
        'trx_date' => 'string',
        'trx_time' => 'string',
        'pan' => 'string',
        'app_pan_enc' => 'string',
        'issuer_code' => 'string',
        'act_seq_cnt' => 'int',
        'trx_ref_num' => 'string',
        'trx_type_ext' => 'string',
        'brand' => 'string',
        'auth_code' => 'string',
        'static_key_index' => 'string',
        'trx_curr_c' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'trm_id' => null,
        'trx_seq_cnt' => null,
        'aid' => null,
        'amt_auth' => null,
        'trx_date' => null,
        'trx_time' => null,
        'pan' => null,
        'app_pan_enc' => null,
        'issuer_code' => null,
        'act_seq_cnt' => 'int32',
        'trx_ref_num' => null,
        'trx_type_ext' => null,
        'brand' => null,
        'auth_code' => null,
        'static_key_index' => null,
        'trx_curr_c' => null
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
        'trm_id' => 'trmID',
        'trx_seq_cnt' => 'trxSeqCnt',
        'aid' => 'aid',
        'amt_auth' => 'amtAuth',
        'trx_date' => 'trxDate',
        'trx_time' => 'trxTime',
        'pan' => 'pan',
        'app_pan_enc' => 'appPanEnc',
        'issuer_code' => 'issuerCode',
        'act_seq_cnt' => 'actSeqCnt',
        'trx_ref_num' => 'trxRefNum',
        'trx_type_ext' => 'trxTypeExt',
        'brand' => 'brand',
        'auth_code' => 'authCode',
        'static_key_index' => 'staticKeyIndex',
        'trx_curr_c' => 'trxCurrC'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'trm_id' => 'setTrmId',
        'trx_seq_cnt' => 'setTrxSeqCnt',
        'aid' => 'setAid',
        'amt_auth' => 'setAmtAuth',
        'trx_date' => 'setTrxDate',
        'trx_time' => 'setTrxTime',
        'pan' => 'setPan',
        'app_pan_enc' => 'setAppPanEnc',
        'issuer_code' => 'setIssuerCode',
        'act_seq_cnt' => 'setActSeqCnt',
        'trx_ref_num' => 'setTrxRefNum',
        'trx_type_ext' => 'setTrxTypeExt',
        'brand' => 'setBrand',
        'auth_code' => 'setAuthCode',
        'static_key_index' => 'setStaticKeyIndex',
        'trx_curr_c' => 'setTrxCurrC'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'trm_id' => 'getTrmId',
        'trx_seq_cnt' => 'getTrxSeqCnt',
        'aid' => 'getAid',
        'amt_auth' => 'getAmtAuth',
        'trx_date' => 'getTrxDate',
        'trx_time' => 'getTrxTime',
        'pan' => 'getPan',
        'app_pan_enc' => 'getAppPanEnc',
        'issuer_code' => 'getIssuerCode',
        'act_seq_cnt' => 'getActSeqCnt',
        'trx_ref_num' => 'getTrxRefNum',
        'trx_type_ext' => 'getTrxTypeExt',
        'brand' => 'getBrand',
        'auth_code' => 'getAuthCode',
        'static_key_index' => 'getStaticKeyIndex',
        'trx_curr_c' => 'getTrxCurrC'
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
        $this->container['trm_id'] = isset($data['trm_id']) ? $data['trm_id'] : null;
        $this->container['trx_seq_cnt'] = isset($data['trx_seq_cnt']) ? $data['trx_seq_cnt'] : null;
        $this->container['aid'] = isset($data['aid']) ? $data['aid'] : null;
        $this->container['amt_auth'] = isset($data['amt_auth']) ? $data['amt_auth'] : null;
        $this->container['trx_date'] = isset($data['trx_date']) ? $data['trx_date'] : null;
        $this->container['trx_time'] = isset($data['trx_time']) ? $data['trx_time'] : null;
        $this->container['pan'] = isset($data['pan']) ? $data['pan'] : null;
        $this->container['app_pan_enc'] = isset($data['app_pan_enc']) ? $data['app_pan_enc'] : null;
        $this->container['issuer_code'] = isset($data['issuer_code']) ? $data['issuer_code'] : null;
        $this->container['act_seq_cnt'] = isset($data['act_seq_cnt']) ? $data['act_seq_cnt'] : null;
        $this->container['trx_ref_num'] = isset($data['trx_ref_num']) ? $data['trx_ref_num'] : null;
        $this->container['trx_type_ext'] = isset($data['trx_type_ext']) ? $data['trx_type_ext'] : null;
        $this->container['brand'] = isset($data['brand']) ? $data['brand'] : null;
        $this->container['auth_code'] = isset($data['auth_code']) ? $data['auth_code'] : null;
        $this->container['static_key_index'] = isset($data['static_key_index']) ? $data['static_key_index'] : null;
        $this->container['trx_curr_c'] = isset($data['trx_curr_c']) ? $data['trx_curr_c'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

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
     * Gets trm_id
     *
     * @return string
     */
    public function getTrmId()
    {
        return $this->container['trm_id'];
    }

    /**
     * Sets trm_id
     *
     * @param string $trm_id The terminal ID
     *
     * @return $this
     */
    public function setTrmId($trm_id)
    {
        $this->container['trm_id'] = $trm_id;

        return $this;
    }

    /**
     * Gets trx_seq_cnt
     *
     * @return string
     */
    public function getTrxSeqCnt()
    {
        return $this->container['trx_seq_cnt'];
    }

    /**
     * Sets trx_seq_cnt
     *
     * @param string $trx_seq_cnt The transaction Sequence Count
     *
     * @return $this
     */
    public function setTrxSeqCnt($trx_seq_cnt)
    {
        $this->container['trx_seq_cnt'] = $trx_seq_cnt;

        return $this;
    }

    /**
     * Gets aid
     *
     * @return string
     */
    public function getAid()
    {
        return $this->container['aid'];
    }

    /**
     * Sets aid
     *
     * @param string $aid The application Identifier
     *
     * @return $this
     */
    public function setAid($aid)
    {
        $this->container['aid'] = $aid;

        return $this;
    }

    /**
     * Gets amt_auth
     *
     * @return string
     */
    public function getAmtAuth()
    {
        return $this->container['amt_auth'];
    }

    /**
     * Sets amt_auth
     *
     * @param string $amt_auth The authorized amount
     *
     * @return $this
     */
    public function setAmtAuth($amt_auth)
    {
        $this->container['amt_auth'] = $amt_auth;

        return $this;
    }

    /**
     * Gets trx_date
     *
     * @return string
     */
    public function getTrxDate()
    {
        return $this->container['trx_date'];
    }

    /**
     * Sets trx_date
     *
     * @param string $trx_date The date of the processing
     *
     * @return $this
     */
    public function setTrxDate($trx_date)
    {
        $this->container['trx_date'] = $trx_date;

        return $this;
    }

    /**
     * Gets trx_time
     *
     * @return string
     */
    public function getTrxTime()
    {
        return $this->container['trx_time'];
    }

    /**
     * Sets trx_time
     *
     * @param string $trx_time The time of the processing
     *
     * @return $this
     */
    public function setTrxTime($trx_time)
    {
        $this->container['trx_time'] = $trx_time;

        return $this;
    }

    /**
     * Gets pan
     *
     * @return string
     */
    public function getPan()
    {
        return $this->container['pan'];
    }

    /**
     * Sets pan
     *
     * @param string $pan The PAN
     *
     * @return $this
     */
    public function setPan($pan)
    {
        $this->container['pan'] = $pan;

        return $this;
    }

    /**
     * Gets app_pan_enc
     *
     * @return string
     */
    public function getAppPanEnc()
    {
        return $this->container['app_pan_enc'];
    }

    /**
     * Sets app_pan_enc
     *
     * @param string $app_pan_enc The encrypted cardholder account number
     *
     * @return $this
     */
    public function setAppPanEnc($app_pan_enc)
    {
        $this->container['app_pan_enc'] = $app_pan_enc;

        return $this;
    }

    /**
     * Gets issuer_code
     *
     * @return string
     */
    public function getIssuerCode()
    {
        return $this->container['issuer_code'];
    }

    /**
     * Sets issuer_code
     *
     * @param string $issuer_code The issuer code
     *
     * @return $this
     */
    public function setIssuerCode($issuer_code)
    {
        $this->container['issuer_code'] = $issuer_code;

        return $this;
    }

    /**
     * Gets act_seq_cnt
     *
     * @return int
     */
    public function getActSeqCnt()
    {
        return $this->container['act_seq_cnt'];
    }

    /**
     * Sets act_seq_cnt
     *
     * @param int $act_seq_cnt The activation sequence count
     *
     * @return $this
     */
    public function setActSeqCnt($act_seq_cnt)
    {
        $this->container['act_seq_cnt'] = $act_seq_cnt;

        return $this;
    }

    /**
     * Gets trx_ref_num
     *
     * @return string
     */
    public function getTrxRefNum()
    {
        return $this->container['trx_ref_num'];
    }

    /**
     * Sets trx_ref_num
     *
     * @param string $trx_ref_num The transaction reference number
     *
     * @return $this
     */
    public function setTrxRefNum($trx_ref_num)
    {
        $this->container['trx_ref_num'] = $trx_ref_num;

        return $this;
    }

    /**
     * Gets trx_type_ext
     *
     * @return string
     */
    public function getTrxTypeExt()
    {
        return $this->container['trx_type_ext'];
    }

    /**
     * Sets trx_type_ext
     *
     * @param string $trx_type_ext The transaction type extension
     *
     * @return $this
     */
    public function setTrxTypeExt($trx_type_ext)
    {
        $this->container['trx_type_ext'] = $trx_type_ext;

        return $this;
    }

    /**
     * Gets brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->container['brand'];
    }

    /**
     * Sets brand
     *
     * @param string $brand The brand
     *
     * @return $this
     */
    public function setBrand($brand)
    {
        $this->container['brand'] = $brand;

        return $this;
    }

    /**
     * Gets auth_code
     *
     * @return string
     */
    public function getAuthCode()
    {
        return $this->container['auth_code'];
    }

    /**
     * Sets auth_code
     *
     * @param string $auth_code The authorization code
     *
     * @return $this
     */
    public function setAuthCode($auth_code)
    {
        $this->container['auth_code'] = $auth_code;

        return $this;
    }

    /**
     * Gets static_key_index
     *
     * @return string
     */
    public function getStaticKeyIndex()
    {
        return $this->container['static_key_index'];
    }

    /**
     * Sets static_key_index
     *
     * @param string $static_key_index The static key index
     *
     * @return $this
     */
    public function setStaticKeyIndex($static_key_index)
    {
        $this->container['static_key_index'] = $static_key_index;

        return $this;
    }

    /**
     * Gets trx_curr_c
     *
     * @return string
     */
    public function getTrxCurrC()
    {
        return $this->container['trx_curr_c'];
    }

    /**
     * Sets trx_curr_c
     *
     * @param string $trx_curr_c The transaction currency code
     *
     * @return $this
     */
    public function setTrxCurrC($trx_curr_c)
    {
        $this->container['trx_curr_c'] = $trx_curr_c;

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
