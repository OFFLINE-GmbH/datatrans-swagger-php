# PayPalAuthorizeRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | An alias (token) received from a previous transaction if &#x60;option.createAlias&#x60; was set to &#x60;true&#x60;. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional] 
**order_transaction_id** | **string** | The transactionId of the order request executed previously, if this authorization is part of the order-authorize-capture (AC2) flow. | [optional] 
**fraud_session_id** | **string** | The PayPal FraudNet session identifier as specified in the API documentation. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

