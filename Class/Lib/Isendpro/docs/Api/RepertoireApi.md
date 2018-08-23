# Isendpro\RepertoireApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**repertoire**](RepertoireApi.md#repertoire) | **PUT** /repertoire | Gestion repertoire (modification)
[**repertoireCrea**](RepertoireApi.md#repertoireCrea) | **POST** /repertoire | Gestion repertoire (creation)


# **repertoire**
> \Isendpro\Model\REPERTOIREmodifreponse repertoire($repertoiremodifrequest)

Gestion repertoire (modification)

Ajoute ou supprime une liste de numéros à un répertoire existant.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\RepertoireApi();
$repertoiremodifrequest = new \Isendpro\Model\REPERTOIREmodifrequest(); // \Isendpro\Model\REPERTOIREmodifrequest | Requête de creation repertoire

try {
    $result = $api_instance->repertoire($repertoiremodifrequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RepertoireApi->repertoire: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **repertoiremodifrequest** | [**\Isendpro\Model\REPERTOIREmodifrequest**](../Model/REPERTOIREmodifrequest.md)| Requête de creation repertoire |

### Return type

[**\Isendpro\Model\REPERTOIREmodifreponse**](../Model/REPERTOIREmodifreponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **repertoireCrea**
> \Isendpro\Model\REPERTOIREcreatereponse repertoireCrea($repertoirecreaterequest)

Gestion repertoire (creation)

Cree un nouveau répertoire et retourne son identifiant. Cet identifiant pourra être utilisé pour ajouter ou supprimer des numéros via l'API.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\RepertoireApi();
$repertoirecreaterequest = new \Isendpro\Model\REPERTOIREcreaterequest(); // \Isendpro\Model\REPERTOIREcreaterequest | Creation repertoire

try {
    $result = $api_instance->repertoireCrea($repertoirecreaterequest);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RepertoireApi->repertoireCrea: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **repertoirecreaterequest** | [**\Isendpro\Model\REPERTOIREcreaterequest**](../Model/REPERTOIREcreaterequest.md)| Creation repertoire |

### Return type

[**\Isendpro\Model\REPERTOIREcreatereponse**](../Model/REPERTOIREcreatereponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

