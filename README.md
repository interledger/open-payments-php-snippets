# 📦 Open Payments Php Snippets


This library provides PHP code snippets demonstrating usage of the [Open Payments Php Library](https://github.com/interledger/open-payments-php). It is intended for use with a  <a href="https://wallet.interledger-test.dev/" target="_blank">Test wallet</a>.

While everyone is welcome to use these examples as a reference, please note that you may need to adapt them to suit your specific application or environment.


---

### Prerequisites

- PHP 8.3
- Sodium extension - needed by [Open Payments Php Library](https://github.com/interledger/open-payments-php) for generating keys
- BCMath extension - needed by [Open Payments Php Library](https://github.com/interledger/open-payments-php) for big numbers comparisons
- An active account on <a href="https://wallet.interledger-test.dev/" target="_blank">Test wallet</a>
- A payment pointer associated with your account
- Payment pointer keys should be generated (refer to [loading the private key](#loading-the-private-key))


### 🚀 Setup


```
composer install
```

Make the Console File Executable:

```
chmod +x bin/console
```



### Loading the private key

When generating the keys for a payment pointer on
<a href="https://wallet.interledger-test.dev/settings/developer-keys" target="_blank">Test wallet Developer Keys section</a>, the
private key will be automatically downloaded to your machine. Copy the base64 enncoded private key to use below.

Ensure you are at the repository root and execute the following command in your terminal:

```sh
cp .env.example .env
```

Open the newly created `.env` file and fill in the following variables:

-   `PRIVATE_KEY` - fill the base 64 encoded private key string copied earlier
-   `KEY_ID` - key id of the private key
-   `WALLET_ADDRESS` - with your wallet address url (The payment pointer url but replace `$` with `https://` )

Now that you have all the necessary components to initialize the authenticated Open Payments client, you're ready to
begin utilizing the php code snippets library.



### ⚙️ Usage

Run the Application:

```
./bin/console command param1 param2
```


### 🛠️ Available Commands

| Command                                | Description                                                                 |
|----------------------------------------|-----------------------------------------------------------------------------|
| `completion`                           | Dump the shell completion script                                            |
| `help`                                 | Display help for a command                                                  |
| `list`                                 | List commands                                                               |
| `list:extended`                        | List commands with parameters description                                   |
| `grant:cancel`                         | Cancel a grant.                                                             |
| `grant:continuation`                  | Outputs an outgoing payment object with the access_token for the request    |
| `grant:interval`                      | Outputs an outgoing payment object with the access_token for the request    |
| `grant:ip`                            | Outputs an incoming payment object with the access_token for the request    |
| `grant:op`                            | Outputs an outgoing payment object with the access_token for the request    |
| `grant:quote`                         | Outputs a quote object with the access_token for the request                |
| `ip:complete`                         | Complete an incoming payment                                                |
| `ip:create`                           | Create an incoming payment                                                  |
| `ip:get`                              | Get an incoming payment                                                     |
| `ip:list`                             | List incoming payments                                                      |
| `op:create`                           | Create an outgoing payment                                                  |
| `op:create:amount`                    | Create an outgoing payment with a specific amount                           |
| `op:get`                              | Get an outgoing payment                                                     |
| `op:list`                             | List outgoing payments                                                      |
| `quote:create`                        | Create a quote                                                              |
| `quote:get`                           | Get a quote                                                                 |
| `token:revoke`                        | Revoke an access token                                                      |
| `token:rotate`                        | Rotate an access token                                                      |
| `wa:get`                              | Get a wallet address                                                        |
| `wa:get-keys`                         | Get the keys of a wallet address                                            |
| `OP:fetch-quote-and-initialize-payment`| Create IP grant, IP create, Quote Grant, quote create and initialize outgoing payment |
| `OP:finish-payment`                    | Create a continuation grant and create an outgoing payment                 |





### 🧾 CLI Command Reference

```bash
➤ wa:get: This command is used to get a wallet address.
   - Argument: WALLET_ADDRESS The wallet address url.

➤ wa:get-keys: This command is used to get the keys of a wallet address.
   - Argument: WALLET_ADDRESS The wallet address url.

➤ grant:ip: Outputs an incoming payment object, with the access_token value needed to make the incoming payment request.

➤ ip:create: This command is used to create an incoming payment.
   - Argument: INCOMING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the incoming payment received from the incoming payment grant.

➤ ip:get: This command is used to get an incoming payment.
   - Argument: INCOMING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the incoming payment received from the incoming payment grant.
   - Argument: INCOMING_PAYMENT_URL The url of the incoming payment.

➤ ip:list: This command is used to list incoming payments.
   - Argument: INCOMING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the incoming payment received from the incoming payment grant.

➤ ip:complete: This command is used to complete an incoming payment.
   - Argument: INCOMING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the incoming payment received from the incoming payment grant.
   - Argument: INCOMING_PAYMENT_URL The url of the incoming payment.

➤ grant:op: Outputs an outgoing payment object, with the access_token value needed to make the outgoing payment request.
   - Argument: INCOMING_PAYMENT_URL The url of the incoming payment that is being paid.

➤ grant:interval: Outputs an outgoing payment object, with the access_token value needed to make the ougoing payment request.

➤ op:create: This command is used to create an outgoing payment.
   - Argument: OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the outgoing payment received from the outgoing payment grant.
   - Argument: QUOTE_URL The url of the quote.

➤ op:create:amount: This command is used to create an outgoing payment with a specific amount.
   - Argument: OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the outgoing payment received from the outgoing payment grant.
   - Argument: INCOMING_PAYMENT_URL The url of the incoming payment.

➤ op:get: This command is used to get an outgoing payment.
   - Argument: OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the outgoing payment received from the outgoing payment grant.
   - Argument: OUTGOING_PAYMENT_URL The url of the outgoing payment.

➤ op:list: This command is used to list outgoing payments.
   - Argument: OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN Access token for the outgoing payment received from the outgoing payment grant.

➤ grant:quote: Outputs an quote object, with the access_token value needed to make the incoming payment request.

➤ quote:create: This command is used to create a quote.
   - Argument: QUOTE_GRANT_ACCESS_TOKEN Access token for the quote received from the quote grant request.
   - Argument: INCOMING_PAYMENT_URL The url of the incoming payment for which we want to get a quote.

➤ quote:get: This command is used to get a quote.
   - Argument: QUOTE_GRANT_ACCESS_TOKEN Access token for the quote received from the quote grant request.
   - Argument: QUOTE_URL The url of the quote.

➤ token:rotate: This command is used to rotate an access token.
   - Argument: ACCESS_TOKEN The (expired) acceess token we want to rotate/refresh.
   - Argument: TOKEN_MANAGE_URL The token manage url received when we created this (grant) token.

➤ token:revoke: This command is used to revoke an access token.
   - Argument: ACCESS_TOKEN The acceess token we want to revoke.
   - Argument: TOKEN_MANAGE_URL The token manage url received when we created this (grant) token.

➤ grant:continuation: Outputs an outgoing payment object, with the access_token value needed to make the outgoing payment request.
   - Argument: CONTINUE_ACCESS_TOKEN The value of CONTINUE_ACCESS_TOKEN
   - Argument: URL_WITH_INTERACT_REF The value of URL_WITH_INTERACT_REF
   - Argument: CONTINUE_URI The value of CONTINUE_URI

➤ grant:cancel: Cancel a grant.
   - Argument: ACCESS_TOKEN The value of ACCESS_TOKEN
   - Argument: CONTINUE_URI The value of CONTINUE_URI

➤ OP:fetch-quote-and-initialize-payment: Create IP grant, IP create, Quote Grant, quote create and initialize outgoing payment

➤ OP:finish-payment: Create a continuation grant and create an outgoing payment
   - Argument: CONTINUE_ACCESS_TOKEN The value of CONTINUE_ACCESS_TOKEN received from the outgoint payment pending grant.
   - Argument: URL_WITH_INTERACT_REF The value of URL_WITH_INTERACT_REF the url where we get redirected after approving the interaction.
                ie: https://localhost/\?paymentId=123423\&hash=26oQzgzm6MluYVmUKfzyy1SaIOYb9wvKEe%2FOIGFmuq8%3D\&interact_ref=7cec4a98-c823-4f28-8c84-8d1d4f2685b3
                note: make sure to esacape the & and ? with \& and \?
   - Argument: CONTINUE_URI The value of CONTINUE_URI received from the outgoint payment pending grant.
   - Argument: QUOTE_URL The url of the quote.

➤ list:extended: List all commands with arguments and options
```


### 📁 Project Structure

```
app/
├── bin/
│   └── console
├── src/
│   ├── Command/
│   │   ├── Grant/
│   │   │   └── CancelGrant.php
│   │   │   └── GrantContinuation.php
│   │   │   └── GrantIncomingPayment.php
│   │   │   └── GrantOutgoingPayment.php
│   │   │   └── GrantOutgoingPaymentInterval.php
│   │   │   └── GrantQuote.php
│   │   ├── IncomingPayment/
│   │   │   └── IncomingPaymentComplete.php
│   │   │   └── IncomingPaymentCreate.php
│   │   │   └── IncomingPaymentGet.php
│   │   │   └── IncomingPaymentList.php
│   │   │   └── PublicIncomingPaymentGet.php
│   │   └── OutgoingPayment/
│   │   │   └── OutgoingPaymentCreate.php
│   │   │   └── OutgoingPaymentCreateAmount.php
│   │   │   └── OutgoingPaymentGet.php
│   │   │   └── OutgoingPaymentList.php
│   │   └── Quote/
│   │   │   └── QuoteCreate.php
│   │   │   └── QuoteGet.php
│   │   └── Token/
│   │   │   └── TokenRevoke.php
│   │   │   └── TokenRotate.php
│   │   └── WalletAddress/
│   │   │   └── PublicGetWalletAddress.php
│   │   │   └── PublicGetWalletAddressKeys.php
│   │   └── FetchQuoteAndInitializePayment.php
│   │   └── FinalizePayment.php
│   └── Application.php
├── tests/
│   └── ApplicationTest.php
├── vendor/
├── .env
├── composer.json
├── composer.lock
└── README.md
```