# Swagger\Client\V1aliasesApi

All URIs are relative to *https://api.sandbox.datatrans.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**aliasesConvert**](V1aliasesApi.md#aliasesconvert) | **POST** /v1/aliases | Convert alias
[**aliasesDelete**](V1aliasesApi.md#aliasesdelete) | **DELETE** /v1/aliases/{alias} | Delete alias
[**aliasesDetokenize**](V1aliasesApi.md#aliasesdetokenize) | **POST** /v1/aliases/detokenize | Bulk detokenization
[**aliasesInfo**](V1aliasesApi.md#aliasesinfo) | **GET** /v1/aliases/{alias} | Get alias info
[**aliasesPatch**](V1aliasesApi.md#aliasespatch) | **PATCH** /v1/aliases/{alias} | Patch alias
[**aliasesTokenize**](V1aliasesApi.md#aliasestokenize) | **POST** /v1/aliases/tokenize | Bulk tokenization

# **aliasesConvert**
> \Swagger\Client\Model\AliasConvertResponse aliasesConvert($body)

Convert alias

Convert a legacy (numeric or masked) alias to the most recent alias format. Currently, only credit card aliases can be converted.

### Example
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
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\AliasConvertRequest**](../Model/AliasConvertRequest.md)|  |

### Return type

[**\Swagger\Client\Model\AliasConvertResponse**](../Model/AliasConvertResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **aliasesDelete**
> aliasesDelete($alias)

Delete alias

Delete an alias with immediate effect. The alias will no longer be recognized if used later with any API call.

### Example
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
$alias = "alias_example"; // string | 

try {
    $apiInstance->aliasesDelete($alias);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesDelete: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **alias** | **string**|  |

### Return type

void (empty response body)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **aliasesDetokenize**
> \Swagger\Client\Model\BulkDetokenizeResponse aliasesDetokenize($body)

Bulk detokenization

Detokenize cards, CVVs and custom fields. It supports single and bulk detokenization for batches of requests.

### Example
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
$body = new \Swagger\Client\Model\BulkDetokenizeRequest(); // \Swagger\Client\Model\BulkDetokenizeRequest | 

try {
    $result = $apiInstance->aliasesDetokenize($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesDetokenize: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\BulkDetokenizeRequest**](../Model/BulkDetokenizeRequest.md)|  |

### Return type

[**\Swagger\Client\Model\BulkDetokenizeResponse**](../Model/BulkDetokenizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **aliasesInfo**
> \Swagger\Client\Model\AliasInfoResponse aliasesInfo($alias)

Get alias info

Get alias info.

### Example
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
$alias = "alias_example"; // string | 

try {
    $result = $apiInstance->aliasesInfo($alias);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesInfo: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **alias** | **string**|  |

### Return type

[**\Swagger\Client\Model\AliasInfoResponse**](../Model/AliasInfoResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **aliasesPatch**
> \Swagger\Client\Model\AliasInfoResponse aliasesPatch($body, $alias)

Patch alias

Update an existing card alias with expiration year and month.

### Example
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
$body = new \Swagger\Client\Model\AliasPatchRequest(); // \Swagger\Client\Model\AliasPatchRequest | 
$alias = "alias_example"; // string | 

try {
    $result = $apiInstance->aliasesPatch($body, $alias);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesPatch: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\AliasPatchRequest**](../Model/AliasPatchRequest.md)|  |
 **alias** | **string**|  |

### Return type

[**\Swagger\Client\Model\AliasInfoResponse**](../Model/AliasInfoResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **aliasesTokenize**
> \Swagger\Client\Model\BulkTokenizeResponse aliasesTokenize($body)

Bulk tokenization

Tokenize cards, CVVs and custom fields. It supports single and bulk tokenization for batches of requests.

### Example
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
$body = new \Swagger\Client\Model\BulkTokenizeRequest(); // \Swagger\Client\Model\BulkTokenizeRequest | 

try {
    $result = $apiInstance->aliasesTokenize($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1aliasesApi->aliasesTokenize: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\BulkTokenizeRequest**](../Model/BulkTokenizeRequest.md)|  |

### Return type

[**\Swagger\Client\Model\BulkTokenizeResponse**](../Model/BulkTokenizeResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

