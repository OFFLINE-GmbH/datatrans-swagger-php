# SwaggerClient-php
Welcome to the Datatrans API reference. This document is meant to be used in combination with https://docs.datatrans.ch. All the parameters used in the curl and web samples are described here. Reach out to support@datatrans.ch if something is missing or unclear.  Last updated: 19.07.23 - 08:42 UTC  # Payment Process The following steps describe how transactions are processed with Datatrans. We separate payments in three categories: Customer-initiated payments, merchant-initiated payments and after the payment.  ## Customer Initiated Payments We have three integrations available: [Redirect](https://docs.datatrans.ch/docs/redirect-lightbox), [Lightbox](https://docs.datatrans.ch/docs/redirect-lightbox) and [Secure Fields](https://docs.datatrans.ch/docs/secure-fields).  ### Redirect & Lightbox - Send the required parameters to initialize a `transactionId` to the [init](#operation/init) endpoint. - Let the customer proceed with the payment by redirecting them to the correct link - or showing them your payment form.   - Redirect: Redirect the browser to the following URL structure     ```     https://pay.sandbox.datatrans.com/v1/start/transactionId     ```   - Lightbox: Load the JavaScript library and initialize the payment form:     ```js     <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/datatrans-2.0.0.js\">     ```     ```js     payButton.onclick = function() {       Datatrans.startPayment({         transactionId:  \"transactionId\"       });     };     ``` - Your customer proceeds with entering their payment information and finally hits the pay or continue button. - For card payments, we check the payment information with your acquirers. The acquirers check the payment information with the issuing parties. The customer proceeds with 3D Secure whenever required. - Once the transaction is completed, we return all relevant information to you (check our [Webhook section](#section/Webhook) for more details). The browser will be redirected to the success, cancel or error URL with our `datatransTrxId` in the response.  ### Secure Fields - Send the required parameters to initialize a transactionId to our [secureFieldsInit](#operation/secureFieldsInit) endpoint. - Load the Secure Fields JavaScript libarary and initialize Secure Fields:   ```js   <script src=\"https://pay.sandbox.datatrans.com/upp/payment/js/secure-fields-2.0.0.js\">   ```   ```js   var secureFields = new SecureFields();   secureFields.init(     {{transactionId}}, {         cardNumber: \"cardNumberPlaceholder\",         cvv: \"cvvPlaceholder\",     });   ``` - Handle the success event of the secureFields.submit() call. - If 3D authentication is required for a specific transaction, the `redirect` property inside the `data` object will indicate the URL that the customer needs to be redirected to. - Use the [Authorize an authenticated transaction](#operation/authorize-split)endpoint to fully authorize the Secure Fields transaction. This is required to finalize the authorization process with Secure Fields.  ## Merchant Initiated Payments Once you have processed a customer-initiated payment or registration you can call our API to process recurring payments. Check our [authorize](#operation/authorize) endpoint to see how to create a recurring payment or our [validate](#operation/validate) endpoint to validate your customers’ saved payment details.  ## After the payment Use the `transactionId` to check the [status](#operation/status) and to [settle](#operation/settle), [cancel](#operation/cancel) or [refund](#operation/credit) a transaction.  # Idempotency  To retry identical requests with the same effect without accidentally performing the same operation more than needed, you can add the header `Idempotency-Key` to your requests. This is useful when API calls are disrupted or you did not receive a response. In other words, retrying identical requests with our idempotency key will not have any side effects. We will return the same response for any identical request that includes the same idempotency key.  If your request failed to reach our servers, no idempotent result is saved because no API endpoint processed your request. In such cases, you can simply retry your operation safely. Idempotency keys remain stored for 60 minutes. After 60 minutes have passed, sending the same request together with the previous idempotency key will create a new operation.  Please note that the idempotency key has to be unique for each request and has to be defined by yourself. We recommend assigning a random value as your idempotency key and using UUID v4. Idempotency is only available for `POST` requests.  Idempotency was implemented according to the [\"The Idempotency HTTP Header Field\" Internet-Draft](https://tools.ietf.org/id/draft-idempotency-header-01.html)  |Scenario|Condition|Expectation| |:---|:---|:---| |First time request|Idempotency key has not been seen during the past 60 minutes.|The request is processed normally.| |Repeated request|The request was retried after the first time request completed.| The response from the first time request will be returned.| |Repeated request|The request was retried before the first time request completed.| 409 Conflict. It is recommended that clients time their retries using an exponential backoff algorithm.| |Repeated request|The request body is different than the one from the first time request.| 422 Unprocessable Entity.|  Example: ```sh curl -i 'https://api.sandbox.datatrans.com/v1/transactions' \\     -H 'Authorization: Basic MTEwMDAwNzI4MzpobDJST1NScUN2am5EVlJL' \\     -H 'Content-Type: application/json; charset=UTF-8' \\     -H 'Idempotency-Key: e75d621b-0e56-4b71-b889-1acec3e9d870' \\     -d '{     \"refno\" : \"58b389331dad\",     \"amount\" : 1000,     \"currency\" : \"CHF\",     \"paymentMethods\" : [ \"VIS\", \"ECA\", \"PAP\" ],     \"option\" : {        \"createAlias\" : true     } }' ```  # Authentication Authentication to the APIs is performed with HTTP basic authentication. Your `merchantId` acts as the username. To get the password, login to the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a> and navigate to the security settings under `UPP Administration > Security`.  Create a base64 encoded value consisting of merchantId and password (most HTTP clients are able to handle the base64 encoding automatically) and submit the Authorization header with your requests. Here’s an example:  ``` base64(merchantId:password) = MTAwMDAxMTAxMTpYMWVXNmkjJA== ```  ``` Authorization: Basic MTAwMDAxMTAxMTpYMWVXNmkjJA== ````  All API requests must be done over HTTPS with TLS >= 1.2.  # Errors Datatrans uses HTTP response codes to indicate if an API call was successful or resulted in a failure. HTTP `2xx` status codes indicate a successful API call whereas HTTP `4xx` status codes indicate client errors or if something with the transaction went wrong - for example a decline. In rare cases HTTP `5xx` status codes are returned. Those indicate errors on Datatrans side.  Here’s the payload of a sample HTTP `400` error, showing that your request has wrong values in it ``` {   \"error\" : {     \"code\" : \"INVALID_PROPERTY\",     \"message\" : \"init.initRequest.currency The given currency does not have the right format\"   } } ```  # Webhook After each authorization Datatrans tries to call the configured Webhook (POST) URL. The Webhook URL can be configured within the <a href='https://admin.sandbox.datatrans.com/' target='_blank'>dashboard</a>. It is also possible to overwrite the configured webhook URL with the `init.webhook` property. The Webhook payload contains the same information as the response of a [Status API](#operation/status) call.  ## Webhook signing If you want your webhook requests to be signed, setup a HMAC key in your merchant configuration. To get your HMAC key, login to our dashboard and navigate to the Security settings in your merchant configuration to view your server to server security settings. Select the radio button `Important parameters will be digitally signed (HMAC-SHA256) and sent with payment messages`. Datatrans will use this key to sign the webhook payload and will add a `Datatrans-Signature` HTTP request header:  ```sh Datatrans-Signature: t=1559303131511,s0=33819a1220fd8e38fc5bad3f57ef31095fac0deb38c001ba347e694f48ffe2fc ```  On your server, calculate the signature of the webhook payload and finally compare it to `s0`. `timestamp` is the `t` value from the Datatrans-Signature header, `payload` represents all UTF-8 bytes from the body of the payload and finally `key` is the HMAC key you configured within the dashboard. If the value of `sign` is equal to `s0` from the `Datatrans-Signature` header, the webhook payload is valid and was not tampered.  **Java**  ```java // hex bytes of the key byte[] key = Hex.decodeHex(key);  // Create sign with timestamp and payload String algorithm = \"HmacSha256\"; SecretKeySpec macKey = new SecretKeySpec(key, algorithm); Mac mac = Mac.getInstance(algorithm); mac.init(macKey); mac.update(String.valueOf(timestamp).getBytes()); byte[] result = mac.doFinal(payload.getBytes()); String sign = Hex.encodeHexString(result); ```  **Python**  ```python # hex bytes of the key key_hex_bytes = bytes.fromhex(key)  # Create sign with timestamp and payload sign = hmac.new(key_hex_bytes, bytes(str(timestamp) + payload, 'utf-8'), hashlib.sha256) ```  # Release notes <details>   <summary>Details</summary>    ### 2.0.37 - 19.07.2023 - added `MPX` paycard number to the status API - added `airlineData` to the Authorize Split API - added wallet indicator in Alias Status response - added Alipay+ support - added documentation for Twint+ parameters - added support for ferry reservations for Klarna - added 3D2.2 feature `3RI` - added support for `MPA` and `MPG` - fixed bug in MCP handling - fixed the handling of `authorize.card.3D.threeDSTransactionId` - fixed Klarna subtype documentation for the Status API  ### 2.0.36 - 16.03.2023 - added `MBP` (MobilePay) payment method - added `uniqueRefno` handling to the `init` API   - if the unique refno feature is enabled the init does not accept duplicated refnos anymore. even if the redirect never happens. - added proper error mappings for various errors with code `UNKNOWN_ERROR`  ### 2.0.35 - 08.02.2023 - added `VPS` (Vipps) payment method - added `SWP` to the authorize API - added `imageUrl` to the `article` property for `KLN` - fixed wrong validation for the `marketplace` property - added proper error mappings for various errors with code `UNKNOWN_ERROR`  ### 2.0.34 - 12.12.2022 * added support for `accertify` * increase the maximum length of `refno` to 40 characters * refactor of `MCP` properties to support static MCP  ### 2.0.33 - 08.11.2022 * fixed the openapi specification   * renamed the models   * removed illegal characters from the specification * added validation to some 3D properties  ### 2.0.32 - 12.10.2022 * added different `card` types `PlainCard`, `AliasCard` and `NetworkTokenCard` for the `authorize` and `init` endpoint   * the old card type is still supported * fixed `webhook.url` for mobile flows * improved the API docs for `statusResponse.status`  ### 2.0.31 - 06.10.2022 * update API docs for `status.language` in the status response  ### 2.0.30 - 23.09.2022 * added `qrData` to `MPX` and `MFX` in the status API response * added support for `KLN` train reservations * added additional `airPlus` properties * added the `ELV` request properties to the API docs (init and authorize API) * fix `MCP` sample request/response examples in the api docs * fix date format issues for `airPlus` properties  ### 2.0.29 - 17.08.2022 * added `merchantId` to the status API response * added `SWH` (Swish) payment method * added `messageExtensions` to `init.card.3d` * added `authorizeResponse.card` to the API docs * added `GFT` (MFG Gift Card) payment method * added `CBL` (Cartes Bancaires) payment method * added `HPC` (Hipercard) payment method * added `airPlus` to the init API request * added more languages to the `init.language` API docs * cleaned up `order.article` property * extended the init flow to work also with tokenization mode * improved the api docs for the `credit` api * no `card` object is returned in the `alias` info response if the content is empty * fix the status api now also returns the `externalCode` for `INT` transactions * fix enrollment check in `init` api if `init.number` is set with plain card number * fix handle `airlineData` date format issues  ### 2.0.28 - 23.05.2022 * Added support to send a webhook URL along the init request. If set, it overwrites the POST URL configured in the dashboard.   * See `init.webhook` for more information.  ### 2.0.27 - 13.04.2022 * Added MCP support (Multi Currency Processing)   * Added new `GET /v1/multicurrency/rates` API to fetch the MCP rates.   * Added `init.mcp` property   * Added `authorize.mcp` property   * Added `mcp` property in the `status` response if available for the transaction  ### 2.0.26 - 16.03.2022 * Added the OpenAPI description for the `GET /v1/aliases/{alias}` response.  ### 2.0.25 - 02.03.2022 * New API `/v1/transactions/{transactionId}/increase` to increase the amount for an authorized transaction (credit cards only).  ### 2.0.24 - 15.12.2021 🎄 * Added full support for `invoiceOnDelivery` when using `MFX` or `MPX` as payment method. * The Status API now returns the ESR data for `MFX` and `MPX` when `invoiceOnDelivery=true` was used.  ### 2.0.23 - 20.10.2021 * Added support for Klarna `KLN` hotel extended merchant data (EMD)  ### 2.0.22 - 21.07.2021 * Added full support for Swisscom Pay `ESY` * The `marketplace` object now accepts an array of splits.  ### 2.0.21 - 21.05.2021 * Updated idempotency handling. See the details here https://api-reference.datatrans.ch/#section/Idempotency  ### 2.0.20 - 18.05.2021 * In addition to `debit` and `credit` the Status API now also returns `prepaid` in the `card.info.type` property. * paysafecard - Added support for `merchantClientId`   ### 2.0.19 - 03.05.2021 * Fixed `PAP.orderTransactionId` to be a string * Added support for `PAP.fraudSessionId` (PayPal FraudNet)  ### 2.0.18 - 21.04.2021 * Added new `POST /v1/transactions/screen` API to check a customer's credit score before sending an actual authorization request. Currently only `INT` (Byjuno) is supported.  ### 2.0.17 - 20.04.2021 * Added new `GET /v1/aliases` API to receive more information about a particular alias.  ### 2.0.16 - 13.04.2021 * Added support for Migros Bank E-Pay <code>MDP</code>  ### 2.0.15 - 24.03.2021 * Byjuno - renamed `subPaymentMethod` to `subtype` (`subPaymentMethod` still works) * Klarna - Returning the `subtype` (`pay_now`, `pay_later`, `pay_over_time`, `direct_debit`, `direct_bank_transfer`) from the Status API  ### 2.0.14 - 09.03.2021 * Byjuno - Added support for `customData` and `firstRateAmount` * Returning the `transactionId` (if available) for a failed Refund API call.  ### 2.0.13 - 15.02.2021 * The Status and Webhook payloads now include the `language` property * Fixed a bug where `card.3D.transStatusReason` and `card.3D.cardholderInfo` was not returned  ### 2.0.12 - 04.02.2021 * Added support for PayPal transaction context (STC) * Fixed a bug where the transaction status did not switch to `failed` after it timed out * Fixed a bug with `option.rememberMe` not returning the Alias from the Status API  ### 2.0.11 - 01.02.2021 * Returning `card.3D.transStatusReason` (if available) from the Status API  ### 2.0.10 - 18.01.2021 * Returning `card.3D.cardholderInfo` (if available) from the Status API  ### 2.0.9 - 21.12.2020 * Added support for Alipay <code>ALP</code>  ### 2.0.8 - 21.12.2020 * Added full support for Klarna <code>KLN</code> * Added support for swissbilling <code>SWB</code>  </details>

