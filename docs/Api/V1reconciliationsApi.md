# Swagger\Client\V1reconciliationsApi

All URIs are relative to *https://api.sandbox.datatrans.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**bulkSaleReport**](V1reconciliationsApi.md#bulksalereport) | **POST** /v1/reconciliations/sales/bulk | Bulk reporting of sales
[**saleReport**](V1reconciliationsApi.md#salereport) | **POST** /v1/reconciliations/sales | Report a sale

# **bulkSaleReport**
> \Swagger\Client\Model\SaleReportResponse bulkSaleReport($body)

Bulk reporting of sales

If you are a merchant using our reconciliation services, you can use this API to confirm multiple sales with a single API call. The matching is based on the `transactionId`. The status of the transaction will change to `compensated`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1reconciliationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\BulkSaleReportRequest(); // \Swagger\Client\Model\BulkSaleReportRequest | 

try {
    $result = $apiInstance->bulkSaleReport($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1reconciliationsApi->bulkSaleReport: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\BulkSaleReportRequest**](../Model/BulkSaleReportRequest.md)|  |

### Return type

[**\Swagger\Client\Model\SaleReportResponse**](../Model/SaleReportResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **saleReport**
> \Swagger\Client\Model\SaleReportResponse saleReport($body)

Report a sale

If you are a merchant using our reconciliation services, you can use this API to confirm a sale. The matching is based on the `transactionId`. The status of the transaction will change to `compensated`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');
// Configure HTTP basic authorization: Basic
$config = Swagger\Client\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');


$apiInstance = new Swagger\Client\Api\V1reconciliationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = new \Swagger\Client\Model\SaleReportRequest(); // \Swagger\Client\Model\SaleReportRequest | 

try {
    $result = $apiInstance->saleReport($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling V1reconciliationsApi->saleReport: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\Swagger\Client\Model\SaleReportRequest**](../Model/SaleReportRequest.md)|  |

### Return type

[**\Swagger\Client\Model\SaleReportResponse**](../Model/SaleReportResponse.md)

### Authorization

[Basic](../../README.md#Basic)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

