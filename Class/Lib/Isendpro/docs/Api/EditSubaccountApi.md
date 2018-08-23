# Isendpro\EditSubaccountApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**subaccountEdit**](EditSubaccountApi.md#subaccountEdit) | **PUT** /subaccount | Edit a subaccount


# **subaccountEdit**
> \Isendpro\Model\SubaccountResponse subaccountEdit($editsubaccountrequest)

Edit a subaccount

Edit a subaccount

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\EditSubaccountApi();
$editsubaccountrequest = new \Isendpro\Model\SubaccountRequest(); // \Isendpro\Model\SubaccountRequest | edit sub account request

try {
    $result = $api_instance->subaccountEdit($editsubaccountrequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling EditSubaccountApi->subaccountEdit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **editsubaccountrequest** | [**\Isendpro\Model\SubaccountRequest**](../Model/SubaccountRequest.md)| edit sub account request |

### Return type

[**\Isendpro\Model\SubaccountResponse**](../Model/SubaccountResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

