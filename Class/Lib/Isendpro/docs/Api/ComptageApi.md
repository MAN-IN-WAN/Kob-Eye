# Isendpro\ComptageApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**comptage**](ComptageApi.md#comptage) | **POST** /comptage | Compter le nombre de caractère


# **comptage**
> \Isendpro\Model\ComptageReponse comptage($comptagerequest)

Compter le nombre de caractère

Compte le nombre de SMS necessaire à un envoi

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\ComptageApi();
$comptagerequest = new \Isendpro\Model\ComptageRequest(); // \Isendpro\Model\ComptageRequest | sms request

try {
    $result = $api_instance->comptage($comptagerequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ComptageApi->comptage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **comptagerequest** | [**\Isendpro\Model\ComptageRequest**](../Model/ComptageRequest.md)| sms request |

### Return type

[**\Isendpro\Model\ComptageReponse**](../Model/ComptageReponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

