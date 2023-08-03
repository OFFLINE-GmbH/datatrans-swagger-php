# OrderMetaData

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**version** | **string** | The version of OrderMetaData field (used for tracking schema changes to the field). | [optional] 
**number_of_items** | **int** | The number of items that the order contains. For example, two cups of coffee. | [optional] 
**type** | **string** | Type of items. Physical, Digital, Mixed | [optional] 
**related_order_reference_id** | **string** | Order ID of the related order. For the deposit this field will be empty, while for any subsequent payment related to the same booking this will be the order ID of the deposit transaction. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

