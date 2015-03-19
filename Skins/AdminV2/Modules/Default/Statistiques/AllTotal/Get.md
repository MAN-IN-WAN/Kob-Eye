{
//Couleurs
[!Couleur::0:=#0000ff!]
[!Couleur::1:=#00ff00!]
[!Couleur::2:=#ff0000!]
//Calcul statistique
  "title":{
    "text":  "[!Titre!]",
    "style": "{font-size: 20px; color:#0000ff; font-family: Arial; text-align: center;}"
  },
 
  "y_legend":{
    "text": "[!Titre!]",
    "style": "{color: #0000ff; font-size: 12px;}"
  },
 
  "elements":[
		{
		"type":      "line",
		"alpha":     0.5,
		"colour":    "[!Couleur::0!]",
		"text":      "[!Fonction!]",
		"font-size": 10,
		"values" :   [
		[STATS [!Module!]/[!Trigger!]/[!Fonction!]|St]
			[STORPROC [!St!]|Sta][IF [!Pos!]>1],[/IF][IF [!Max!]<[!Sta::Value!]][!Max:=[!Sta::Value!]!][/IF][IF [!Min!]>[!Sta::Value!]][!Min:=[!Sta::Value!]!][/IF][!Sta::Value!][/STORPROC]
		]
		}
  ],
 
  "x_axis":{
    "stroke":1,
    "tick_height":10,
    "colour":"#d000d0",
    "grid_colour":"#00ff00",
    "labels": {
        "labels": [[STORPROC [!St!]|Sta][IF [!Pos!]>1],[/IF]"[!Sta::Id!]"[/STORPROC]]
    }
   },
 
  "y_axis":{
    "stroke":      4,
    "tick_length": 3,
    "colour":      "#d000d0",
    "grid_colour": "#00ff00",
    "offset":      [!Min!],
    "max":         [!Max!]
  }
}
 
 