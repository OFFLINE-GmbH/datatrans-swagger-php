# BuyerMetaData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**version** | **string** | The version of BuyerMetaData field (used for tracking schema changes to the field). | [optional] 
**is_first_time_customer** | **bool** | True if the buyer is purchasing from the merchant for the first time. Else false. | [optional] 
**number_of_past_purchases** | **int** | The number of purchases the buyer has made from the merchant in the past. | [optional] 
**number_of_disputed_purchases** | **int** | The number of purchases that has been disputed by the buyer when making purchases from the merchant. | [optional] 
**has_open_dispute** | **bool** | True if the buyer has an ongoing dispute regarding a past purchase. | [optional] 
**risk_score** | **string** | The risk score which the merchant computes for a buyer. The value must be a decimal in the range between 0 (lowest risk) and 1 (highest risk). | [optional] 
**user_agent** | **string** | The user agent of the browser used by the buyer to make the purchase on merchant site. Example: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36. | [optional] 
**language** | **string** | Language in which the buyer is viewing the site at the time of placing the order in &#x27;language-LOCALE&#x27; format example: en-US. Use ISO 639-1:2002 code for the language part (en) and ISO 3166-1 alpha-2 for the LOCALE part (US). | [optional] 
**recipient_email_matches** | **bool** | True, if the recipient email is exactly the same as the one on the amazon account used for payment, false otherwise. | [optional] 
**buyer_is_a_traveler** | **bool** | True, if the account holder of the amazon account is actually one of the travelers, false otherwise. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

