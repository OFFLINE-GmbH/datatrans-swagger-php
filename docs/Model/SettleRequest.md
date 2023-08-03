# SettleRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. | 
**currency** | **string** | 3 letter &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_4217&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; | 
**refno** | **string** | The merchant&#x27;s reference number. Most payment methods require you to have a unique reference for a transaction. In case you must change the reference number in settlement, ensure first it is supported by the dedicated payment method. | 
**refno2** | **string** | Optional customer&#x27;s reference number. Supported by some payment methods or acquirers. | [optional] 
**airline_data** | [**\Swagger\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional] 
**marketplace** | [**\Swagger\Client\Model\MarketPlaceSettle**](MarketPlaceSettle.md) |  | [optional] 
**mcp** | [**\Swagger\Client\Model\SettleMcpRequest**](SettleMcpRequest.md) |  | [optional] 
**partial_capture** | [**\Swagger\Client\Model\MultiplePartialCapture**](MultiplePartialCapture.md) |  | [optional] 
**extensions** | [**\Swagger\Client\Model\Extension**](Extension.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

