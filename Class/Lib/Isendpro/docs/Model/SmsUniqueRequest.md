# SmsUniqueRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**keyid** | **string** | Clé API | 
**gmt_zone** | **string** | Fuseau horaire de la date d&#39;envoi | [optional] 
**date_envoi** | **string** | Date d&#39;envoi au format YYYY-MM-DD hh:mm . Ce paramètre est optionnel, si il est omis l&#39;envoi est réalisé immédiatement. | [optional] 
**sms** | **string** | Message à envoyer aux destinataires. Le message doit être encodé au format utf-8 et ne contenir que des caractères existant dans l&#39;alphabet GSM. Il est également possible d&#39;envoyer (à l&#39;étranger uniquement) des SMS en UCS-2, cf paramètre ucs2 pour plus de détails. | 
**num** | **string** | Numero de téléphone au format national (exemple 0680010203) ou international (example 33680010203) | 
**emetteur** | **string** | - L&#39;emetteur doit être une chaîne alphanumérique comprise entre 4 et 11 caractères.  - Les caractères acceptés sont les chiffres entre 0 et 9, les lettres entre A et Z et l’espace.  - Il ne peut pas comporter uniquement des chiffres.   - Pour la modification de l&#39;émetteur et dans le cadre de campagnes commerciales, les opérateurs imposent contractuellement d&#39;ajouter en fin de message le texte \&quot;STOP XXXXX\&quot;. De ce fait, le message envoyé ne pourra excéder une longueur de 148 caractères au lieu des 160 caractères, le « STOP » étant rajouté automatiquement. | [optional] 
**tracker** | **string** | Le tracker doit être une chaine alphanumérique de moins de 50 caractères. Ce tracker sera ensuite renvoyé en paramètre des urls pour les retours des accusés de réception. | [optional] 
**smslong** | **string** | Le SMS long permet de dépasser la limite de 160 caractères en envoyant un message constitué de plusieurs SMS. Il est possible d’envoyer jusqu’à 6 SMS concaténés pour une longueur totale maximale de 918 caractères par message. Pour des raisons technique, la limite par SMS concaténé étant de 153 caractères. En cas de modification de l’émetteur, il faut considérer l’ajout automatique de 12 caractères du « STOP SMS ». Pour envoyer un smslong, il faut ajouter le paramètre smslong aux appels. La valeur de SMS doit être le nombre maximum de sms concaténé autorisé.   Pour ne pas avoir ce message d’erreur et obtenir un calcul dynamique du nombre de SMS alors il faut renseigner smslong &#x3D; \&quot;999\&quot; | [optional] 
**nostop** | **string** | Si le message n’est pas à but commercial, vous pouvez faire une demande pour retirer l’obligation du STOP. Une fois votre demande validée par nos services, vous pourrez supprimer la mention STOP SMS en ajoutant nostop &#x3D; \&quot;1\&quot; | [optional] 
**num_azur** | **string** |  | [optional] 
**ucs2** | **string** | Il est également possible d’envoyer des SMS en alphabet non latin (russe, chinois, arabe, etc) sur les numéros hors France métropolitaine. Pour ce faire, la requête devrait être encodée au format UTF-8 et contenir l’argument ucs2 &#x3D; \&quot;1\&quot; Du fait de contraintes techniques, 1 SMS unique ne pourra pas dépasser 70 caractères (au lieu des 160 usuels) et dans le cas de SMS long, chaque sms ne pourra dépasser 67 caractères. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


