<?php
/**
 * Browser
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
 * Browser Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Browser implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Browser';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'browser_accept_header' => 'string',
        'browser_ip' => 'string',
        'browser_java_enabled' => 'bool',
        'browser_language' => 'string',
        'browser_color_depth' => 'string',
        'browser_screen_height' => 'int',
        'browser_screen_width' => 'int',
        'browser_tz' => 'int',
        'browser_user_agent' => 'string',
        'challenge_window_size' => 'string',
        'browser_javascript_enabled' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'browser_accept_header' => null,
        'browser_ip' => null,
        'browser_java_enabled' => null,
        'browser_language' => null,
        'browser_color_depth' => null,
        'browser_screen_height' => 'int32',
        'browser_screen_width' => 'int32',
        'browser_tz' => 'int32',
        'browser_user_agent' => null,
        'challenge_window_size' => null,
        'browser_javascript_enabled' => null
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
        'browser_accept_header' => 'browserAcceptHeader',
        'browser_ip' => 'browserIP',
        'browser_java_enabled' => 'browserJavaEnabled',
        'browser_language' => 'browserLanguage',
        'browser_color_depth' => 'browserColorDepth',
        'browser_screen_height' => 'browserScreenHeight',
        'browser_screen_width' => 'browserScreenWidth',
        'browser_tz' => 'browserTZ',
        'browser_user_agent' => 'browserUserAgent',
        'challenge_window_size' => 'challengeWindowSize',
        'browser_javascript_enabled' => 'browserJavascriptEnabled'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'browser_accept_header' => 'setBrowserAcceptHeader',
        'browser_ip' => 'setBrowserIp',
        'browser_java_enabled' => 'setBrowserJavaEnabled',
        'browser_language' => 'setBrowserLanguage',
        'browser_color_depth' => 'setBrowserColorDepth',
        'browser_screen_height' => 'setBrowserScreenHeight',
        'browser_screen_width' => 'setBrowserScreenWidth',
        'browser_tz' => 'setBrowserTz',
        'browser_user_agent' => 'setBrowserUserAgent',
        'challenge_window_size' => 'setChallengeWindowSize',
        'browser_javascript_enabled' => 'setBrowserJavascriptEnabled'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'browser_accept_header' => 'getBrowserAcceptHeader',
        'browser_ip' => 'getBrowserIp',
        'browser_java_enabled' => 'getBrowserJavaEnabled',
        'browser_language' => 'getBrowserLanguage',
        'browser_color_depth' => 'getBrowserColorDepth',
        'browser_screen_height' => 'getBrowserScreenHeight',
        'browser_screen_width' => 'getBrowserScreenWidth',
        'browser_tz' => 'getBrowserTz',
        'browser_user_agent' => 'getBrowserUserAgent',
        'challenge_window_size' => 'getChallengeWindowSize',
        'browser_javascript_enabled' => 'getBrowserJavascriptEnabled'
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

    const BROWSER_COLOR_DEPTH__1 = '1';
    const BROWSER_COLOR_DEPTH__4 = '4';
    const BROWSER_COLOR_DEPTH__8 = '8';
    const BROWSER_COLOR_DEPTH__15 = '15';
    const BROWSER_COLOR_DEPTH__16 = '16';
    const BROWSER_COLOR_DEPTH__24 = '24';
    const BROWSER_COLOR_DEPTH__32 = '32';
    const BROWSER_COLOR_DEPTH__48 = '48';
    const CHALLENGE_WINDOW_SIZE__01 = '01';
    const CHALLENGE_WINDOW_SIZE__02 = '02';
    const CHALLENGE_WINDOW_SIZE__03 = '03';
    const CHALLENGE_WINDOW_SIZE__04 = '04';
    const CHALLENGE_WINDOW_SIZE__05 = '05';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getBrowserColorDepthAllowableValues()
    {
        return [
            self::BROWSER_COLOR_DEPTH__1,
            self::BROWSER_COLOR_DEPTH__4,
            self::BROWSER_COLOR_DEPTH__8,
            self::BROWSER_COLOR_DEPTH__15,
            self::BROWSER_COLOR_DEPTH__16,
            self::BROWSER_COLOR_DEPTH__24,
            self::BROWSER_COLOR_DEPTH__32,
            self::BROWSER_COLOR_DEPTH__48,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getChallengeWindowSizeAllowableValues()
    {
        return [
            self::CHALLENGE_WINDOW_SIZE__01,
            self::CHALLENGE_WINDOW_SIZE__02,
            self::CHALLENGE_WINDOW_SIZE__03,
            self::CHALLENGE_WINDOW_SIZE__04,
            self::CHALLENGE_WINDOW_SIZE__05,
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
        $this->container['browser_accept_header'] = isset($data['browser_accept_header']) ? $data['browser_accept_header'] : null;
        $this->container['browser_ip'] = isset($data['browser_ip']) ? $data['browser_ip'] : null;
        $this->container['browser_java_enabled'] = isset($data['browser_java_enabled']) ? $data['browser_java_enabled'] : null;
        $this->container['browser_language'] = isset($data['browser_language']) ? $data['browser_language'] : null;
        $this->container['browser_color_depth'] = isset($data['browser_color_depth']) ? $data['browser_color_depth'] : null;
        $this->container['browser_screen_height'] = isset($data['browser_screen_height']) ? $data['browser_screen_height'] : null;
        $this->container['browser_screen_width'] = isset($data['browser_screen_width']) ? $data['browser_screen_width'] : null;
        $this->container['browser_tz'] = isset($data['browser_tz']) ? $data['browser_tz'] : null;
        $this->container['browser_user_agent'] = isset($data['browser_user_agent']) ? $data['browser_user_agent'] : null;
        $this->container['challenge_window_size'] = isset($data['challenge_window_size']) ? $data['challenge_window_size'] : null;
        $this->container['browser_javascript_enabled'] = isset($data['browser_javascript_enabled']) ? $data['browser_javascript_enabled'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getBrowserColorDepthAllowableValues();
        if (!is_null($this->container['browser_color_depth']) && !in_array($this->container['browser_color_depth'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'browser_color_depth', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getChallengeWindowSizeAllowableValues();
        if (!is_null($this->container['challenge_window_size']) && !in_array($this->container['challenge_window_size'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'challenge_window_size', must be one of '%s'",
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
     * Gets browser_accept_header
     *
     * @return string
     */
    public function getBrowserAcceptHeader()
    {
        return $this->container['browser_accept_header'];
    }

    /**
     * Sets browser_accept_header
     *
     * @param string $browser_accept_header browser_accept_header
     *
     * @return $this
     */
    public function setBrowserAcceptHeader($browser_accept_header)
    {
        $this->container['browser_accept_header'] = $browser_accept_header;

        return $this;
    }

    /**
     * Gets browser_ip
     *
     * @return string
     */
    public function getBrowserIp()
    {
        return $this->container['browser_ip'];
    }

    /**
     * Sets browser_ip
     *
     * @param string $browser_ip browser_ip
     *
     * @return $this
     */
    public function setBrowserIp($browser_ip)
    {
        $this->container['browser_ip'] = $browser_ip;

        return $this;
    }

    /**
     * Gets browser_java_enabled
     *
     * @return bool
     */
    public function getBrowserJavaEnabled()
    {
        return $this->container['browser_java_enabled'];
    }

    /**
     * Sets browser_java_enabled
     *
     * @param bool $browser_java_enabled browser_java_enabled
     *
     * @return $this
     */
    public function setBrowserJavaEnabled($browser_java_enabled)
    {
        $this->container['browser_java_enabled'] = $browser_java_enabled;

        return $this;
    }

    /**
     * Gets browser_language
     *
     * @return string
     */
    public function getBrowserLanguage()
    {
        return $this->container['browser_language'];
    }

    /**
     * Sets browser_language
     *
     * @param string $browser_language browser_language
     *
     * @return $this
     */
    public function setBrowserLanguage($browser_language)
    {
        $this->container['browser_language'] = $browser_language;

        return $this;
    }

    /**
     * Gets browser_color_depth
     *
     * @return string
     */
    public function getBrowserColorDepth()
    {
        return $this->container['browser_color_depth'];
    }

    /**
     * Sets browser_color_depth
     *
     * @param string $browser_color_depth browser_color_depth
     *
     * @return $this
     */
    public function setBrowserColorDepth($browser_color_depth)
    {
        $allowedValues = $this->getBrowserColorDepthAllowableValues();
        if (!is_null($browser_color_depth) && !in_array($browser_color_depth, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'browser_color_depth', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['browser_color_depth'] = $browser_color_depth;

        return $this;
    }

    /**
     * Gets browser_screen_height
     *
     * @return int
     */
    public function getBrowserScreenHeight()
    {
        return $this->container['browser_screen_height'];
    }

    /**
     * Sets browser_screen_height
     *
     * @param int $browser_screen_height browser_screen_height
     *
     * @return $this
     */
    public function setBrowserScreenHeight($browser_screen_height)
    {
        $this->container['browser_screen_height'] = $browser_screen_height;

        return $this;
    }

    /**
     * Gets browser_screen_width
     *
     * @return int
     */
    public function getBrowserScreenWidth()
    {
        return $this->container['browser_screen_width'];
    }

    /**
     * Sets browser_screen_width
     *
     * @param int $browser_screen_width browser_screen_width
     *
     * @return $this
     */
    public function setBrowserScreenWidth($browser_screen_width)
    {
        $this->container['browser_screen_width'] = $browser_screen_width;

        return $this;
    }

    /**
     * Gets browser_tz
     *
     * @return int
     */
    public function getBrowserTz()
    {
        return $this->container['browser_tz'];
    }

    /**
     * Sets browser_tz
     *
     * @param int $browser_tz browser_tz
     *
     * @return $this
     */
    public function setBrowserTz($browser_tz)
    {
        $this->container['browser_tz'] = $browser_tz;

        return $this;
    }

    /**
     * Gets browser_user_agent
     *
     * @return string
     */
    public function getBrowserUserAgent()
    {
        return $this->container['browser_user_agent'];
    }

    /**
     * Sets browser_user_agent
     *
     * @param string $browser_user_agent browser_user_agent
     *
     * @return $this
     */
    public function setBrowserUserAgent($browser_user_agent)
    {
        $this->container['browser_user_agent'] = $browser_user_agent;

        return $this;
    }

    /**
     * Gets challenge_window_size
     *
     * @return string
     */
    public function getChallengeWindowSize()
    {
        return $this->container['challenge_window_size'];
    }

    /**
     * Sets challenge_window_size
     *
     * @param string $challenge_window_size challenge_window_size
     *
     * @return $this
     */
    public function setChallengeWindowSize($challenge_window_size)
    {
        $allowedValues = $this->getChallengeWindowSizeAllowableValues();
        if (!is_null($challenge_window_size) && !in_array($challenge_window_size, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'challenge_window_size', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['challenge_window_size'] = $challenge_window_size;

        return $this;
    }

    /**
     * Gets browser_javascript_enabled
     *
     * @return bool
     */
    public function getBrowserJavascriptEnabled()
    {
        return $this->container['browser_javascript_enabled'];
    }

    /**
     * Sets browser_javascript_enabled
     *
     * @param bool $browser_javascript_enabled browser_javascript_enabled
     *
     * @return $this
     */
    public function setBrowserJavascriptEnabled($browser_javascript_enabled)
    {
        $this->container['browser_javascript_enabled'] = $browser_javascript_enabled;

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
