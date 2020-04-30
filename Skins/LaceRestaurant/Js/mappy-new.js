$(document).ready(
		function() {
			// Initialisation de l'API Ajax
			Mappy.api.init("PJVED2008", "ITdG0rShLPjwoLe6tlwRYVqwBhRxmOrFSLYMc/ZjBJm/XE6wnqOqFPn2iHuyIpUJUiB15ajSGNtNLlUL0r6f5Q==", { staticPath : _DATA.mappy_images });
			
			function init() {
				// Charge la carto Mappy
				var plan = $("#planMappy");
				var coord;
				var i;
				var num;
				var map;

				//plan page nous situer, zi et presentation hors HTML5
				if (plan.length === 1) {
					var longitude;
					var latitude;
					var projection;
					var centre;
					var p;

					if (typeof Mappy !== "undefined") {

						// nouvelle zone d'intervention
						if (typeof _DATA.zi === "undefined") {
							_DATA.zi = [];
						}
						_DATA.zi = $.unique(_DATA.zi);

						// ancienne zi pour garder compatibilité de version
						if (typeof _DATA.zoneIntervention === "undefined") {
							_DATA.zoneIntervention = [];
						}
						_DATA.zoneIntervention = $.unique(_DATA.zoneIntervention);
						if (typeof _DATA.zoneChalandise === "undefined") {
							_DATA.zoneChalandise = [];
						}
						_DATA.zoneChalandise = $.unique(_DATA.zoneChalandise);

						// initialisation de la carte
						map = new Mappy.api.map.Map({
							container : "#planMappy"
						});

						   // Ajout d'une barre d'outils
		    		    var toolbar = new Mappy.api.map.tools.ToolBar({
		    		        zoom :      true,
		    		        move:       true,
		    		        selection:  true,
		    		        slider:     true,
		    		        viewMode:   true
		    		    }, new Mappy.api.map.tools.ToolPosition("rt", new Mappy.api.types.Point(0, 30)));
		    		    map.addTool(toolbar);
						
		    		    map.disableScrollWheelZoom();
		    		    
						// le calque des zone de chalandise
						shapeLayer = new Mappy.api.map.layer.ShapeLayer(81);
						map.addLayer(shapeLayer);

						// le calque de pois
						layerCible = new Mappy.api.map.layer.MarkerLayer(100);
						map.addLayer(layerCible);

						// le calque de l'itinéraire (et des drapeaux)
						routeLayer = new Mappy.api.map.layer.RoadbookLayer(40);
						map.addLayer(routeLayer);
						layerFlag = new Mappy.api.map.layer.MarkerLayer(121);
						map.addLayer(layerFlag);

						// les géo-coordonnées pour le zoom final
						var ne = {
							x : 0,
							y : 0
						};
						var sw = {
							x : 0,
							y : 0
						};

						var setZoom = function() {
							var bounds;
							var bounds1 = layerCible.getBounds();
							var bounds2 = shapeLayer.getBounds();
							
							if (bounds2) {
								if (bounds1) {
									bounds = map.getBounds([ bounds1.ne, bounds1.sw, bounds2.ne, bounds2.sw ]);
								} else {
									bounds = bounds2;
								}
							} else {
								bounds = bounds1;
							}
							if (bounds) {

								//Si page plan alors on prend le zoom du xml si ancienne page plan
								map.setCenter(bounds.center, (typeof _DATA.zoom !== "undefined" && (typeof _DATA.forceZoom !== "undefined" && _DATA.forceZoom.length !== 0) && _DATA.geoCoordonnees.length === 1 ? parseInt(_DATA.zoom, 10) : map.getBoundsZoomLevel(bounds)));
								
								// récupération en cas de non-présence de geoloc ou zi
							} else if (typeof _DATA.loc !== "undefined") {
								var geocoder = new Mappy.api.geolocation.Geocoder();
								geocoder.setLanguage($("meta[content=en]").length === 1 ? 'eng' : 'fre');
								geocoder.geocode(_DATA.loc, function(results) {
									var resultats = [];
									var i;
									for (i = 0; i < results.length; i++) {
										var resultat = results[index];
										// 250 est le code ISO 3166-1 numeric de
										// la France
										if (resultat.Placemark.name !== "" && parseInt(resultat.Placemark.AddressDetails.Country.CountryNameCode.value, 10) === 250) {
											resultats.push(resultat);
										}
									}
									if (resultats.length === 1) {
										var center = resultats[0].Placemark.Point.coordinates;
										addMarker({
											lon : center[0],
											lat : center[1],
											lab : _DATA.loc
										});
										map.setCenter(new Mappy.api.geo.Coordinates(center[0], center[1]), (typeof _DATA.zoom !== "undefined" ? parseInt(_DATA.zoom, 10) : 8));
									} else {
										plan.hide();
									}
								}, function() {
									plan.hide();
								});
							} else {
								plan.hide();
							}
						};
						var colorMappy;
						// le style des zones
						if(isExist(_DATA.color2) && _DATA.color2.length !== 0) {
							colorMappy = _DATA.color2;
						} else {
							colorMappy = rgb2hex($('#left h2, .center-left h2').css("color"));
						}
						
						colorMappy = colorMappy.substr(4, 2) + colorMappy.substr(2, 2) + colorMappy.substr(0, 2);
						var shapeStyle = new Mappy.api.map.shape.ShapeStyle({
							lineWidth : 0,
							fillStyle : "60" + colorMappy
						});
						var onSuccess;
						var onError;

						if (_DATA.zoneIntervention.length === 0) {
							// ajoute les pois
							if (typeof _DATA.geoCoordonnees !== "undefined") {
								if (_DATA.geoCoordonnees.length > 0) {
									var max = 50;
									var saut = 1;
									if (_DATA.geoCoordonnees.length > max) {
										saut = Math.floor(_DATA.geoCoordonnees.length / max);
									}
									
									addMarker = function(geo) {
										var longitude = geo.lon;
										var latitude = geo.lat;
										var label = geo.lab;
										var poi = geo.poi;
										poi = poi > 0 && poi < 3 ? poi : 1;
										var projection = geo.proj;
										if (projection === "Lambert2Etendu") {
											// conversion Proj4js
											p = Proj4js.transform(new Proj4js.Proj("EPSG:27572"), new Proj4js.Proj("EPSG:4326"), new Proj4js.Point(longitude, latitude));
											coord = new Mappy.api.geo.Coordinates(p.x, p.y);
										} else {
											coord = new Mappy.api.geo.Coordinates(longitude, latitude);
										}
										var markerImage;
										if(poi === 2){ markerImage = _DATA.marker2;}
										else {markerImage = _DATA.marker1;}
										
										var marker = new Mappy.api.map.Marker(coord, new Mappy.api.ui.Icon({
											//cssClass : "cible-picto-ville-localisation" + poi,
											image: markerImage,
											iconAnchor : new Mappy.api.types.Point(2, 40),
											size : new Mappy.api.types.Size(22, 46)
										}), new Mappy.api.map.PopUpOptions({
											mappyDecodation : false,
											autoLayout : false,
											left : 15,
											bottom : 12
										}));
										
										
										if (label !== '"') {
											if (typeof geo.url !== "undefined" && geo.url !== "") {
												var popHTML = '<strong class="bleu">' + label + '</strong><br/>';
												if (typeof geo.adr1 !== "undefined" && geo.adr1 !== "") {
													popHTML += geo.adr1 + '<br/>';
												}
												if (typeof geo.adr2 !== "undefined" && geo.adr2 !== "") {
													popHTML += geo.adr2 + '<br/>';
												}
												if (typeof geo.tel !== "undefined" && geo.tel !== "") {
													popHTML += '<span class="bleu">Tél.</span> :' + geo.tel + '<br/>';
												}

												popHTML += '<a href="' + geo.url + '#nousSituer" class="bleu">info +</a>';
												marker.addListener("mouseover", function() {
													var i;
													for (i = 0; i < layerCible.markers.length; i++) {
														layerCible.markers[i].closePopUp();
													}
													marker.openPopUp(popHTML + '<div class="patte"></div><div class="popup-close"></div>');
													marker._popUp.div.find(".popup-close").click(

													function(event) {
														marker.closePopUp();
														event.stopPropagation();
													});
												});
											} else {
												marker.addToolTip(label);
											}
										}
										layerCible.addMarker(marker);
									};

									var multiAdresse = false;
									// var i;
									for (i = 0; i < _DATA.geoCoordonnees.length; i += saut) {
										addMarker(_DATA.geoCoordonnees[i]);
										if (typeof _DATA.geoCoordonnees[i].url !== "undefined" && _DATA.geoCoordonnees[i].url !== "") {
											multiAdresse = true;
										}
									}
									// zoom pour carte simple
									if (_DATA.zi.length === 0) {
										if (!multiAdresse) {
											// var coord;
											longitude = _DATA.geoCoordonnees[0].lon;
											latitude = _DATA.geoCoordonnees[0].lat;
											projection = _DATA.geoCoordonnees[0].proj;

											if (projection === "Lambert2Etendu") {
												// conversion Proj4js
												p = Proj4js.transform(new Proj4js.Proj("EPSG:27572"), new Proj4js.Proj("EPSG:4326"), new Proj4js.Point(longitude, latitude));
												coord = new Mappy.api.geo.Coordinates(p.x, p.y);
											} else {
												coord = new Mappy.api.geo.Coordinates(longitude, latitude);
											}
											map.setCenter(coord, (typeof _DATA.zoom !== "undefined" ? parseInt(_DATA.zoom, 10) : 10));
										} else {
											setZoom();
										}
									}
								} else if ((typeof _DATA.zi === "undefined" || _DATA.zi.length === 0) && (typeof _DATA.zoneChalandise === "undefined" || _DATA.zoneChalandise.length === 0)
										&& (typeof _DATA.zoneIntervention === "undefined" || _DATA.zoneIntervention.length === 0)) {
									plan.hide();
								}
							} else if ((typeof _DATA.zi === "undefined" || _DATA.zi.length === 0) && (typeof _DATA.zoneChalandise === "undefined" || _DATA.zoneChalandise.length === 0)
									&& (typeof _DATA.zoneIntervention === "undefined" || _DATA.zoneIntervention.length === 0)) {
								plan.hide();
							}

							// hack mappy 2.10 permettant d'éviter que les
							// tooltip des pois fond de plan s'affichent
							// au-dessus des tooltip des pois
							try {
								var _layerDescr = map.controller.mapdescr;
								var _openToolTip = null;
								if (_layerDescr) {
									if (_layerDescr._pois.length > 0) {
										$(".layer[name=markerLayer]>div").mouseenter(

										function() {
											var i;
											_openToolTip = _layerDescr._pois[0].openToolTip;
											noop = function() {
											};
											for (i = 0; i < _layerDescr._pois.length; i++) {
												_layerDescr._pois[i].openToolTip = noop;
											}
										});
										$(".layer[name=markerLayer]>div").mouseleave(

										function() {
											var i;
											if (_openToolTip) {
												for (i = 0; i < _layerDescr._pois.length; i++) {
													_layerDescr._pois[i].openToolTip = _openToolTip;
												}
											}
										});
									}
								}
							} catch (e) {
							}

							// ajoute les zones de chalandise
							if (_DATA.zoneChalandise.length > 0) {
								var zoneChalandiseAdded = 0;
								// var num = _DATA.zoneChalandise.length;
								num = _DATA.zoneChalandise.length;
								// var i;
								if (num > 20) {
									num = 20;
								}

								onSuccess = function(data) {
									var json = Mappy.api.utils.xml2json(data);
									var kmlReader = new Mappy.api.map.shape.kml.KmlReader();
									var formes = kmlReader.getShapes(json.kml);
									var j;
									for (j = 0; j < formes.length; j++) {
										formes[j].setStyle(shapeStyle);
										shapeLayer.addShape(formes[j]);
									}
									zoneChalandiseAdded++;
									if (zoneChalandiseAdded === num) {
										setZoom();
									}
								};

								onError = function() {
									zoneChalandiseAdded++;
									if (zoneChalandiseAdded === num) {
										setZoom();
									}
								};

								for (i = 0; i < num; i++) {
									$.ajax({
										url : _DATA.zoneChalandise[i],
										dataType : "xml",
										error : onError,
										success : onSuccess
									});
								}
							}

							// ajoute les nouvelles zones d'intervention
							if (_DATA.zi.length > 0) {
								var ziAdded = 0;
								// var num = _DATA.zi.length;
								num = _DATA.zi.length;
								// if(num>50)num=50; --- remove by Net-ng -
								// tracker #472
								var tooltip = $('<div class="default-tooltip" style="position:absolute;z-index:10000;top:-1000px;left:-1000px;"></div>');
								// var i;
								$('body').append(tooltip);
								var addForme = function(forme, nom) {
									forme.setStyle(shapeStyle);
									shapeLayer.addShape(forme);
									if (nom !== "") {
										forme.addListener("mouseover", function(e) {
											tooltip.html(nom).css("top", (e.pageY - 40) + "px").css("left", e.pageX + "px").show();
										});
										forme.addListener("mouseout", function() {
											tooltip.hide();
										});
									}
								};

								onSuccess = function(data) {
									var json = Mappy.api.utils.xml2json(data);
									var nom = String(json.kml.Document.name).replace(/ \([0-9]{5}\)/, "");
									var kmlReader = new Mappy.api.map.shape.kml.KmlReader();
									var formes = kmlReader.getShapes(json.kml);
									var i;
									for (i = 0; i < formes.length; i++) {
										addForme(formes[i], nom);
									}
									ziAdded++;
									if (ziAdded === _DATA.zi.length) {
										setZoom();
									}
								};

								onError = function() {
									ziAdded++;
									if (ziAdded === _DATA.zi.length) {
										setZoom();
									}
								};

								for (i = 0; i < num; i++) {
									var type = _DATA.zi[i].type;
									var value = _DATA.zi[i].value;
									// fichier kml
									if (type === "region" || type === "departement" || type === "ville" || type === "arrondissement" || type === "pays") {
										var file = _DATA.kmlRootPath + type + '/' + (type === 'ville' ? String(value).substr(0, 2) + '/' + value : value) + '.xml';
										$.ajax({
											url : file,
											dataType : "xml",
											error : onError,
											success : onSuccess
										});
										// cercle
									} else if (type === "cercle") {
										if (_DATA.geoCoordonnees.length > 0) {
											centre = _DATA.geoCoordonnees[0];
											longitude = centre.lon;
											latitude = centre.lat;
											projection = centre.proj;
											if (projection === "Lambert2Etendu") {
												// conversion Proj4js
												p = Proj4js.transform(new Proj4js.Proj("EPSG:27572"), new Proj4js.Proj("EPSG:4326"), new Proj4js.Point(longitude, latitude));
												coord = new Mappy.api.geo.Coordinates(p.x, p.y);
											} else {
												coord = new Mappy.api.geo.Coordinates(longitude, latitude);
											}
											addForme(new Mappy.api.map.shape.Circle(coord, value), "");
										}
										ziAdded++;
										if (ziAdded === _DATA.zi.length) {
											setZoom();
										}
										// zone libre
									} else if (type === "libre") {
										if (value.length > 2) {
											var fo = [];
											var j;
											for (j = 0; j < value.length; j++) {
												fo.push(new Mappy.api.geo.Coordinates(value[j][0], value[j][1]));
											}
											addForme(new Mappy.api.map.shape.Polygon(fo), "");
										}
										ziAdded++;
										if (ziAdded === _DATA.zi.length) {
											setZoom();
										}
									}
								}
							} else if (typeof _DATA.geoCoordonnees === "undefined") {
								if (typeof _DATA.zoom !== "undefined") {
									centre = _DATA.geoCoordonnees[0];
									longitude = centre.lon;
									latitude = centre.lat;
									projection = centre.proj;
									if (projection === "Lambert2Etendu") {
										// conversion Proj4js
										p = Proj4js.transform(new Proj4js.Proj("EPSG:27572"), new Proj4js.Proj("EPSG:4326"), new Proj4js.Point(longitude, latitude));
										coord = new Mappy.api.geo.Coordinates(p.x, p.y);
									} else {
										coord = new Mappy.api.geo.Coordinates(longitude, latitude);
									}
									map.setCenter(coord, parseInt(_DATA.zoom, 10));
								} else {
									setZoom();
								}
							}
						} else {
							// ajoute les zones d'intervention
							var zoneInterventionAdded = 0;
							if (_DATA.zoneIntervention.length > 0) {
								num = _DATA.zoneIntervention.length;
								if (num > 20) {
									num = 20;
								}
								// var i;

								onSuccess = function(data) {
									var json = Mappy.api.utils.xml2json(data);
									var kmlReader = new Mappy.api.map.shape.kml.KmlReader();
									var formes = kmlReader.getShapes(json.kml);
									var j;
									for (j = 0; j < formes.length; j++) {
										formes[j].setStyle(shapeStyle);
										shapeLayer.addShape(formes[j]);
									}
									zoneInterventionAdded++;
									if (zoneInterventionAdded === num) {
										setZoom();
									}
								};

								onError = function() {
									zoneInterventionAdded++;
									if (zoneInterventionAdded === num) {
										setZoom();
									}
								};

								for (i = 0; i < num; i++) {
									$.ajax({
										url : _DATA.zoneIntervention[i],
										dataType : "xml",
										error : onError,
										success : onSuccess
									});
								}
							}
						}

						// switch entre les vues et calcul d'itinéraire
						var vue = $("#vueMappy");
						var from = $("#itiDepart");
						var to = $("#itiArrive");
						if (vue.length === 1) {
							// var i = 0;
							i = 0;
							var link = vue.find("li a");
							link.each(function() {
								$(this).bind("click", {
									pos : i
								}, function(event) {
									var mode = [ "map"];
									if (!$(this).parent().hasClass("on")) {
										$(".bloc.moyensAcces h3").css("height", "auto"); // correction
																							// float
																							// bug
										if (event.data.pos < mode.length) {
											// switch entre itinéraire et carte
											$("#planMappy").show();
											$("#actionMappy").show();
											routeLayer.clean();
											layerFlag.clean();
											$("#itineraireMappy").removeClass("on");
											// option de la carte
											var vm = new Mappy.api.map.ViewMode(mode[event.data.pos]);
											  map.addTileLayer(vm);
											//map.setViewMode(vm);
											// itinéraire
										} else {
											$(".bloc.moyensAcces h3").css("height", "1%"); // correction
																							// float
																							// bug
											// switch entre itinéraire et carte
											$("#planMappy").hide();
											$("#itineraireMappy").addClass("on");
											$("#itineraireForm").removeClass("off");
											$("#itineraireResultat").addClass("off");

											// switch entre "y aller" et "en
											// partir"
											$("input[name='itineraire']").change(

											function() {
												$(this).each(

												function() {
													$(this).removeClass("on");
												});
												var id = $("input[name='itineraire']:checked").attr("id");
												// style du label
												$(".choix label").each(

												function() {
													$(this).removeClass("on");
												});
												$(".choix label[for='" + id + "']").addClass("on");
												// selon les cas
												if (id === 'YAller') {
													from = $("#itiDepart");
													to = $("#itiArrive");
												} else {
													to = $("#itiDepart");
													from = $("#itiArrive");
												}
												// inverse les textes
												var tmp = from.val();
												from.val(to.val());
												to.val(tmp);

												// change les styles
												from.addClass("on");
												from.removeAttr("disabled");
												to.removeClass("on");
												to.attr("disabled", "disabled");
											});
											$("input[name='itineraire']").trigger("change");

											// calcul de l'itinéraire
											$("#itineraireMappy .validation").click(function() {
												getDepart();
												return false;
											});
										}
									}
									vue.find("li").removeClass("on");
									$(this).parent().addClass("on");
									return false;
								});
								i++;
							});
						}
						// si le chargement mappy a raté, on masque
					} else {
						plan.hide();
					}
				}

				getDepart = function() {
					var id = $("input[name='itineraire']:checked").attr("id");
					if (id === 'YAller') {
						from = $("#itiDepart");
						to = $("#itiArrive");
					} else {
						to = $("#itiDepart");
						from = $("#itiArrive");
					}

					var erreur = $("#itineraireForm .error");
					var label = $.trim(from.val());
					if (label === "") {
						erreur.html(_TEXTES.itiErreur4);
						erreur.addClass("on");
					} else {
						var geocoder = new Mappy.api.geolocation.Geocoder();
						geocoder.setLanguage($("meta[content=en]").length === 1 ? 'eng' : 'fre');
						geocoder.geocode(from.val(), function(results) {
							$("#itineraireForm .error").removeClass("on");
							var resultats = [];
							var i;
							var resultat;
							for (i = 0; i < results.length; i++) {
								resultat = results[i];
								// 250 est le code ISO 3166-1 numeric de la
								// France
								if (resultat.Placemark.name !== "" && parseInt(resultat.Placemark.AddressDetails.Country.CountryNameCode.value, 10) === 250) {
									resultats.push(resultat);
								}
							}
							// aucun résultat
							if (resultats.length === 0) {
								erreur.html(_TEXTES.itiErreur1);
								erreur.addClass("on");
								// un résultat
							} else if (resultats.length === 1) {
								// DOM TOM
								if (isDomTom(resultats[0])) {
									erreur.html(_TEXTES.itiErreur2);
									erreur.addClass("on");
									// France
								} else {
									var coords1 = resultats[0].Placemark.Point.coordinates;
									var loc = _DATA.geoCoordonnees[0];
									var longitude = loc.lon;
									var latitude = loc.lat;
									var coords;
									if (loc.proj === "Lambert2Etendu") {
										// conversion proj4js
										p = Proj4js.transform(new Proj4js.Proj("EPSG:27572"), new Proj4js.Proj("EPSG:4326"), new Proj4js.Point(longitude, latitude));
										coords = new Mappy.api.geo.Coordinates(p.x, p.y);
									} else {
										coords = new Mappy.api.geo.Coordinates(longitude, latitude);
									}
									var pointDepart;
									var pointArrivee;
									if ($("input[name:'itineraire']:checked").attr("id") === "EnPartir") {
										pointDepart = coords;
										pointArrivee = new Mappy.api.geo.Coordinates(coords1[0], coords1[1]);
									} else {
										pointArrivee = coords;
										pointDepart = new Mappy.api.geo.Coordinates(coords1[0], coords1[1]);
									}
									// On lance la recherche d'itineraire
									getItineraire(pointDepart, pointArrivee);
								}
								// plusieurs résultats : ambiguité
							} else {
								var htmlListe = '<ul>';
								for (i = 0; i < resultats.length; i++) {
									resultat = resultats[i];
									htmlListe += '<li><a href="' + resultat.Placemark.Point.coordinates + '">' + resultat.Placemark.name + '</a></li>';
								}
								htmlListe += '</ul>';
								erreur.html(htmlListe);
								erreur.addClass("on");
								$(".error ul li a").each(

								function() {
									$(this).click(

									function() {
										from.val($(this).html());
										erreur.removeClass("on");
										erreur.empty();
										var c = String($(this).attr("href")).split(",");
										if (c.length === 2) {
											var loc = _DATA.geoCoordonnees[0];
											var longitude = loc.lon;
											var latitude = loc.lat;
											if (loc.proj === "Lambert2Etendu") {
												// conversion proj4js
												p = Proj4js.transform(new Proj4js.Proj("EPSG:27572"), new Proj4js.Proj("EPSG:4326"), new Proj4js.Point(longitude, latitude));
												coords = new Mappy.api.geo.Coordinates(p.x, p.y);
											} else {
												coords = new Mappy.api.geo.Coordinates(longitude, latitude);
											}
											if ($("input[name:'itineraire']:checked").attr("id") === "EnPartir") {
												pointDepart = coords;
												pointArrivee = new Mappy.api.geo.Coordinates(parseFloat(c[0]), parseFloat(c[1]));
											} else {
												pointArrivee = coords;
												pointDepart = new Mappy.api.geo.Coordinates(parseFloat(c[0]), parseFloat(c[1]));
											}
											// On lance la recherche
											// d'itineraire
											getItineraire(pointDepart, pointArrivee);
										}
										return false;
									});
								});
							}
						}, function() {
							erreur.html(_TEXTES.itiErreur3);
							erreur.addClass("on");
						});
					}
				};

				isDomTom = function(localiteMappy) {
					if (localiteMappy.Placemark !== null) {
						if (localiteMappy.Placemark.name !== null) {
							var tab = localiteMappy.Placemark.name.split(" ");
							if (tab !== null) {
								if (tab.length > 0) {
									code = tab[tab.length - 1];
									if ((code.indexOf("971") === 0) || (code.indexOf("972") === 0) || (code.indexOf("973") === 0) || (code.indexOf("974") === 0) || (code.indexOf("975") === 0)
											|| (code.indexOf("976") === 0)) {
										return true;
									}
								}
							}
						}
					}
					return false;
				};

				// fonction de recherche d'itinéraire
				getItineraire = function(pointDepart, pointArrivee) {
					// efface le tracé précédent
					routeLayer.clean();
					layerFlag.clean();
					// lance la recherche d'itinéraire
					var routeService = new Mappy.api.route.RouteService();
					routeService.loadRoute([ pointDepart, pointArrivee ], {
						Vehicle : 'midcar',
						caravane : '0',
						gas : 'petrol',
						bestcost : 'time',
						cost : 'time',
						language : $("meta[content=en]").length === 1 ? 'eng' : 'fre'
					}, function(roadbook) {
						// affiche les calques de réponse
						$("#itineraireResultat").removeClass("off");
						// imprimer l'itinéraire
						$("#itineraireMappy .imprimer").unbind("click");
						$("#itineraireMappy .imprimer").click(

						function() {
							var id = $("input[name='itineraire']:checked").attr("id");
							if (id === 'YAller') {
								popup(_DATA.printiti_link + "/YAller/" + from.val() + "/Adresse/" + to.val(), 1000, 590, "yes");
								return false;
							} else {
								popup(_DATA.printiti_link + "/EnPartir/" + from.val() + "/Adresse/" + to.val(), 1000, 590, "yes");
							}

						});
						// retour sur calcul d'itinéraire
						$("#itineraireMappy .validation2").click(function() {
							$("#itineraireForm").removeClass("off");
							$("#itineraireResultat").addClass("off");
							$("#planMappy").hide();
							return false;
						});
						$("#planMappy").show();
						$("#actionMappy").hide();
						$("#itineraireForm").addClass("off");
						var i;
						// affiche l'itinéraire sur la carte
						for (i = 0; i < roadbook.routes.length; i++) {
							//routeLayer.addShape(roadbook.routes[i]);
							$("#roadbook").html(roadbook.routes[i].toHtml());
						}
						
						routeLayer.setRoadbook(roadbook);
						
						var bounds = routeLayer.getBounds();
						map.setCenter(bounds.center, map.getBoundsZoomLevel(bounds));
						// option de la carte
					//	map.setViewMode(new Mappy.api.map.ViewMode("map"));
						map.addTileLayer(new Mappy.api.map.ViewMode("map"));
						// affiche le contenu html
					//	$("#roadbook").html(roadbook.toHtml());
						// permet d'afficher correctement la feuille de route si
						// la carto est en accordéon
						if ($(".bloc.mappy").parent().hasClass("content")) {
							$(".bloc.mappy").parent().css("height", "auto");
						}
						// affiche les drapeaux
						layerFlag.addMarker(new Mappy.api.map.Marker(pointDepart, new Mappy.api.ui.Icon({
							cssClass : 'drapeauDepart'
						})));
						layerFlag.addMarker(new Mappy.api.map.Marker(pointArrivee, new Mappy.api.ui.Icon({
							cssClass : 'drapeauArrivee'
						})));
					}, function(e) {
						alert(_TEXTES.itiErreur2);
					});
				};

				/* Page accueil/presentation template HTML5*/
				if ($("body.index").length === 1 && $("#planMappy").length === 1 && typeof Mappy !== "undefined") {		
					map.disableDblClickZoom();
					map.disableScrollWheelZoom();
					map.disableDraggable();
					$(".map").click(function(){ document.location.href = _DATA.map_link;});
				}
				
			}
			
			/*
			 * Determine map center
			 */
			if (typeof _DATA !== 'undefined' && _DATA.geoCoordonnees) {
				var longitude = _DATA.geoCoordonnees[0].lon;
				var latitude = _DATA.geoCoordonnees[0].lat;

				if ((!longitude) && (!latitude)) {

					/*
					 * No map defined Determine from address
					 */
					var default_address = _DATA.geoCoordonnees[0].default_address;
					var default_city = _DATA.geoCoordonnees[0].default_city;
					var default_country = _DATA.geoCoordonnees[0].default_country;

					var home = new Mappy.api.geolocation.AddressLocation(default_country, default_city, default_address, '');
					var geocoder = new Mappy.api.geolocation.Geocoder();
					geocodeOnSuccess = function(results) {
						if (results.length > 0) {
							_DATA.geoCoordonnees[0].lon = results[0].Placemark.Point.coordinates[0];
							_DATA.geoCoordonnees[0].lat = results[0].Placemark.Point.coordinates[1];
							init();
						}
					};

					geocoder.geocode(home, geocodeOnSuccess);
				} else {
					init();
				}
				
				
			}

});
