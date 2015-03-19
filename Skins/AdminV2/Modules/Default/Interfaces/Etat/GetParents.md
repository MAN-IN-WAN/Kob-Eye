<div class="ParentDisplay">
    <div class="CatTitle">
      <img></img>
      Emplacement
    </div>
    <table class="ListeMini Liste">
      <thead>
  	<tr>
  	  <th class="Actions"> Type </th>
  	  <th class="NumCol"> Num </th>
  	  <th class="NomCol"> Nom </th>
  	</tr>
      </thead>
      <tbody>
  	[STORPROC [!Obj::typesParent!]|Par]
	    [!DispName:=[!Par::Description!]!]
	    [!Prefix:=[!Module::Actuel::Nom!]!]
	    [STORPROC [!Obj::Historique!]|Histo|0|10]
	    	      [IF [!Pos!]<[!NbResult!]]
		      	  [!Prefix+=/[!Histo::ObjectType!]/[!Histo::Id!]!]
			  [/IF]	
	    [/STORPROC]
	    [IF [!DispName!]=]
		[!DispName:=[!Par::Titre!]!]
	    [/IF]
	    [STORPROC [!Module::Actuel::Nom!]/[!Par::Titre!]/[!Obj::ObjectType!]/[!Obj::Id!]|O]
		<tr>
		    <td><a href="[!Prefix!]/[!Par::Titre!]/[!O::Id!]">[!DispName!]</a></td>
		    <td><a href="[!Prefix!]/[!Par::Titre!]/[!O::Id!]">[!O::Id!]</a></td>
		    <td><a href="[!Prefix!]/[!Par::Titre!]/[!O::Id!]">[!O::getFirstSearchOrder!]</a></td>
		</tr>
	    [/STORPROC]
  	[/STORPROC]
  	</tbody>
    </table>
  </div>
