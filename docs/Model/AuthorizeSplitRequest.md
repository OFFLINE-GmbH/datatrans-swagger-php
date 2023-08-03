# AuthorizeSplitRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. | [optional] 
**refno** | **string** | The merchant&#x27;s reference number. It should be unique for each transaction. | 
**refno2** | **string** | Optional customer&#x27;s reference number. Supported by some payment methods or acquirers. | [optional] 
**auto_settle** | **bool** | Whether to automatically settle the transaction after an authorization or not. If not present with the init request, the settings defined in the dashboard (&#x27;Authorisation / Settlement&#x27; or &#x27;Direct Debit&#x27;) will be used. Those settings will only be used for web transactions and not for server to server API calls. | [optional] 
**cdm** | [**\Swagger\Client\Model\CDMRequest**](CDMRequest.md) |  | [optional] 
**airline_data** | [**\Swagger\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional] 
**_3_d** | [**\Swagger\Client\Model\AuthorizeSplitThreeDSecure**](AuthorizeSplitThreeDSecure.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

