# ValidateRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**refno** | **string** | The merchant&#x27;s reference number. It should be unique for each transaction. | 
**refno2** | **string** | Optional customer&#x27;s reference number. Supported by some payment methods or acquirers. | [optional] 
**currency** | **string** | 3 letter &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_4217&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; | 
**card** | [**\Swagger\Client\Model\CardValidateRequest**](CardValidateRequest.md) |  | [optional] 
**pfc** | [**\Swagger\Client\Model\PfcValidateRequest**](PfcValidateRequest.md) |  | [optional] 
**kln** | [**\Swagger\Client\Model\KlarnaValidateRequest**](KlarnaValidateRequest.md) |  | [optional] 
**pap** | [**\Swagger\Client\Model\PayPalValidateRequest**](PayPalValidateRequest.md) |  | [optional] 
**pay** | [**\Swagger\Client\Model\GooglePayValidateRequest**](GooglePayValidateRequest.md) |  | [optional] 
**apl** | [**\Swagger\Client\Model\ApplePayValidateRequest**](ApplePayValidateRequest.md) |  | [optional] 
**esy** | [**\Swagger\Client\Model\EasyPayValidateRequest**](EasyPayValidateRequest.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

