# KlarnaHotelItinerary

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**hotel_name** | **string** | Name of hotel | [optional] 
**address** | [**\Swagger\Client\Model\KlarnaAddress**](KlarnaAddress.md) |  | [optional] 
**start_time** | [**\DateTime**](\DateTime.md) | The start date and time of the reservation. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional] 
**end_time** | [**\DateTime**](\DateTime.md) | The end date and time of the reservation. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). | [optional] 
**number_of_rooms** | **int** |  | [optional] 
**passenger_id** | **int[]** |  | [optional] 
**ticket_delivery_method** | **string** |  | [optional] 
**ticket_delivery_recipient** | **string** | The name of the recipient the ticket is delivered to. If email or phone, then use either the email address or the phone number. | [optional] 
**hotel_price** | **float** | Local currency | [optional] 
**class** | **string** |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

