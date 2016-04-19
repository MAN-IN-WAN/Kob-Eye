[IF [!Module::Systeme::retrievePassword([!Form_Email!])!]]
{
    "success": 1,
    "message": "<div class=\"alert alert-success\">Votre nouveau mot de passe a été envoyé sur votre boite email.</div>"
}
[ELSE]
{
"success": 0,
"message": "<div class=\"alert alert-danger\">Compte introuvable. Vérifiez votre saisie.</div>"
}
[/IF]