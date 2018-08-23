# Isendpro\GetListeNoireApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getListeNoire**](GetListeNoireApi.md#getListeNoire) | **POST** /getlistenoire | Retourne le liste noire


# **getListeNoire**
> \SplFileObject getListeNoire($keyid, $get_liste_noire)

Retourne le liste noire

Retourne un fichier csv zippé contenant la liste noire

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\GetListeNoireApi();
$keyid = "keyid_example"; // string | Clé API
$get_liste_noire = "get_liste_noire_example"; // string | Doit valoir \"1\"

try {
    $result = $api_instance->getListeNoire($keyid, $get_liste_noire);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GetListeNoireApi->getListeNoire: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **keyid** | **string**| Clé API |
 **get_liste_noire** | **string**| Doit valoir \&quot;1\&quot; |

### Return type

[**\SplFileObject**](../Model/\SplFileObject.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

