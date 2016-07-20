[STORPROC [!Query!]|Objet|0|1]
	[IF [!Action!]=Vider]
            [STORPROC [!Query!]/Contact|C|0|10000]
                [!C::Delete!]
            [/STORPROC]
             [REDIRECT][!Query!][/REDIRECT]
	[ELSE]
				<h1>Vider le groupe de contact</h1>
				//Maintenant on ouvre le fichier en ecriture
				<form enctype="multipart/form-data" action="/[!Lien!].csv" method="post" name="frm" >
					//VALIDER
					<div class="Bouton" style="width:100%;height:15px;">
						<b class="b1"></b>
						<b class="b2" style="text-align:center;display:inline;left:15px;right:15px;position:absolute;">
							<input type="submit" value="Vider" name="Action" class="btn btn-danger"/>
						</b>
						<b class="b3" style="position:absolute;right:0px;"></b>
					</div>
				</form>
	[/IF]
[/STORPROC]
