# Isendpro\AddSubaccountApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**subaccountAdd**](AddSubaccountApi.md#subaccountAdd) | **POST** /subaccount | Ajoute un sous compte


# **subaccountAdd**
> \Isendpro\Model\SubaccountAddResponse subaccountAdd($addsubaccountrequest)

Ajoute un sous compte

Ajoute un sous compte

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\AddSubaccountApi();
$addsubaccountrequest = new \Isendpro\Model\SubaccountAddRequest(); // \Isendpro\Model\SubaccountAddRequest | add sub account request

try {
    $result = $api_instance->subaccountAdd($addsubaccountrequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AddSubaccountApi->subaccountAdd: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **addsubaccountrequest** | [**\Isendpro\Model\SubaccountAddRequest**](../Model/SubaccountAddRequest.md)| add sub account request |

### Return type

[**\Isendpro\Model\SubaccountAddResponse**](../Model/SubaccountAddResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

