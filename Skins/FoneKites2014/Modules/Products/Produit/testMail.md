[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@f-onekites.com[/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM]noreply@f-onekites.com[/PARAM][/METHOD]
[METHOD LeMail|To][PARAM]enguer@enguer.com[/PARAM][/METHOD]
[METHOD LeMail|Body]
[PARAM]
//[BLOC Mail]
$MSGSEARCH$
__HELLO__ [!Zob!],<br />
__SUSCRIBE_SUCCESSFULL__
//[/BLOC]
[/PARAM]
[/METHOD]
[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]