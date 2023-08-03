# Swagger\Client\V1multicurrencyApi

All URIs are relative to *https://api.sandbox.datatrans.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getRates**](V1multicurrencyApi.md#getrates) | **GET** /v1/multicurrency/rates | Get conversion rates for different currencies

# **getRates**
> \Swagger\Client\Model\MultiCurrencyReportResponse getRates()

Get conversion rates for different currencies

To get current rates call this endpoint. It will return all available rates for the configured merchant. Note: These rates are Acquirer specific, need a specific acquiring contract and need to be set up by Datatrans.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1multicurrencyApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->getRates();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1multicurrencyApi->getRates: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\Swagger\Client\Model\MultiCurrencyReportResponse**](../Model/MultiCurrencyReportResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

