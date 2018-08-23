# Isendpro\SetListeNoireApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**setListeNoire**](SetListeNoireApi.md#setListeNoire) | **POST** /setlistenoire | Ajoute un numero en liste noire


# **setListeNoire**
> \Isendpro\Model\LISTENOIREReponse setListeNoire($keyid, $setliste_noire, $num)

Ajoute un numero en liste noire

Ajoute un numero en liste noire. Une fois ajouté, les requêtes d'envoi de SMS marketing vers ce numéro seront refusées.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\SetListeNoireApi();
$keyid = "keyid_example"; // string | Clé API
$setliste_noire = "setliste_noire_example"; // string | Doit valoir \"1\"
$num = "num_example"; // string | numéro de mobile à insérer en liste noire

try {
    $result = $api_instance->setListeNoire($keyid, $setliste_noire, $num);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SetListeNoireApi->setListeNoire: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **keyid** | **string**| Clé API |
 **setliste_noire** | **string**| Doit valoir \&quot;1\&quot; |
 **num** | **string**| numéro de mobile à insérer en liste noire |

### Return type

[**\Isendpro\Model\LISTENOIREReponse**](../Model/LISTENOIREReponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

