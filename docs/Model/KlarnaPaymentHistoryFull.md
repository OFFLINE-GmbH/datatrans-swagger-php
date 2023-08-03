# KlarnaPaymentHistoryFull

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**unique_identifier** | **string** | Unique name or number to identify the specific customer account. Max. 24 characters. | [optional] 
**payment_option** | **string** | The type of the line item | [optional] 
**paid_purchases** | **int** | The total number of successful purchases. | [optional] 
**total_amount_paid_purchases** | **double** | The total amount of successful purchases (in local currency). | [optional] 
**last_paid_purchase** | [**\DateTime**](\DateTime.md) | The date and time of the last paid purchase. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional] 
**first_paid_purchase** | [**\DateTime**](\DateTime.md) | The date and time of the first paid purchase. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

