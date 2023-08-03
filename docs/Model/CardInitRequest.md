# CardInitRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | An alias (token) received from a previous transaction if &#x60;option.createAlias&#x60; was set to &#x60;true&#x60;. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional] 
**number** | **string** | Merchants that have the option to store card information on their end can use the &#x60;number&#x60; property instead of &#x60;alias&#x60;. Please note that this option is only available to merchants that fulfill the requirements by PCI DSS to store sensitive information on their side and only upon request. | [optional] 
**expiry_month** | **string** | The expiry month of the credit card alias. | [optional] 
**expiry_year** | **string** | The expiry year of the credit card alias | [optional] 
**create_alias_cvv** | **bool** | Specifies whether a CVV alias should be created | [optional] 
**_3_d** | [**\Swagger\Client\Model\CardInitThreeDSecure**](CardInitThreeDSecure.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

