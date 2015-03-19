<div class="tabbable tabs-left">
	    <ul class="nav nav-tabs">
	    	<li><a data-toggle="tab" href="#lA">Nouvelle demande</a></li>
	    	<li><a data-toggle="tab" href="#lB">Synthèse de mes demandes</a></li>
	    	<li><a data-toggle="tab" href="#lC">Interdium Volgus</a></li>
	    	<li><a data-toggle="tab" href="#lD">Interdium Volgus</a></li>
	    	<li><a data-toggle="tab" href="#lE">Interdium Volgus</a></li>
	    </ul>
    <div class="tab-content">
   		<div id="lA" class="tab-pane">
			<ul class="breadcrumb">
			    <li><a href="#">Demandes</a> <span class="divider">></span></li>
			    <li class="active">New</li>
		    </ul>
		    
		    <div id="new">
				<h4>> Nouvelle demande</h4>
				<div id="formulaire_new">
		    	    <form class="form-horizontal">
		    	    	<div class="well">
						    <div class="control-group">
							    <label class="control-label" for="Varietal">Varietal</label>
							    <div class="controls">
							    	<select id="Varietal">
							    	[STORPROC Murphy/Varietal|Var|0|100000]
							    		<option value="[!Var::Id!]" [IF [!Var::Id!]=[!Object::Varietal!]]selected="selected"[/IF]>[!Var::Varietal!]</option>
							    	[/STORPROC]
							    	</select>
						    	</div>
						    </div>
						    <div class="control-group">
							    <label class="control-label" for="Appellation">Appellation</label>
							    <div class="controls">
							    	<select id="Appellation">
							    	[STORPROC Murphy/Appellation|Var|0|100000]
							    		<option value="[!Var::Id!]" [IF [!Var::Id!]=[!Object::Appellation!]]selected="selected"[/IF]>[!Var::Appellation!]</option>
							    	[/STORPROC]
							    	</select>
						    	</div>
						    </div>
						    <div class="control-group">
							    <label class="control-label" for="Vintage">Vintage</label>
							    <div class="controls">
							    	<input type="text" id="Vintage" placeholder="Vintage" value="[!Object::Vintage!]">
							    </div>
						    </div>
						</div>
					    <div class="control-group">
						    <label class="control-label" for="Quantity">Quantity</label>
						    <div class="controls">
						    	<select id="Quantity">
						    	[STORPROC Murphy/Quantity|Var|0|100000]
						    		<option value="[!Var::Id!]" [IF [!Var::Id!]=[!Object::Quantity!]]selected="selected"[/IF]>[!Var::Quantity!]</option>
						    	[/STORPROC]
						    	</select>
					    	</div>
					    </div>
					    <div class="control-group">
						    <label class="control-label" for="Filtration">Filtration</label>
						    <div class="controls">
						    	<select id="Filtration">
						    	[STORPROC Murphy/Filtration|Var|0|100000]
						    		<option value="[!Var::Id!]" [IF [!Var::Id!]=[!Object::Filtration!]]selected="selected"[/IF]>[!Var::Filtration!]</option>
						    	[/STORPROC]
						    	</select>
					    	</div>
					    </div>
					    <div class="control-group">
						    <label class="control-label" for="Volume">Volume</label>
						    <div class="controls">
					    	    <div class="input-prepend input-append">
								    <input class="span2" id="Volume" type="text" placeholder="Volume" value="[!Object::Volume!]">
								    <span class="add-on">L</span>
							    </div>
						    </div>
					    </div>
					    <div class="control-group">
						    <label class="control-label" for="EndDate">Shipping Date (All by)</label>
						    <div class="controls">
					    	    <div class="input-prepend input-append">
								    <input class="span2" id="EndDate" type="text" placeholder="Shipping Date" value="[DATE d/m/Y][!Object::EndDate!][/DATE]">
								    <span class="add-on"><img src="Skins/[!Systeme::Skin!]/Img/images/icone_calendrier.gif" width="20px" /></span>
							    </div>
						    </div>
					    </div>
					    <div class="control-group">
						    <label class="control-label" for="inputDemande">Demande échantillon</label>
						    <div class="controls">
						    	<input type="checkbox" id="inputDemande">
						    </div>
					    </div>
					    <div class="control-group">
						    <label class="control-label" for="Comments">Comments</label>
						    <div class="controls">
						    	<textarea id="Comments" cols="10" rows="5"></textarea>
						    </div>
					    </div>
					    <div class="control-group">
						    <div class="controls pull-right">
						    	<button type="submit" class="btn btn-inverse">Soumettre</button>
						    	<button type="reset" class="btn btn-inverse">Rejeter</button>
					   		</div>
					    </div>
					    <hr style="background-color:grey;color:grey;height:4px"/>
					    
					    <div class="control-group">
						    <label class="control-label" for="inputAvaible">Avaible Volume</label>
						    <div class="controls">
						    	<input class="span2" id="inputAvaible" type="text" placeholder="Avaible Volume">
						    </div>
					    </div>
				    </form>
			   </div>
			</div>
			
			
		</div>
		<div id="lB" class="tab-pane">
			<ul class="breadcrumb">
			    <li><a href="#">Demandes</a> <span class="divider">></span></li>
			    <li class="active">Synthèse</li>
		    </ul>
		    <div id="synthese">
				<h4>> Synthèse de mes demandes</h4>
			    <table class="table table-striped">
			    	<tr>
				    	<th class="span_h">Date</th>
				    	<th class="span_h">Vin</th>
					    <th class="span_h">Millésime</th>
					    <th class="span_h">Quantité</th>
					    <th class="span_h">Prix Demandé</th>
					    <th class="span_h">Tarif Proposé</th>
					    <th class="span_h">Echantillon</th>
					    <th class="span_h">Accepté</th>
			    	</tr>
			    	<tr>
				    	<td class="span_c1">Date</td>
					    <td class="span_c1">Vin</td>
					    <td class="span_c1">Millésime</td>
					    <td class="span_c1">Quantité</td>
					    <td class="span_c1">Prix Demandé</td>
					    <td class="span_c1">Tarif Proposé</td>
					    <td class="span_c1">Echantillon</td>
					    <td class="span_c1">Accepté</td>
			    	</tr>
			    	<tr>
				    	<td class="span_c2">Date</td>
					    <td class="span_c2">Vin</td>
					    <td class="span_c2">Millésime</td>
					    <td class="span_c2">Quantité</td>
					    <td class="span_c2">Prix Demandé</td>
					    <td class="span_c2">Tarif Proposé</td>
					    <td class="span_c2">Echantillon</td>
					    <td class="span_c2">Accepté</td>
			    	</tr>
			    </table>
			</div>
		</div>
    </div>
</div>