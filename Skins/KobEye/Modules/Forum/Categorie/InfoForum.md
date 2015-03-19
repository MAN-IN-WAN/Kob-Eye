<div style="border-bottom:dashed 1px #4d4d4d;margin-bottom:20px;">
	[COUNT Systeme/Group/11817/User|Usr]<br />
	[STORPROC Systeme/Group/11817/User|Us|0|1|tmsCreate][/STORPROC]
	La communaut&eacute;e de Kob-Eye c&lsquo;est un total de <b>[!Usr!] membres</b> &agrave; ce jour. Le dernier membre est <a href="/Systeme/User/[!Us::Login!]/InfoUser">[!Us::Nom!]</a> inscrit le [UTIL FULLDATEFR][!Us::tmsCreate!][/UTIL]<br /><br />
	
	[COUNT Forum/Sujet|Suj]
	[COUNT Forum/Post|Pos]
	Il y actuellement [!Suj!] sujets de discussion et un total de [!Pos!] r&eacute;ponses<br /><br />
	
	
	
	
	
	[COUNT Systeme/Connexion|Con]
	[IF [!Con!]=1]
		Il y a actuellement [!Con!] presonne en ligne<br />
		[ELSE]
		Il y a actuellement [!Con!] presonnes en ligne<br />
	[/IF]

	Qui est connect&eacute; :	
	[STORPROC Systeme/Connexion|Con]

		[STORPROC Systeme/User/[!Con::userEdit!]|U|0|100][/STORPROC]
			[IF [!U::Login!]=Kobeye]
			[ELSE]
				<a href="/Systeme/User/[!U::Login!]/InfoUser">[!U::Login!]</a> - 
			[/IF]
	[/STORPROC]<br />
	[COUNT Systeme/Connexion/Session=|Ses]
	[!Ses!] invit&eacute;(s)<br />

</div>