## README
A practical PHP 7 REST API client on-top-of [pecl-http](https://pecl.php.net/package/pecl_http).

## FEATURES
- Include authorization header generator (basic, bearer, jwt) with optional custom prefix value
- Auto json body based on request content-type
- Configurable http client and request options (based on pecl-http options)
- Built-in pipelined request support (pecl-http feature)
- Client/server error check by response status code
- Default request accept-encoding is `gzip, deflate` and auto inflate if server response is gziped or deflated

## REQUIREMENTS
- [php >=7.0.0](https://secure.php.net/)
- [pecl-http >=3.0.0](https://pecl.php.net/package/pecl_http)
- [mbstring](http://php.net/manual/en/book.mbstring.php)

## REQUEST
### GET
```php
<?php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://api.github.com');
$request->setPath('/users/simukti/repos')
    ->addQuery('sort', 'updated')
    ->addQuery('type', 'owner')
    ->addHeader('accept', 'application/json');

// default http method is GET
$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```

### POST
```php
<?php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://httpbin.org');
$request->setPath('/post')
    ->setMethod(ApiRequest::METHOD_POST)
    ->addData('username', 'kadalkesit')
    ->addData('password', 'superkesit')
    ->addQuery('foo', 'bar')
    ->addQuery('baz', 'bat');
// application/x-www-form-urlencoded
$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```

### POST (UPLOAD FILES)
```php
<?php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://httpbin.org');
$request->setPath('/post')
    ->setMethod(ApiRequest::METHOD_POST)
    ->addData('user_id', 100)
    ->addFile('picture', '/path/to/your/file_to_upload.extension');

$oken          = 'your_generated_token';
$authorization = new \RestClient\Authorization\BearerStringAuthorization($githubToken);
$request->setAuthorization($authorization);

$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```

### PUT
```php
<?php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://httpbin.org');
$request->setPath('/put')
    ->setMethod(ApiRequest::METHOD_PUT)
    ->addData('username', 'kadalkesit')
    ->addData('password', 'superkesit')
    ->addQuery('foo', 'bar')
    ->addQuery('baz', 'bat')
    ->addHeader('content-type', 'application/json');
// json body
$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```

### DELETE
```php
<?php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://httpbin.org');
$request->setPath('/delete')
    ->setMethod(ApiRequest::METHOD_DELETE)
    ->addQuery('user_id', 1);

$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```
### AUTHORIZATION
#### JWT
```php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://httpbin.org');
$request->setPath('/post')
    ->setMethod(ApiRequest::METHOD_POST)
    ->setData([
        'username' => 'kadalkesit',
        'password' => 'superkesit'
    ])
    ->addQuery('expand', 'user,role');

$simpleJWT = new \RestClient\Authorization\JWTAuthorization('key_as_ISS', 'secret', [
    'jti' => 'jtid',
    'scope' => [
        'user', 'writer'
    ]
]);
$request->setAuthorization($simpleJWT);

$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```

#### Bearer
This is example if you have token from api server.

```php
<?php
use RestClient\HttpClient\PeclHttpClient;
use RestClient\Request\ApiRequest;
use RestClient\Response\ApiResponse;

$request = new ApiRequest('https://httpbin.org');
$request->setPath('/post')
    ->setMethod(ApiRequest::METHOD_POST)
    ->setData(
        [
            'username' => 'kadalkesit',
            'password' => 'superkesit',
        ]
    )
    ->addQuery('include_refresh_token', 0);

$githubToken      = 'your_generated_token';
$githubAuthHeader = new \RestClient\Authorization\BearerStringAuthorization($githubToken);
$request->setAuthorization($githubAuthHeader);

$response   = new ApiResponse;
$httpClient = new PeclHttpClient;
$httpClient->send($request, $response);
```

## RESPONSE

### Error Checking
```php
$response->isError(); // true|false (status >= 400)
$response->isClientError(); // true|false (status 400 -> 499)
$response->isServerError(); // true|false (status 500 -> 520)
```

### Result
```php
$response->getContentType(); // application/json, text/html, text/plain, application/xml
$response->getContent(); // get result body (string)
$response->getHeaders(); // response header
```

## LICENSE
This project is released under the MIT licence.