# InitResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_id** | **string** | The &#x60;transactionId&#x60; to be used when using Redirect- or Lightbox Mode. If no further action happens with the &#x60;transactionId&#x60; after initialization, it will be invalidated after 30 minutes. | [optional] 
**mobile_token** | **string** | Mobile token which is needed to initialize the Mobile SDKs. | [optional] 
**wec** | [**\Swagger\Client\Model\WeChatResponse**](WeChatResponse.md) |  | [optional] 
**twi** | [**\Swagger\Client\Model\TwintResponse**](TwintResponse.md) |  | [optional] 
**alp** | [**\Swagger\Client\Model\AlipayResponse**](AlipayResponse.md) |  | [optional] 
**_3_d** | [**\Swagger\Client\Model\Secure3DResponse**](Secure3DResponse.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

