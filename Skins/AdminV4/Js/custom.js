	$(document).ready( function() {   
		setup_wizard();
		setup_chart();
	});

	/* end validation_setup_demo */

	/* ---------------------------------------------------------------------- */
	/*	Setup_wizard_demo
	/* ---------------------------------------------------------------------- */	
	
	function setup_wizard() {
		if ($('#wizard_name').length) {
			$('#wizard_name').bootstrapWizard({
				'tabClass' : 'nav',
				'debug' : false,
				onShow : function(tab, navigation, index) {
					//console.log('onShow');
				},
				onNext : function(tab, navigation, index) {
					//console.log('onNext');
					switch (index){
						case 1:
							$.jGrowl("Félicitation <b>"+$('#name').val()+",</b> vous avez atteint l'étape 2. Prenez une grande attention à la saisie de vos informations. Elles influent sur les proposition de programmes !", { 
								header: 'Vos mensurations', 
								sticky: false,
								easing: 'easeOutBack',
								animateOpen: { 
									height: "show"
								},
								animateClose: { 
									opacity: 'hide' 
								}
							});
						break;
						case 2:
							$.jGrowl("", { 
								header: 'Votre objectif', 
								sticky: false,
								easing: 'easeOutBack',
								animateOpen: { 
									height: "show"
								},
								animateClose: { 
									opacity: 'hide' 
								}
							});
						break;
						case 3:
							$.jGrowl("voici la dernière étape", { 
								header: 'Date de départ', 
								sticky: false,
								easing: 'easeOutBack',
								animateOpen: { 
									height: "show"
								},
								animateClose: { 
									opacity: 'hide' 
								}
							});
						break;
						case 4:
							$.jGrowl("Vous pouvez maintenant cliquer sur enregistrer mon profil afin de commencer votre programme minceur !", { 
								header: 'Etape 4 OK', 
								sticky: false,
								easing: 'easeOutBack',
								animateOpen: { 
									height: "show"
								},
								animateClose: { 
									opacity: 'hide' 
								}
							});
						break;
					}
					// Set the name for the next tab
					//$('#inverse-tab3').html('Hello, ' + $('#name').val());
	
				},
				onPrevious : function(tab, navigation, index) {
					//console.log('onPrevious');
				},
				onLast : function(tab, navigation, index) {
					//console.log('onLast');
				},
				onTabClick : function(tab, navigation, index) {
					//console.log('onTabClick');
					//alert('on tab click disabled');
					//return false;
				},
				onTabShow : function(tab, navigation, index) {
					//console.log('onTabShow');
					var $total = navigation.find('li').length;
					var $current = index + 1;
					var $percent = ($current / $total) * 100;
					$('#wizard_name').find('.bar').css({
						width : $percent + '%'
					});
					//on cache le suivant
					if ($total==$current){
						$('#next').css("visibility","hidden");
					}else $('#next').css("visibility","visible");
					//on cache le precedent
					if ($current==1){
						$('#previous').css("visibility","hidden");
					}else $('#previous').css("visibility","visible");
				}
			});
		}// end if
	
	}
	
	/* end setup_wizard_demo */

	function setup_chart() {
		if ($("#site-stats").length) {
	
			var poids = $.poids;
			var poidssouhaite = $.poidssouhaite;
			//var visitors = [[1, 65], [3, 50], [4, 73], [5, 100], [6, 95], [7, 103], [8, 111], [9, 97], [10, 125], [11, 100], [12, 95], [13, 141], [14, 126], [15, 131], [16, 146], [17, 158], [18, 160], [19, 151], [20, 125], [21, 110], [22, 100], [23, 85], [24, 37]];
			//console.log(pageviews)
			var plot = $.plot($("#site-stats"), [{
				data : poids,
				label : "Votre poids"
			},{
				data : poidssouhaite,
				label : "Votre poids souhaité"
			}], {
				series : {
					lines : {
						show : true,
						lineWidth : 1,
						fill : true,
						fillColor : {
							colors : [{
								opacity : 0.1
							}, {
								opacity : 0.15
							}]
						}
					},
					points : {
						show : true
					},
					shadowSize : 0
				},
				xaxis : {
					mode: "time",
    					timeformat: "%d/%m/%Y"
				},
	
				yaxes : [{
					min : 20,
					tickLength : 5
				}],
				grid : {
					hoverable : true,
					clickable : true,
					tickColor : $chrt_border_color,
					borderWidth : 0,
					borderColor : $chrt_border_color,
				},
				tooltip : true,
				tooltipOpts : {
					content : "%y kg le <b>%x",
					dateFormat : "%0d/%0m/%y",
					defaultTheme : true
				},
				colors : [$chrt_main, $chrt_second],
				xaxis : {
					mode: "time",
    					timeformat: "%d/%m/%y",
					ticks : 5,
					tickDecimals : 5
				},
				yaxis : {
					ticks : 15,
					tickDecimals : 0
				},
			});
	
		}
	}
