# TwintInitRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**alias** | **string** | An alias (token) received from a previous transaction if &#x60;option.createAlias&#x60; was set to &#x60;true&#x60;. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional] 
**order_details_url** | **string** | URL to the order details page, where the merchant displays a summary of the order and/or allows other functionality that is relevant for the use case. | [optional] 
**message_type_id_validity_hours** | **int** | Number of hours that messageTypeId (the Spotlight Message identifier provided by TWINT) will be valid. Spotlight message will be displayed during these validity hours. | [optional] 
**return_app_scheme** | **string** | Parameter which needs to be appended to the URL used in Twint Browser-to-App switch for iOS. For example &#x27;twint-issuer1&#x27;. | [optional] 
**return_app_package** | **string** | Parameter which needs to be appended to the URL used in Twint Browser-to-App switch for Android. For example &#x27;ch.twint.payment&#x27;. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

