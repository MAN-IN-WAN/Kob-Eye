[HEADER]
<style type="text/css">
	div#Global {
		overflow: auto;
	}
</style>
[/HEADER]

[STORPROC [!Query!]|Serv]
	[METHOD Serv|Synchroniser][/METHOD]
[/STORPROC]

[!DATE:=1366712288!]

//CLIENT
[STORPROC Parc/Client/tmsCreate>[!DATE!]|Do|0|10000][!Do::Save()!][/STORPROC]

//DOMAIN
[STORPROC Parc/Domain/tmsCreate>[!DATE!]|Do|0|10000][!Do::Save()!][/STORPROC]
//SUBDOMAIN
[STORPROC Parc/Subdomain/tmsCreate>[!DATE!]|S|0|100000]<li>[!S::Url!]</li>[!S::Save()!][/STORPROC]
//CNAME
[STORPROC Parc/CNAME/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]
//MX
[STORPROC Parc/MX/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]
//NS
[STORPROC Parc/NS/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]
//TXT
[STORPROC Parc/TXT/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]


//HOST
[STORPROC Parc/Host/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]
//Ftpuser
[STORPROC Parc/Ftpuser/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]
//Apache
[STORPROC Parc/Apache/tmsCreate>[!DATE!]|S|0|100000][!S::Save()!][/STORPROC]
