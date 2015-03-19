[STORPROC Systeme/User|U|0|1][/STORPROC]
[OBJ Systeme|ActiveTemplate|T]
[METHOD T|test][PARAM][!T!][/PARAM][/METHOD]
[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM]enguerrand@abtel.fr[/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]test@abtel.fr[/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM]TEST[/PARAM][/METHOD]
[METHOD LeMail|Body][PARAM]
	<ul>
	[STORPROC Systeme/User|V]
		<li>[!V::Login!]</li>
	[/STORPROC]
	</ul>
[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]


