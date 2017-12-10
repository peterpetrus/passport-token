# passport-token
Dirty Decoder for Laravel Passport (Bearer) Access Token.

## Functionality

Decode Access-Token and return array with its IDs (token_id, user_id), Dates (date of expire, date of creation, start date) and validity and errors.

## Installation

Installation via composer:

``` bash
composer require peterpetrus/passport-token
```

## Example

### Decode token as Object

Object has same properties as returned array fields below.

```php
use PeterPetrus\Auth\PassportToken;

$token = new PassportToken(
    'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjI2YTUyMTMxNDAzOGQ0NGY3OTVkMzYwZGQ0ZDlkNDBlYTQyNGU4N2ZlMjUyMmVhMTk5ZjU2ZWVmODg0ZTFhNWNmNjg2Nzk3NmQ2MDRmOWY5In0.eyJhdWQiOiIyIiwianRpIjoiMjZhNTIxMzE0MDM4ZDQ0Zjc5NWQzNjBkZDRkOWQ0MGVhNDI0ZTg3ZmUyNTIyZWExOTlmNTZlZWY4ODRlMWE1Y2Y2ODY3OTc2ZDYwNGY5ZjkiLCJpYXQiOjE1MTI3NDc1OTIsIm5iZiI6MTUxMjc0NzU5MiwiZXhwIjoxNTE4MTA0MzkyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.toksHokX_RZ7eRToL_owakMJ3gbi0nppD5yrhA9C5McVSnn3WraA4NwBcwQVlkv316BTUOaJ14unBNEg1UKGuK4EhoiTBMdT1cSkgH1HKZg2SXNBrCPi9YY4g4-4qpfxQqLeBM5JsVbouD6VeeBmDJUGVcoXDXimKEft4lgkIIqPCmWOV9HscKkRQ23lyVhXaQo4TMoCUZfM2ppyqdl2wTsrXp7woQMbqwVo9bnc4d6opj55XvMgal5MmY8YXDHpJO29UWkn2mTIL3kB6KP_WDHg5LJU0r1ua1lTn8Om97Z4eMFFUlipq7yODSgtML92kiZef7JAX3DecxJbzB9tcDk22NtSoBzlHy86ZJHU9rKhcIuKbpys6X2dAHAlkS7GUCWHqZcwN38LfjoyUEiP7QHkLNogSZQZE_I7FPKLYpxyOiR83K4IZGlOEeiEJZGCVqUWviyyIfWRA3gusk6p5cB4begxOne_l0vnNRH2WiB-WOKBytL1fKeXwaJj8AIFGj03Wvb0OYqp01ef05kiX9Y-PbHYYi_x5L8fcywXqo3ubKKiChqTCXirLH9ENcTwQT0C32Z2EgLlNnyF5iH9XQuW5UN36ke63ad0iIjlEhinoOOF8OK8IDKiHGIQ0qJwZxFG3EVDX3UFQpZUaAgYlNTTXhyT-fvf1dNR8msy-h0'
);

if ($token->valid) {
    // Check if token exists in DB (table 'oauth_access_tokens'), require \Illuminate\Support\Facades\DB class
    if ($token->existsValid()) {
        Auth::login(User::find($token->user_id));
        return redirect(...);
    }
}
```

### Decode token with static methods

```php
use PeterPetrus\Auth\PassportToken;

$decoded_token = PassportToken::dirtyDecode(
    'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjI2YTUyMTMxNDAzOGQ0NGY3OTVkMzYwZGQ0ZDlkNDBlYTQyNGU4N2ZlMjUyMmVhMTk5ZjU2ZWVmODg0ZTFhNWNmNjg2Nzk3NmQ2MDRmOWY5In0.eyJhdWQiOiIyIiwianRpIjoiMjZhNTIxMzE0MDM4ZDQ0Zjc5NWQzNjBkZDRkOWQ0MGVhNDI0ZTg3ZmUyNTIyZWExOTlmNTZlZWY4ODRlMWE1Y2Y2ODY3OTc2ZDYwNGY5ZjkiLCJpYXQiOjE1MTI3NDc1OTIsIm5iZiI6MTUxMjc0NzU5MiwiZXhwIjoxNTE4MTA0MzkyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.toksHokX_RZ7eRToL_owakMJ3gbi0nppD5yrhA9C5McVSnn3WraA4NwBcwQVlkv316BTUOaJ14unBNEg1UKGuK4EhoiTBMdT1cSkgH1HKZg2SXNBrCPi9YY4g4-4qpfxQqLeBM5JsVbouD6VeeBmDJUGVcoXDXimKEft4lgkIIqPCmWOV9HscKkRQ23lyVhXaQo4TMoCUZfM2ppyqdl2wTsrXp7woQMbqwVo9bnc4d6opj55XvMgal5MmY8YXDHpJO29UWkn2mTIL3kB6KP_WDHg5LJU0r1ua1lTn8Om97Z4eMFFUlipq7yODSgtML92kiZef7JAX3DecxJbzB9tcDk22NtSoBzlHy86ZJHU9rKhcIuKbpys6X2dAHAlkS7GUCWHqZcwN38LfjoyUEiP7QHkLNogSZQZE_I7FPKLYpxyOiR83K4IZGlOEeiEJZGCVqUWviyyIfWRA3gusk6p5cB4begxOne_l0vnNRH2WiB-WOKBytL1fKeXwaJj8AIFGj03Wvb0OYqp01ef05kiX9Y-PbHYYi_x5L8fcywXqo3ubKKiChqTCXirLH9ENcTwQT0C32Z2EgLlNnyF5iH9XQuW5UN36ke63ad0iIjlEhinoOOF8OK8IDKiHGIQ0qJwZxFG3EVDX3UFQpZUaAgYlNTTXhyT-fvf1dNR8msy-h0'
);

if ($decoded_token['valid']) {
    // Check if token exists in DB (table 'oauth_access_tokens'), require \Illuminate\Support\Facades\DB class
    $token_exists = PassportToken::existsValidToken(
        $decoded_token['token_id'], 
        $decoded_token['user_id']
    );
    
    if ($token_exists) {
        Auth::login(User::find($decoded_token['user_id']));
        return redirect(...);
    }
}
```

### Returned array $decoded_token

```php
[
    "token_id" => "26a521314038d44f795d360dd4d9d40ea424e87fe2522ea199f56eef884e1a5cf6867976d604f9f9",
    "user_id" => "1",
    "expecting" => false,
    "start_at_unix" => 1512747592,
    "start_at" => "2017-12-08T15:39:52+0000",
    "incorrect" => false,
    "created_at_unix" => 1512747592,
    "created_at" => "2017-12-08T15:39:52+0000",
    "expired" => false,
    "expires_at_unix" => 1518104392,
    "expires_at" => "2018-02-08T15:39:52+0000",
    "error" => false,
    "errors" => [],
    "valid" => true
]
```