This PHP package is automatically generated by the [Swagger Codegen](https://github.com/swagger-api/swagger-codegen) project:

- API version: 2.0.37
- Build package: io.swagger.codegen.v3.generators.php.PhpClientCodegen
For more information, please visit [https://docs.datatrans.ch](https://docs.datatrans.ch)

## Requirements

PHP 5.5 and later

## Installation & Usage
### Composer

To install the bindings via [Composer](http://getcomposer.org/), add the following to `composer.json`:

```
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/git_user_id/git_repo_id.git"
    }
  ],
  "require": {
    "git_user_id/git_repo_id": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
    require_once('/path/to/SwaggerClient-php/vendor/autoload.php');
```

## Tests

To run the unit tests:

```
composer install
./vendor/bin/phpunit
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
    ->setUsername('YOUR_USERNAME')
    ->setPassword('YOUR_PASSWORD');

$apiInstance = new Swagger\Client\Api\V1aliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\AliasConvertRequest(); // \Swagger\Client\Model\AliasConvertRequest | 

try {
    $result = $apiInstance->aliasesConvert($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesConvert: ', $e->getMessage(), PHP_EOL;
}
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
    ->setUsername('YOUR_USERNAME')
    ->setPassword('YOUR_PASSWORD');

$apiInstance = new Swagger\Client\Api\V1aliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$alias = "alias_example"; // string | 

try {
    $apiInstance->aliasesDelete($alias);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesDelete: ', $e->getMessage(), PHP_EOL;
}
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
    ->setUsername('YOUR_USERNAME')
    ->setPassword('YOUR_PASSWORD');

$apiInstance = new Swagger\Client\Api\V1aliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\BulkDetokenizeRequest(); // \Swagger\Client\Model\BulkDetokenizeRequest | 

try {
    $result = $apiInstance->aliasesDetokenize($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesDetokenize: ', $e->getMessage(), PHP_EOL;
}
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
    ->setUsername('YOUR_USERNAME')
    ->setPassword('YOUR_PASSWORD');

$apiInstance = new Swagger\Client\Api\V1aliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$alias = "alias_example"; // string | 

try {
    $result = $apiInstance->aliasesInfo($alias);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesInfo: ', $e->getMessage(), PHP_EOL;
}
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
    ->setUsername('YOUR_USERNAME')
    ->setPassword('YOUR_PASSWORD');

$apiInstance = new Swagger\Client\Api\V1aliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\AliasPatchRequest(); // \Swagger\Client\Model\AliasPatchRequest | 
$alias = "alias_example"; // string | 

try {
    $result = $apiInstance->aliasesPatch($body, $alias);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesPatch: ', $e->getMessage(), PHP_EOL;
}
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
    ->setUsername('YOUR_USERNAME')
    ->setPassword('YOUR_PASSWORD');

$apiInstance = new Swagger\Client\Api\V1aliasesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\BulkTokenizeRequest(); // \Swagger\Client\Model\BulkTokenizeRequest | 

try {
    $result = $apiInstance->aliasesTokenize($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesTokenize: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Documentation for API Endpoints

All URIs are relative to *https://api.sandbox.datatrans.com*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*V1aliasesApi* | [**aliasesConvert**](docs/Api/V1aliasesApi.md#aliasesconvert) | **POST** /v1/aliases | Convert alias
*V1aliasesApi* | [**aliasesDelete**](docs/Api/V1aliasesApi.md#aliasesdelete) | **DELETE** /v1/aliases/{alias} | Delete alias
*V1aliasesApi* | [**aliasesDetokenize**](docs/Api/V1aliasesApi.md#aliasesdetokenize) | **POST** /v1/aliases/detokenize | Bulk detokenization
*V1aliasesApi* | [**aliasesInfo**](docs/Api/V1aliasesApi.md#aliasesinfo) | **GET** /v1/aliases/{alias} | Get alias info
*V1aliasesApi* | [**aliasesPatch**](docs/Api/V1aliasesApi.md#aliasespatch) | **PATCH** /v1/aliases/{alias} | Patch alias
*V1aliasesApi* | [**aliasesTokenize**](docs/Api/V1aliasesApi.md#aliasestokenize) | **POST** /v1/aliases/tokenize | Bulk tokenization
*V1multicurrencyApi* | [**getRates**](docs/Api/V1multicurrencyApi.md#getrates) | **GET** /v1/multicurrency/rates | Get conversion rates for different currencies
*V1openapiApi* | [**get**](docs/Api/V1openapiApi.md#get) | **GET** /v1/openapi | 
*V1reconciliationsApi* | [**bulkSaleReport**](docs/Api/V1reconciliationsApi.md#bulksalereport) | **POST** /v1/reconciliations/sales/bulk | Bulk reporting of sales
*V1reconciliationsApi* | [**saleReport**](docs/Api/V1reconciliationsApi.md#salereport) | **POST** /v1/reconciliations/sales | Report a sale
*V1transactionsApi* | [**authorize**](docs/Api/V1transactionsApi.md#authorize) | **POST** /v1/transactions/authorize | Authorize a transaction
*V1transactionsApi* | [**authorizeSplit**](docs/Api/V1transactionsApi.md#authorizesplit) | **POST** /v1/transactions/{transactionId}/authorize | Authorize an authenticated transaction
*V1transactionsApi* | [**cancel**](docs/Api/V1transactionsApi.md#cancel) | **POST** /v1/transactions/{transactionId}/cancel | Cancel a transaction
*V1transactionsApi* | [**credit**](docs/Api/V1transactionsApi.md#credit) | **POST** /v1/transactions/{transactionId}/credit | Refund a transaction
*V1transactionsApi* | [**increase**](docs/Api/V1transactionsApi.md#increase) | **POST** /v1/transactions/{transactionId}/increase | Increase the authorized amount of a transaction
*V1transactionsApi* | [**init**](docs/Api/V1transactionsApi.md#init) | **POST** /v1/transactions | Initialize a transaction
*V1transactionsApi* | [**screen**](docs/Api/V1transactionsApi.md#screen) | **POST** /v1/transactions/screen | Screen the customer details
*V1transactionsApi* | [**secureFieldsInit**](docs/Api/V1transactionsApi.md#securefieldsinit) | **POST** /v1/transactions/secureFields | Initialize a Secure Fields transaction
*V1transactionsApi* | [**secureFieldsUpdate**](docs/Api/V1transactionsApi.md#securefieldsupdate) | **PATCH** /v1/transactions/secureFields/{transactionId} | Update the amount of a Secure Fields transaction
*V1transactionsApi* | [**settle**](docs/Api/V1transactionsApi.md#settle) | **POST** /v1/transactions/{transactionId}/settle | Settle a transaction
*V1transactionsApi* | [**status**](docs/Api/V1transactionsApi.md#status) | **GET** /v1/transactions/{transactionId} | Checking the status of a transaction
*V1transactionsApi* | [**validate**](docs/Api/V1transactionsApi.md#validate) | **POST** /v1/transactions/validate | Validate an existing alias

## Documentation For Models

 - [AccardaAttachment](docs/Model/AccardaAttachment.md)
 - [AccardaRequest](docs/Model/AccardaRequest.md)
 - [Accertify](docs/Model/Accertify.md)
 - [AccommodationMetaData](docs/Model/AccommodationMetaData.md)
 - [Action](docs/Model/Action.md)
 - [AirlineDataRequest](docs/Model/AirlineDataRequest.md)
 - [AirlineMetaData](docs/Model/AirlineMetaData.md)
 - [AliasCard](docs/Model/AliasCard.md)
 - [AliasConvertRequest](docs/Model/AliasConvertRequest.md)
 - [AliasConvertResponse](docs/Model/AliasConvertResponse.md)
 - [AliasInfoCardInfoDetail](docs/Model/AliasInfoCardInfoDetail.md)
 - [AliasInfoResponse](docs/Model/AliasInfoResponse.md)
 - [AliasPatchRequest](docs/Model/AliasPatchRequest.md)
 - [AliasesError](docs/Model/AliasesError.md)
 - [AliasesErrorCode](docs/Model/AliasesErrorCode.md)
 - [AliasesResponseBase](docs/Model/AliasesResponseBase.md)
 - [AlipayRequest](docs/Model/AlipayRequest.md)
 - [AlipayResponse](docs/Model/AlipayResponse.md)
 - [AmazonFraudContext](docs/Model/AmazonFraudContext.md)
 - [AmazonPayRequest](docs/Model/AmazonPayRequest.md)
 - [ApplePayRequest](docs/Model/ApplePayRequest.md)
 - [ApplePayTokenizeRequest](docs/Model/ApplePayTokenizeRequest.md)
 - [ApplePayTokenizeResponse](docs/Model/ApplePayTokenizeResponse.md)
 - [ApplePayValidateRequest](docs/Model/ApplePayValidateRequest.md)
 - [Article](docs/Model/Article.md)
 - [AuthorizeCardDetail](docs/Model/AuthorizeCardDetail.md)
 - [AuthorizeDetail](docs/Model/AuthorizeDetail.md)
 - [AuthorizeError](docs/Model/AuthorizeError.md)
 - [AuthorizeMcpRequest](docs/Model/AuthorizeMcpRequest.md)
 - [AuthorizeRequest](docs/Model/AuthorizeRequest.md)
 - [AuthorizeResponse](docs/Model/AuthorizeResponse.md)
 - [AuthorizeSplitError](docs/Model/AuthorizeSplitError.md)
 - [AuthorizeSplitRequest](docs/Model/AuthorizeSplitRequest.md)
 - [AuthorizeSplitResponse](docs/Model/AuthorizeSplitResponse.md)
 - [AuthorizeSplitThreeDSecure](docs/Model/AuthorizeSplitThreeDSecure.md)
 - [BillingAddress](docs/Model/BillingAddress.md)
 - [BoncardRequest](docs/Model/BoncardRequest.md)
 - [Browser](docs/Model/Browser.md)
 - [BulkDetokenizeRequest](docs/Model/BulkDetokenizeRequest.md)
 - [BulkDetokenizeResponse](docs/Model/BulkDetokenizeResponse.md)
 - [BulkSaleReportRequest](docs/Model/BulkSaleReportRequest.md)
 - [BulkTokenizeRequest](docs/Model/BulkTokenizeRequest.md)
 - [BulkTokenizeResponse](docs/Model/BulkTokenizeResponse.md)
 - [BuyerMetaData](docs/Model/BuyerMetaData.md)
 - [ByjunoAuthorizeRequest](docs/Model/ByjunoAuthorizeRequest.md)
 - [ByjunoDetail](docs/Model/ByjunoDetail.md)
 - [ByjunoScreenRequest](docs/Model/ByjunoScreenRequest.md)
 - [CDMRequest](docs/Model/CDMRequest.md)
 - [CDMResponse](docs/Model/CDMResponse.md)
 - [CancelDetail](docs/Model/CancelDetail.md)
 - [CancelRequest](docs/Model/CancelRequest.md)
 - [Card](docs/Model/Card.md)
 - [CardDetokenizeRequest](docs/Model/CardDetokenizeRequest.md)
 - [CardDetokenizeResponse](docs/Model/CardDetokenizeResponse.md)
 - [CardInfo](docs/Model/CardInfo.md)
 - [CardInitRequest](docs/Model/CardInitRequest.md)
 - [CardInitThreeDSecure](docs/Model/CardInitThreeDSecure.md)
 - [CardTokenizeRequest](docs/Model/CardTokenizeRequest.md)
 - [CardTokenizeResponse](docs/Model/CardTokenizeResponse.md)
 - [CardValidateRequest](docs/Model/CardValidateRequest.md)
 - [Cardholder](docs/Model/Cardholder.md)
 - [CardholderAccount](docs/Model/CardholderAccount.md)
 - [CardholderAccountInformation](docs/Model/CardholderAccountInformation.md)
 - [CardholderPhoneNumber](docs/Model/CardholderPhoneNumber.md)
 - [CreditDetail](docs/Model/CreditDetail.md)
 - [CreditError](docs/Model/CreditError.md)
 - [CreditMcpRequest](docs/Model/CreditMcpRequest.md)
 - [CreditRequest](docs/Model/CreditRequest.md)
 - [CreditResponse](docs/Model/CreditResponse.md)
 - [Creditor](docs/Model/Creditor.md)
 - [CreditorInformation](docs/Model/CreditorInformation.md)
 - [CustomDetokenizeRequest](docs/Model/CustomDetokenizeRequest.md)
 - [CustomDetokenizeResponse](docs/Model/CustomDetokenizeResponse.md)
 - [CustomTokenizeRequest](docs/Model/CustomTokenizeRequest.md)
 - [CustomTokenizeResponse](docs/Model/CustomTokenizeResponse.md)
 - [Customer](docs/Model/Customer.md)
 - [CustomerRequest](docs/Model/CustomerRequest.md)
 - [CvvDetokenizeRequest](docs/Model/CvvDetokenizeRequest.md)
 - [CvvDetokenizeResponse](docs/Model/CvvDetokenizeResponse.md)
 - [CvvTokenizeRequest](docs/Model/CvvTokenizeRequest.md)
 - [CvvTokenizeResponse](docs/Model/CvvTokenizeResponse.md)
 - [Dcc](docs/Model/Dcc.md)
 - [DefaultSecureFieldsInitRequest](docs/Model/DefaultSecureFieldsInitRequest.md)
 - [Detail](docs/Model/Detail.md)
 - [DetokenizeRequest](docs/Model/DetokenizeRequest.md)
 - [DetokenizeResponse](docs/Model/DetokenizeResponse.md)
 - [EMVCo3DAuthenticationDataAuthorizeRequest](docs/Model/EMVCo3DAuthenticationDataAuthorizeRequest.md)
 - [EMVCo3DAuthenticationDataStatusResponse](docs/Model/EMVCo3DAuthenticationDataStatusResponse.md)
 - [ESY](docs/Model/ESY.md)
 - [EasyPayValidateRequest](docs/Model/EasyPayValidateRequest.md)
 - [ElvDetail](docs/Model/ElvDetail.md)
 - [ElvInitRequest](docs/Model/ElvInitRequest.md)
 - [ElvRequest](docs/Model/ElvRequest.md)
 - [Ep2](docs/Model/Ep2.md)
 - [EpsRequest](docs/Model/EpsRequest.md)
 - [EsrData](docs/Model/EsrData.md)
 - [Extension](docs/Model/Extension.md)
 - [FailDetail](docs/Model/FailDetail.md)
 - [GooglePayRequest](docs/Model/GooglePayRequest.md)
 - [GooglePayTokenizeRequest](docs/Model/GooglePayTokenizeRequest.md)
 - [GooglePayValidateRequest](docs/Model/GooglePayValidateRequest.md)
 - [Header](docs/Model/Header.md)
 - [IncreaseRequest](docs/Model/IncreaseRequest.md)
 - [IncreaseResponse](docs/Model/IncreaseResponse.md)
 - [InitDetail](docs/Model/InitDetail.md)
 - [InitMcpRequest](docs/Model/InitMcpRequest.md)
 - [InitRequest](docs/Model/InitRequest.md)
 - [InitResponse](docs/Model/InitResponse.md)
 - [Installment](docs/Model/Installment.md)
 - [IntermediateSigningKey](docs/Model/IntermediateSigningKey.md)
 - [KlarnaAddress](docs/Model/KlarnaAddress.md)
 - [KlarnaArena](docs/Model/KlarnaArena.md)
 - [KlarnaAuthorizeRequest](docs/Model/KlarnaAuthorizeRequest.md)
 - [KlarnaCustomerAccountInfo](docs/Model/KlarnaCustomerAccountInfo.md)
 - [KlarnaDetail](docs/Model/KlarnaDetail.md)
 - [KlarnaEvent](docs/Model/KlarnaEvent.md)
 - [KlarnaFerryInsurance](docs/Model/KlarnaFerryInsurance.md)
 - [KlarnaFerryItinerary](docs/Model/KlarnaFerryItinerary.md)
 - [KlarnaFerryPassenger](docs/Model/KlarnaFerryPassenger.md)
 - [KlarnaFerryReservationDetail](docs/Model/KlarnaFerryReservationDetail.md)
 - [KlarnaHotelItinerary](docs/Model/KlarnaHotelItinerary.md)
 - [KlarnaHotelReservationDetail](docs/Model/KlarnaHotelReservationDetail.md)
 - [KlarnaInitRequest](docs/Model/KlarnaInitRequest.md)
 - [KlarnaInsurance](docs/Model/KlarnaInsurance.md)
 - [KlarnaPassenger](docs/Model/KlarnaPassenger.md)
 - [KlarnaPaymentHistoryFull](docs/Model/KlarnaPaymentHistoryFull.md)
 - [KlarnaPaymentHistorySimple](docs/Model/KlarnaPaymentHistorySimple.md)
 - [KlarnaSubscription](docs/Model/KlarnaSubscription.md)
 - [KlarnaTrainInsurance](docs/Model/KlarnaTrainInsurance.md)
 - [KlarnaTrainItinerary](docs/Model/KlarnaTrainItinerary.md)
 - [KlarnaTrainPassenger](docs/Model/KlarnaTrainPassenger.md)
 - [KlarnaTrainReservationDetail](docs/Model/KlarnaTrainReservationDetail.md)
 - [KlarnaValidateRequest](docs/Model/KlarnaValidateRequest.md)
 - [Leg](docs/Model/Leg.md)
 - [LocalTime](docs/Model/LocalTime.md)
 - [MDPDetail](docs/Model/MDPDetail.md)
 - [MDPInitRequest](docs/Model/MDPInitRequest.md)
 - [MFXDetail](docs/Model/MFXDetail.md)
 - [MFXRequest](docs/Model/MFXRequest.md)
 - [MPGDetail](docs/Model/MPGDetail.md)
 - [MPXDetail](docs/Model/MPXDetail.md)
 - [MPXRequest](docs/Model/MPXRequest.md)
 - [MarketPlace](docs/Model/MarketPlace.md)
 - [MarketPlaceAuthorize](docs/Model/MarketPlaceAuthorize.md)
 - [MarketPlaceCredit](docs/Model/MarketPlaceCredit.md)
 - [MarketPlaceSettle](docs/Model/MarketPlaceSettle.md)
 - [MarketPlaceSplit](docs/Model/MarketPlaceSplit.md)
 - [MerchantData](docs/Model/MerchantData.md)
 - [MerchantRiskIndicator](docs/Model/MerchantRiskIndicator.md)
 - [MfaAuthorizeRequest](docs/Model/MfaAuthorizeRequest.md)
 - [MfgAuthorizeRequest](docs/Model/MfgAuthorizeRequest.md)
 - [MobilePayRequest](docs/Model/MobilePayRequest.md)
 - [MpaAuthorizeRequest](docs/Model/MpaAuthorizeRequest.md)
 - [MpgAuthorizeRequest](docs/Model/MpgAuthorizeRequest.md)
 - [MultiCurrencyProcessing](docs/Model/MultiCurrencyProcessing.md)
 - [MultiCurrencyProcessingError](docs/Model/MultiCurrencyProcessingError.md)
 - [MultiCurrencyProcessingErrorCode](docs/Model/MultiCurrencyProcessingErrorCode.md)
 - [MultiCurrencyReportResponse](docs/Model/MultiCurrencyReportResponse.md)
 - [MultiplePartialCapture](docs/Model/MultiplePartialCapture.md)
 - [NetworkToken](docs/Model/NetworkToken.md)
 - [NetworkToken3DAuthenticationData](docs/Model/NetworkToken3DAuthenticationData.md)
 - [NetworkTokenCard](docs/Model/NetworkTokenCard.md)
 - [NetworkTokenError](docs/Model/NetworkTokenError.md)
 - [NetworkTokenInfo](docs/Model/NetworkTokenInfo.md)
 - [NetworkTokenOptions](docs/Model/NetworkTokenOptions.md)
 - [OneOfDetokenizeRequest](docs/Model/OneOfDetokenizeRequest.md)
 - [OneOfDetokenizeResponse](docs/Model/OneOfDetokenizeResponse.md)
 - [OneOfTokenizeRequest](docs/Model/OneOfTokenizeRequest.md)
 - [OneOfTokenizeResponse](docs/Model/OneOfTokenizeResponse.md)
 - [OptionRequest](docs/Model/OptionRequest.md)
 - [OrderMetaData](docs/Model/OrderMetaData.md)
 - [OrderRequest](docs/Model/OrderRequest.md)
 - [Passenger](docs/Model/Passenger.md)
 - [PayPalAuthorizeRequest](docs/Model/PayPalAuthorizeRequest.md)
 - [PayPalDetail](docs/Model/PayPalDetail.md)
 - [PayPalInitRequest](docs/Model/PayPalInitRequest.md)
 - [PayPalValidateRequest](docs/Model/PayPalValidateRequest.md)
 - [PaymentReference](docs/Model/PaymentReference.md)
 - [PaysafecardRequest](docs/Model/PaysafecardRequest.md)
 - [PfcAuthorizeRequest](docs/Model/PfcAuthorizeRequest.md)
 - [PfcInitRequest](docs/Model/PfcInitRequest.md)
 - [PfcValidateRequest](docs/Model/PfcValidateRequest.md)
 - [PlainCard](docs/Model/PlainCard.md)
 - [PlanetPaymentRate](docs/Model/PlanetPaymentRate.md)
 - [PostfinanceDetail](docs/Model/PostfinanceDetail.md)
 - [Purchase](docs/Model/Purchase.md)
 - [QrData](docs/Model/QrData.md)
 - [ReconciliationsError](docs/Model/ReconciliationsError.md)
 - [ReconciliationsErrorCode](docs/Model/ReconciliationsErrorCode.md)
 - [RedirectRequest](docs/Model/RedirectRequest.md)
 - [RekaDetail](docs/Model/RekaDetail.md)
 - [RekaRequest](docs/Model/RekaRequest.md)
 - [ReportDetail](docs/Model/ReportDetail.md)
 - [ResponseOverview](docs/Model/ResponseOverview.md)
 - [SaleReportRequest](docs/Model/SaleReportRequest.md)
 - [SaleReportResponse](docs/Model/SaleReportResponse.md)
 - [ScreenRequest](docs/Model/ScreenRequest.md)
 - [Secure3DResponse](docs/Model/Secure3DResponse.md)
 - [SecureFieldsInitResponse](docs/Model/SecureFieldsInitResponse.md)
 - [SecureFieldsThreeDSecure](docs/Model/SecureFieldsThreeDSecure.md)
 - [SecureFieldsUpdateRequest](docs/Model/SecureFieldsUpdateRequest.md)
 - [SettleDetail](docs/Model/SettleDetail.md)
 - [SettleMcpRequest](docs/Model/SettleMcpRequest.md)
 - [SettleRequest](docs/Model/SettleRequest.md)
 - [ShippingAddress](docs/Model/ShippingAddress.md)
 - [StatusCardDetail](docs/Model/StatusCardDetail.md)
 - [StatusResponse](docs/Model/StatusResponse.md)
 - [SuperCard](docs/Model/SuperCard.md)
 - [SwishRequest](docs/Model/SwishRequest.md)
 - [SwissBillingAuthorizeRequest](docs/Model/SwissBillingAuthorizeRequest.md)
 - [SwissBillingRequest](docs/Model/SwissBillingRequest.md)
 - [SwissPassDetail](docs/Model/SwissPassDetail.md)
 - [SwissPassRequest](docs/Model/SwissPassRequest.md)
 - [SwisscomPayDetail](docs/Model/SwisscomPayDetail.md)
 - [Theme](docs/Model/Theme.md)
 - [ThemeConfiguration](docs/Model/ThemeConfiguration.md)
 - [ThreeDInfo](docs/Model/ThreeDInfo.md)
 - [ThreeDSRequestor](docs/Model/ThreeDSRequestor.md)
 - [ThreeDSRequestorAuthenticationInformation](docs/Model/ThreeDSRequestorAuthenticationInformation.md)
 - [ThreeRI](docs/Model/ThreeRI.md)
 - [ThreeRIData](docs/Model/ThreeRIData.md)
 - [Ticket](docs/Model/Ticket.md)
 - [TokenizeRequest](docs/Model/TokenizeRequest.md)
 - [TokenizeResponse](docs/Model/TokenizeResponse.md)
 - [TransactionContext](docs/Model/TransactionContext.md)
 - [TransactionsError](docs/Model/TransactionsError.md)
 - [TransactionsErrorCode](docs/Model/TransactionsErrorCode.md)
 - [TransactionsResponseBase](docs/Model/TransactionsResponseBase.md)
 - [TwintAuthorizeRequest](docs/Model/TwintAuthorizeRequest.md)
 - [TwintDetail](docs/Model/TwintDetail.md)
 - [TwintInitRequest](docs/Model/TwintInitRequest.md)
 - [TwintResponse](docs/Model/TwintResponse.md)
 - [UltimateDebtor](docs/Model/UltimateDebtor.md)
 - [ValidateRequest](docs/Model/ValidateRequest.md)
 - [VippsRequest](docs/Model/VippsRequest.md)
 - [WeChatDetail](docs/Model/WeChatDetail.md)
 - [WeChatRequest](docs/Model/WeChatRequest.md)
 - [WeChatResponse](docs/Model/WeChatResponse.md)
 - [WebhookRequest](docs/Model/WebhookRequest.md)

## Documentation For Authorization


## Basic

- **Type**: HTTP basic authentication


## Author

support@datatrans.ch

