# PHP Api Client for Datatrans

Generated from https://api-reference.datatrans.ch/

## Usage

Refer to the [Datatrans API Reference](https://api-reference.datatrans.ch) for advanced usage information.

### Build API Client

```php
$config = \Swagger\Client\Configuration::getDefaultConfiguration()
  // Get these from:
  // -> https://admin.sandbox.datatrans.com/
  // -> Change merchant to "web" (top right)
  // -> Manage UPP 
  // -> Security
  ->setUsername('your-upp-username')
  ->setPassword('your-upp-password');

$api =  new \Swagger\Client\Api\V1transactionsApi(
  new \GuzzleHttp\Client(),
  $config
);
```

### Initialize a Transaction

```php
$response = $api->init(new \Swagger\Client\Model\InitRequest([
  'auto_settle' => true, // Set to false to manually settle (see below).
  'language' => 'en',
  'currency' => 'CHF',
  'refno' => 'some-unique-string',
  'amount' => 1000, // 10.00 CHF
  'redirect' => [
    'successUrl' => 'https://example.com/success',
    'cancelUrl' => 'https://example.com/cancelled',
    'errorUrl' => 'https://example.com/error',
  ],
]));

// Build the redirect URL using the transaction ID.
$redirectUrl = "https://pay.sandbox.datatrans.com/v1/start/{$response->getTransactionId()}";

header("Location: $redirectUrl");
exit;
```

### Manually settle a transaction

```php
$api->settle(new \Swagger\Client\Model\InitRequest([
  'currency' => 'CHF',
  'refno' => 'some-unique-string',
  'amount' => 1000, // 10.00 CHF
]));
```