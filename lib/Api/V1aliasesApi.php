<?php
/**
 * V1aliasesApi
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

namespace Swagger\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Swagger\Client\ApiException;
use Swagger\Client\Configuration;
use Swagger\Client\HeaderSelector;
use Swagger\Client\ObjectSerializer;

/**
 * V1aliasesApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class V1aliasesApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation aliasesConvert
     *
     * Convert alias
     *
     * @param  \Swagger\Client\Model\AliasConvertRequest $body body (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Swagger\Client\Model\AliasConvertResponse
     */
    public function aliasesConvert($body)
    {
        list($response) = $this->aliasesConvertWithHttpInfo($body);
        return $response;
    }

    /**
     * Operation aliasesConvertWithHttpInfo
     *
     * Convert alias
     *
     * @param  \Swagger\Client\Model\AliasConvertRequest $body (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Swagger\Client\Model\AliasConvertResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function aliasesConvertWithHttpInfo($body)
    {
        $returnType = '\Swagger\Client\Model\AliasConvertResponse';
        $request = $this->aliasesConvertRequest($body);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasConvertResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasesResponseBase',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation aliasesConvertAsync
     *
     * Convert alias
     *
     * @param  \Swagger\Client\Model\AliasConvertRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesConvertAsync($body)
    {
        return $this->aliasesConvertAsyncWithHttpInfo($body)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation aliasesConvertAsyncWithHttpInfo
     *
     * Convert alias
     *
     * @param  \Swagger\Client\Model\AliasConvertRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesConvertAsyncWithHttpInfo($body)
    {
        $returnType = '\Swagger\Client\Model\AliasConvertResponse';
        $request = $this->aliasesConvertRequest($body);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'aliasesConvert'
     *
     * @param  \Swagger\Client\Model\AliasConvertRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function aliasesConvertRequest($body)
    {
        // verify the required parameter 'body' is set
        if ($body === null || (is_array($body) && count($body) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $body when calling aliasesConvert'
            );
        }

        $resourcePath = '/v1/aliases';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if ($this->config->getUsername() !== null || $this->config->getPassword() !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation aliasesDelete
     *
     * Delete alias
     *
     * @param  string $alias alias (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return void
     */
    public function aliasesDelete($alias)
    {
        $this->aliasesDeleteWithHttpInfo($alias);
    }

    /**
     * Operation aliasesDeleteWithHttpInfo
     *
     * Delete alias
     *
     * @param  string $alias (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of null, HTTP status code, HTTP response headers (array of strings)
     */
    public function aliasesDeleteWithHttpInfo($alias)
    {
        $returnType = '';
        $request = $this->aliasesDeleteRequest($alias);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            return [null, $statusCode, $response->getHeaders()];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasesResponseBase',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation aliasesDeleteAsync
     *
     * Delete alias
     *
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesDeleteAsync($alias)
    {
        return $this->aliasesDeleteAsyncWithHttpInfo($alias)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation aliasesDeleteAsyncWithHttpInfo
     *
     * Delete alias
     *
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesDeleteAsyncWithHttpInfo($alias)
    {
        $returnType = '';
        $request = $this->aliasesDeleteRequest($alias);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    return [null, $response->getStatusCode(), $response->getHeaders()];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'aliasesDelete'
     *
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function aliasesDeleteRequest($alias)
    {
        // verify the required parameter 'alias' is set
        if ($alias === null || (is_array($alias) && count($alias) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $alias when calling aliasesDelete'
            );
        }

        $resourcePath = '/v1/aliases/{alias}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // path params
        if ($alias !== null) {
            $resourcePath = str_replace(
                '{' . 'alias' . '}',
                ObjectSerializer::toPathValue($alias),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if ($this->config->getUsername() !== null || $this->config->getPassword() !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'DELETE',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation aliasesDetokenize
     *
     * Bulk detokenization
     *
     * @param  \Swagger\Client\Model\BulkDetokenizeRequest $body body (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Swagger\Client\Model\BulkDetokenizeResponse
     */
    public function aliasesDetokenize($body)
    {
        list($response) = $this->aliasesDetokenizeWithHttpInfo($body);
        return $response;
    }

    /**
     * Operation aliasesDetokenizeWithHttpInfo
     *
     * Bulk detokenization
     *
     * @param  \Swagger\Client\Model\BulkDetokenizeRequest $body (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Swagger\Client\Model\BulkDetokenizeResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function aliasesDetokenizeWithHttpInfo($body)
    {
        $returnType = '\Swagger\Client\Model\BulkDetokenizeResponse';
        $request = $this->aliasesDetokenizeRequest($body);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\BulkDetokenizeResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasesResponseBase',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation aliasesDetokenizeAsync
     *
     * Bulk detokenization
     *
     * @param  \Swagger\Client\Model\BulkDetokenizeRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesDetokenizeAsync($body)
    {
        return $this->aliasesDetokenizeAsyncWithHttpInfo($body)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation aliasesDetokenizeAsyncWithHttpInfo
     *
     * Bulk detokenization
     *
     * @param  \Swagger\Client\Model\BulkDetokenizeRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesDetokenizeAsyncWithHttpInfo($body)
    {
        $returnType = '\Swagger\Client\Model\BulkDetokenizeResponse';
        $request = $this->aliasesDetokenizeRequest($body);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'aliasesDetokenize'
     *
     * @param  \Swagger\Client\Model\BulkDetokenizeRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function aliasesDetokenizeRequest($body)
    {
        // verify the required parameter 'body' is set
        if ($body === null || (is_array($body) && count($body) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $body when calling aliasesDetokenize'
            );
        }

        $resourcePath = '/v1/aliases/detokenize';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if ($this->config->getUsername() !== null || $this->config->getPassword() !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation aliasesInfo
     *
     * Get alias info
     *
     * @param  string $alias alias (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Swagger\Client\Model\AliasInfoResponse
     */
    public function aliasesInfo($alias)
    {
        list($response) = $this->aliasesInfoWithHttpInfo($alias);
        return $response;
    }

    /**
     * Operation aliasesInfoWithHttpInfo
     *
     * Get alias info
     *
     * @param  string $alias (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Swagger\Client\Model\AliasInfoResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function aliasesInfoWithHttpInfo($alias)
    {
        $returnType = '\Swagger\Client\Model\AliasInfoResponse';
        $request = $this->aliasesInfoRequest($alias);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasInfoResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasesResponseBase',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation aliasesInfoAsync
     *
     * Get alias info
     *
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesInfoAsync($alias)
    {
        return $this->aliasesInfoAsyncWithHttpInfo($alias)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation aliasesInfoAsyncWithHttpInfo
     *
     * Get alias info
     *
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesInfoAsyncWithHttpInfo($alias)
    {
        $returnType = '\Swagger\Client\Model\AliasInfoResponse';
        $request = $this->aliasesInfoRequest($alias);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'aliasesInfo'
     *
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function aliasesInfoRequest($alias)
    {
        // verify the required parameter 'alias' is set
        if ($alias === null || (is_array($alias) && count($alias) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $alias when calling aliasesInfo'
            );
        }

        $resourcePath = '/v1/aliases/{alias}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // path params
        if ($alias !== null) {
            $resourcePath = str_replace(
                '{' . 'alias' . '}',
                ObjectSerializer::toPathValue($alias),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if ($this->config->getUsername() !== null || $this->config->getPassword() !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation aliasesPatch
     *
     * Patch alias
     *
     * @param  \Swagger\Client\Model\AliasPatchRequest $body body (required)
     * @param  string $alias alias (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Swagger\Client\Model\AliasInfoResponse
     */
    public function aliasesPatch($body, $alias)
    {
        list($response) = $this->aliasesPatchWithHttpInfo($body, $alias);
        return $response;
    }

    /**
     * Operation aliasesPatchWithHttpInfo
     *
     * Patch alias
     *
     * @param  \Swagger\Client\Model\AliasPatchRequest $body (required)
     * @param  string $alias (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Swagger\Client\Model\AliasInfoResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function aliasesPatchWithHttpInfo($body, $alias)
    {
        $returnType = '\Swagger\Client\Model\AliasInfoResponse';
        $request = $this->aliasesPatchRequest($body, $alias);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasInfoResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasesResponseBase',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation aliasesPatchAsync
     *
     * Patch alias
     *
     * @param  \Swagger\Client\Model\AliasPatchRequest $body (required)
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesPatchAsync($body, $alias)
    {
        return $this->aliasesPatchAsyncWithHttpInfo($body, $alias)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation aliasesPatchAsyncWithHttpInfo
     *
     * Patch alias
     *
     * @param  \Swagger\Client\Model\AliasPatchRequest $body (required)
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesPatchAsyncWithHttpInfo($body, $alias)
    {
        $returnType = '\Swagger\Client\Model\AliasInfoResponse';
        $request = $this->aliasesPatchRequest($body, $alias);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'aliasesPatch'
     *
     * @param  \Swagger\Client\Model\AliasPatchRequest $body (required)
     * @param  string $alias (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function aliasesPatchRequest($body, $alias)
    {
        // verify the required parameter 'body' is set
        if ($body === null || (is_array($body) && count($body) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $body when calling aliasesPatch'
            );
        }
        // verify the required parameter 'alias' is set
        if ($alias === null || (is_array($alias) && count($alias) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $alias when calling aliasesPatch'
            );
        }

        $resourcePath = '/v1/aliases/{alias}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // path params
        if ($alias !== null) {
            $resourcePath = str_replace(
                '{' . 'alias' . '}',
                ObjectSerializer::toPathValue($alias),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if ($this->config->getUsername() !== null || $this->config->getPassword() !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'PATCH',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation aliasesTokenize
     *
     * Bulk tokenization
     *
     * @param  \Swagger\Client\Model\BulkTokenizeRequest $body body (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Swagger\Client\Model\BulkTokenizeResponse
     */
    public function aliasesTokenize($body)
    {
        list($response) = $this->aliasesTokenizeWithHttpInfo($body);
        return $response;
    }

    /**
     * Operation aliasesTokenizeWithHttpInfo
     *
     * Bulk tokenization
     *
     * @param  \Swagger\Client\Model\BulkTokenizeRequest $body (required)
     *
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Swagger\Client\Model\BulkTokenizeResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function aliasesTokenizeWithHttpInfo($body)
    {
        $returnType = '\Swagger\Client\Model\BulkTokenizeResponse';
        $request = $this->aliasesTokenizeRequest($body);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\BulkTokenizeResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Swagger\Client\Model\AliasesResponseBase',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation aliasesTokenizeAsync
     *
     * Bulk tokenization
     *
     * @param  \Swagger\Client\Model\BulkTokenizeRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesTokenizeAsync($body)
    {
        return $this->aliasesTokenizeAsyncWithHttpInfo($body)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation aliasesTokenizeAsyncWithHttpInfo
     *
     * Bulk tokenization
     *
     * @param  \Swagger\Client\Model\BulkTokenizeRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function aliasesTokenizeAsyncWithHttpInfo($body)
    {
        $returnType = '\Swagger\Client\Model\BulkTokenizeResponse';
        $request = $this->aliasesTokenizeRequest($body);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'aliasesTokenize'
     *
     * @param  \Swagger\Client\Model\BulkTokenizeRequest $body (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function aliasesTokenizeRequest($body)
    {
        // verify the required parameter 'body' is set
        if ($body === null || (is_array($body) && count($body) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $body when calling aliasesTokenize'
            );
        }

        $resourcePath = '/v1/aliases/tokenize';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if ($this->config->getUsername() !== null || $this->config->getPassword() !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
