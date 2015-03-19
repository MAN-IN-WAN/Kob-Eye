[INFO [!Query!]|I]
//Fil Ariane
<ol class="breadcrumb">
  <li><a href="/">Accueil</a></li>
  [!LNK:=!]
  [STORPROC [!Systeme::CurrentMenu::MenuParent!]|M]
        [STORPROC [!M::MenuParent!]|M2]
            [!LNK+=/[!M2::Url!]!]
            <li><a href="[!LNK!]" >[!M2::Titre!]</a></li>
        [/STORPROC]
        [!LNK+=/[!M::Url!]!]
        <li><a href="[!LNK!]" >[!M::Titre!]</a></li>
  [/STORPROC]
  [!LNK:=/[!Systeme::CurrentMenu::Url!]!]
  <li><a href="[!LNK!]">[!Systeme::CurrentMenu::Titre!]</a></li>
  [!REQ:=!]
  [STORPROC [!I::Historique!]|H|0|10]
        [IF [!Req!]=]
            [!REQ:=[!I::Module!]/!]
        [ELSE]
            [!REQ+=/!]
        [/IF]
        [!REQ+=[!H::DataSource!]/[!H::Value!]!]
        [INFO [!REQ!]|J]
        [IF [!J::TypeSearch!]!=Child]
            [IF [!Pos!]>1]
                [!LNK+=/[!H::DataSource!]!]
            [/IF]
            [STORPROC [!REQ!]|O|0|1]
                [!LNK+=/[!O::Id!]!]
                <li class="[IF /[!Lien!]=[!LNK!]]active[/IF]" href="[!LNK!]">Fiche [!O::ObjectType!] [!O::getFirstSearchOrder()!] ([!O::Id!])</li>
            [/STORPROC]
        [ELSE]
            [IF [!NbResult!]>1]
                <li class="[IF /[!Lien!]=[!LNK!]]active[/IF]" href="[!LNK!]">Liste [!H::DataSource!]</li>
            [/IF]
        [/IF]
  [/STORPROC]
</ol>


[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
    [IF [!I::Reflexive!]]
	[MODULE [!Query!]/Tree]
    [ELSE]
	[MODULE [!Query!]/List]
    [/IF]
[ELSE]
	[MODULE [!Query!]/Fiche]
[/IF]
