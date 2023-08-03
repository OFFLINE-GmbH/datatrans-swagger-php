# AuthorizeRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**currency** | **string** | 3 letter &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_4217&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; | 
**refno** | **string** | The merchant&#x27;s reference number. It should be unique for each transaction. | 
**refno2** | **string** | Optional customer&#x27;s reference number. Supported by some payment methods or acquirers. | [optional] 
**auto_settle** | **bool** | Whether to automatically settle the transaction after an authorization or not. If not present with the init request, the settings defined in the dashboard (&#x27;Authorisation / Settlement&#x27; or &#x27;Direct Debit&#x27;) will be used. Those settings will only be used for web transactions and not for server to server API calls. | [optional] 
**customer** | [**\Swagger\Client\Model\CustomerRequest**](CustomerRequest.md) |  | [optional] 
**billing** | [**\Swagger\Client\Model\BillingAddress**](BillingAddress.md) |  | [optional] 
**shipping** | [**\Swagger\Client\Model\ShippingAddress**](ShippingAddress.md) |  | [optional] 
**order** | [**\Swagger\Client\Model\OrderRequest**](OrderRequest.md) |  | [optional] 
**card** | [**\Swagger\Client\Model\Card**](Card.md) |  | [optional] 
**bon** | [**\Swagger\Client\Model\BoncardRequest**](BoncardRequest.md) |  | [optional] 
**pap** | [**\Swagger\Client\Model\PayPalAuthorizeRequest**](PayPalAuthorizeRequest.md) |  | [optional] 
**pfc** | [**\Swagger\Client\Model\PfcAuthorizeRequest**](PfcAuthorizeRequest.md) |  | [optional] 
**rek** | [**\Swagger\Client\Model\RekaRequest**](RekaRequest.md) |  | [optional] 
**kln** | [**\Swagger\Client\Model\KlarnaAuthorizeRequest**](KlarnaAuthorizeRequest.md) |  | [optional] 
**twi** | [**\Swagger\Client\Model\TwintAuthorizeRequest**](TwintAuthorizeRequest.md) |  | [optional] 
**int** | [**\Swagger\Client\Model\ByjunoAuthorizeRequest**](ByjunoAuthorizeRequest.md) |  | [optional] 
**alp** | [**\Swagger\Client\Model\AlipayRequest**](AlipayRequest.md) |  | [optional] 
**esy** | [**\Swagger\Client\Model\ESY**](ESY.md) |  | [optional] 
**mfa** | [**\Swagger\Client\Model\MfaAuthorizeRequest**](MfaAuthorizeRequest.md) |  | [optional] 
**swp** | [**\Swagger\Client\Model\SwissPassRequest**](SwissPassRequest.md) |  | [optional] 
**airline_data** | [**\Swagger\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional] 
**accertify** | [**\Swagger\Client\Model\Accertify**](Accertify.md) |  | [optional] 
**three_ri_data** | [**\Swagger\Client\Model\ThreeRIData**](ThreeRIData.md) |  | [optional] 
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. | 
**acc** | [**\Swagger\Client\Model\AccardaRequest**](AccardaRequest.md) |  | [optional] 
**pay** | [**\Swagger\Client\Model\GooglePayRequest**](GooglePayRequest.md) |  | [optional] 
**apl** | [**\Swagger\Client\Model\ApplePayRequest**](ApplePayRequest.md) |  | [optional] 
**mpa** | [**\Swagger\Client\Model\MpaAuthorizeRequest**](MpaAuthorizeRequest.md) |  | [optional] 
**mpg** | [**\Swagger\Client\Model\MpgAuthorizeRequest**](MpgAuthorizeRequest.md) |  | [optional] 
**mfg** | [**\Swagger\Client\Model\MfgAuthorizeRequest**](MfgAuthorizeRequest.md) |  | [optional] 
**marketplace** | [**\Swagger\Client\Model\MarketPlaceAuthorize**](MarketPlaceAuthorize.md) |  | [optional] 
**elv** | [**\Swagger\Client\Model\ElvRequest**](ElvRequest.md) |  | [optional] 
**swb** | [**\Swagger\Client\Model\SwissBillingAuthorizeRequest**](SwissBillingAuthorizeRequest.md) |  | [optional] 
**mcp** | [**\Swagger\Client\Model\AuthorizeMcpRequest**](AuthorizeMcpRequest.md) |  | [optional] 
**extensions** | [**\Swagger\Client\Model\Extension**](Extension.md) |  | [optional] 
**_3_ri** | [**\Swagger\Client\Model\ThreeRI**](ThreeRI.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

