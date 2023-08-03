# CustomerRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** | Unique customer identifier | [optional] 
**title** | **string** | Something like &#x60;Ms&#x60; or &#x60;Mrs&#x60; | [optional] 
**first_name** | **string** | The first name of the customer. | [optional] 
**last_name** | **string** | The last name of the customer. | [optional] 
**street** | **string** | The street of the customer. | [optional] 
**street2** | **string** | Additional street information. For example: &#x27;3rd floor&#x27; | [optional] 
**city** | **string** | The city of the customer. | [optional] 
**country** | **string** | 2 letter ISO 3166-1 alpha-2 country code | [optional] 
**zip_code** | **string** | Zip code of the customer. | [optional] 
**phone** | **string** | Phone number of the customer. | [optional] 
**cell_phone** | **string** | Cell Phone number of the customer. | [optional] 
**email** | **string** | The email address of the customer. | [optional] 
**gender** | **string** | Gender of the customer. &#x60;female&#x60; or &#x60;male&#x60;. | [optional] 
**birth_date** | [**\DateTime**](\DateTime.md) | The birth date of the customer. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (&#x60;YYYY-MM-DD&#x60;). | [optional] 
**language** | **string** | The language of the customer. | [optional] 
**type** | **string** | &#x60;P&#x60; or &#x60;C&#x60; depending on whether the customer is private or a company. If &#x60;C&#x60;, the fields &#x60;name&#x60; and &#x60;companyRegisterNumber&#x60; are required | [optional] 
**name** | **string** | The name of the company. Only applicable if &#x60;type&#x3D;C&#x60; | [optional] 
**company_legal_form** | **string** | The legal form of the company (AG, GmbH, ...) | [optional] 
**company_register_number** | **string** | The register number of the company. Only applicable if &#x60;type&#x3D;C&#x60; | [optional] 
**ip_address** | **string** | The ip address of the customer. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

