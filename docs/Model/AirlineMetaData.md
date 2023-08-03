# AirlineMetaData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**version** | **string** | The version of AirlineMetaData field (used for tracking schema changes to the field) | [optional] 
**airline_code** | **string** | IATA 2-letter airline code. It identifies the carrier. Example: AA (American Airlines) | [optional] 
**flight_date** | [**\DateTime**](\DateTime.md) | Flight departure date. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). The time mentioned here is local time | [optional] 
**departure_airport** | **string** | IATA 3-letter code of the departure airport. Example: CDG | [optional] 
**destination_airport** | **string** | IATA 3-letter code of the departure airport. Example: LUX | [optional] 
**class_of_travel** | **string** | travel class identifier. | [optional] 
**booked_last_time** | **int** | Days since the buyer booked a flight to the same destination last time. Use value -1 if buyer books this destination for the first time. | [optional] 
**passengers** | [**\Swagger\Client\Model\Passenger**](Passenger.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

