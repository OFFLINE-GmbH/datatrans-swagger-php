# PayPalInitRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | An alias (token) received from a previous transaction if &#x60;option.createAlias&#x60; was set to &#x60;true&#x60;. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional] 
**image_url** | **string** | A https URL to the logo of the merchant. | [optional] 
**display_shipping_details** | **bool** | Regulates whether the shipping details are displayed or not. (Note: &#x60;forwardCustomerDetails&#x60; and &#x60;displayShippingDetails&#x60; should be set to &#x60;no&#x60; if the shipping details should not be shown on the PayPal page. | [optional] 
**forward_customer_details** | **bool** | &#x60;true&#x60; if the customer details (if submitted) should be forwarded to PayPal. Default is &#x60;false&#x60;. | [optional] 
**return_customer_details** | **bool** | &#x60;true&#x60; if the customer details should be retrieved from PayPal. | [optional] 
**create_order** | **bool** | &#x60;true&#x60; if a PayPal AC2 order is to be created. Default is &#x60;false&#x60;. | [optional] 
**fraud_session_id** | **string** | The PayPal FraudNet session identifier as specified in the API documentation. | [optional] 
**transaction_context** | [**\Swagger\Client\Model\TransactionContext**](TransactionContext.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

