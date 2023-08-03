# Swagger\Client\V1transactionsApi

All URIs are relative to *https://api.sandbox.datatrans.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**authorize**](V1transactionsApi.md#authorize) | **POST** /v1/transactions/authorize | Authorize a transaction
[**authorizeSplit**](V1transactionsApi.md#authorizesplit) | **POST** /v1/transactions/{transactionId}/authorize | Authorize an authenticated transaction
[**cancel**](V1transactionsApi.md#cancel) | **POST** /v1/transactions/{transactionId}/cancel | Cancel a transaction
[**credit**](V1transactionsApi.md#credit) | **POST** /v1/transactions/{transactionId}/credit | Refund a transaction
[**increase**](V1transactionsApi.md#increase) | **POST** /v1/transactions/{transactionId}/increase | Increase the authorized amount of a transaction
[**init**](V1transactionsApi.md#init) | **POST** /v1/transactions | Initialize a transaction
[**screen**](V1transactionsApi.md#screen) | **POST** /v1/transactions/screen | Screen the customer details
[**secureFieldsInit**](V1transactionsApi.md#securefieldsinit) | **POST** /v1/transactions/secureFields | Initialize a Secure Fields transaction
[**secureFieldsUpdate**](V1transactionsApi.md#securefieldsupdate) | **PATCH** /v1/transactions/secureFields/{transactionId} | Update the amount of a Secure Fields transaction
[**settle**](V1transactionsApi.md#settle) | **POST** /v1/transactions/{transactionId}/settle | Settle a transaction
[**status**](V1transactionsApi.md#status) | **GET** /v1/transactions/{transactionId} | Checking the status of a transaction
[**validate**](V1transactionsApi.md#validate) | **POST** /v1/transactions/validate | Validate an existing alias

# **authorize**
> \Swagger\Client\Model\AuthorizeResponse authorize($body)

Authorize a transaction

To create a transaction without user interaction, send all required parameters to our authorize endpoint. This is the API call for merchant-initiated transactions with an existing `alias`. Depending on the payment method, additional parameters will be required. Refer to the payment method specific objects (for example `PAP`) to see which parameters are required additionally send. For credit cards, the `card` object has to be used

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\AuthorizeRequest(); // \Swagger\Client\Model\AuthorizeRequest | 

try {
    $result = $apiInstance->authorize($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->authorize: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\AuthorizeRequest**](../Model/AuthorizeRequest.md)|  |

### Return type

[**\Swagger\Client\Model\AuthorizeResponse**](../Model/AuthorizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **authorizeSplit**
> \Swagger\Client\Model\AuthorizeSplitResponse authorizeSplit($body, $transaction_id)

Authorize an authenticated transaction

Use this API endpoint to fully authorize an already authenticated transaction. This call is required for any transaction done with our Secure Fields or if during the initialization of a transaction the parameter `option.authenticationOnly` was set to `true`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\AuthorizeSplitRequest(); // \Swagger\Client\Model\AuthorizeSplitRequest | 
$transaction_id = 789; // int | 

try {
    $result = $apiInstance->authorizeSplit($body, $transaction_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->authorizeSplit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\AuthorizeSplitRequest**](../Model/AuthorizeSplitRequest.md)|  |
 **transaction_id** | **int**|  |

### Return type

[**\Swagger\Client\Model\AuthorizeSplitResponse**](../Model/AuthorizeSplitResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **cancel**
> cancel($transaction_id, $body)

Cancel a transaction

Cancel requests can be used to release a blocked amount from an authorization. The transaction must either be in status `authorized` or `settled`. The `transactionId` is needed to cancel an authorization

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 789; // int | 
$body = new \Swagger\Client\Model\CancelRequest(); // \Swagger\Client\Model\CancelRequest | 

try {
    $apiInstance->cancel($transaction_id, $body);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->cancel: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |
 **body** | [**\Swagger\Client\Model\CancelRequest**](../Model/CancelRequest.md)|  | [optional]

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **credit**
> \Swagger\Client\Model\CreditResponse credit($body, $transaction_id)

Refund a transaction

Refund requests can be used to credit a transaction which is in status `settled` or `transmitted`. The previously settled amount must not be exceeded.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\CreditRequest(); // \Swagger\Client\Model\CreditRequest | Credit a transaction
$transaction_id = 789; // int | 

try {
    $result = $apiInstance->credit($body, $transaction_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->credit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\CreditRequest**](../Model/CreditRequest.md)| Credit a transaction |
 **transaction_id** | **int**|  |

### Return type

[**\Swagger\Client\Model\CreditResponse**](../Model/CreditResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **increase**
> \Swagger\Client\Model\IncreaseResponse increase($body, $transaction_id)

Increase the authorized amount of a transaction

Use this API to increase the authorized amount for a transaction. The transaction must be in status `authorized`. The `transactionId` is needed to increase the amount for an authorization. Only credit cards support increase of the authorized amount.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\IncreaseRequest(); // \Swagger\Client\Model\IncreaseRequest | Increase authorization amount
$transaction_id = 789; // int | 

try {
    $result = $apiInstance->increase($body, $transaction_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->increase: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\IncreaseRequest**](../Model/IncreaseRequest.md)| Increase authorization amount |
 **transaction_id** | **int**|  |

### Return type

[**\Swagger\Client\Model\IncreaseResponse**](../Model/IncreaseResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **init**
> \Swagger\Client\Model\InitResponse init($body)

Initialize a transaction

Securely send all the needed parameters to the transaction initialization API. The result of this API call is a `HTTP 201` status code with a `transactionId` in the response body and the `Location` header set. This call is required to proceed with our Redirect and Lightbox integration

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\InitRequest(); // \Swagger\Client\Model\InitRequest | 

try {
    $result = $apiInstance->init($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->init: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\InitRequest**](../Model/InitRequest.md)|  |

### Return type

[**\Swagger\Client\Model\InitResponse**](../Model/InitResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **screen**
> \Swagger\Client\Model\AuthorizeResponse screen($body)

Screen the customer details

Check the customer's credit score before sending an actual authorization request. No amount will be blocked on the customers account. Currently, only invoicing method `INT` support screening.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ScreenRequest(); // \Swagger\Client\Model\ScreenRequest | Screen request

try {
    $result = $apiInstance->screen($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->screen: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ScreenRequest**](../Model/ScreenRequest.md)| Screen request |

### Return type

[**\Swagger\Client\Model\AuthorizeResponse**](../Model/AuthorizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **secureFieldsInit**
> \Swagger\Client\Model\SecureFieldsInitResponse secureFieldsInit($body, $client_name)

Initialize a Secure Fields transaction

Proceed with the steps below to process [Secure Fields payment transactions](https://docs.datatrans.ch/docs/integrations-secure-fields):  - Call the /v1/transactions/secureFields endpoint to retrieve a `transactionId`. The success result of this API call is a `HTTP 201` status code with a `transactionId` in the response body. - Initialize the `SecureFields` JavaScript library with the returned `transactionId`: ```js var secureFields = new SecureFields(); secureFields.init(     transactionId, {         cardNumber: \"cardNumberPlaceholder\",         cvv: \"cvvPlaceholder\",     }); ``` - Handle the `success` event of the `secureFields.submit()` call. Example `success` event data: ```json {     \"event\":\"success\",     \"data\": {         \"transactionId\":\"{transactionId}\",         \"cardInfo\":{\"brand\":\"MASTERCARD\",\"type\":\"credit\",\"usage\":\"consumer\",\"country\":\"CH\",\"issuer\":\"DATATRANS\"},         \"redirect\":\"https://pay.sandbox.datatrans.com/upp/v1/3D2/{transactionId}\"     } } ``` - If 3D authentication is required, the `redirect` property will indicate the URL that the browser needs to be redirected to. - Use the [Authorize an authenticated transaction](#operation/authorize-split) endpoint to authorize the Secure Fields transaction. This is required to finalize the authorization process with Secure Fields. - Use the `transactionId` to check the [status](#operation/status) and to [settle](#operation/settle), [cancel](#operation/cancel) or [credit (refund)](#operation/refund) an transaction.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\DefaultSecureFieldsInitRequest(); // \Swagger\Client\Model\DefaultSecureFieldsInitRequest | 
$client_name = "client_name_example"; // string | 

try {
    $result = $apiInstance->secureFieldsInit($body, $client_name);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->secureFieldsInit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\DefaultSecureFieldsInitRequest**](../Model/DefaultSecureFieldsInitRequest.md)|  |
 **client_name** | **string**|  | [optional]

### Return type

[**\Swagger\Client\Model\SecureFieldsInitResponse**](../Model/SecureFieldsInitResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **secureFieldsUpdate**
> secureFieldsUpdate($body, $transaction_id)

Update the amount of a Secure Fields transaction

Use this API to update the amount of a Secure Fields transaction. This action is only allowed before the 3D process. At least one property must be updated.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\SecureFieldsUpdateRequest(); // \Swagger\Client\Model\SecureFieldsUpdateRequest | 
$transaction_id = 789; // int | 

try {
    $apiInstance->secureFieldsUpdate($body, $transaction_id);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->secureFieldsUpdate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\SecureFieldsUpdateRequest**](../Model/SecureFieldsUpdateRequest.md)|  |
 **transaction_id** | **int**|  |

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **settle**
> settle($body, $transaction_id)

Settle a transaction

The Settlement request is often also referred to as “Capture” or “Clearing”. It can be used for the settlement of previously authorized transactions. Only after settling a transaction the funds will be credited to your bank account. The `transactionId` is needed to settle an authorization. This API call is not needed if `autoSettle` was set to `true` when [initializing a transaction](#operation/init).

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\SettleRequest(); // \Swagger\Client\Model\SettleRequest | 
$transaction_id = 789; // int | 

try {
    $apiInstance->settle($body, $transaction_id);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->settle: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\SettleRequest**](../Model/SettleRequest.md)|  |
 **transaction_id** | **int**|  |

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **status**
> \Swagger\Client\Model\StatusResponse status($transaction_id)

Checking the status of a transaction

The API endpoint status can be used to check the status of any transaction, see its history, and retrieve the card information.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$transaction_id = 789; // int | 

try {
    $result = $apiInstance->status($transaction_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->status: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **transaction_id** | **int**|  |

### Return type

[**\Swagger\Client\Model\StatusResponse**](../Model/StatusResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **validate**
> \Swagger\Client\Model\AuthorizeResponse validate($body)

Validate an existing alias

An existing alias can be validated at any time with the transaction validate API. No amount will be blocked on the customers account. Only credit cards (including Apple Pay and Google Pay), `PFC`, `KLN` and `PAP` support validation of an existing alias.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1transactionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\ValidateRequest(); // \Swagger\Client\Model\ValidateRequest | Validate an alias

try {
    $result = $apiInstance->validate($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1transactionsApi->validate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\ValidateRequest**](../Model/ValidateRequest.md)| Validate an alias |

### Return type

[**\Swagger\Client\Model\AuthorizeResponse**](../Model/AuthorizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

