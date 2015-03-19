[STORPROC [!Query!]|S|0|1]
	[IF [!conf!]="OK"]
		[IF [!S::userCreate!]=[!Systeme::User::Id!]]			
			[STORPROC Forum/Sujet/Post/[!S::Id!]|Sj]
				[STORPROC Forum/Sujet/[!Sj::Id!]/Post|Pst|0|1|tmsCreate|ASC]
					[IF [!S::Id!]=[!Pst::Id!]]
						[!Sj::Delete!]
					[/IF]
				[/STORPROC]
			[/STORPROC]
			[!S::Delete!]
		[ELSE]
			[IF [!Systeme::User::Admin!]]
				[STORPROC Forum/Sujet/Post/[!S::Id!]|Sj]
					[STORPROC Forum/Sujet/[!Sj::Id!]/Post|Pst|0|1|tmsCreate|ASC]
						[IF [!S::Id!]=[!Pst::Id!]]
							[!Sj::Delete!]
						[/IF]
					[/STORPROC]
				[/STORPROC]
				[!S::Delete!]
			[/IF]
		[/IF]
		[REDIRECT]Forum[/REDIRECT]
	[ELSE]
		<div class="infosBox">
			<p style="text-align:center">&Ecirc;tes vous s&ucirc;r de vouloir supprimer "[!S::getFirstSearchOrder!]" ?</p>
			<div style="text-align:center">
				<a href="/[!Lien!]?conf=OK">OUI</a>
				<a href="[!Systeme::Connection::Ref!]">NON</a>
			</div>
		</div>
	[/IF]
[/STORPROC]