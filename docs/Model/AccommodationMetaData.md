# AccommodationMetaData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**version** | **string** | The version of AccommodationMetaData field (used for tracking schema changes to the field) | [optional] 
**length_of_stay** | **int** | The number of nights that the accommodation was booked for. | [optional] 
**number_of_guests** | **int** | The number of guests for which the accommodation is booked | [optional] 
**start_date** | [**\DateTime**](\DateTime.md) | The date on which the accommodation starts. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). Internally, Amazon will store the number of days and hours between accommodation.startDate and time of the purchase. | [optional] 
**end_date** | [**\DateTime**](\DateTime.md) | The date on which the accommodation ends. Must be in &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_8601&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-8601&lt;/a&gt; format (e.g. &#x60;YYYY-MM-DDTHH:MM:ss.SSSZ&#x60;). If accommodation.lengthOfStay is given, we default to an endDate derived from startDate and lengthOfStay. | [optional] 
**star_rating** | **int** | Star rating of the accommodation. From 0 (for no star rating) to 5 (for five star hotels) | [optional] 
**booked_last_time** | **int** | Days since the buyer booked the same accommodation last time. Use value -1 if buyer books this accommodation for the first time. | [optional] 
**city** | **string** | The city where the accommodation is located. Example: Milan. | [optional] 
**country_code** | **string** | ISO 3166-1 alpha-2, two-letter country code, representing the country where the accommodation is located. Example: IT. | [optional] 
**zip_code** | **string** | The zip code of the accommodation address. Example: 40127. | [optional] 
**accommodation_type** | **string** | Describes the type of accommodation, valid values:[Hotel] | [optional] 
**accommodation_name** | **string** | The name of the accommodation, as provided to the merchant by the accommodation itself. | [optional] 
**class** | **string** | Suite, Standard or Deluxe accommodation | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

