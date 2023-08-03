# ByjunoAuthorizeRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**subtype** | **string** | The Byjuno specific payment method used for the transaction. | 
**alias** | **string** | An alias (token) received from a previous transaction if &#x60;option.createAlias&#x60; was set to &#x60;true&#x60;. In order to retrieve the alias from a previous transaction, use the [Status API](#operation/status). | [optional] 
**customer_email_confirmed** | **bool** | Indicates that the customer has confirmed the email address to the merchant | [optional] [default to false]
**customer_info1** | **string** | Customer information for credit check. | [optional] 
**customer_info2** | **string** | Customer information for credit check. | [optional] 
**delivery_method** | **string** | Can be one of POST (Delivery by Swiss Post), SHOP (Point of Sale) or HLD (Home Delivery Service) | [optional] 
**device_fingerprint_id** | **string** | Identification of the customer in the shop | [optional] 
**paper_invoice** | **bool** | Whether or not to send a paper invoice. | [optional] 
**repayment_type** | **int** | Number from 1 to 20 to indicate the repayment schedule. This is used in combination with payment methods and defined per client configuration. | [optional] 
**risk_owner** | **string** | Defines which party should take the risk. | [optional] 
**site_id** | **string** | Can be used in case when client operates different legally separated stores / points of sale. | [optional] 
**verified_document1_type** | **string** | Indication if merchant is having verified documents from client request. | [optional] 
**verified_document1_number** | **string** | Verified document number. | [optional] 
**verified_document1_issuer** | **string** | Verified document issuer. | [optional] 
**custom_data** | **string[]** | A list of custom data fields. It can accept up to 10 entries. | [optional] 
**first_rate_amount** | **int** | Amount of the first rate paid by the customer. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

