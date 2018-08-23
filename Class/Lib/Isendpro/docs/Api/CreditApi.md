# Isendpro\CreditApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getCredit**](CreditApi.md#getCredit) | **GET** /credit | Interrogation credit


# **getCredit**
> \Isendpro\Model\CreditResponse getCredit($keyid, $credit)

Interrogation credit

Retourne le credit existant associe au compte.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\CreditApi();
$keyid = "keyid_example"; // string | Clé API
$credit = "credit_example"; // string | Type de reponse demandée, 1 pour euro, 2 pour euro + estimation quantité

try {
    $result = $api_instance->getCredit($keyid, $credit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CreditApi->getCredit: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **keyid** | **string**| Clé API |
 **credit** | **string**| Type de reponse demandée, 1 pour euro, 2 pour euro + estimation quantité |

### Return type

[**\Isendpro\Model\CreditResponse**](../Model/CreditResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

