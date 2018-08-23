# Isendpro\AddShortlinkApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addShortlink**](AddShortlinkApi.md#addShortlink) | **POST** /shortlink | add a shortlink


# **addShortlink**
> \Isendpro\Model\ShortlinkResponse addShortlink($addshortlinkrequest)

add a shortlink

add a shortlink

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\AddShortlinkApi();
$addshortlinkrequest = new \Isendpro\Model\ShortlinkRequest(); // \Isendpro\Model\ShortlinkRequest | add sub account request

try {
    $result = $api_instance->addShortlink($addshortlinkrequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AddShortlinkApi->addShortlink: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **addshortlinkrequest** | [**\Isendpro\Model\ShortlinkRequest**](../Model/ShortlinkRequest.md)| add sub account request |

### Return type

[**\Isendpro\Model\ShortlinkResponse**](../Model/ShortlinkResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

