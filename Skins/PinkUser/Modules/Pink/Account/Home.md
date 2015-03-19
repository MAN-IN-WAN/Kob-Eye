<div id="UserHome" class="row">
	<div class="col-md-3">
		<a class="Bloc ModifierMonCompte" href="/[!Systeme::getMenu(Pink/Account)!]/Update" title="Modifier mes données">Modifier<br /> mes données</a>
	</div>
	<div class="col-md-3">
		<a class="Bloc HistoriqueCommandes" href="/[!Systeme::getMenu(Pink/Account)!]/Calls" title="Mon historique d'appel">Mon historique<br /> d'appels</a>
	</div>
</div>
<div id="UserHome2" class="row">
	<div class="col-md-2"></div><div class="col-md-4"><a class="Bloc TelechargmentMonCompte" href="/[!Systeme::getMenu(Pink/Account)!]/Telechargement" title="Mon espace téléchargement">Mes téléchargments</a></div>
	<div class="col-md-4"><a class="Bloc ServicesMonCompte" href="/[!Systeme::getMenu(Pink/Account)!]/Abonnement" title="Mon espace abonnement">Mes abonnements</a></div>
	<div class="col-md-2"></div>
</div>

[HEADER JS]Skins/[!Systeme::Skin!]/Js/jPlayer/jquery.jplayer.min.js[/HEADER]
[HEADER CSS]Skins/[!Systeme::Skin!]/Js/jPlayer/black-yellow/jplayer.css[/HEADER]

  <script type="text/javascript">
    $(document).ready(function(){
      $("#jquery_jplayer_1").jPlayer({
        ready: function () {
          $(this).jPlayer("setMedia", {
            title: "Bubble",
            m4a: "http://www.jplayer.org/audio/m4a/Miaow-07-Bubble.m4a",
            oga: "http://www.jplayer.org/audio/ogg/Miaow-07-Bubble.ogg"
          });
        },
        swfPath: "Skins/[!Systeme::Skin!]/Js/jPlayer",
        supplied: "m4a, oga"
      });
    });
  </script>


  <div id="jquery_jplayer_1" class="jp-jplayer"></div>
  <div id="jp_container_1" class="jp-audio" style="width:100px">
    <div class="jp-type-single">
      <div class="jp-gui jp-interface">
        <ul class="jp-controls">
          <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
          <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
          <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
          <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
          <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
          <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
        </ul>
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
          </div>
        </div>
        <div class="jp-volume-bar">
          <div class="jp-volume-bar-value"></div>
        </div>
      </div>
      <div class="jp-no-solution">
        <span>Update Required</span>
        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
      </div>
    </div>
  </div>
