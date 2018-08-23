# Isendpro\DelListeNoireApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**delListeNoire**](DelListeNoireApi.md#delListeNoire) | **POST** /dellistenoire | Ajoute un numero en liste noire


# **delListeNoire**
> \Isendpro\Model\LISTENOIREReponse delListeNoire($keyid, $del_liste_noire, $num)

Ajoute un numero en liste noire

Supprime un numero en liste noire

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\DelListeNoireApi();
$keyid = "keyid_example"; // string | Clé API
$del_liste_noire = "del_liste_noire_example"; // string | Doit valoir \"1\"
$num = "num_example"; // string | numéro de mobile à supprimer

try {
    $result = $api_instance->delListeNoire($keyid, $del_liste_noire, $num);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DelListeNoireApi->delListeNoire: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **keyid** | **string**| Clé API |
 **del_liste_noire** | **string**| Doit valoir \&quot;1\&quot; |
 **num** | **string**| numéro de mobile à supprimer |

### Return type

[**\Isendpro\Model\LISTENOIREReponse**](../Model/LISTENOIREReponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

