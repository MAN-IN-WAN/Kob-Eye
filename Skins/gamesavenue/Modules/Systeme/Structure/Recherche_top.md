	<div class="ligneSelectGris">
		<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
		<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
		<div class="ligneSelectGrisCentre" style="overflow:hidden">
			<div style="float:left;width:45%;padding-top:3px;text-align:left;margin-top:3px;"><input type="text" name="Recherche" size="60" value="[!Recherche!]"  onfocus="this.value='';"/></div>
			<div style="float:left;width:55%;padding-top:3px;text-align:right;margin-top:3px;">
				Popularité : <select name="Popularite" class="selectfin">
					<option  value="">Indifférente</option>
					<option [IF [!Popularite!]=5]selected="selected"[/IF] value=5>* * * * * </option>
					<option [IF [!Popularite!]=4]selected="selected"[/IF] value=4>* * * * </option>
					<option [IF [!Popularite!]=3]selected="selected"[/IF] value=3>* * *  </option>
					<option [IF [!Popularite!]=2]selected="selected"[/IF] value=2>* *  </option>
					<option [IF [!Popularite!]=1]selected="selected"[/IF] value=1>* </option>
				</select>
				&nbsp;&nbsp;&nbsp;<input type="submit" name="Rech" value="Rechercher"/>
			</div>			
		</div>
	</div> // fin ligne popularité