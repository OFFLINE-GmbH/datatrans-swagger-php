# OptionRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**create_alias** | **bool** | Whether an alias should be created for this transaction or not. If set to &#x60;true&#x60; an alias will be created. This alias can then be used to [initialize](#operation/init) or [authorize](#operation/authorize) a transaction. One possible use case is to charge the card of an existing (registered) cardholder. | [optional] 
**return_masked_card_number** | **bool** | Whether to return the masked card number. Format: &#x60;520000xxxxxx0080&#x60; | [optional] 
**return_customer_country** | **bool** | If set to &#x60;true&#x60;, the country of the customers issuer will be returned. | [optional] 
**authentication_only** | **bool** | Whether to only authenticate the transaction (3D process only). If set to &#x60;true&#x60;, the actual authorization will not take place. | [optional] 
**remember_me** | **string** | Whether to show a checkbox on the payment page to let the customer choose if they want to save their card information. | [optional] 
**return_mobile_token** | **bool** | Indicates that a mobile token should be created. This is needed when using our Mobile SDKs. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

