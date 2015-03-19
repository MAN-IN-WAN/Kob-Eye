

<div class="item-rating"></div>
	[STORPROC [!Query!]|C|0|1]
		<div class="row-fluid">
			<div class="span12">
				[IF [!C::Icone!]]<img src="/[!C::Icone!]" alt="logo">[/IF]
			</div> 
		</div> 
		<div class="row-fluid" style="margin-top:10px;">
			<div class="well span12"> 
				<h1>[!C::Titre!]</h1>
				<blockquote>[!C::Description!]</blockquote>
			</div>
		</div>

		<div class="row-fluid">
			[STORPROC Redaction/Categorie/[!C::Id!]/Article|A|0|10]
			<div class="span6 well">
				<h2>[!A::Titre!]</h2>
					<blockquote>	[!A::Contenu!]</blockquote>
				[STORPROC Redaction/Article/[!A::Id!]/Image|I]
					<img src="/[!I::URL!].mini.400x300.jpg">
				[/STORPROC]
			</div>
			[/STORPROC]
		</div>
	[/STORPROC]
<div class="item-separator"></div>
