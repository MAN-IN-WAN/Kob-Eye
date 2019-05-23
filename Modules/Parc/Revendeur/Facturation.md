//INIT
[!NbHost:=0!]
[!NbHostExpert:=0!]
[!NbHostHors:=0!]
[!TotalHT:=0!]
[!TotalHTBdd:=0!]
[!TotalHTAp:=0!]
//CONFIG
[!BddTarif:=3!]
[!VhostTarif:=0!]
[!BaseTarif:=6!]
[!ExpertTarif:=12!]
[!HorsTarif:=24!]
[!MaxBdd:=200!]

<script type="text/javascript">
    var ajax = {};
    ajax.x = function () {
        if (typeof XMLHttpRequest !== 'undefined') {
            return new XMLHttpRequest();
        }
        var versions = [
            "MSXML2.XmlHttp.6.0",
            "MSXML2.XmlHttp.5.0",
            "MSXML2.XmlHttp.4.0",
            "MSXML2.XmlHttp.3.0",
            "MSXML2.XmlHttp.2.0",
            "Microsoft.XmlHttp"
        ];

        var xhr;
        for (var i = 0; i < versions.length; i++) {
            try {
                xhr = new ActiveXObject(versions[i]);
                break;
            } catch (e) {
            }
        }
        return xhr;
    };

    ajax.send = function (url, callback, method, data, async) {
        if (async === undefined) {
            async = true;
        }
        var x = ajax.x();
        x.open(method, url, async);
        x.onreadystatechange = function () {
            if (x.readyState == 4) {
                callback(x.responseText)
            }
        };
        if (method == 'POST') {
            x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        }
        x.send(data)
    };

    ajax.get = function (url, data, callback, async) {
        var query = [];
        for (var key in data) {
            query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
        ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
    };

    ajax.post = function (url, data, callback, async) {
        var query = [];
        for (var key in data) {
            query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
        ajax.send(url, callback, 'POST', query.join('&'), async)
    };
</script>


<h1>Récap des tarifs</h1>
<ul>
    <li>Hébergement basic: [!BaseTarif!]€</li>
    <li>Hébergement expert: [!ExpertTarif!]€</li>
    <li>Hébergement hors forfait: [!HorsTarif!]€</li>
    <li>Base de donnée en dépassement (total des bdds supérieur à 200Mo) : [!BddTarif!]€</li>
    <li>Vhost supplémentaire : [!VhostTarif!]€</li>
</ul>

[STORPROC [!Query!]|R|0|1]
<ul>
    [STORPROC Parc/Revendeur/[!R::Id!]/Client|C|0|1000]
    <li><h2>Client [!C::Nom!]</h2><p>
        Nb Instances: [COUNT Parc/Client/[!C::Id!]/Instance|NbInst]<b>[!NbInst!]</b><br/>
        Nb Hosts: [COUNT Parc/Client/[!C::Id!]/Host|NbHostCli]<b>[!NbHostCli!]</b><br/>
        Nb Domaines: [COUNT Parc/Client/[!C::Id!]/Domain|NbDomain]<b>[!NbDomain!]</b>
    </p></li>
    <ul>
        [STORPROC Parc/Client/[!C::Id!]/Host|H|0|1000]
            [COUNT Parc/Host/[!H::Id!]/Instance|isInst]
            [IF [!isInst!]]
                [STORPROC Parc/Host/[!H::Id!]/Instance|Inst|0|1][/STORPROC]
            [/IF]
            [COUNT Parc/Host/[!H::Id!]/Apache|NbAp]
            [COUNT Parc/Host/[!H::Id!]/Bdd|NbBdd]

            [!Server:=[!H::getServer()!]!]
            //check hébergement mutualisé
            [IF [!Server::Id!]!=30&&[!Server::Id!]!=29&&[!Server::Id!]!=28&&[!Server::Id!]!=25]
                [!NbHost+=1!]
                <li><h3>[IF [!isInst!]] Instance [!Inst::Nom!] - [!Inst::Plugin!][ELSE]Hébergement [!H::Nom!][/IF] / Nb Apache [!NbAp!] </h3></li>
                [IF [!isInst!]&&[!Inst::Produit!]!=base]
                    [IF [!Inst::Produit!]=expert]
                        [!TotalHT+=[!ExpertTarif!]!]
                        [!NbHostExpert+=1!]
                        <p>Prix <b>[!ExpertTarif!]€</b> <b style="color:orange;">Forfait Expert</b></p>
                    [ELSE]
                        [!TotalHT+=[!HorsTarif!]!]
                        [!NbHostHors+=1!]
                        <p>Prix <b>[!HorsTarif!]€</b> <b style="color:red;">Hors forfait</b></p>
                    [/IF]
                [ELSE]
                    [IF [!Math::Round([!H::DiskSpace!])!] > [!5:*[!1024:*1024!]!]]
                        [IF [!Math::Round([!H::DiskSpace!])!] > [!20:*[!1024:*1024!]!]]
                            [!TotalHT+=[!HorsTarif!]!]
                            [!NbHostHors+=1!]
                            <p>Prix <b>[!HorsTarif!]€</b> <b style="color:red;">Hors forfait</b></p>
                        [ELSE]
                            [!TotalHT+=[!ExpertTarif!]!]
                            [!NbHostExpert+=1!]
                            <p>Prix <b>[!ExpertTarif!]€</b> <b style="color:orange;">Forfait Expert</b></p>
                        [/IF]
                    [ELSE]
                        [!TotalHT+=[!BaseTarif!]!]
                        <p>Prix <b>[!BaseTarif!]€</b> <b style="color:blue;">Forfait Base</b></p>
                    [/IF]
                [/IF]
                [!SurcoutAp:=[![!NbAp:-1!]:*[!VhostTarif!]!]!]
                <div>Surcout Vhost [!SurcoutAp!] €</div>
                [IF [!SurcoutAp!]>0][!TotalHTAp+=[!SurcoutAp!]!][/IF]
                [IF [!isInst!]]
                    [IF [!Inst::Status!]=1]<div style="color:purple;font-weight:bold">---- DEV ----</div>
                    [/IF]
                    <div>Espace disque <b>[!Math::Round([!Inst::DiskSpace:/1024!])!] Mo</b></div>
                [ELSE]
                    <div>Espace disque <b>[!Math::Round([!H::DiskSpace:/1024!])!] Mo</b></div>
                [/IF]
                <div>Plugin [!Inst::Plugin!]</div>
                <h4>Liste des bdds</h4>
                <ul>
                    [!TotalBdd:=0!]
                    [!SurcoutBdd:=0!]
                    [STORPROC Parc/Host/[!H::Id!]/Bdd|Bdd]
                        [!TotalBdd+=[!Bdd::Size!]!]
                        <li>[!Bdd::Nom!] - [!Math::Round([!Bdd::Size:/1024!])!] Mo</li>
                    [/STORPROC]
                </ul>
                [IF [!TotalBdd!]>[![!MaxBdd!]:*1024!]][!SurcoutBdd:=[!BddTarif:*[![!Math::Floor([!TotalBdd:/[![!MaxBdd!]:*1024!]!])!]:-1!]!]!][/IF]
                [IF [!SurcoutBdd!]>0]
                    [!TotalHTBdd+=[!SurcoutBdd!]!]
                    <div style="color:red;font-weight:bold">Surcout Bdd [!SurcoutBdd!] €</div>
                [/IF]
                [IF [!NbAp!]>1]
                    <h4>Liste des domaines</h4>
                    <ul>
                        [STORPROC Parc/Host/[!H::Id!]/Apache|Ap]
                            [!Domains:=[!Ap::getDomainsToCheck()!]!]
                            [!Domains:=[![!Domains!]:/ !]!]
                            [STORPROC [!Domains!]|Dom|0|100]
                                [IF [!Dom!]]
                                <li>[!Dom!] - <span id="[!Dom!]"> ...chargement...</span></li>
                                <script type="text/javascript">
                                    ajax.get('/Parc/Apache/getDomainIp.htm', {domain: '[!Dom!]'}, function(response) {
                                        switch (response){
                                            case '185.87.66.11':
                                                document.getElementById("[!Dom!]").innerHTML = '<b style="color:green;">'+response+'</b>';
                                                break;
                                            case '178.32.130.20':
                                            case '178.32.130.24':
                                            case '145.239.103.211':
                                                document.getElementById("[!Dom!]").innerHTML = '<b style="color:orange;">'+response+'</b>';
                                                break;
                                            default:
                                                document.getElementById("[!Dom!]").innerHTML = '<b style="color:red;">'+response+'</b>';
                                                break;
                                        }
                                    },true);
                                </script>
                                [/IF]
                            [/STORPROC]
                        [/STORPROC]
                    </ul>
                [/IF]
            [/IF]
        [/STORPROC]
    </ul>
    [/STORPROC]
</ul>
[/STORPROC]

<h1>TOTAL HT FORFAIT: [!TotalHT!]</h1>
<h1>TOTAL HT SURCOUT BDD: [!TotalHTBdd!]</h1>
<h1>TOTAL HT: [!TotalHT:+[!TotalHTBdd!]!]</h1>
<h1>Nombre d'hébergement: [!NbHost!] dont [!NbHostExpert!] experts et <b style="color:red">[!NbHostHors!] hors forfait</b></h1>