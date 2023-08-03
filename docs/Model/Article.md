# Article

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** | The article identifier. | [optional] 
**name** | **string** | The name of the article. | [optional] 
**description** | **string** | The description of the article. | [optional] 
**image_url** | **string** | A https URL to the image of the article. | [optional] 
**price** | **int** | The line item price including the VAT. | [optional] 
**price_gross** | **int** | The article gross price including VAT. | [optional] 
**quantity** | **int** | The number of similar articles on the current invoice line item. | [optional] 
**tax** | **int** | This field is deprecated. Please use &#x60;taxAmount&#x60; for the tax amount and &#x60;taxPercent&#x60; for the tax rate. | [optional] 
**tax_percent** | **double** | The tax rate in percent [%]. | [optional] 
**tax_amount** | **int** | The tax amount in the currency’s smallest unit. For example use 1000 for CHF 10.00. | [optional] 
**price_without_vat** | **int** | The article price without VAT | [optional] 
**discount** | **int** | The article discount. | [optional] 
**type** | **string** | The type of the article. Possible values vary per payment method. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

