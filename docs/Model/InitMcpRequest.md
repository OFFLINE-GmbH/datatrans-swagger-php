# InitMcpRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**currency** | **string** | The targeted currency | 
**amount** | **int** | The amount in your targeted currency | 
**conversion_rate** | **double** | Conversion rate received from the currency rates endpoint. Required in case of dynamic MCP. | [optional] 
**transaction_date** | [**\DateTime**](\DateTime.md) | Transaction datetime received from the currency rates endpoint | [optional] 
**retrieval_reference_number** | **string** | RetrievalReferenceNumber received from the currency rates endpoint | [optional] 
**user_id** | **string** |  | 
**provider** | **string** | The provider for multi currency processing | 
**reason_indicator** | **string** | If received from acquirer the reason indicator can be set | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

