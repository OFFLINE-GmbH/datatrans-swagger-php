# InitRequest

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
**card** | [**\Swagger\Client\Model\CardInitRequest**](CardInitRequest.md) |  | [optional] 
**bon** | [**\Swagger\Client\Model\BoncardRequest**](BoncardRequest.md) |  | [optional] 
**pap** | [**\Swagger\Client\Model\PayPalInitRequest**](PayPalInitRequest.md) |  | [optional] 
**pfc** | [**\Swagger\Client\Model\PfcInitRequest**](PfcInitRequest.md) |  | [optional] 
**rek** | [**\Swagger\Client\Model\RekaRequest**](RekaRequest.md) |  | [optional] 
**kln** | [**\Swagger\Client\Model\KlarnaInitRequest**](KlarnaInitRequest.md) |  | [optional] 
**twi** | [**\Swagger\Client\Model\TwintInitRequest**](TwintInitRequest.md) |  | [optional] 
**int** | [**\Swagger\Client\Model\ByjunoAuthorizeRequest**](ByjunoAuthorizeRequest.md) |  | [optional] 
**alp** | [**\Swagger\Client\Model\AlipayRequest**](AlipayRequest.md) |  | [optional] 
**esy** | [**\Swagger\Client\Model\ESY**](ESY.md) |  | [optional] 
**mfa** | [**\Swagger\Client\Model\MfaAuthorizeRequest**](MfaAuthorizeRequest.md) |  | [optional] 
**swp** | [**\Swagger\Client\Model\SwissPassRequest**](SwissPassRequest.md) |  | [optional] 
**airline_data** | [**\Swagger\Client\Model\AirlineDataRequest**](AirlineDataRequest.md) |  | [optional] 
**accertify** | [**\Swagger\Client\Model\Accertify**](Accertify.md) |  | [optional] 
**three_ri_data** | [**\Swagger\Client\Model\ThreeRIData**](ThreeRIData.md) |  | [optional] 
**amount** | **int** | The amount of the transaction in the currency’s smallest unit. For example use 1000 for CHF 10.00. Can be omitted for use cases where only a registration should take place (if the payment method supports registrations) | [optional] 
**language** | **string** | This parameter specifies the language (language code) in which the payment page should be presented to the cardholder. The &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-639-1&lt;/a&gt; two letter language codes listed above are supported | [optional] 
**payment_methods** | **string[]** | An array of payment method shortnames. For example &#x60;[\&quot;VIS\&quot;, \&quot;PFC\&quot;]&#x60;. If omitted, all available payment methods will be displayed on the payment page. If the Mobile SDKs are used (&#x60;returnMobileToken&#x60;), this array is mandatory. | [optional] 
**theme** | [**\Swagger\Client\Model\Theme**](Theme.md) |  | [optional] 
**redirect** | [**\Swagger\Client\Model\RedirectRequest**](RedirectRequest.md) |  | [optional] 
**webhook** | [**\Swagger\Client\Model\WebhookRequest**](WebhookRequest.md) |  | [optional] 
**option** | [**\Swagger\Client\Model\OptionRequest**](OptionRequest.md) |  | [optional] 
**mfx** | [**\Swagger\Client\Model\MFXRequest**](MFXRequest.md) |  | [optional] 
**mpx** | [**\Swagger\Client\Model\MPXRequest**](MPXRequest.md) |  | [optional] 
**azp** | [**\Swagger\Client\Model\AmazonPayRequest**](AmazonPayRequest.md) |  | [optional] 
**eps** | [**\Swagger\Client\Model\EpsRequest**](EpsRequest.md) |  | [optional] 
**swh** | [**\Swagger\Client\Model\SwishRequest**](SwishRequest.md) |  | [optional] 
**vps** | [**\Swagger\Client\Model\VippsRequest**](VippsRequest.md) |  | [optional] 
**mbp** | [**\Swagger\Client\Model\MobilePayRequest**](MobilePayRequest.md) |  | [optional] 
**wec** | [**\Swagger\Client\Model\WeChatRequest**](WeChatRequest.md) |  | [optional] 
**elv** | [**\Swagger\Client\Model\ElvInitRequest**](ElvInitRequest.md) |  | [optional] 
**swb** | [**\Swagger\Client\Model\SwissBillingRequest**](SwissBillingRequest.md) |  | [optional] 
**mdp** | [**\Swagger\Client\Model\MDPInitRequest**](MDPInitRequest.md) |  | [optional] 
**psc** | [**\Swagger\Client\Model\PaysafecardRequest**](PaysafecardRequest.md) |  | [optional] 
**mcp** | [**\Swagger\Client\Model\InitMcpRequest**](InitMcpRequest.md) |  | [optional] 
**extensions** | [**\Swagger\Client\Model\Extension**](Extension.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

