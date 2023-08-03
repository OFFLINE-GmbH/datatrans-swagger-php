<?php
/**
 * InitRequest
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
 * InitRequest Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class InitRequest implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'InitRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'currency' => 'string',
        'refno' => 'string',
        'refno2' => 'string',
        'auto_settle' => 'bool',
        'customer' => '\Swagger\Client\Model\CustomerRequest',
        'billing' => '\Swagger\Client\Model\BillingAddress',
        'shipping' => '\Swagger\Client\Model\ShippingAddress',
        'order' => '\Swagger\Client\Model\OrderRequest',
        'card' => '\Swagger\Client\Model\CardInitRequest',
        'bon' => '\Swagger\Client\Model\BoncardRequest',
        'pap' => '\Swagger\Client\Model\PayPalInitRequest',
        'pfc' => '\Swagger\Client\Model\PfcInitRequest',
        'rek' => '\Swagger\Client\Model\RekaRequest',
        'kln' => '\Swagger\Client\Model\KlarnaInitRequest',
        'twi' => '\Swagger\Client\Model\TwintInitRequest',
        'int' => '\Swagger\Client\Model\ByjunoAuthorizeRequest',
        'alp' => '\Swagger\Client\Model\AlipayRequest',
        'esy' => '\Swagger\Client\Model\ESY',
        'mfa' => '\Swagger\Client\Model\MfaAuthorizeRequest',
        'swp' => '\Swagger\Client\Model\SwissPassRequest',
        'airline_data' => '\Swagger\Client\Model\AirlineDataRequest',
        'accertify' => '\Swagger\Client\Model\Accertify',
        'three_ri_data' => '\Swagger\Client\Model\ThreeRIData',
        'amount' => 'int',
        'language' => 'string',
        'payment_methods' => 'string[]',
        'theme' => '\Swagger\Client\Model\Theme',
        'redirect' => '\Swagger\Client\Model\RedirectRequest',
        'webhook' => '\Swagger\Client\Model\WebhookRequest',
        'option' => '\Swagger\Client\Model\OptionRequest',
        'mfx' => '\Swagger\Client\Model\MFXRequest',
        'mpx' => '\Swagger\Client\Model\MPXRequest',
        'azp' => '\Swagger\Client\Model\AmazonPayRequest',
        'eps' => '\Swagger\Client\Model\EpsRequest',
        'swh' => '\Swagger\Client\Model\SwishRequest',
        'vps' => '\Swagger\Client\Model\VippsRequest',
        'mbp' => '\Swagger\Client\Model\MobilePayRequest',
        'wec' => '\Swagger\Client\Model\WeChatRequest',
        'elv' => '\Swagger\Client\Model\ElvInitRequest',
        'swb' => '\Swagger\Client\Model\SwissBillingRequest',
        'mdp' => '\Swagger\Client\Model\MDPInitRequest',
        'psc' => '\Swagger\Client\Model\PaysafecardRequest',
        'mcp' => '\Swagger\Client\Model\InitMcpRequest',
        'extensions' => '\Swagger\Client\Model\Extension'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'currency' => null,
        'refno' => null,
        'refno2' => null,
        'auto_settle' => null,
        'customer' => null,
        'billing' => null,
        'shipping' => null,
        'order' => null,
        'card' => null,
        'bon' => null,
        'pap' => null,
        'pfc' => null,
        'rek' => null,
        'kln' => null,
        'twi' => null,
        'int' => null,
        'alp' => null,
        'esy' => null,
        'mfa' => null,
        'swp' => null,
        'airline_data' => null,
        'accertify' => null,
        'three_ri_data' => null,
        'amount' => 'int64',
        'language' => null,
        'payment_methods' => null,
        'theme' => null,
        'redirect' => null,
        'webhook' => null,
        'option' => null,
        'mfx' => null,
        'mpx' => null,
        'azp' => null,
        'eps' => null,
        'swh' => null,
        'vps' => null,
        'mbp' => null,
        'wec' => null,
        'elv' => null,
        'swb' => null,
        'mdp' => null,
        'psc' => null,
        'mcp' => null,
        'extensions' => null
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
        'currency' => 'currency',
        'refno' => 'refno',
        'refno2' => 'refno2',
        'auto_settle' => 'autoSettle',
        'customer' => 'customer',
        'billing' => 'billing',
        'shipping' => 'shipping',
        'order' => 'order',
        'card' => 'card',
        'bon' => 'BON',
        'pap' => 'PAP',
        'pfc' => 'PFC',
        'rek' => 'REK',
        'kln' => 'KLN',
        'twi' => 'TWI',
        'int' => 'INT',
        'alp' => 'ALP',
        'esy' => 'ESY',
        'mfa' => 'MFA',
        'swp' => 'SWP',
        'airline_data' => 'airlineData',
        'accertify' => 'accertify',
        'three_ri_data' => 'threeRIData',
        'amount' => 'amount',
        'language' => 'language',
        'payment_methods' => 'paymentMethods',
        'theme' => 'theme',
        'redirect' => 'redirect',
        'webhook' => 'webhook',
        'option' => 'option',
        'mfx' => 'MFX',
        'mpx' => 'MPX',
        'azp' => 'AZP',
        'eps' => 'EPS',
        'swh' => 'SWH',
        'vps' => 'VPS',
        'mbp' => 'MBP',
        'wec' => 'WEC',
        'elv' => 'ELV',
        'swb' => 'SWB',
        'mdp' => 'MDP',
        'psc' => 'PSC',
        'mcp' => 'mcp',
        'extensions' => 'extensions'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'currency' => 'setCurrency',
        'refno' => 'setRefno',
        'refno2' => 'setRefno2',
        'auto_settle' => 'setAutoSettle',
        'customer' => 'setCustomer',
        'billing' => 'setBilling',
        'shipping' => 'setShipping',
        'order' => 'setOrder',
        'card' => 'setCard',
        'bon' => 'setBon',
        'pap' => 'setPap',
        'pfc' => 'setPfc',
        'rek' => 'setRek',
        'kln' => 'setKln',
        'twi' => 'setTwi',
        'int' => 'setInt',
        'alp' => 'setAlp',
        'esy' => 'setEsy',
        'mfa' => 'setMfa',
        'swp' => 'setSwp',
        'airline_data' => 'setAirlineData',
        'accertify' => 'setAccertify',
        'three_ri_data' => 'setThreeRiData',
        'amount' => 'setAmount',
        'language' => 'setLanguage',
        'payment_methods' => 'setPaymentMethods',
        'theme' => 'setTheme',
        'redirect' => 'setRedirect',
        'webhook' => 'setWebhook',
        'option' => 'setOption',
        'mfx' => 'setMfx',
        'mpx' => 'setMpx',
        'azp' => 'setAzp',
        'eps' => 'setEps',
        'swh' => 'setSwh',
        'vps' => 'setVps',
        'mbp' => 'setMbp',
        'wec' => 'setWec',
        'elv' => 'setElv',
        'swb' => 'setSwb',
        'mdp' => 'setMdp',
        'psc' => 'setPsc',
        'mcp' => 'setMcp',
        'extensions' => 'setExtensions'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'currency' => 'getCurrency',
        'refno' => 'getRefno',
        'refno2' => 'getRefno2',
        'auto_settle' => 'getAutoSettle',
        'customer' => 'getCustomer',
        'billing' => 'getBilling',
        'shipping' => 'getShipping',
        'order' => 'getOrder',
        'card' => 'getCard',
        'bon' => 'getBon',
        'pap' => 'getPap',
        'pfc' => 'getPfc',
        'rek' => 'getRek',
        'kln' => 'getKln',
        'twi' => 'getTwi',
        'int' => 'getInt',
        'alp' => 'getAlp',
        'esy' => 'getEsy',
        'mfa' => 'getMfa',
        'swp' => 'getSwp',
        'airline_data' => 'getAirlineData',
        'accertify' => 'getAccertify',
        'three_ri_data' => 'getThreeRiData',
        'amount' => 'getAmount',
        'language' => 'getLanguage',
        'payment_methods' => 'getPaymentMethods',
        'theme' => 'getTheme',
        'redirect' => 'getRedirect',
        'webhook' => 'getWebhook',
        'option' => 'getOption',
        'mfx' => 'getMfx',
        'mpx' => 'getMpx',
        'azp' => 'getAzp',
        'eps' => 'getEps',
        'swh' => 'getSwh',
        'vps' => 'getVps',
        'mbp' => 'getMbp',
        'wec' => 'getWec',
        'elv' => 'getElv',
        'swb' => 'getSwb',
        'mdp' => 'getMdp',
        'psc' => 'getPsc',
        'mcp' => 'getMcp',
        'extensions' => 'getExtensions'
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
    const PAYMENT_METHODS_ACC = 'ACC';
    const PAYMENT_METHODS_ALP = 'ALP';
    const PAYMENT_METHODS_APL = 'APL';
    const PAYMENT_METHODS_AMX = 'AMX';
    const PAYMENT_METHODS_AZP = 'AZP';
    const PAYMENT_METHODS_BAC = 'BAC';
    const PAYMENT_METHODS_BON = 'BON';
    const PAYMENT_METHODS_CBL = 'CBL';
    const PAYMENT_METHODS_CFY = 'CFY';
    const PAYMENT_METHODS_CSY = 'CSY';
    const PAYMENT_METHODS_CUP = 'CUP';
    const PAYMENT_METHODS_DEA = 'DEA';
    const PAYMENT_METHODS_DIN = 'DIN';
    const PAYMENT_METHODS_DII = 'DII';
    const PAYMENT_METHODS_DIB = 'DIB';
    const PAYMENT_METHODS_DIS = 'DIS';
    const PAYMENT_METHODS_DNK = 'DNK';
    const PAYMENT_METHODS_ECA = 'ECA';
    const PAYMENT_METHODS_ELV = 'ELV';
    const PAYMENT_METHODS_EPS = 'EPS';
    const PAYMENT_METHODS_ESY = 'ESY';
    const PAYMENT_METHODS_GFT = 'GFT';
    const PAYMENT_METHODS_GPA = 'GPA';
    const PAYMENT_METHODS_HPC = 'HPC';
    const PAYMENT_METHODS_INT = 'INT';
    const PAYMENT_METHODS_JCB = 'JCB';
    const PAYMENT_METHODS_JEL = 'JEL';
    const PAYMENT_METHODS_KLN = 'KLN';
    const PAYMENT_METHODS_MAU = 'MAU';
    const PAYMENT_METHODS_MDP = 'MDP';
    const PAYMENT_METHODS_MFA = 'MFA';
    const PAYMENT_METHODS_MFX = 'MFX';
    const PAYMENT_METHODS_MPA = 'MPA';
    const PAYMENT_METHODS_MFG = 'MFG';
    const PAYMENT_METHODS_MPG = 'MPG';
    const PAYMENT_METHODS_MPX = 'MPX';
    const PAYMENT_METHODS_MYO = 'MYO';
    const PAYMENT_METHODS_PAP = 'PAP';
    const PAYMENT_METHODS_PAY = 'PAY';
    const PAYMENT_METHODS_PEF = 'PEF';
    const PAYMENT_METHODS_PFC = 'PFC';
    const PAYMENT_METHODS_PSC = 'PSC';
    const PAYMENT_METHODS_REK = 'REK';
    const PAYMENT_METHODS_SAM = 'SAM';
    const PAYMENT_METHODS_SWB = 'SWB';
    const PAYMENT_METHODS_SCX = 'SCX';
    const PAYMENT_METHODS_SWP = 'SWP';
    const PAYMENT_METHODS_TWI = 'TWI';
    const PAYMENT_METHODS_UAP = 'UAP';
    const PAYMENT_METHODS_VIS = 'VIS';
    const PAYMENT_METHODS_WEC = 'WEC';
    const PAYMENT_METHODS_SWH = 'SWH';
    const PAYMENT_METHODS_VPS = 'VPS';
    const PAYMENT_METHODS_MBP = 'MBP';
    const PAYMENT_METHODS_GEP = 'GEP';

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
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPaymentMethodsAllowableValues()
    {
        return [
            self::PAYMENT_METHODS_ACC,
            self::PAYMENT_METHODS_ALP,
            self::PAYMENT_METHODS_APL,
            self::PAYMENT_METHODS_AMX,
            self::PAYMENT_METHODS_AZP,
            self::PAYMENT_METHODS_BAC,
            self::PAYMENT_METHODS_BON,
            self::PAYMENT_METHODS_CBL,
            self::PAYMENT_METHODS_CFY,
            self::PAYMENT_METHODS_CSY,
            self::PAYMENT_METHODS_CUP,
            self::PAYMENT_METHODS_DEA,
            self::PAYMENT_METHODS_DIN,
            self::PAYMENT_METHODS_DII,
            self::PAYMENT_METHODS_DIB,
            self::PAYMENT_METHODS_DIS,
            self::PAYMENT_METHODS_DNK,
            self::PAYMENT_METHODS_ECA,
            self::PAYMENT_METHODS_ELV,
            self::PAYMENT_METHODS_EPS,
            self::PAYMENT_METHODS_ESY,
            self::PAYMENT_METHODS_GFT,
            self::PAYMENT_METHODS_GPA,
            self::PAYMENT_METHODS_HPC,
            self::PAYMENT_METHODS_INT,
            self::PAYMENT_METHODS_JCB,
            self::PAYMENT_METHODS_JEL,
            self::PAYMENT_METHODS_KLN,
            self::PAYMENT_METHODS_MAU,
            self::PAYMENT_METHODS_MDP,
            self::PAYMENT_METHODS_MFA,
            self::PAYMENT_METHODS_MFX,
            self::PAYMENT_METHODS_MPA,
            self::PAYMENT_METHODS_MFG,
            self::PAYMENT_METHODS_MPG,
            self::PAYMENT_METHODS_MPX,
            self::PAYMENT_METHODS_MYO,
            self::PAYMENT_METHODS_PAP,
            self::PAYMENT_METHODS_PAY,
            self::PAYMENT_METHODS_PEF,
            self::PAYMENT_METHODS_PFC,
            self::PAYMENT_METHODS_PSC,
            self::PAYMENT_METHODS_REK,
            self::PAYMENT_METHODS_SAM,
            self::PAYMENT_METHODS_SWB,
            self::PAYMENT_METHODS_SCX,
            self::PAYMENT_METHODS_SWP,
            self::PAYMENT_METHODS_TWI,
            self::PAYMENT_METHODS_UAP,
            self::PAYMENT_METHODS_VIS,
            self::PAYMENT_METHODS_WEC,
            self::PAYMENT_METHODS_SWH,
            self::PAYMENT_METHODS_VPS,
            self::PAYMENT_METHODS_MBP,
            self::PAYMENT_METHODS_GEP,
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
        $this->container['currency'] = isset($data['currency']) ? $data['currency'] : null;
        $this->container['refno'] = isset($data['refno']) ? $data['refno'] : null;
        $this->container['refno2'] = isset($data['refno2']) ? $data['refno2'] : null;
        $this->container['auto_settle'] = isset($data['auto_settle']) ? $data['auto_settle'] : null;
        $this->container['customer'] = isset($data['customer']) ? $data['customer'] : null;
        $this->container['billing'] = isset($data['billing']) ? $data['billing'] : null;
        $this->container['shipping'] = isset($data['shipping']) ? $data['shipping'] : null;
        $this->container['order'] = isset($data['order']) ? $data['order'] : null;
        $this->container['card'] = isset($data['card']) ? $data['card'] : null;
        $this->container['bon'] = isset($data['bon']) ? $data['bon'] : null;
        $this->container['pap'] = isset($data['pap']) ? $data['pap'] : null;
        $this->container['pfc'] = isset($data['pfc']) ? $data['pfc'] : null;
        $this->container['rek'] = isset($data['rek']) ? $data['rek'] : null;
        $this->container['kln'] = isset($data['kln']) ? $data['kln'] : null;
        $this->container['twi'] = isset($data['twi']) ? $data['twi'] : null;
        $this->container['int'] = isset($data['int']) ? $data['int'] : null;
        $this->container['alp'] = isset($data['alp']) ? $data['alp'] : null;
        $this->container['esy'] = isset($data['esy']) ? $data['esy'] : null;
        $this->container['mfa'] = isset($data['mfa']) ? $data['mfa'] : null;
        $this->container['swp'] = isset($data['swp']) ? $data['swp'] : null;
        $this->container['airline_data'] = isset($data['airline_data']) ? $data['airline_data'] : null;
        $this->container['accertify'] = isset($data['accertify']) ? $data['accertify'] : null;
        $this->container['three_ri_data'] = isset($data['three_ri_data']) ? $data['three_ri_data'] : null;
        $this->container['amount'] = isset($data['amount']) ? $data['amount'] : null;
        $this->container['language'] = isset($data['language']) ? $data['language'] : null;
        $this->container['payment_methods'] = isset($data['payment_methods']) ? $data['payment_methods'] : null;
        $this->container['theme'] = isset($data['theme']) ? $data['theme'] : null;
        $this->container['redirect'] = isset($data['redirect']) ? $data['redirect'] : null;
        $this->container['webhook'] = isset($data['webhook']) ? $data['webhook'] : null;
        $this->container['option'] = isset($data['option']) ? $data['option'] : null;
        $this->container['mfx'] = isset($data['mfx']) ? $data['mfx'] : null;
        $this->container['mpx'] = isset($data['mpx']) ? $data['mpx'] : null;
        $this->container['azp'] = isset($data['azp']) ? $data['azp'] : null;
        $this->container['eps'] = isset($data['eps']) ? $data['eps'] : null;
        $this->container['swh'] = isset($data['swh']) ? $data['swh'] : null;
        $this->container['vps'] = isset($data['vps']) ? $data['vps'] : null;
        $this->container['mbp'] = isset($data['mbp']) ? $data['mbp'] : null;
        $this->container['wec'] = isset($data['wec']) ? $data['wec'] : null;
        $this->container['elv'] = isset($data['elv']) ? $data['elv'] : null;
        $this->container['swb'] = isset($data['swb']) ? $data['swb'] : null;
        $this->container['mdp'] = isset($data['mdp']) ? $data['mdp'] : null;
        $this->container['psc'] = isset($data['psc']) ? $data['psc'] : null;
        $this->container['mcp'] = isset($data['mcp']) ? $data['mcp'] : null;
        $this->container['extensions'] = isset($data['extensions']) ? $data['extensions'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['currency'] === null) {
            $invalidProperties[] = "'currency' can't be null";
        }
        if ($this->container['refno'] === null) {
            $invalidProperties[] = "'refno' can't be null";
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
     * Gets auto_settle
     *
     * @return bool
     */
    public function getAutoSettle()
    {
        return $this->container['auto_settle'];
    }

    /**
     * Sets auto_settle
     *
     * @param bool $auto_settle Whether to automatically settle the transaction after an authorization or not. If not present with the init request, the settings defined in the dashboard ('Authorisation / Settlement' or 'Direct Debit') will be used. Those settings will only be used for web transactions and not for server to server API calls.
     *
     * @return $this
     */
    public function setAutoSettle($auto_settle)
    {
        $this->container['auto_settle'] = $auto_settle;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return \Swagger\Client\Model\CustomerRequest
     */
    public function getCustomer()
    {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param \Swagger\Client\Model\CustomerRequest $customer customer
     *
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets billing
     *
     * @return \Swagger\Client\Model\BillingAddress
     */
    public function getBilling()
    {
        return $this->container['billing'];
    }

    /**
     * Sets billing
     *
     * @param \Swagger\Client\Model\BillingAddress $billing billing
     *
     * @return $this
     */
    public function setBilling($billing)
    {
        $this->container['billing'] = $billing;

        return $this;
    }

    /**
     * Gets shipping
     *
     * @return \Swagger\Client\Model\ShippingAddress
     */
    public function getShipping()
    {
        return $this->container['shipping'];
    }

    /**
     * Sets shipping
     *
     * @param \Swagger\Client\Model\ShippingAddress $shipping shipping
     *
     * @return $this
     */
    public function setShipping($shipping)
    {
        $this->container['shipping'] = $shipping;

        return $this;
    }

    /**
     * Gets order
     *
     * @return \Swagger\Client\Model\OrderRequest
     */
    public function getOrder()
    {
        return $this->container['order'];
    }

    /**
     * Sets order
     *
     * @param \Swagger\Client\Model\OrderRequest $order order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->container['order'] = $order;

        return $this;
    }

    /**
     * Gets card
     *
     * @return \Swagger\Client\Model\CardInitRequest
     */
    public function getCard()
    {
        return $this->container['card'];
    }

    /**
     * Sets card
     *
     * @param \Swagger\Client\Model\CardInitRequest $card card
     *
     * @return $this
     */
    public function setCard($card)
    {
        $this->container['card'] = $card;

        return $this;
    }

    /**
     * Gets bon
     *
     * @return \Swagger\Client\Model\BoncardRequest
     */
    public function getBon()
    {
        return $this->container['bon'];
    }

    /**
     * Sets bon
     *
     * @param \Swagger\Client\Model\BoncardRequest $bon bon
     *
     * @return $this
     */
    public function setBon($bon)
    {
        $this->container['bon'] = $bon;

        return $this;
    }

    /**
     * Gets pap
     *
     * @return \Swagger\Client\Model\PayPalInitRequest
     */
    public function getPap()
    {
        return $this->container['pap'];
    }

    /**
     * Sets pap
     *
     * @param \Swagger\Client\Model\PayPalInitRequest $pap pap
     *
     * @return $this
     */
    public function setPap($pap)
    {
        $this->container['pap'] = $pap;

        return $this;
    }

    /**
     * Gets pfc
     *
     * @return \Swagger\Client\Model\PfcInitRequest
     */
    public function getPfc()
    {
        return $this->container['pfc'];
    }

    /**
     * Sets pfc
     *
     * @param \Swagger\Client\Model\PfcInitRequest $pfc pfc
     *
     * @return $this
     */
    public function setPfc($pfc)
    {
        $this->container['pfc'] = $pfc;

        return $this;
    }

    /**
     * Gets rek
     *
     * @return \Swagger\Client\Model\RekaRequest
     */
    public function getRek()
    {
        return $this->container['rek'];
    }

    /**
     * Sets rek
     *
     * @param \Swagger\Client\Model\RekaRequest $rek rek
     *
     * @return $this
     */
    public function setRek($rek)
    {
        $this->container['rek'] = $rek;

        return $this;
    }

    /**
     * Gets kln
     *
     * @return \Swagger\Client\Model\KlarnaInitRequest
     */
    public function getKln()
    {
        return $this->container['kln'];
    }

    /**
     * Sets kln
     *
     * @param \Swagger\Client\Model\KlarnaInitRequest $kln kln
     *
     * @return $this
     */
    public function setKln($kln)
    {
        $this->container['kln'] = $kln;

        return $this;
    }

    /**
     * Gets twi
     *
     * @return \Swagger\Client\Model\TwintInitRequest
     */
    public function getTwi()
    {
        return $this->container['twi'];
    }

    /**
     * Sets twi
     *
     * @param \Swagger\Client\Model\TwintInitRequest $twi twi
     *
     * @return $this
     */
    public function setTwi($twi)
    {
        $this->container['twi'] = $twi;

        return $this;
    }

    /**
     * Gets int
     *
     * @return \Swagger\Client\Model\ByjunoAuthorizeRequest
     */
    public function getInt()
    {
        return $this->container['int'];
    }

    /**
     * Sets int
     *
     * @param \Swagger\Client\Model\ByjunoAuthorizeRequest $int int
     *
     * @return $this
     */
    public function setInt($int)
    {
        $this->container['int'] = $int;

        return $this;
    }

    /**
     * Gets alp
     *
     * @return \Swagger\Client\Model\AlipayRequest
     */
    public function getAlp()
    {
        return $this->container['alp'];
    }

    /**
     * Sets alp
     *
     * @param \Swagger\Client\Model\AlipayRequest $alp alp
     *
     * @return $this
     */
    public function setAlp($alp)
    {
        $this->container['alp'] = $alp;

        return $this;
    }

    /**
     * Gets esy
     *
     * @return \Swagger\Client\Model\ESY
     */
    public function getEsy()
    {
        return $this->container['esy'];
    }

    /**
     * Sets esy
     *
     * @param \Swagger\Client\Model\ESY $esy esy
     *
     * @return $this
     */
    public function setEsy($esy)
    {
        $this->container['esy'] = $esy;

        return $this;
    }

    /**
     * Gets mfa
     *
     * @return \Swagger\Client\Model\MfaAuthorizeRequest
     */
    public function getMfa()
    {
        return $this->container['mfa'];
    }

    /**
     * Sets mfa
     *
     * @param \Swagger\Client\Model\MfaAuthorizeRequest $mfa mfa
     *
     * @return $this
     */
    public function setMfa($mfa)
    {
        $this->container['mfa'] = $mfa;

        return $this;
    }

    /**
     * Gets swp
     *
     * @return \Swagger\Client\Model\SwissPassRequest
     */
    public function getSwp()
    {
        return $this->container['swp'];
    }

    /**
     * Sets swp
     *
     * @param \Swagger\Client\Model\SwissPassRequest $swp swp
     *
     * @return $this
     */
    public function setSwp($swp)
    {
        $this->container['swp'] = $swp;

        return $this;
    }

    /**
     * Gets airline_data
     *
     * @return \Swagger\Client\Model\AirlineDataRequest
     */
    public function getAirlineData()
    {
        return $this->container['airline_data'];
    }

    /**
     * Sets airline_data
     *
     * @param \Swagger\Client\Model\AirlineDataRequest $airline_data airline_data
     *
     * @return $this
     */
    public function setAirlineData($airline_data)
    {
        $this->container['airline_data'] = $airline_data;

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
     * Gets three_ri_data
     *
     * @return \Swagger\Client\Model\ThreeRIData
     */
    public function getThreeRiData()
    {
        return $this->container['three_ri_data'];
    }

    /**
     * Sets three_ri_data
     *
     * @param \Swagger\Client\Model\ThreeRIData $three_ri_data three_ri_data
     *
     * @return $this
     */
    public function setThreeRiData($three_ri_data)
    {
        $this->container['three_ri_data'] = $three_ri_data;

        return $this;
    }

    /**
     * Gets amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param int $amount The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. Can be omitted for use cases where only a registration should take place (if the payment method supports registrations)
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->container['amount'] = $amount;

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
     * @param string $language This parameter specifies the language (language code) in which the payment page should be presented to the cardholder. The <a href='https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes' target='_blank'>ISO-639-1</a> two letter language codes listed above are supported
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
     * Gets payment_methods
     *
     * @return string[]
     */
    public function getPaymentMethods()
    {
        return $this->container['payment_methods'];
    }

    /**
     * Sets payment_methods
     *
     * @param string[] $payment_methods An array of payment method shortnames. For example `[\"VIS\", \"PFC\"]`. If omitted, all available payment methods will be displayed on the payment page. If the Mobile SDKs are used (`returnMobileToken`), this array is mandatory.
     *
     * @return $this
     */
    public function setPaymentMethods($payment_methods)
    {
        $allowedValues = $this->getPaymentMethodsAllowableValues();
        if (!is_null($payment_methods) && array_diff($payment_methods, $allowedValues)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'payment_methods', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['payment_methods'] = $payment_methods;

        return $this;
    }

    /**
     * Gets theme
     *
     * @return \Swagger\Client\Model\Theme
     */
    public function getTheme()
    {
        return $this->container['theme'];
    }

    /**
     * Sets theme
     *
     * @param \Swagger\Client\Model\Theme $theme theme
     *
     * @return $this
     */
    public function setTheme($theme)
    {
        $this->container['theme'] = $theme;

        return $this;
    }

    /**
     * Gets redirect
     *
     * @return \Swagger\Client\Model\RedirectRequest
     */
    public function getRedirect()
    {
        return $this->container['redirect'];
    }

    /**
     * Sets redirect
     *
     * @param \Swagger\Client\Model\RedirectRequest $redirect redirect
     *
     * @return $this
     */
    public function setRedirect($redirect)
    {
        $this->container['redirect'] = $redirect;

        return $this;
    }

    /**
     * Gets webhook
     *
     * @return \Swagger\Client\Model\WebhookRequest
     */
    public function getWebhook()
    {
        return $this->container['webhook'];
    }

    /**
     * Sets webhook
     *
     * @param \Swagger\Client\Model\WebhookRequest $webhook webhook
     *
     * @return $this
     */
    public function setWebhook($webhook)
    {
        $this->container['webhook'] = $webhook;

        return $this;
    }

    /**
     * Gets option
     *
     * @return \Swagger\Client\Model\OptionRequest
     */
    public function getOption()
    {
        return $this->container['option'];
    }

    /**
     * Sets option
     *
     * @param \Swagger\Client\Model\OptionRequest $option option
     *
     * @return $this
     */
    public function setOption($option)
    {
        $this->container['option'] = $option;

        return $this;
    }

    /**
     * Gets mfx
     *
     * @return \Swagger\Client\Model\MFXRequest
     */
    public function getMfx()
    {
        return $this->container['mfx'];
    }

    /**
     * Sets mfx
     *
     * @param \Swagger\Client\Model\MFXRequest $mfx mfx
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
     * @return \Swagger\Client\Model\MPXRequest
     */
    public function getMpx()
    {
        return $this->container['mpx'];
    }

    /**
     * Sets mpx
     *
     * @param \Swagger\Client\Model\MPXRequest $mpx mpx
     *
     * @return $this
     */
    public function setMpx($mpx)
    {
        $this->container['mpx'] = $mpx;

        return $this;
    }

    /**
     * Gets azp
     *
     * @return \Swagger\Client\Model\AmazonPayRequest
     */
    public function getAzp()
    {
        return $this->container['azp'];
    }

    /**
     * Sets azp
     *
     * @param \Swagger\Client\Model\AmazonPayRequest $azp azp
     *
     * @return $this
     */
    public function setAzp($azp)
    {
        $this->container['azp'] = $azp;

        return $this;
    }

    /**
     * Gets eps
     *
     * @return \Swagger\Client\Model\EpsRequest
     */
    public function getEps()
    {
        return $this->container['eps'];
    }

    /**
     * Sets eps
     *
     * @param \Swagger\Client\Model\EpsRequest $eps eps
     *
     * @return $this
     */
    public function setEps($eps)
    {
        $this->container['eps'] = $eps;

        return $this;
    }

    /**
     * Gets swh
     *
     * @return \Swagger\Client\Model\SwishRequest
     */
    public function getSwh()
    {
        return $this->container['swh'];
    }

    /**
     * Sets swh
     *
     * @param \Swagger\Client\Model\SwishRequest $swh swh
     *
     * @return $this
     */
    public function setSwh($swh)
    {
        $this->container['swh'] = $swh;

        return $this;
    }

    /**
     * Gets vps
     *
     * @return \Swagger\Client\Model\VippsRequest
     */
    public function getVps()
    {
        return $this->container['vps'];
    }

    /**
     * Sets vps
     *
     * @param \Swagger\Client\Model\VippsRequest $vps vps
     *
     * @return $this
     */
    public function setVps($vps)
    {
        $this->container['vps'] = $vps;

        return $this;
    }

    /**
     * Gets mbp
     *
     * @return \Swagger\Client\Model\MobilePayRequest
     */
    public function getMbp()
    {
        return $this->container['mbp'];
    }

    /**
     * Sets mbp
     *
     * @param \Swagger\Client\Model\MobilePayRequest $mbp mbp
     *
     * @return $this
     */
    public function setMbp($mbp)
    {
        $this->container['mbp'] = $mbp;

        return $this;
    }

    /**
     * Gets wec
     *
     * @return \Swagger\Client\Model\WeChatRequest
     */
    public function getWec()
    {
        return $this->container['wec'];
    }

    /**
     * Sets wec
     *
     * @param \Swagger\Client\Model\WeChatRequest $wec wec
     *
     * @return $this
     */
    public function setWec($wec)
    {
        $this->container['wec'] = $wec;

        return $this;
    }

    /**
     * Gets elv
     *
     * @return \Swagger\Client\Model\ElvInitRequest
     */
    public function getElv()
    {
        return $this->container['elv'];
    }

    /**
     * Sets elv
     *
     * @param \Swagger\Client\Model\ElvInitRequest $elv elv
     *
     * @return $this
     */
    public function setElv($elv)
    {
        $this->container['elv'] = $elv;

        return $this;
    }

    /**
     * Gets swb
     *
     * @return \Swagger\Client\Model\SwissBillingRequest
     */
    public function getSwb()
    {
        return $this->container['swb'];
    }

    /**
     * Sets swb
     *
     * @param \Swagger\Client\Model\SwissBillingRequest $swb swb
     *
     * @return $this
     */
    public function setSwb($swb)
    {
        $this->container['swb'] = $swb;

        return $this;
    }

    /**
     * Gets mdp
     *
     * @return \Swagger\Client\Model\MDPInitRequest
     */
    public function getMdp()
    {
        return $this->container['mdp'];
    }

    /**
     * Sets mdp
     *
     * @param \Swagger\Client\Model\MDPInitRequest $mdp mdp
     *
     * @return $this
     */
    public function setMdp($mdp)
    {
        $this->container['mdp'] = $mdp;

        return $this;
    }

    /**
     * Gets psc
     *
     * @return \Swagger\Client\Model\PaysafecardRequest
     */
    public function getPsc()
    {
        return $this->container['psc'];
    }

    /**
     * Sets psc
     *
     * @param \Swagger\Client\Model\PaysafecardRequest $psc psc
     *
     * @return $this
     */
    public function setPsc($psc)
    {
        $this->container['psc'] = $psc;

        return $this;
    }

    /**
     * Gets mcp
     *
     * @return \Swagger\Client\Model\InitMcpRequest
     */
    public function getMcp()
    {
        return $this->container['mcp'];
    }

    /**
     * Sets mcp
     *
     * @param \Swagger\Client\Model\InitMcpRequest $mcp mcp
     *
     * @return $this
     */
    public function setMcp($mcp)
    {
        $this->container['mcp'] = $mcp;

        return $this;
    }

    /**
     * Gets extensions
     *
     * @return \Swagger\Client\Model\Extension
     */
    public function getExtensions()
    {
        return $this->container['extensions'];
    }

    /**
     * Sets extensions
     *
     * @param \Swagger\Client\Model\Extension $extensions extensions
     *
     * @return $this
     */
    public function setExtensions($extensions)
    {
        $this->container['extensions'] = $extensions;

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
