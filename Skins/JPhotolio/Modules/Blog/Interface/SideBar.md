			<div class="span4 sidebar">
				<div class="containerborder"> <!--  Search sidebar -->
					<div class="inner-container">
						<h3>Recherche</h3>
						<div class="widget-line"></div>
						<form method="get" id="searchform" action="#">
							<input type="text" class="field" name="s" id="s" placeholder="Recherche" />
						</form>
					</div>
				</div> <!--  End Search sidebar -->
				<div class="containerborder"> <!--  Category sidebar -->
					<div class="inner-container">
						<h3>Cat&eacute;gories</h3>
						<div class="widget-line"></div>
						<ul>
							[STORPROC Blog/Categorie|Cat]
							<li class="cat-item"><a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]" title="Voir tous les posts de la catégorie [!Cat::Titre!]">[!Cat::Titre!]</a></li>
							[/STORPROC]
						</ul>
					</div>
				</div> <!--  End sidebar -->
				
				<div class="containerborder"> <!--  Caleder sidebar -->
					<div class="inner-container">
						<h3>Calendrier</h3>
						<div class="widget-line"></div>
						<div id="calendar_wrap">
							<table id="wp-calendar">
								<caption>[DATE d/m/Y][!TMS::Now!][/DATE]</caption>
								<thead>
								<tr>
									<th scope="col" title="Lundi">L</th>
									<th scope="col" title="Mardi">M</th>
									<th scope="col" title="Mercredi">M</th>
									<th scope="col" title="Jeudi">J</th>
									<th scope="col" title="Vendredi">V</th>
									<th scope="col" title="Samedi">S</th>
									<th scope="col" title="Dimanche">D</th>
								</tr>
								</thead>
								
								<tfoot>
									<tr>
										<td colspan="3" id="prev">
										<a href="#" title="View posts for August 2012">&laquo; Aug</a></td>
										<td class="pad">&nbsp;</td>
										<td colspan="3" id="next" class="pad">&nbsp;</td>
									</tr>
								</tfoot>
		
								<tbody>
									<tr>
										<td colspan="5" class="pad">&nbsp;</td>
										<td>1</td>
										<td>2</td>
									</tr>
									<tr>
										<td>3</td>
										<td>4</td>
										<td>5</td>
										<td>6</td>
										<td>7</td>
										<td>8</td>
										<td>9</td>
									</tr>
									<tr>
										<td>10</td>
										<td>11</td>
										<td>12</td>
										<td>13</td>
										<td>14</td>
										<td id="today">15</td>
										<td>16</td>
									</tr>
									<tr>
										<td>17</td>
										<td>18</td>
										<td>19</td>
										<td>20</td>
										<td>21</td>
										<td>22</td>
										<td>23</td>
									</tr>
									<tr>
										<td>24</td>
										<td>25</td>
										<td>26</td>
										<td>27</td>
										<td>28</td>
										<td>29</td>
										<td>30</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>	<!--  End Caleder sidebar -->
				
				
				<div class="containerborder"> <!--  Recent Post sidebar -->
					<div class="inner-container">		
						<h3>Posts Récents</h3>
						<div class="widget-line"></div>		
						<ul>
							[STORPROC Blog/Post|P2|0|5|tmsCreate|DESC]
								[STORPROC [!P2::getParents(Categorie)!]|C|0|1][/STORPROC]
							<li><a href="/[!Systeme::CurrentMenu::Url!]/[!C::Url!]/Post/[!P2::Url!]" title="[!P2::Titre!]">[!P2::Titre!]</a></li>
							[/STORPROC]
						</ul>
					</div>
				</div>  <!--  End Recent Post sidebar -->
			</div>	
