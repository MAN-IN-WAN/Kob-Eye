
var timer;
$(document).ready(function () {
	$(".Speaker").on("click", greetingStart);
	$(".PlayerStop").on("click", greetingStop);
	$(".CallMe").on("click", callMe);
//	$timer = setInterval(checkExpert,5000);
});
var currPlayer = null;
function greetingStart() {
	if(currPlayer) {
		var c = currPlayer.find(".Audio");
		c.mb_miniPlayer_stop();
		currPlayer.css("display", "none");
		currPlayer = null;
	}
	var d = $(this).parent().find(".Player");
	d.css("display", "inline");
	var p = d.find(".Audio");
	p.mb_miniPlayer({width:110,inLine:false,id3:false,animated:false,autoplay:true});
	p.mb_miniPlayer_play();
	currPlayer = d;
}
function greetingStop() {
	var d = $(this).parent();
	var p = d.find(".Audio");
	p.mb_miniPlayer_stop();
	d.css("display", "none");
	currPlayer = null;
}
function checkExpert() {
	var ph = "";
	var st = "";
	var bt = "";
	$.ajax({url:'Home/tmp/status.json',dataType:'json'}).done(function(data){
		var sts = data.data;
		var n = sts.length;
		for(var i = 0; i < n; i++) {
			var s = sts[i];
			var p = $('#phone-'+s.id);
			if(p) {
				bt = "Message moi";
				if(s.available) {
					if(s.online) {
						ph = "PhoneOn";
						st = "En ligne";
					}
					else {
						ph = "PhoneIdle";
						st = "";
						bt = "Appelle moi";
					}
				}
				else {
					ph = "PhoneOff";
					st = "Indisponible";
				}
				if(p.attr("class") != ph) {
					var t = $('#state-'+s.id);
					var b = $('#button-'+s.id);
					p.attr("class", ph);
					t.html(st);
					b.html(bt);
				}
			}
		}
	});
}


function callMe() {
	$.ajax({url:'Pink/Expert/CallMe.htm',dataType:'html',type:'post',data:{expert:this.id}}).done(function(msg){
		$('#lemodal').find('.modal-body').html(msg);
		$('#lemodal').modal('show');
	});
}

