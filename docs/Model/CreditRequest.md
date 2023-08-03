# CreditRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. | [optional] 
**currency** | **string** | 3 letter &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_4217&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; | 
**refno** | **string** | The merchant&#x27;s reference number. It should be unique for each transaction. | 
**refno2** | **string** | Optional customer&#x27;s reference number. Supported by some payment methods or acquirers. | [optional] 
**marketplace** | [**\Swagger\Client\Model\MarketPlaceCredit**](MarketPlaceCredit.md) |  | [optional] 
**extensions** | [**\Swagger\Client\Model\Extension**](Extension.md) |  | [optional] 
**mcp** | [**\Swagger\Client\Model\CreditMcpRequest**](CreditMcpRequest.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

