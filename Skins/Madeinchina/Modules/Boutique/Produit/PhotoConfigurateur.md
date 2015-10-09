[STORPROC [!Query!]|Prod|0|1][/STORPROC]

<div class="PhotoProduit" id="photoconfigurateur" style="  background-color: #F9F9F9;">
//	<a href="[!Domaine!]/[!Prod::Image!]"  class="zoombox" ><img src="/[!Prod::Image!]" alt="[!Utils::noHtml([!Prod::Description!])!]" class="img-thumbnail image-responsive" /></a>
	<section class="receptacle">
		<div id="fond1" class="face"><canvas id="carte1"></canvas></div>
		<div id="fond2" class="face"><canvas id="carte2"></canvas></div>
		<div id="popup"><canvas id="popup1"></canvas></div>
		<canvas id="temp" style="display:none; visibility:hidden;">
	</section>
</div>
<div>
	<canvas id="carte3" width="400" height="200"></canvas>
//	<canvas id="inter3" width="200" height="100"></canvas>
//	<canvas id="popup2" width="100" height="100"></canvas>
</div>

[HEADER]
<style media="screen">
.receptacle {
  width: 200px;
  height: 200px;
  margin: 0px 100px 200px;
  position: relative;
}
.receptacle #fond1 {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0px;
  left: 0px;
  transform: perspective( 600px ) rotateX( 63deg );
  transform-origin: bottom center;
  -webkit-transform: perspective( 600px ) rotateX( 63deg );
  -webkit-transform-origin: bottom center;
  -moz-transform: perspective( 600px ) rotateX( 63deg );
  -moz-transform-origin: bottom center;
  -ms-transform: perspective( 600px ) rotateX( 63deg );
  -ms-transform-origin: bottom center;
  -o-transform: perspective( 600px ) rotateX( 63deg );
  -o-transform-origin: bottom center;
}
.receptacle #fond2 {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 100%;
  left: 0px;
  transform: perspective( 600px ) rotateX( 70deg );
  transform-origin: top center;
  -webkit-transform: perspective( 600px ) rotateX( 70deg );
  -webkit-transform-origin: top center;
  -moz-transform: perspective( 600px ) rotateX( 70deg );
  -moz-transform-origin: top center;
  -ms-transform: perspective( 600px ) rotateX( 70deg );
  -ms-transform-origin: top center;
  -o-transform: perspective( 600px ) rotateX( 70deg );
  -o-transform-origin: top center;
}
.receptacle #popup {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0px;
  left: 0px;
}
</style>


