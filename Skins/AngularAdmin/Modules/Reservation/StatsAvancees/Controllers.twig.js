app.controller('{{ Url }}Ctrl', function ($anchorScroll, $interval, $location, $scope, $rootScope, $http) {

        var colors = {
            _primary: '#008FFB',
            _secondary: '#3F51B5',
            _default: '	#C4BBAF',
            _success: '#00E396',
            _danger: '#FF4560',
            _warning: '#FEB019',
            _io: '#775DD0'
        };

        Object.size = function (obj) {
            var size = 0, key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) size++;
            }
            return size;
        };

        $scope.nbJoursCurrentMonth = new Array();
        $scope.nbPersCurrentMonth = new Array();
        $scope.nbResCurrentMonth = new Array();

        $scope.nbJoursOldMonth = new Array();
        $scope.nbPersOldMonth = new Array();
        $scope.nbResOldMonth = new Array();

        $scope.nbMoisCurrentYear = new Array();
        $scope.nbPersCurrentYear = new Array();
        $scope.nbResCurrentYear = new Array();

        $scope.nbMoisOldYear = new Array();
        $scope.nbPersOldYear = new Array();
        $scope.nbResOldYear = new Array();

        $scope.nomResCurrentMonthGenre = new Array();
        $scope.nbPlacesCurrentMonthGenre = new Array();

        $scope.nomResOldMonthGenre = new Array();
        $scope.nbPlacesOldMonthGenre = new Array();

        $scope.nomResCurrentYearGenre = new Array();
        $scope.nbPlacesCurrentYearGenre = new Array();

        $scope.nomResOldYearGenre = new Array();
        $scope.nbPlacesOldYearGenre = new Array();

        $scope.nomProgCurrentMonthGenre = new Array();
        $scope.nbPlacesProgCurrentMonthGenre = new Array();

        $scope.nomProgOldMonthGenre = new Array();
        $scope.nbPlacesProgOldMonthGenre = new Array();

        $scope.nomProgCurrentYearGenre = new Array();
        $scope.nbPlacesProgCurrentYearGenre = new Array();

        $scope.nomProgOldYearGenre = new Array();
        $scope.nbPlacesProgOldYearGenre = new Array();

        $scope.nomSpecCurrentYearGenre = new Array();
        $scope.nbSpecCurrentYearGenre = new Array();

        $scope.nomSpecOldYearGenre = new Array();
        $scope.nbSpecOldYearGenre = new Array();

        $scope.nbPersOuiCurrentYear = new Array();

        $scope.nbPersOuiOldYear = new Array();

        $scope.nbPersRsaCurrentYear = new Array();

        $scope.nbPersRsaOldYear = new Array();

        $scope.nbPersHCurrentYear = new Array();

        $scope.nbPersHOldYear = new Array();

        $scope.NomVillCurrentMonth = new Array();
        $scope.nbResVillCurrentMonth = new Array();

        $scope.NomVillCurrentYear = new Array();
        $scope.nbResVillCurrentYear = new Array();

        $scope.NomVillOldYear = new Array();
        $scope.nbResVillOldYear = new Array();

        var nb07 = 0;
        var nb13 = 0;
        var nb26 = 0;
        var nb40 = 0;
        var nb60 = 0;
        var nb70 = 0;


        $scope.init = function () {
            $http.post('/Reservation/StatsAvancees/getData.json', {})
                .success(function (data) {
                    console.log(data+'gfdxgxfgfgf');
                    for (var i = 0; i < data[0].length; i++) {
                        $scope.nbJoursCurrentMonth.push(data[0][i].Legende);
                        $scope.nbPersCurrentMonth.push(data[0][i].NbPlaces);
                        $scope.nbResCurrentMonth.push(data[0][i].NbResa);
                    }
                    for (var i = 0; i < data[1].length; i++) {
                        $scope.nbJoursOldMonth.push(data[1][i].Legende);
                        $scope.nbPersOldMonth.push(data[1][i].NbPlaces);
                        $scope.nbResOldMonth.push(data[1][i].NbResa);
                    }
                    for (var i = 0; i < data[2].length; i++) {
                        $scope.nbMoisCurrentYear.push(data[2][i].Legende);
                        $scope.nbPersCurrentYear.push(data[2][i].NbPlaces);
                        $scope.nbResCurrentYear.push(data[2][i].NbResa);
                    }
                    for (var i = 0; i < data[3].length; i++) {
                        $scope.nbMoisOldYear.push(data[3][i].Legende);
                        $scope.nbPersOldYear.push(data[3][i].NbPlaces);
                        $scope.nbResOldYear.push(data[3][i].NbResa);
                    }
                    for (var i = 0; i < data[4].length; i++) {
                        $scope.nomResCurrentMonthGenre.push(data[4][i].Nom);
                        $scope.nbPlacesCurrentMonthGenre.push(parseInt(data[4][i].NbPlaces));
                    }
                    for (var i = 0; i < data[5].length; i++) {
                        $scope.nomResOldMonthGenre.push(data[5][i].Nom);
                        $scope.nbPlacesOldMonthGenre.push(parseInt(data[5][i].NbPlaces));
                    }
                    for (var i = 0; i < data[6].length; i++) {
                        $scope.nomResCurrentYearGenre.push(data[6][i].Nom);
                        $scope.nbPlacesCurrentYearGenre.push(parseInt(data[6][i].NbPlaces));
                    }
                    for (var i = 0; i < data[7].length; i++) {
                        $scope.nomResOldYearGenre.push(data[7][i].Nom);
                        $scope.nbPlacesOldYearGenre.push(parseInt(data[7][i].NbPlaces));
                    }
                    for (var i = 0; i < data[8].length; i++) {
                        $scope.nomProgCurrentMonthGenre.push(data[8][i].Nom);
                        $scope.nbPlacesProgCurrentMonthGenre.push(parseInt(data[8][i].NbProgrammees));
                    }
                    for (var i = 0; i < data[9].length; i++) {
                        $scope.nomProgOldMonthGenre.push(data[9][i].Nom);
                        $scope.nbPlacesProgOldMonthGenre.push(parseInt(data[9][i].NbProgrammees));
                    }
                    for (var i = 0; i < data[10].length; i++) {
                        $scope.nomProgCurrentYearGenre.push(data[10][i].Nom);
                        $scope.nbPlacesProgCurrentYearGenre.push(parseInt(data[10][i].NbProgrammees));
                    }
                    for (var i = 0; i < data[11].length; i++) {
                        $scope.nomProgOldYearGenre.push(data[11][i].Nom);
                        $scope.nbPlacesProgOldYearGenre.push(parseInt(data[11][i].NbProgrammees));
                    }
                    for (var i = 0; i < data[12].length; i++) {
                        $scope.nomSpecCurrentYearGenre.push(data[12][i].Nom);
                        $scope.nbSpecCurrentYearGenre.push(parseInt(data[12][i].NbSpec));
                    }
                    for (var i = 0; i < data[13].length; i++) {
                        $scope.nomSpecOldYearGenre.push(data[13][i].Nom);
                        $scope.nbSpecOldYearGenre.push(parseInt(data[13][i].NbSpec));
                    }
                    $scope.nbPersOuiCurrentYear.push(data[14].NbPersonneAccom);
                    $scope.nbPersOuiCurrentYear.push(data[14].NbPersonneNonAccom);

                    $scope.nbPersOuiOldYear.push(data[15].NbPersonneAccom);
                    $scope.nbPersOuiOldYear.push(data[15].NbPersonneNonAccom);

                    $scope.nbPersRsaCurrentYear.push(data[16].NbPersonneRsa);
                    $scope.nbPersRsaCurrentYear.push(data[16].NbPersonneNonRsa);

                    $scope.nbPersRsaOldYear.push(data[17].NbPersonneRsa);
                    $scope.nbPersRsaOldYear.push(data[17].NbPersonneNonRsa);

                    $scope.nbPersHCurrentYear.push(data[18].NbPersonneH);
                    $scope.nbPersHCurrentYear.push(data[18].NbPersonneF);

                    $scope.nbPersHOldYear.push(data[19].NbPersonneH);
                    $scope.nbPersHOldYear.push(data[19].NbPersonneF);

                    for (var i = 0; i < data[20].length; i++) {
                        $scope.NomVillCurrentMonth.push(data[20][i].Ville);
                        $scope.nbResVillCurrentMonth.push(parseInt(data[20][i].NbResa));
                    }
                    for (var i = 0; i < data[21].length; i++) {
                        $scope.NomVillCurrentYear.push(data[21][i].Ville);
                        $scope.nbResVillCurrentYear.push(parseInt(data[21][i].NbResa));
                    }
                    for (var i = 0; i < data[22].length; i++) {
                        $scope.NomVillOldYear.push(data[22][i].Ville);
                        $scope.nbResVillOldYear.push(parseInt(data[22][i].NbResa));
                    }
                    for (var i = 0; i < data[23].length; i++) {
                        if (data[23][i].Age.length < 3) {
                            if (parseInt(data[23][i].Age) <= 7) {
                                nb07 += parseInt(data[23][i].NbPersonne);
                                data[23][i].Age = 'ok';
                            }
                            if (parseInt(data[23][i].Age) <= 13) {
                                nb13 += parseInt(data[23][i].NbPersonne);
                                data[23][i].Age = 'ok';
                            }
                            if (parseInt(data[23][i].Age) <= 26) {
                                nb26 += parseInt(data[23][i].NbPersonne);
                                data[23][i].Age = 'ok';
                            }
                            if (parseInt(data[23][i].Age) <= 40) {
                                nb40 += parseInt(data[23][i].NbPersonne);
                                data[23][i].Age = 'ok';
                            }
                            if (parseInt(data[23][i].Age) <= 60) {
                                nb60 += parseInt(data[23][i].NbPersonne);
                                data[23][i].Age = 'ok';
                            }
                            if (parseInt(data[23][i].Age) > 60) {
                                nb70 += parseInt(data[23][i].NbPersonne);
                                data[23][i].Age = 'ok';
                            }
                        } else if (data[23][i].Age == '0-7') {
                            data[23][i].NbPersonne = parseInt(data[23][i].NbPersonne)
                            data[23][i].NbPersonne += nb07;
                        } else if (data[23][i].Age == '8-13') {
                            data[23][i].NbPersonne = parseInt(data[23][i].NbPersonne)
                            data[23][i].NbPersonne += nb13;
                        } else if (data[23][i].Age == '14-26') {
                            data[23][i].NbPersonne = parseInt(data[23][i].NbPersonne)
                            data[23][i].NbPersonne += nb26;
                        } else if (data[23][i].Age == '27-40') {
                            data[23][i].NbPersonne = parseInt(data[23][i].NbPersonne)
                            data[23][i].NbPersonne += nb40;
                        } else if (data[23][i].Age == '41-60') {
                            data[23][i].NbPersonne = parseInt(data[23][i].NbPersonne)
                            data[23][i].NbPersonne += nb60;
                        } else if (data[23][i].Age == '60+') {
                            data[23][i].NbPersonne = parseInt(data[23][i].NbPersonne)
                            data[23][i].NbPersonne += nb70;
                        }
                        console.log(data[23][i]);

                    }
                    refreshSizes();
                })
                .error(function (data) {
                    console.error(data);
                });
        }
        $scope.init();

        var refreshSizes = function () {

            var optionsNbResPersCurrentMonth = {
                series: [{
                    name: 'Nombre de places',
                    type: 'column',
                    data: $scope.nbPersCurrentMonth
                }, {
                    name: 'Nombre de reservations',
                    type: 'column',
                    data: $scope.nbResCurrentMonth
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                title: {
                    text: 'Mois courant'
                },
                xaxis: {
                    categories: $scope.nbJoursCurrentMonth
                }
            };

            var optionsNbResPersOldMonth = {
                series: [{
                    name: 'Nombre de places',
                    type: 'column',
                    data: $scope.nbPersOldMonth
                }, {
                    name: 'Nombre de reservations',
                    type: 'column',
                    data: $scope.nbResOldMonth
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                title: {
                    text: 'Mois précedent'
                },
                xaxis: {
                    categories: $scope.nbJoursOldMonth
                }
            };

            var optionsNbResPersCurrentYear = {
                series: [{
                    name: 'Nombre de places',
                    type: 'column',
                    data: $scope.nbPersCurrentYear
                }, {
                    name: 'Nombre de reservations',
                    type: 'column',
                    data: $scope.nbResCurrentYear
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                title: {
                    text: 'Année courante'
                },
                xaxis: {
                    categories: $scope.nbMoisCurrentYear
                }
            };

            var optionsNbResPersOldYear = {
                series: [{
                    name: 'Nombre de places',
                    type: 'column',
                    data: $scope.nbPersOldYear
                }, {
                    name: 'Nombre de reservations',
                    type: 'column',
                    data: $scope.nbResOldYear
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                title: {
                    text: 'Année passée'
                },
                xaxis: {
                    categories: $scope.nbMoisOldYear
                }
            };

            var optionsNbPlaceCurrentMonthGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesCurrentMonthGenre,
                labels: $scope.nomResCurrentMonthGenre
            }

            var optionsNbPlaceOldMonthGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesOldMonthGenre,
                labels: $scope.nomResOldMonthGenre
            }


            var optionsNbPlaceCurrentYearGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesCurrentYearGenre,
                labels: $scope.nomResCurrentYearGenre
            }

            var optionsNbPlaceOldYearGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesOldYearGenre,
                labels: $scope.nomResOldYearGenre
            }

            var optionsNbPlaceProgCurrentMonthGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesProgCurrentMonthGenre,
                labels: $scope.nomProgCurrentMonthGenre
            }

            var optionsNbPlaceProgOldMonthGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesProgOldMonthGenre,
                labels: $scope.nomProgOldMonthGenre
            }


            var optionsNbPlaceProgCurrentYearGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesProgCurrentYearGenre,
                labels: $scope.nomProgCurrentYearGenre
            }

            var optionsNbPlaceProgOldYearGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesProgOldYearGenre,
                labels: $scope.nomProgOldYearGenre
            }

            var optionsNbSpecCurrentYearGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesProgCurrentYearGenre,
                labels: $scope.nomProgCurrentYearGenre
            }

            var optionsNbSpecOldYearGenre = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPlacesProgOldYearGenre,
                labels: $scope.nomProgOldYearGenre
            }

            var optionsNbPersAccomCurrentYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPersOuiCurrentYear,
                labels: ['oui', 'non']
            }

            var optionsNbPersAccomOldYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPersOuiOldYear,
                labels: ['non', 'oui']
            }

            var optionsNbPersRsaCurrentYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPersRsaCurrentYear,
                labels: ['non', 'oui']
            }

            var optionsNbPersRsaOldYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPersRsaOldYear,
                labels: ['non', 'oui']
            }

            var optionsNbPersHCurrentYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPersHCurrentYear,
                labels: ['Homme', 'Femme']
            }

            var optionsNbPersHOldYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbPersHOldYear,
                labels: ['Homme', 'Femme']
            }

            var optionsNbResVillCurrentMonth = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbResVillCurrentMonth,
                labels: $scope.NomVillCurrentMonth
            }

            var optionsNbResVillCurrentYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbResVillCurrentYear,
                labels: $scope.NomVillCurrentYear
            }

            var optionsNbResVillOldYear = {
                chart: {
                    type: 'pie'
                },
                series: $scope.nbResVillOldYear,
                labels: $scope.NomVillOldYear
            }

            $scope.histoCharCurr = new ApexCharts(document.querySelector("#HistNbPersCurrentMonth"), optionsNbResPersCurrentMonth);
            $scope.histoCharOld = new ApexCharts(document.querySelector("#HistNbPersOldMonth"), optionsNbResPersOldMonth);
            $scope.histoCharCurrYear = new ApexCharts(document.querySelector("#HistNbPersCurrentYear"), optionsNbResPersCurrentYear);
            $scope.histoCharOldYear = new ApexCharts(document.querySelector("#HistNbPersOldYear"), optionsNbResPersOldYear);

            $scope.PieCharGenreCurrentMonth = new ApexCharts(document.querySelector("#PieNbPersCurrentMonth"), optionsNbPlaceCurrentMonthGenre);
            $scope.PieCharGenreOldMonth = new ApexCharts(document.querySelector("#PieNbPersOldMonth"), optionsNbPlaceOldMonthGenre);

            $scope.PieCharGenreCurrentYear = new ApexCharts(document.querySelector("#PieNbPersCurrentYear"), optionsNbPlaceCurrentYearGenre);
            $scope.PieCharGenreOldYear = new ApexCharts(document.querySelector("#PieNbPersOldYear"), optionsNbPlaceOldYearGenre);

            $scope.PieCharProgGenreCurrentMonth = new ApexCharts(document.querySelector("#PieNbProgCurrentMonth"), optionsNbPlaceProgCurrentMonthGenre);
            $scope.PieCharProgGenreOldMonth = new ApexCharts(document.querySelector("#PieNbProgOldMonth"), optionsNbPlaceProgOldMonthGenre);

            $scope.PieCharProgGenreCurrentYear = new ApexCharts(document.querySelector("#PieNbProgCurrentYear"), optionsNbPlaceProgCurrentYearGenre);
            $scope.PieCharProgGenreOldYear = new ApexCharts(document.querySelector("#PieNbProgOldYear"), optionsNbPlaceProgOldYearGenre);

            $scope.PieCharNbSpecGenreCurrentYear = new ApexCharts(document.querySelector("#PieNbSpecCurrentYear"), optionsNbSpecCurrentYearGenre);
            $scope.PieCharNbSpecGenreOldYear = new ApexCharts(document.querySelector("#PieNbSpecOldYear"), optionsNbSpecOldYearGenre);

            $scope.PieCharNbPersAccomCurrentYear = new ApexCharts(document.querySelector("#PieAccomCurrentYear"), optionsNbPersAccomCurrentYear);
            $scope.PieCharNbPersAccomOldYear = new ApexCharts(document.querySelector("#PieAccomOldYear"), optionsNbPersAccomOldYear);

            $scope.PieCharNbPersRsaCurrentYear = new ApexCharts(document.querySelector("#PieRsaCurrentYear"), optionsNbPersRsaCurrentYear);
            $scope.PieCharNbPersRsaOldYear = new ApexCharts(document.querySelector("#PieRsaOldYear"), optionsNbPersRsaOldYear);

            $scope.PieCharNbPersHCurrentYear = new ApexCharts(document.querySelector("#PieHCurrentYear"), optionsNbPersHCurrentYear);
            $scope.PieCharNbPersHOldYear = new ApexCharts(document.querySelector("#PieHOldYear"), optionsNbPersHOldYear);

            $scope.PieCharNbResVille = new ApexCharts(document.querySelector("#PieResVilCurrentMonth"), optionsNbResVillCurrentMonth);

            $scope.PieCharNbResVilleCurrentYear = new ApexCharts(document.querySelector("#PieResVilCurrentYear"), optionsNbResVillCurrentYear);
            $scope.PieCharNbResVilleOldYear = new ApexCharts(document.querySelector("#PieResVilOldYear"), optionsNbResVillOldYear);


            $scope.histoCharCurr.render();
            $scope.histoCharOld.render();
            $scope.histoCharCurrYear.render();
            $scope.histoCharOldYear.render();

            $scope.PieCharGenreCurrentMonth.render();
            $scope.PieCharGenreOldMonth.render();

            $scope.PieCharGenreCurrentYear.render();
            $scope.PieCharGenreOldYear.render();

            $scope.PieCharProgGenreCurrentMonth.render();
            $scope.PieCharProgGenreOldMonth.render();

            $scope.PieCharProgGenreCurrentYear.render();
            $scope.PieCharProgGenreOldYear.render();

            $scope.PieCharNbSpecGenreCurrentYear.render();
            $scope.PieCharNbSpecGenreOldYear.render();

            $scope.PieCharNbPersAccomCurrentYear.render();
            $scope.PieCharNbPersAccomOldYear.render();

            $scope.PieCharNbPersRsaCurrentYear.render();
            $scope.PieCharNbPersRsaOldYear.render();

            $scope.PieCharNbPersHCurrentYear.render();
            $scope.PieCharNbPersHOldYear.render();

            $scope.PieCharNbResVille.render();
            $scope.PieCharNbResVilleCurrentYear.render();
            $scope.PieCharNbResVilleOldYear.render();

            return true;
        }


    }
);