# StatusCardDetail

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | The resulting alias, if requested or available. | [optional] 
**fingerprint** | **string** | An unique identifier of the card number. Useful to identify multiple customers&#x27; or the same customer&#x27;s transactions where the same card was used. | [optional] 
**masked** | **string** | Masked credit card number. Can be used to display on a users profile page. For example: &#x60;424242xxxxxx4242&#x60; | [optional] 
**alias_cvv** | **string** | Alias of the CVV. Will be deleted immediately after authorization. | [optional] 
**expiry_month** | **string** | The expiry month of the credit card alias. | [optional] 
**expiry_year** | **string** | The expiry year of the credit card alias | [optional] 
**info** | [**\Swagger\Client\Model\CardInfo**](CardInfo.md) |  | [optional] 
**wallet_indicator** | **string** |  | [optional] 
**_3_d** | [**\Swagger\Client\Model\EMVCo3DAuthenticationDataStatusResponse**](EMVCo3DAuthenticationDataStatusResponse.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

