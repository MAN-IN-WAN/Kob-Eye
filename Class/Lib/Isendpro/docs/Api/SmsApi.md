# Isendpro\SmsApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**sendSms**](SmsApi.md#sendSms) | **POST** /sms | Envoyer un sms
[**sendSmsMulti**](SmsApi.md#sendSmsMulti) | **POST** /smsmulti | Envoyer des SMS


# **sendSms**
> \Isendpro\Model\SMSReponse sendSms($smsrequest)

Envoyer un sms

Envoi un sms vers un unique destinataire

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\SmsApi();
$smsrequest = new \Isendpro\Model\SmsUniqueRequest(); // \Isendpro\Model\SmsUniqueRequest | sms request

try {
    $result = $api_instance->sendSms($smsrequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SmsApi->sendSms: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **smsrequest** | [**\Isendpro\Model\SmsUniqueRequest**](../Model/SmsUniqueRequest.md)| sms request |

### Return type

[**\Isendpro\Model\SMSReponse**](../Model/SMSReponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **sendSmsMulti**
> \Isendpro\Model\SMSReponse sendSmsMulti($smsrequest)

Envoyer des SMS

Envoi de SMS vers 1 ou plusieurs destinataires

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\SmsApi();
$smsrequest = new \Isendpro\Model\SMSRequest(); // \Isendpro\Model\SMSRequest | sms request

try {
    $result = $api_instance->sendSmsMulti($smsrequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SmsApi->sendSmsMulti: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **smsrequest** | [**\Isendpro\Model\SMSRequest**](../Model/SMSRequest.md)| sms request |

### Return type

[**\Isendpro\Model\SMSReponse**](../Model/SMSReponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

