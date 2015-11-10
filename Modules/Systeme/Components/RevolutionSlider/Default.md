[HEADER]
<!-- RS5.0 Main Stylesheet -->
<link rel="stylesheet" type="text/css" href="/Tools/Js/RevolutionSlider-5.0/css/settings.css">

<!-- RS5.0 Layers and Navigation Styles -->
<link rel="stylesheet" type="text/css" href="/Tools/Js/RevolutionSlider-5.0/css/layers.css">
<link rel="stylesheet" type="text/css" href="/Tools/Js/RevolutionSlider-5.0/css/navigation.css">

[/HEADER]

<!-- RS5.0 Core JS Files -->
<script type="text/javascript" src="/Tools/Js/RevolutionSlider-5.0/js/jquery.themepunch.tools.min.js?rev=5.0"></script>
<script type="text/javascript" src="/Tools/Js/RevolutionSlider-5.0/js/jquery.themepunch.revolution.min.js?rev=5.0"></script>

<!--
	#################################
		- THEMEPUNCH BANNER -
	#################################
	-->
<div class="tp-banner-container">
	<div class="tp-banner" >
		<ul>
			<!-- SLIDE  -->
			<li data-transition="fade" data-slotamount="7" data-masterspeed="1500" >
				<!-- MAIN IMAGE -->
				<img src="images/slidebg1.jpg"  alt="slidebg1"  data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat">
				<!-- LAYERS -->
				<!-- LAYER NR. 1 -->
				<div class="tp-caption lightgrey_divider skewfromrightshort fadeout"
					 data-x="85"
					 data-y="224"
					 data-speed="500"
					 data-start="1200"
					 data-easing="Power4.easeOut">My Caption
				</div>
				...

			</li>
			<!-- SLIDE  -->
			<li data-transition="zoomout" data-slotamount="7" data-masterspeed="1000" >
				<!-- MAIN IMAGE -->
				<img src="images/darkblurbg.jpg"  alt="darkblurbg"  data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat">
				<!-- LAYERS -->
				<!-- LAYER NR. 1 -->
				<div class="tp-caption lightgrey_divider skewfromrightshort fadeout"
					 data-x="85"
					 data-y="224"
					 data-speed="500"
					 data-start="1200"
					 data-easing="Power4.easeOut">My Caption
				</div>
				...
			</li>
			....
		</ul>
	</div>
</div>

<script type="text/javascript">
	console.log('TEST');
	jQuery(document).ready(function() {
		console.log('TEST');
		jQuery('.tp-banner').revolution(
				{
					delay:9000,
					startwidth:1170,
					startheight:500,
					hideThumbs:10
				});
	});
</script>