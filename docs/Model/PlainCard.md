# PlainCard

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **string** |  | [optional] 
**number** | **string** | Merchants that have the option to store card information on their end can use the &#x60;number&#x60; property instead of &#x60;alias&#x60;. Please note that this option is only available to merchants that fulfill the requirements by PCI DSS to store sensitive information on their side and only upon request. | [optional] 
**expiry_month** | **string** | The expiry month of the credit card. | [optional] 
**expiry_year** | **string** | The expiry year of the credit card. | [optional] 
**_3_d** | [**\Swagger\Client\Model\EMVCo3DAuthenticationDataAuthorizeRequest**](EMVCo3DAuthenticationDataAuthorizeRequest.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

