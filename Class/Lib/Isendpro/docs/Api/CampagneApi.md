# Isendpro\CampagneApi

All URIs are relative to *https://apirest.isendpro.com/cgi-bin*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getCampagne**](CampagneApi.md#getCampagne) | **GET** /campagne | Retourne les SMS envoyés sur une période donnée


# **getCampagne**
> \SplFileObject getCampagne($keyid, $rapport_campagne, $date_deb, $date_fin)

Retourne les SMS envoyés sur une période donnée

Retourne les SMS envoyés sur une période donnée en fonction d'une date de début et d'une date de fin.   Les dates sont au format YYYY-MM-DD hh:mm.   Le fichier rapport de campagne est sous la forme d'un fichier zip + contenant un fichier csv contenant le détail des envois.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Isendpro\Api\CampagneApi();
$keyid = "keyid_example"; // string | Clé API
$rapport_campagne = "rapport_campagne_example"; // string | Doit valoir \"1\"
$date_deb = "date_deb_example"; // string | date de debut au format YYYY-MM-DD hh:mm
$date_fin = "date_fin_example"; // string | date de fin au format YYYY-MM-DD hh:mm

try {
    $result = $api_instance->getCampagne($keyid, $rapport_campagne, $date_deb, $date_fin);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CampagneApi->getCampagne: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **keyid** | **string**| Clé API |
 **rapport_campagne** | **string**| Doit valoir \&quot;1\&quot; |
 **date_deb** | **string**| date de debut au format YYYY-MM-DD hh:mm |
 **date_fin** | **string**| date de fin au format YYYY-MM-DD hh:mm |

### Return type

[**\SplFileObject**](../Model/\SplFileObject.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, file

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

