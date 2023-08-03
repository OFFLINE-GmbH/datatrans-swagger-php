# AirlineDataRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**country_code** | **string** | Passenger country code in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-3166-1-alpha2&lt;/a&gt; format. | [optional] 
**agent_code** | **string** | IATA agency code | [optional] 
**pnr** | **string** | PNR | [optional] 
**issue_date** | [**\DateTime**](\DateTime.md) | Ticket issuing date. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (&#x60;YYYY-MM-DD&#x60;). | [optional] 
**tickets** | [**\Swagger\Client\Model\Ticket[]**](Ticket.md) | A list of tickets for this purchase. Note: PAP only supports one ticket. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

