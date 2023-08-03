# StatusResponse

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**transaction_id** | **string** | The transactionId received after an authorization. | [optional] 
**merchant_id** | **string** | The merchant id. | [optional] 
**type** | **string** |  | [optional] 
**status** | **string** | The transaction status  |Status|Description| |:---|:---| |initialized| When a transaction was initialized. A transaction is initialized after a successful init request. This status is only set for customer-initiated flows before consumers start their payment via our payment forms.| |authenticated| When a transaction was authenticated. This status is only set if you defer the authorization from the authentication.| |authorized| When a transaction was authorized. This status is only set if you defer the settlement from the authorization.| |settled| When a transaction was settled partially or fully.| |canceled| When a transaction was canceled by the user or automatically by the system after a time out occurred on our payment forms. |transmitted| When a transaction was transmitted to the acquirer for processing. This is automatically set by our system.| |failed| When a transaction failed.| | [optional] 
**currency** | **string** | 3 letter &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/ISO_4217&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-4217&lt;/a&gt; character code. For example &#x60;CHF&#x60; or &#x60;USD&#x60; | [optional] 
**refno** | **string** | The merchant&#x27;s reference number. It should be unique for each transaction. | [optional] 
**refno2** | **string** | Optional customer&#x27;s reference number. Supported by some payment methods or acquirers. | [optional] 
**payment_method** | **string** |  | [optional] 
**detail** | [**\Swagger\Client\Model\Detail**](Detail.md) |  | [optional] 
**customer** | [**\Swagger\Client\Model\Customer**](Customer.md) |  | [optional] 
**cdm** | [**\Swagger\Client\Model\CDMResponse**](CDMResponse.md) |  | [optional] 
**accertify** | [**\Swagger\Client\Model\Accertify**](Accertify.md) |  | [optional] 
**language** | **string** | The language (language code) in which the payment was presented to the cardholder. The &lt;a href&#x3D;&#x27;https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes&#x27; target&#x3D;&#x27;_blank&#x27;&gt;ISO-639-1&lt;/a&gt; two letter language codes listed above are supported | [optional] 
**card** | [**\Swagger\Client\Model\StatusCardDetail**](StatusCardDetail.md) |  | [optional] 
**twi** | [**\Swagger\Client\Model\TwintDetail**](TwintDetail.md) |  | [optional] 
**pap** | [**\Swagger\Client\Model\PayPalDetail**](PayPalDetail.md) |  | [optional] 
**rek** | [**\Swagger\Client\Model\RekaDetail**](RekaDetail.md) |  | [optional] 
**elv** | [**\Swagger\Client\Model\ElvDetail**](ElvDetail.md) |  | [optional] 
**kln** | [**\Swagger\Client\Model\KlarnaDetail**](KlarnaDetail.md) |  | [optional] 
**int** | [**\Swagger\Client\Model\ByjunoDetail**](ByjunoDetail.md) |  | [optional] 
**swp** | [**\Swagger\Client\Model\SwissPassDetail**](SwissPassDetail.md) |  | [optional] 
**mpg** | [**\Swagger\Client\Model\MPGDetail**](MPGDetail.md) |  | [optional] 
**mfx** | [**\Swagger\Client\Model\MFXDetail**](MFXDetail.md) |  | [optional] 
**mpx** | [**\Swagger\Client\Model\MPXDetail**](MPXDetail.md) |  | [optional] 
**mdp** | [**\Swagger\Client\Model\MDPDetail**](MDPDetail.md) |  | [optional] 
**esy** | [**\Swagger\Client\Model\SwisscomPayDetail**](SwisscomPayDetail.md) |  | [optional] 
**pfc** | [**\Swagger\Client\Model\PostfinanceDetail**](PostfinanceDetail.md) |  | [optional] 
**wec** | [**\Swagger\Client\Model\WeChatDetail**](WeChatDetail.md) |  | [optional] 
**scx** | [**\Swagger\Client\Model\SuperCard**](SuperCard.md) |  | [optional] 
**history** | [**\Swagger\Client\Model\Action[]**](Action.md) |  | [optional] 
**ep2** | [**\Swagger\Client\Model\Ep2**](Ep2.md) |  | [optional] 
**dcc** | [**\Swagger\Client\Model\Dcc**](Dcc.md) |  | [optional] 
**multi_currency_processing** | [**\Swagger\Client\Model\MultiCurrencyProcessing**](MultiCurrencyProcessing.md) |  | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

