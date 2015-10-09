<h1>Sélectionnez le canal Wifi à utiliser</h1>
<p>
    Utile si vous rencontrez des problèmes de connexion. Changer cette valeur uniquement si la connexion semble instable ou perturbées.
    Si vous modifiez ce paramètre, le service Wifi redemrrera. Vos données ne seront pas perdue.
    Reconnectez - vous au Wifi si besoin après le redemarrage.
</p>


[IF [!action!]=switchchannel]
        [IF [!Module::Formation::setChannel([!channel!])!]]
        <div class="alert alert-success">Le cannal Wifi a bien été changé en [!channel!].</div>
        [ELSE]
        <div class="alert alert-danger">Opération impossible ...</div>
        [/IF]
[/IF]

[!CURRENT:=[!Module::Formation::getCurrentChannel()!]!]
[!WifiTab:=[!Module::Formation::getWifiChannels()!]!]
[IF [!WifiTab::[!CURRENT!]!]>0]
    <div class="alert alert-danger">
        Votre Canal Wifi ( [!CURRENT!] ) est surchargé! . Veuillez sélectionner un canal Wifi moins chargé.
    </div>
[ELSE]
<div class="alert alert-success">
    Votre canal Wifi ( [!CURRENT!] ) semble peu chargé. Si vous rencontrez quand même des problèmes de connexion, tentez de changer de canal pour vous éloigner des canaux surchargés.
</div>
[/IF]
<div class="btn-group" role="group" aria-label="...">
    [STORPROC 9|C]
    <a class="btn btn-default [IF [!CURRENT!]=[!Pos!]]btn-primary[/IF]" href="?action=switchchannel&channel=[!Pos!]">[!Pos!]</a>
    [/STORPROC]
</div>
<h1>Etat du réseau (par canaux wifi)</h1>
<p>Veuillez sélectionner un canal peu chargé.<br />
    En rouge: les autres réseaux <br>
    En vert: le réseau du hub.
</p>
<div class="well">
    <div id="state"></div>
    <script>
        function getWifiStats() {
            $.ajax({
                url: '/Systeme/WifiStats.htm',
                context: $( '#state' )
            }).done(function(data) {
                $( '#state').html(data);
            });
        }
        var i = setInterval(function () {
            getWifiStats();
        }, 10000)
        getWifiStats();
    </script>

</div>