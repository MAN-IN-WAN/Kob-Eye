// Recoit OBJ --> Nom de l'objet
// Recoit Prop --> Nom de la propriete
// Recoit Module --> Module de l'objet
// Recoit Prefixe --> Le prefixe


[OBJ [!Module!]|[!Obj!]|T]
[!Passe:=False!]
[STORPROC [!T::Proprietes!]|P]
    [IF [!Prop!]==[!P::Nom!]]
	[!Passe:=True!]
	[STORPROC [![!P::query!]:/::!]|Q|0|1][/STORPROC]
	[STORPROC [![!P::query!]:/::!]|OutVar|1|1][/STORPROC]
	[IF [!Prop::module!]]
	    [![!Prefixe!]Explorer_Module:=[!Prop::module!]!]
	[/IF]		
    [/IF]
[/STORPROC]
[IF [!Passe!]=False]
    [IF [!Prop!]=gid]
	[!Q:=Systeme/Group!]
	[!Passe:=True!]
	[!OutVar:=Nom!]
    [/IF]
    [IF [!Prop!]=uid]
	[!Q:=Systeme/User!]
	[!Passe:=True!]
	[!OutVar:=Login!]
    [/IF]
[/IF]
[INFO [!Q!]|Test]
[IF [!Passe!]=True]
    [IF [!Test::Reflexive!]]
	<div id="AffichArbo">
	</div>    
	<script type="text/javascript">
	tree = new Mif.Tree({
		container: $('AffichArbo'),// tree container
		types: {// node types
			folder:{
				openIcon: 'mif-tree-open-icon',//css class open icon
				closeIcon: 'mif-tree-close-icon'// css class close icon
			}
		},
		dfltType:'folder',//default node type
		height: 18//node height
	});


/*testTree.load({
    [
        {
            property: {name: 'root'},
            children:[
                {
                    property:{name:'node1'}
                },
                {
                    property:{name:'node2'},
                    children:[
                        {property:{name:'node2.1'}}
                    ]
                }
            ]
        }
    ]
});*/
var json=
    [
      {"property" :{"name":"Racine"},
       "children":[
    [STORPROC [!Q!]|M]
	{  "property" : {"name":"[!M::Id!]: [!M::getFirstSearchOrder!]"},  
	   "children" : [ [RECURSIV] ]
	}[IF [!Pos!]!=[!NbResult!]],[/IF]
    [/STORPROC]
      ]}
    ]    
	
	// load tree from json.
	tree.load({
		json: json
	});
	tree.addEvent('select',function(node){
	   if (node.name != "Racine"){
         	   $("[!InputId!]").set('value', "[!Prefixe!][IF [!Prefixe!]]/[/IF]" + node.name.split(':')[0]);
		   Fl.closePopup();
		   }
	});



	</script>    
	//[MODULE Systeme/Interfaces/Arborescence?Prefixe=[!Prefixe!]&Chemin=[!Q!]&NbChamp=4&TypeEnf=[!P::Nom!]&Inter=radio&Var=[!Prefixe!][!Prop!]&PrefixeVar=[!PrefixeVar!]&FromAjax=True]
    [ELSE]
	[MODULE Systeme/Interfaces/Liste?Chemin=[!Q!]&Inter=radio&Type=Select&Prefixe=[!Prefixe!]&Var=[!Prefixe!][!Prop!]&Top=10&RechPrefixe=Explore&OutVar=[!OutVar!]&FromAjax=True]
	<script type="text/javascript">
	    var myFct = function(el){
	      el.removeEvents('click');
	      el.rel = "noMoreAjax";
	      el.addEvent('click',function(e)
	      {
	        e.stop();
	        if (this.getParent('td').hasClass('NumCol'))
		{
		  var a = this.get('html');
		}
		else
		{
		    var a = this.getParent().getParent().getElement(".NumCol a").get('html');
		}
		b = a.match(/[0123456789]+/);
		$("[!InputId!]").set('value', "[!Prefixe!][IF [!Prefixe!]]/[/IF]" + b);
		Fl.closePopup();
	      }.bind(el))};
	    $$(".NomCol a ").each(myFct);
	    $$(".NumCol a ").each(myFct);
	</script>    
    [/IF]
[/IF]









