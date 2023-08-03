# ApplePayValidateRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**data** | **string** | Encrypted payment data. | [optional] 
**header** | [**\Swagger\Client\Model\Header**](Header.md) |  | [optional] 
**signature** | **string** | Signature of the payment and header data. The signature includes the signing certificate, its intermediate CA certificate, and information about the signing algorithm. | [optional] 
**version** | **string** | Version information about the payment token. The token uses &#x60;EC_v1&#x60; for ECC-encrypted data, and &#x60;RSA_v1&#x60; for RSA-encrypted data. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

