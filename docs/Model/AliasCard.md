# AliasCard

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **string** |  | [optional] 
**alias** | **string** | An alias (token) received from a previous transaction if &#x60;option.createAlias&#x60; was set to &#x60;true&#x60;. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional] 
**expiry_month** | **string** | The expiry month of the credit card alias. | [optional] 
**expiry_year** | **string** | The expiry year of the credit card alias. | [optional] 
**_3_d** | [**\Swagger\Client\Model\EMVCo3DAuthenticationDataAuthorizeRequest**](EMVCo3DAuthenticationDataAuthorizeRequest.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

