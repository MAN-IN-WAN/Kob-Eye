[IF [!SendContact!]!=&&[!CoteEmailEc!]>0]
        [!MessageInfos:=!]
        [!Success:=0!]
        
        //recvherche du shop
        [STORPROC Distributeur/Shop/[!CoteEmailEc!]|S][/STORPROC]
        [!CONTACTMAIL:=[!S::Email!]!]

        //Verification des informations du formulaire
        [!C_Error:=0!]
	[IF [!agree!]=1][ELSE][!agree_Error:=1!][!C_Error:=1!][/IF]
        [IF [!FullName!]][ELSE][!FullName_Error:=1!][!C_Error:=1!][/IF]
        [IF [!Utils::isMail([!Email!])!]][ELSE][!Email_Error:=1!][!C_Error:=1!][/IF]
        [IF [!Country!]][ELSE][!Country_Error:=1!][!C_Error:=1!][/IF]
        [IF [!Subject!]][ELSE][!Subject_Error:=1!][!C_Error:=1!][/IF]
        [IF [!Message!]][ELSE][!Message_Error:=1!][!C_Error:=1!][/IF]
        [IF [!C_Error!]]
                [!MessageInfos:=<strong>__CONTACT_ERRORS__</strong><br />!]
		[IF [!agree_Error!]][!MessageInfos+=__PLEASE_ACEEPT_ALL_TERMS_AND_CONDITIONS__<br />!][/IF]
                [IF [!FullName_Error!]][!MessageInfos+=__ERROR_NAME__<br />!][/IF]
                [IF [!Email_Error!]][!MessageInfos+=__ERROR_EMAIL__<br />!][/IF]
                [IF [!Country_Error!]][!MessageInfos+=__ERROR_COUNTRY__<br />!][/IF]
                [IF [!Subject_Error!]][!MessageInfos+=__ERROR_SUBJECT__<br />!][/IF]
                [IF [!Message_Error!]][!MessageInfos+=__ERROR_MESSAGE__<br />!][/IF]
        [ELSE]
                // Sinon envoi du mail
                [LIB Mail|LeMail]
                [METHOD LeMail|Subject][PARAM]__INVITE_EMAIL_LOCATOR__ [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
                [METHOD LeMail|From][PARAM][!Email!][/PARAM][/METHOD]
                [METHOD LeMail|ReplyTo][PARAM][!Email!][/PARAM][/METHOD]
                [METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
                [IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD][/IF]
                [METHOD LeMail|Body]
                        [PARAM]
                                [BLOC Mail]
                                        <font face="arial" color="#000000" size="2">
                                        <strong>SUBJECT</strong> : [!Subject!]<br/>
                                        <strong>SENT BY</strong> : <span style="text-transform:uppercase">[!FullName!]</span><br/>
                                        <strong>EMAIL</strong> : [!Email!]<br/>
                                        <strong>COUNTRY</strong> : [!Country!]<br/>
                                        <strong>MESSAGE</strong> : [UTIL BBCODE][!Message!][/UTIL]<br /></font>
                                [/BLOC]
                        [/PARAM]
                [/METHOD]
                [METHOD LeMail|BuildMail][/METHOD]
                [METHOD LeMail|Send][/METHOD]

                [IF [!newsletter!]]
                        //Enregistrement à la newsletter
                        [COUNT Newsletter/GroupeEnvoi/17/Contact/Email=[!Email!]|C]
                        [IF [!C!]=0]
                                [OBJ Newsletter|Contact|Con]
                                [METHOD Con|Set]
                                        [PARAM]Email[/PARAM][PARAM][!Email!][/PARAM]
                                [/METHOD]
                                [METHOD Con|Set]
                                        [PARAM]Nom[/PARAM][PARAM][!FullName!][/PARAM]
                                [/METHOD]
                                [METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
                                [METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/17[/PARAM][/METHOD]
                                [METHOD Con|Save][/METHOD]

                                // 3 - enregistrement du message
                                [OBJ Newsletter|Reception|Rec]
                                [METHOD Rec|Set]
                                        [PARAM]Contenu[/PARAM]
                                        [PARAM][!Message!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Set]
                                        [PARAM]Sujet[/PARAM]
                                        [PARAM][!Subject!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|AddParent]
                                        [PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Save][/METHOD]
                        [/IF]
                [/IF]

                // 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
                [STORPROC Newsletter/Contact/Email=[!Email!]|Con|0|1]
                        [NORESULT]
                                [OBJ Newsletter|Contact|Con]
                                [METHOD Con|Set]
                                        [PARAM]Email[/PARAM]
                                        [PARAM][!Email!][/PARAM]
                                [/METHOD]
                                [METHOD Con|Set]
                                        [PARAM]Nom[/PARAM]
                                        [PARAM][!FullName!][/PARAM]
                                [/METHOD]
                                [METHOD Con|AddParent]
                                        [PARAM]Newsletter/GroupeEnvoi/10[/PARAM]
                                [/METHOD]
                                [METHOD Con|Save][/METHOD]
                        [/NORESULT]
                [/STORPROC]
                
                // 3 - enregistrement du message
                [OBJ Newsletter|Reception|Rec]
                [METHOD Rec|Set]
                        [PARAM]Contenu[/PARAM]
                        [PARAM][!Message!][/PARAM]
                [/METHOD]
                [METHOD Rec|Set]
                        [PARAM]Destinataire[/PARAM]
                        [PARAM][!CONTACTMAIL!][/PARAM]
                [/METHOD]
                [METHOD Rec|Set]
                        [PARAM]Sujet[/PARAM]
                        [PARAM][!Subject!][/PARAM]
                [/METHOD]
                [METHOD Rec|AddParent]
                        [PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
                [/METHOD]
                [METHOD Rec|Save][/METHOD]
                        
                // Mail de confirmation
                [LIB Mail|LeMail]
                [METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
                [METHOD LeMail|From][PARAM]noreply@f-onekites.com[/PARAM][/METHOD]
                [METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
                [METHOD LeMail|To][PARAM][!Email!][/PARAM][/METHOD]
                [METHOD LeMail|Body]
                        [PARAM]
                                [BLOC Mail]
                                        __HELLO__ [!FullName!],<br />
                                        __MSG_RECEPTION__
                                [/BLOC]
                        [/PARAM]
                [/METHOD]
                [METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
                [METHOD LeMail|BuildMail][/METHOD]
                [METHOD LeMail|Send][/METHOD]
                
                //Message success
                [!Success:=1!]
                [!MessageInfos:=__MESSAGE_SUCCESS__!]
        [/IF]
        {
            "message":"[JSON][!MessageInfos!][/JSON]",
            "success":[!Success!]
        }
[/IF]

