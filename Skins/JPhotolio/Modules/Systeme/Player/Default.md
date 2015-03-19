  
  <script type="text/javascript">

	var mplang = {
		playlistmuted 		: "Music Playlist Muted",
		playlistunmuted 	: "Music Playlist UnMuted",
		playliststartplay 	: "Playlist started",
	};

	var musicplayer = new jPlayerPlaylist({
		jPlayer: "#jquery_jplayer",
		cssSelectorAncestor: "#jp_container"	
	}, [
			[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Son|Son]
				[IF [!Pos!]>1],[/IF]
				{"title":"[!Son::Titre!]","mp3":"[UTIL ADDSLASHES]/[!Son::Lien!][/UTIL]","oga":"[UTIL ADDSLASHES]/[!Son::Alternatif!][/UTIL]"}
			[/STORPROC]
	] , {
//				{"title":"Ten To Five - I Do","mp3":"https:\/\/dl.dropbox.com\/u\/31421656\/jphotolio\/ttfid.mp3","oga":"https:\/\/dl.dropbox.com\/u\/31421656\/jphotolio\/ttfid.ogg"},{"title":"Ten To Five - I Will Fly","mp3":"https:\/\/dl.dropbox.com\/u\/31421656\/jphotolio\/ttfiwillfly.mp3","oga":"https:\/\/dl.dropbox.com\/u\/31421656\/jphotolio\/ttfiwillfly.ogg"}
		swfPath: "/Skins/JPhotolio/js",
		supplied: "mp3, oga",
		wmode: "window",
		loop: true,
		volume: 0.5,
		ready : function() {
			mpnotifbox(mplang.playliststartplay);
		},
		playlistOptions: {
						autoPlay: true,
						enableRemoveControls: false,
			loopOnPrevious: true
		}
	});
  </script>
