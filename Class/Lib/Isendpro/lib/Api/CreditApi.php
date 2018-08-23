<?php
/**
 * CreditApi
 * PHP version 5
 *
 * @category Class
 * @package  Isendpro
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * API iSendPro
 *
 * [1] Liste des fonctionnalités : - envoi de SMS à un ou plusieurs destinataires, - lookup HLR, - récupération des récapitulatifs de campagne, - gestion des répertoires, - ajout en liste noire. - comptage du nombre de caractères des SMS  [2] Pour utiliser cette API vous devez: - Créer un compte iSendPro sur https://isendpro.com/ - Créditer votre compte      - Remarque: obtention d'un crédit de test possible sous conditions - Noter votre clé de compte (keyid)   - Elle vous sera indispensable à l'utilisation de l'API   - Vous pouvez la trouver dans le rubrique mon \"compte\", sous-rubrique \"mon API\" - Configurer le contrôle IP   - Le contrôle IP est configurable dans le rubrique mon \"compte\", sous-rubrique \"mon API\"   - Il s'agit d'un système de liste blanche, vous devez entrer les IP utilisées pour appeler l'API   - Vous pouvez également désactiver totalement le contrôle IP
 *
 * OpenAPI spec version: 1.1.1
 * Contact: support@isendpro.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Isendpro\Api;

use \Isendpro\ApiClient;
use \Isendpro\ApiException;
use \Isendpro\Configuration;
use \Isendpro\ObjectSerializer;

/**
 * CreditApi Class Doc Comment
 *
 * @category Class
 * @package  Isendpro
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreditApi
{
    /**
     * API Client
     *
     * @var \Isendpro\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \Isendpro\ApiClient|null $apiClient The api client to use
     */
    public function __construct(\Isendpro\ApiClient $apiClient = null)
    {
        if ($apiClient === null) {
            $apiClient = new ApiClient();
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \Isendpro\ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \Isendpro\ApiClient $apiClient set the API client
     *
     * @return CreditApi
     */
    public function setApiClient(\Isendpro\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation getCredit
     *
     * Interrogation credit
     *
     * @param string $keyid Clé API (required)
     * @param string $credit Type de reponse demandée, 1 pour euro, 2 pour euro + estimation quantité (required)
     * @throws \Isendpro\ApiException on non-2xx response
     * @return \Isendpro\Model\CreditResponse
     */
    public function getCredit($keyid, $credit)
    {
        list($response) = $this->getCreditWithHttpInfo($keyid, $credit);
        return $response;
    }

    /**
     * Operation getCreditWithHttpInfo
     *
     * Interrogation credit
     *
     * @param string $keyid Clé API (required)
     * @param string $credit Type de reponse demandée, 1 pour euro, 2 pour euro + estimation quantité (required)
     * @throws \Isendpro\ApiException on non-2xx response
     * @return array of \Isendpro\Model\CreditResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getCreditWithHttpInfo($keyid, $credit)
    {
        // verify the required parameter 'keyid' is set
        if ($keyid === null) {
            throw new \InvalidArgumentException('Missing the required parameter $keyid when calling getCredit');
        }
        // verify the required parameter 'credit' is set
        if ($credit === null) {
            throw new \InvalidArgumentException('Missing the required parameter $credit when calling getCredit');
        }
        // parse inputs
        $resourcePath = "/credit";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/x-www-form-urlencoded']);

        // query params
        if ($keyid !== null) {
            $queryParams['keyid'] = $this->apiClient->getSerializer()->toQueryValue($keyid);
        }
        // query params
        if ($credit !== null) {
            $queryParams['credit'] = $this->apiClient->getSerializer()->toQueryValue($credit);
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Isendpro\Model\CreditResponse',
                '/credit'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Isendpro\Model\CreditResponse', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Isendpro\Model\CreditResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Isendpro\Model\Erreur', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }
}