<script type="text/javascript">
	var carte
	var popup;
	var inter;
	var encar;
	var carteColor = 0xcccccc;
	var popupColor = 0x000000;
	var interColor = 0x000000;
	var encarColor = 0xffffff;
	var unit = 200;
	var unit2 = 200;
	var initPopupImg='';
	var initCarteImg='';
	var initInterImg='';
	var initEncarImg='';
	var bright = 4;

	$(document).ready(function () {
		var rec = $(".receptacle");
		$(rec).css('width',unit+'px');
		$(rec).css('height',unit+'px');
		$(".receptacle canvas").each(function(index,item){
			$(item).attr('width',$(item).parent().width());
			$(item).attr('height',$(item).parent().height());
		});
		var tcv = $("#temp")[0];
		$(tcv).attr('width',400);
		$(tcv).attr('height',200);

		var light = new Photon.Light(0,0,100);
		$crane = $('.receptacle');
		crane = new Photon.FaceGroup($('.receptacle')[0], $('.receptacle .face'), .12, .1, false);
		crane.render(light, true);
		
		if(initPopupImg) choixElement('1', initPopupImg)
		if(initCarteImg) choixElement('3', initCarteImg)
		if(initInterImg) choixElement('5', initInterImg)
		if(initEncarImg) choixElement('7', initEncarImg)
 	});


	function choixElement(type, image) {
		switch(type) {
			case '1':
				popup = new Image();
				popup.src = "/"+ image;
				popup.onload = function() {redraw();}
				break;
			case '3':
				carte = new Image();
				carte.src = "/"+ image;
				carte.onload = function() {redraw();}
				break;
			case '5':
				inter = new Image();
				inter.src = "/"+ image;
				inter.onload = function() {redraw();}
				break;
			case '7':
				encar = new Image();
				encar.src =  "/"+  image;
				encar.onload = function() {redraw();}
				break;
		}
	}
	
	function couleurElement(type, color) {

		color = parseInt('0x'+color.substring(1));
		switch(type) {
			case '4':
				carteColor = color;
				break;
			case '2':
				popupColor = color;
				break;
			case '6':
				interColor = color;
				break;
			case '8':
				encarColor = color;
				break;
		}
		redraw();
	}
	
	
	function redraw() {
		$("#popup1")[0].getContext('2d').clearRect(0,0,unit,unit);
		$("#carte1")[0].getContext('2d').clearRect(0,0,unit,unit);
		$("#carte2")[0].getContext('2d').clearRect(0,0,unit,unit);
		$("#carte3")[0].getContext('2d').clearRect(0,0,unit2*2,unit2);
//		$("#popup2")[0].getContext('2d').clearRect(0,0,unit,unit);
//		$("#inter3")[0].getContext('2d').clearRect(0,0,unit2*2,unit2);
		var can;
		if(popup) {
			setColor("#popup1",popup,popupColor,unit,unit,0,0,1);
//			setColor("#popup2",popup,popupColor,unit2,unit2,0,0,0);
		}
		if(carte) {
			setColor("#carte1",carte,carteColor,unit,unit,1,1,0);
			setColor("#carte2",carte,carteColor,unit,unit,2,1,0);
			if(inter) setColor("#carte3",inter,interColor,unit2*2,unit2,0,2,0);
			setColor("#carte3",carte,carteColor,unit2*2,unit2,0,0,0);
		}
		if(inter) {
			setColor("#carte1",inter,interColor,unit,unit,1,0,0);
			setColor("#carte2",inter,interColor,unit,unit,2,0,0);
//			setColor("#inter3",inter,interColor,unit2*2,unit2,0,0,0);
		}
		if(encar) {
			setColor("#carte1",encar,encarColor,unit,unit,3,0);
			setColor("#carte2",encar,encarColor,unit,unit,3,0);
		}
	}
	
	function setColor(can, img, color, w, h, z, inv, light) {

		var a = z == 2 ? img.width/2 : 0;
		var b = z == 1 || z == 2 ? img.width/2 : img.width;
		var tcv = $("#temp")[0];
		
		var tmp = tcv.getContext('2d');
		tmp.save();
		if(z>0) {
			if(inv==1) tmp.scale(-1,1);
			else tmp.translate(w,0);
			tmp.rotate(90*Math.PI/180);
		}
		if(inv==2) {
			tmp.scale(1,-1);
			tmp.translate(0,-h);
		}
		tmp.drawImage(img,a,0,b,img.height,0,0,w,h);
	
		var r = (color & 0xff0000) >>> 16;
		var g = (color & 0x00ff00) >>> 8;
		var b = color & 0x0000ff;
		if(light==1) {
			r = r+bright>255 ? 255 : r+bright;
			g = g+bright>255 ? 255 : g+bright;
			b = b+bright>255 ? 255 : b+bright;
		}
		var id = tmp.getImageData(0,0,w,h);
		var data = id.data;
		var l = data.length;
		for(var i = 0; i < l; i += 4) {
			if(data[i+3]>0) {
				data[i] = r;
				data[i+1] = g;
				data[i+2] = b;
			}
		}
		tmp.putImageData(id,0,0);
		var ctx = $(can)[0].getContext('2d');
		ctx.drawImage(tcv,0,0,w,h,0,0,w,h);
		tmp.restore();
		tmp.clearRect(0,0,w,h);
	}


</script>
[/HEADER]

