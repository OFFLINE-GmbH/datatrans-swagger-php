<?php
/**
 * EMVCo3DAuthenticationDataAuthorizeRequest
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
 * EMVCo3DAuthenticationDataAuthorizeRequest Class Doc Comment
 *
 * @category Class
 * @description If 3D authentication data is available, the &#x60;3D&#x60; object can be used to send the relevant 3D parameters. Please get in contact with us if you have a dedicated 3D provider.
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class EMVCo3DAuthenticationDataAuthorizeRequest implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'EMVCo3DAuthenticationDataAuthorizeRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'eci' => 'string',
        'xid' => 'string',
        'three_ds_transaction_id' => 'string',
        'cavv' => 'string',
        'three_ds_version' => 'string',
        'cavv_algorithm' => 'string',
        'directory_response' => 'string',
        'authentication_response' => 'string',
        'trans_status_reason' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'eci' => null,
        'xid' => null,
        'three_ds_transaction_id' => null,
        'cavv' => null,
        'three_ds_version' => null,
        'cavv_algorithm' => null,
        'directory_response' => null,
        'authentication_response' => null,
        'trans_status_reason' => null
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
        'eci' => 'eci',
        'xid' => 'xid',
        'three_ds_transaction_id' => 'threeDSTransactionId',
        'cavv' => 'cavv',
        'three_ds_version' => 'threeDSVersion',
        'cavv_algorithm' => 'cavvAlgorithm',
        'directory_response' => 'directoryResponse',
        'authentication_response' => 'authenticationResponse',
        'trans_status_reason' => 'transStatusReason'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'eci' => 'setEci',
        'xid' => 'setXid',
        'three_ds_transaction_id' => 'setThreeDsTransactionId',
        'cavv' => 'setCavv',
        'three_ds_version' => 'setThreeDsVersion',
        'cavv_algorithm' => 'setCavvAlgorithm',
        'directory_response' => 'setDirectoryResponse',
        'authentication_response' => 'setAuthenticationResponse',
        'trans_status_reason' => 'setTransStatusReason'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'eci' => 'getEci',
        'xid' => 'getXid',
        'three_ds_transaction_id' => 'getThreeDsTransactionId',
        'cavv' => 'getCavv',
        'three_ds_version' => 'getThreeDsVersion',
        'cavv_algorithm' => 'getCavvAlgorithm',
        'directory_response' => 'getDirectoryResponse',
        'authentication_response' => 'getAuthenticationResponse',
        'trans_status_reason' => 'getTransStatusReason'
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

    const ECI__01 = '01';
    const ECI__02 = '02';
    const ECI__05 = '05';
    const ECI__06 = '06';
    const ECI__07 = '07';
    const DIRECTORY_RESPONSE_Y = 'Y';
    const DIRECTORY_RESPONSE_N = 'N';
    const DIRECTORY_RESPONSE_U = 'U';
    const DIRECTORY_RESPONSE_C = 'C';
    const DIRECTORY_RESPONSE_R = 'R';
    const DIRECTORY_RESPONSE_A = 'A';
    const AUTHENTICATION_RESPONSE_Y = 'Y';
    const AUTHENTICATION_RESPONSE_N = 'N';
    const AUTHENTICATION_RESPONSE_U = 'U';
    const AUTHENTICATION_RESPONSE_A = 'A';
    const AUTHENTICATION_RESPONSE_C = 'C';
    const AUTHENTICATION_RESPONSE_D = 'D';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getEciAllowableValues()
    {
        return [
            self::ECI__01,
            self::ECI__02,
            self::ECI__05,
            self::ECI__06,
            self::ECI__07,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getDirectoryResponseAllowableValues()
    {
        return [
            self::DIRECTORY_RESPONSE_Y,
            self::DIRECTORY_RESPONSE_N,
            self::DIRECTORY_RESPONSE_U,
            self::DIRECTORY_RESPONSE_C,
            self::DIRECTORY_RESPONSE_R,
            self::DIRECTORY_RESPONSE_A,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getAuthenticationResponseAllowableValues()
    {
        return [
            self::AUTHENTICATION_RESPONSE_Y,
            self::AUTHENTICATION_RESPONSE_N,
            self::AUTHENTICATION_RESPONSE_U,
            self::AUTHENTICATION_RESPONSE_A,
            self::AUTHENTICATION_RESPONSE_C,
            self::AUTHENTICATION_RESPONSE_D,
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
        $this->container['eci'] = isset($data['eci']) ? $data['eci'] : null;
        $this->container['xid'] = isset($data['xid']) ? $data['xid'] : null;
        $this->container['three_ds_transaction_id'] = isset($data['three_ds_transaction_id']) ? $data['three_ds_transaction_id'] : null;
        $this->container['cavv'] = isset($data['cavv']) ? $data['cavv'] : null;
        $this->container['three_ds_version'] = isset($data['three_ds_version']) ? $data['three_ds_version'] : null;
        $this->container['cavv_algorithm'] = isset($data['cavv_algorithm']) ? $data['cavv_algorithm'] : null;
        $this->container['directory_response'] = isset($data['directory_response']) ? $data['directory_response'] : null;
        $this->container['authentication_response'] = isset($data['authentication_response']) ? $data['authentication_response'] : null;
        $this->container['trans_status_reason'] = isset($data['trans_status_reason']) ? $data['trans_status_reason'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getEciAllowableValues();
        if (!is_null($this->container['eci']) && !in_array($this->container['eci'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'eci', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getDirectoryResponseAllowableValues();
        if (!is_null($this->container['directory_response']) && !in_array($this->container['directory_response'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'directory_response', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getAuthenticationResponseAllowableValues();
        if (!is_null($this->container['authentication_response']) && !in_array($this->container['authentication_response'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'authentication_response', must be one of '%s'",
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
     * Gets eci
     *
     * @return string
     */
    public function getEci()
    {
        return $this->container['eci'];
    }

    /**
     * Sets eci
     *
     * @param string $eci The Electronic Commerce Indicator
     *
     * @return $this
     */
    public function setEci($eci)
    {
        $allowedValues = $this->getEciAllowableValues();
        if (!is_null($eci) && !in_array($eci, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'eci', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['eci'] = $eci;

        return $this;
    }

    /**
     * Gets xid
     *
     * @return string
     */
    public function getXid()
    {
        return $this->container['xid'];
    }

    /**
     * Sets xid
     *
     * @param string $xid The transaction ID returned by the directory server
     *
     * @return $this
     */
    public function setXid($xid)
    {
        $this->container['xid'] = $xid;

        return $this;
    }

    /**
     * Gets three_ds_transaction_id
     *
     * @return string
     */
    public function getThreeDsTransactionId()
    {
        return $this->container['three_ds_transaction_id'];
    }

    /**
     * Sets three_ds_transaction_id
     *
     * @param string $three_ds_transaction_id The transaction ID returned by the 3D Secure Provider
     *
     * @return $this
     */
    public function setThreeDsTransactionId($three_ds_transaction_id)
    {
        $this->container['three_ds_transaction_id'] = $three_ds_transaction_id;

        return $this;
    }

    /**
     * Gets cavv
     *
     * @return string
     */
    public function getCavv()
    {
        return $this->container['cavv'];
    }

    /**
     * Sets cavv
     *
     * @param string $cavv The Cardholder Authentication Verification Value
     *
     * @return $this
     */
    public function setCavv($cavv)
    {
        $this->container['cavv'] = $cavv;

        return $this;
    }

    /**
     * Gets three_ds_version
     *
     * @return string
     */
    public function getThreeDsVersion()
    {
        return $this->container['three_ds_version'];
    }

    /**
     * Sets three_ds_version
     *
     * @param string $three_ds_version The 3D version
     *
     * @return $this
     */
    public function setThreeDsVersion($three_ds_version)
    {
        $this->container['three_ds_version'] = $three_ds_version;

        return $this;
    }

    /**
     * Gets cavv_algorithm
     *
     * @return string
     */
    public function getCavvAlgorithm()
    {
        return $this->container['cavv_algorithm'];
    }

    /**
     * Sets cavv_algorithm
     *
     * @param string $cavv_algorithm The 3D algorithm
     *
     * @return $this
     */
    public function setCavvAlgorithm($cavv_algorithm)
    {
        $this->container['cavv_algorithm'] = $cavv_algorithm;

        return $this;
    }

    /**
     * Gets directory_response
     *
     * @return string
     */
    public function getDirectoryResponse()
    {
        return $this->container['directory_response'];
    }

    /**
     * Sets directory_response
     *
     * @param string $directory_response Transaction status after `ARes`  |Value|3Dv1|3Dv2| |:---|:---|:---| |Y| enrolled| authenticated| |N| not enrolled| authentication failed| |U| not available| not available| |C| |challenge needed| |R| |rejected| |A| |authentication attempt|
     *
     * @return $this
     */
    public function setDirectoryResponse($directory_response)
    {
        $allowedValues = $this->getDirectoryResponseAllowableValues();
        if (!is_null($directory_response) && !in_array($directory_response, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'directory_response', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['directory_response'] = $directory_response;

        return $this;
    }

    /**
     * Gets authentication_response
     *
     * @return string
     */
    public function getAuthenticationResponse()
    {
        return $this->container['authentication_response'];
    }

    /**
     * Sets authentication_response
     *
     * @param string $authentication_response Transaction status after `RReq` (Challenge flow)  |Value|3Dv1|3Dv2| |:---|:---|:---| |Y| authenticated| authenticated| |N| authentication failed| authentication failed| |U| not available| not available| |A| authentication attempt| authentication attempt| |C| process incomplete| process incomplete| |D| not enrolled| |
     *
     * @return $this
     */
    public function setAuthenticationResponse($authentication_response)
    {
        $allowedValues = $this->getAuthenticationResponseAllowableValues();
        if (!is_null($authentication_response) && !in_array($authentication_response, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'authentication_response', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['authentication_response'] = $authentication_response;

        return $this;
    }

    /**
     * Gets trans_status_reason
     *
     * @return string
     */
    public function getTransStatusReason()
    {
        return $this->container['trans_status_reason'];
    }

    /**
     * Sets trans_status_reason
     *
     * @param string $trans_status_reason Transaction status reason  |Value|Description| |:---|:---| |01| Card authentication failed| |02| Unknown Device| |03| Unsupported Device| |04| Exceeds authentication frequency limit| |05| Expired card| |06| Invalid card number| |07| Invalid transaction| |08| No Card record| |09| Security failure| |10| Stolen card| |11| Suspected fraud| |12| Transaction not permitted to cardholder| |13| Cardholder not enrolled in service| |14| Transaction timed out at the ACS| |15| Low confidence| |16| Medium confidence| |17| High confidence| |18| Very High confidence| |19| Exceeds ACS maximum challenges| |20| Non-Payment transaction not supported| |21| 3RI transaction not supported| |22| ACS technical issue| |23| Decoupled Authentication required by ACS but not requested by 3DS Requestor| |24| 3DS Requestor Decoupled Max Expiry Time exceeded| |25| Decoupled Authentication was provided insufficient time to authenticate cardholder. ACS will not make attempt| |26| Authentication attempted but not performed by the cardholder| |27–79| Reserved for EMVCo future use (values invalid until defined by EMVCo)| |80–99 | Reserved for DS use|
     *
     * @return $this
     */
    public function setTransStatusReason($trans_status_reason)
    {
        $this->container['trans_status_reason'] = $trans_status_reason;

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
