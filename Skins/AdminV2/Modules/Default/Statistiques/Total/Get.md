{
//Parametres
[!TEST:=[![!Requete!]:/-!]!]
[STORPROC [!TEST!]|R|0|1][!Module:=[!R!]!][/STORPROC]
[STORPROC [!TEST!]|R|1|1][!Trigger:=[!R!]!][/STORPROC]
[STORPROC [!TEST!]|R|2|1][!Fonction:=[!R!]!][/STORPROC]
[STORPROC [!TEST!]|R|3|1][!Dd:=[!R!]!][/STORPROC]
[STORPROC [!TEST!]|R|4|1][!Df:=[!R!]!][/STORPROC]
[STATS [!Module!]/[!Trigger!]|S]
[STORPROC [!S!]|F]
	[IF [!F::Name!]=[!Fonction!]][!Func:=[!F!]!][/IF]
[/STORPROC]
//Couleurs
[!Couleur::0:=#057390!]
[!Couleur::1:=#00ff00!]
[!Couleur::2:=#ff0000!]
//Calcul statistique
  "title":{
    "text":  "[!Func::Name!]",
    "style": "{font-size: 20px; color:#000000; text-align: center;}"
  },
 
  "y_legend":{
    "text": "[!Func::Unit!]",
    "style": "{color: #000000; font-size: 12px;}"
  },
 
  "elements":[
	{
	[IF [!Func::Graph!]!=]
	"type": "[!Func::Graph!]",
	[ELSE]
	"type": "line",
	[/IF]
	"alpha":     0.5,
	"colour":    "[!Couleur::0!]",
	"text":      "[!Fonction!]",
	"font-size": 10,
	"values" :   [
	[STATS [!Module!]/[!Trigger!]/[!Fonction!]|St|[!Dd!]|[!Df!]]
		[STORPROC [!St!]|Sta][IF [!Pos!]>1],[/IF][IF [!Max!]<[!Sta::Value!]][!Max:=[!Sta::Value!]!][/IF][IF [!Min!]>[!Sta::Value!]||[!Min!]=][!Min:=[!Sta::Value!]!][/IF]
		[IF [!Func::Graph!]!=]
			{
			"colour": "[!Couleur::0!]",
			"top": [!Sta::Value!]
		[ELSE]
			{
			"type": "solid-dot",
			"dot-size": 3,
			"halo-size": 1,
			"colour": "[!Couleur::0!]",
			"value": [!Sta::Value!]
		[/IF]
		[IF [!Func::Unit!]=Secondes]
			[!Hour:=[!Math::Floor([!Sta::Value:/3600!])!]!][!Minu:=[!Math::Floor([![!Sta::Value:-[!Hour:*3600!]!]:/60!])!]!][!Sec:=[![!Sta::Value:-[!Minu:*60!]!]:-[!Hour:*3600!]!]!]
			,"tip": "#val# [!Func::Unit!]<br> soit [!Hour!]h [!Minu!]m [!Sec!]s<br>[!Sta::Title!]"
			}
		[ELSE]
			,"tip": "#val# [!Func::Unit!]<br>[!Sta::Title!]"
			}
		[/IF]
		[/STORPROC]
	]
	}
  ],
 
  "x_axis":{
    "stroke":1,
    "tick_height":10,
    "colour":"#057390",
    "grid_colour":"#00ff00",
    "labels": {
	"rotate": 310,
	"size": 9,
        "labels": [
		[STORPROC [!St!]|Sta][IF [!Pos!]>1],[/IF]"[!Sta::Title!]"[/STORPROC]
	]
    }
   },
 
  "y_axis":{
    "stroke":      4,
    "tick_length": 3,
    "steps":[!Step:=[![!Max:-[!Min!]!]:/8!]!][!Step!],
    "colour":      "#057390",
    "grid_colour": "#057390",
    "offset":      [!Min!],
    "max":         [!Max!]
[IF [!Func::Unit!]=Seconde]
	,
    "labels": {
	"rotate": 80,
        "labels": [
		[STORPROC [!St!]|Ste][IF [!Pos!]>1],[/IF]"[!Date::getDate(H-i-s,[!Ste::Value!])!][!Ste::Value!]"[/STORPROC]
	]
    }
[/IF]
  },
  "num_decimals": 0,
  "is_fixed_num_decimals_forced": false,
  "is_decimal_separator_comma": true,
  "is_thousand_separator_disabled": true,
 "bg_colour": "#FFFFFF"
}
 
 