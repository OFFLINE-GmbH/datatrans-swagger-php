# RedirectRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**success_url** | **string** | The URL where the customer gets redirected to if the transaction was successful. | [optional] 
**cancel_url** | **string** | The URL where the customer gets redirected to if the transaction was canceled. | [optional] 
**error_url** | **string** | The URL where the customer gets redirected to if an error occurred. | [optional] 
**start_target** | **string** | If the payment is started within an iframe or when using the Lightbox Mode, use value &#x60;_top&#x60;. This ensures a proper browser flow for payment methods who need a redirect. | [optional] 
**return_target** | **string** | If the payment is started within an iframe or when using the Lightbox Mode, use &#x60;_top&#x60; if the redirect URLs should be opened full screen when payment returns from a 3rd party (for example 3D). | [optional] 
**method** | **string** | The preferred HTTP method for the redirect request (&#x60;GET&#x60; or &#x60;POST&#x60;). When using GET as a method, the query string parameter &#x60;datatransTrxId&#x60; will be added to the corresponding return url upon redirection. In case of POST, all the query parameters from the corresponding return url will be moved to the application/x-www-form-urlencoded body of the redirection request along with the added &#x60;datatransTrxId&#x60; parameter. | [optional] [default to 'GET']

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

