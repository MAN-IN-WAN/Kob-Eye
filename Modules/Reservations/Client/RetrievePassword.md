[IF [!Module::Systeme::retrievePassword([!Form_Email!])!]]
{
    success: true,
    message: '<div class="alert alert-success">Votre nouveau mot de passe a été envoyé sur votre boite email.</div>'
}
[ELSE]
{
success: false,
message: '<div class="alert alert-danger">Compte introuvable. Vérifiez votre saisie.</div>'
}
[/IF]