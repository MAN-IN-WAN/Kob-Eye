
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

[!DATE:=[!TMS::Now!]!]
[IF [!DATE_DEBUG!]]
[!DATE:=[!DATE_DEBUG!]!]
[/IF]

[STORPROC Systeme/Menu/[!Sys::CurrentMenu::Id!]/Donnee/Type=Image|S|0|10]

<!-- START REVOLUTION SLIDER 5.0 -->
<div style="width:calc( 100% - 20px );margin-left:20px; overflow:hidden;margin-bottom: 20px;">
    <div class="rev_slider_wrapper">
        <div id="slider1" class="rev_slider"  data-version="5.0">
            <ul>
            [LIMIT 0|100]
                //Liste des transitions
                //values="fade,boxfade,slotfade-horizontal,slotfade-vertical,fadefromright,fadefromleft,fadefromtop,fadefrombottom,fadetoleftfadefromright,fadetorightfadetoleft,fadetobottomfadefromtop,fadetotopfadefrombottom,scaledownfromright,scaledownfromleft,scaledownfromtop,scaledownfrombottom,zoomout,zoomin,slotzoom-horizontal,slotzoom-vertical,parallaxtoright,parallaxtoleft,parallaxtotop,parallaxtobottom,slideup,slidedown,slideright,slideleft,slidehorizontal,slidevertical,boxslide,slotslide-horizontal,slotslide-vertical,curtain-1,curtain-2,curtain-3,3dcurtain-horizontal,3dcurtain-vertical,cubic,cubic-horizontal,incube,incube-horizontal,turnoff,turnoff-vertical,papercut,flyin,random-static,random"
                <li data-transition="boxfade">
                    <!-- MAIN IMAGE -->
                    <img src="/[!S::Lien!]"  alt="" data-bgposition="center center"
                    data-kenburns="on" data-duration="20000" data-start="300" data-ease="Linear.easeNone" data-scalestart="100" data-scaleend="120" data-rotatestart="0" data-rotateend="0" data-offsetstart="0 -500" data-offsetend="0 500" data-bgparallax="10"
                    >
                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption largepinkbg"
                         data-x="left"
                         data-y="80"
                         data-transform_in="z:0;rX:0deg;rY:0;rZ:0;sX:1.5;sY:1.5;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeOut;"
                         data-transform_out="y:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                    >[!S::Titre!]</div>
                </li>
            [/LIMIT]
            </ul>
        </div><!-- END REVOLUTION SLIDER -->
    </div><!-- END OF SLIDER WRAPPER -->
</div>
[/STORPROC]

<script>
console.log('revolution slider launch');
jQuery("#slider1").revolution({
    sliderType:"standard",
    sliderLayout:"auto",
    delay:4500,
    navigation: {
        arrows:{enable:true}
    },
    autoHeight:"on",
    gridwidth:1100,
    gridheight:400
});
</script>