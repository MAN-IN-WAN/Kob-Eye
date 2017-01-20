[STORPROC [!Query!]|Objet|0|1]
	[IF [!Action!]=Vider]
            [STORPROC [!Query!]/Contact|C|0|10000]
                [!C::Delete!]
            [/STORPROC]
	<div class="alert alert-success">Le groupe [!Objet::getFirstSearchOrder()!] a été vidé avec succès.</div>

[ELSE]
				<h1>Etes vous sur de vouloir vider le groupe [!Objet::getFirstSearchOrder()!]?</h1>
				<input type="hidden" name="Action" value="Vider" />
	[/IF]
[/STORPROC]
