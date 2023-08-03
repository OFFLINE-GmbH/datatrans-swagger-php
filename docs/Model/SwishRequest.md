# SwishRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**payer_alias** | **string** | The registered cellphone number of the person that makes the payment. It can only contain numbers and has to be at least 8 and at most 15 numbers. It also needs to match the following format in order to be found in Swish: country code + cellphone number (without leading zero). E.g.: 46712345678 | [optional] 
**payment_request_message** | **string** | Merchant supplied message about the payment/order. Max 50 characters. Common allowed characters are the letters a-ö, A-Ö, the numbers 0-9, and special characters !?&#x3D;#$%&amp;()*+,-./:;&lt;&#x27;\&quot;@. In addition, the following special characters are also allowed: ^¡¢£€¥¿Š§šŽžŒœŸÀÁÂÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕØØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

