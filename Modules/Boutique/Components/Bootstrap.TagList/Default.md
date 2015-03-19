
<!-- Block tags module -->
<div id="tags_block_left" class="block tags_block">
	<h3 class="title_block title_block_green">Tags</h3>
	<p class="block_content">
		[STORPROC Systeme/Tag|T|0|10|tmsCreate|DESC]
			[COUNT Systeme/Tag/[!T::Id!]/Page|NbPage]
		<a href="/[!Systeme::getMenu(Systeme/Search)!]?search=[!T::Nom!]" title="Recherche avec le mot clef [!T::Nom!]" class="tag_level[!NbPage!] [IF [!Pos!]=1]first_item[ELSE]item[/IF]">[!T::Nom!]</a>
		[/STORPROC]
	</p>
</div>
<!-- /Block tags module -->
