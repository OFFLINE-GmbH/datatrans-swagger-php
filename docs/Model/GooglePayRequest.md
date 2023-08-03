# GooglePayRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**signature** | **string** | Verifies that the message came from Google. It&#x27;s Base64-encoded, and created with ECDSA by the intermediate signing key. | [optional] 
**protocol_version** | **string** | Identifies the encryption or signing scheme under which the message was created. It allows the protocol to evolve over time, if needed. | [optional] 
**signed_message** | **string** | A JSON object serialized as a string that contains the encryptedMessage, ephemeralPublicKey, and tag. It&#x27;s serialized to simplify the signature verification process. | [optional] 
**intermediate_signing_key** | [**\Swagger\Client\Model\IntermediateSigningKey**](IntermediateSigningKey.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

