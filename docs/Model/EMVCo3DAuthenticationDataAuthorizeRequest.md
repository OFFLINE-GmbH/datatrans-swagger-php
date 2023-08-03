# EMVCo3DAuthenticationDataAuthorizeRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**eci** | **string** | The Electronic Commerce Indicator | [optional] 
**xid** | **string** | The transaction ID returned by the directory server | [optional] 
**three_ds_transaction_id** | **string** | The transaction ID returned by the 3D Secure Provider | [optional] 
**cavv** | **string** | The Cardholder Authentication Verification Value | [optional] 
**three_ds_version** | **string** | The 3D version | [optional] 
**cavv_algorithm** | **string** | The 3D algorithm | [optional] 
**directory_response** | **string** | Transaction status after &#x60;ARes&#x60;  |Value|3Dv1|3Dv2| |:---|:---|:---| |Y| enrolled| authenticated| |N| not enrolled| authentication failed| |U| not available| not available| |C| |challenge needed| |R| |rejected| |A| |authentication attempt| | [optional] 
**authentication_response** | **string** | Transaction status after &#x60;RReq&#x60; (Challenge flow)  |Value|3Dv1|3Dv2| |:---|:---|:---| |Y| authenticated| authenticated| |N| authentication failed| authentication failed| |U| not available| not available| |A| authentication attempt| authentication attempt| |C| process incomplete| process incomplete| |D| not enrolled| | | [optional] 
**trans_status_reason** | **string** | Transaction status reason  |Value|Description| |:---|:---| |01| Card authentication failed| |02| Unknown Device| |03| Unsupported Device| |04| Exceeds authentication frequency limit| |05| Expired card| |06| Invalid card number| |07| Invalid transaction| |08| No Card record| |09| Security failure| |10| Stolen card| |11| Suspected fraud| |12| Transaction not permitted to cardholder| |13| Cardholder not enrolled in service| |14| Transaction timed out at the ACS| |15| Low confidence| |16| Medium confidence| |17| High confidence| |18| Very High confidence| |19| Exceeds ACS maximum challenges| |20| Non-Payment transaction not supported| |21| 3RI transaction not supported| |22| ACS technical issue| |23| Decoupled Authentication required by ACS but not requested by 3DS Requestor| |24| 3DS Requestor Decoupled Max Expiry Time exceeded| |25| Decoupled Authentication was provided insufficient time to authenticate cardholder. ACS will not make attempt| |26| Authentication attempted but not performed by the cardholder| |27–79| Reserved for EMVCo future use (values invalid until defined by EMVCo)| |80–99 | Reserved for DS use| | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

