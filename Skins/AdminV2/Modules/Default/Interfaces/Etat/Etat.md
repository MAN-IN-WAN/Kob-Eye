[IF [!Obj!]=]
    [STORPROC [!QueryObj!]|Obj|0|1]
    [/STORPROC]
[ELSE]
    [!QueryObj:=[!Query!]!]
[/IF]
[IF [!Module!]]
[ELSE]
    [!Module:=[!Module::Actuel::Nom!]!]
[/IF]


[MODULE Systeme/Interfaces/BarreAction?Query=[!QueryObj!]]

[BLOC Panneau|top:50px;]
      
     //Titre de la page
//     <div class="BigTitle">
//         <div></div>
//         <span style="">[!Obj::getFirstSearchOrder!]</span>
//     </div>
     
     <div class="BlocIconePub" style="width:100%">
       [!ObjImg:=Unknown!]
       [STORPROC [!Module::[!Module!]::Db::AccessPoint!]|ObjClass]
         [IF [!ObjClass::titre!]=[!Obj::ObjectType!]]
           [!ObjImg:=[!ObjClass::Icon!]!]
         [/IF]
       [/STORPROC]
       <div class="background:url([!ObjImg!])">
	 <h1 style="float:right">[!Obj::getFirstSearchOrder!]</h1>
       </div>
     </div>
     //Affichage de l'état
     
[/BLOC]
