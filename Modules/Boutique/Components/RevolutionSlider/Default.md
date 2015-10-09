
[STORPROC Boutique/Promotion/DateDebutPromo<[!TMS::Now!]&DateFinPromo>[!TMS::Now!]&Display=1&SliderEnable=1|S|0|100]
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

    <!-- START REVOLUTION SLIDER 5.0 -->
    <div style="width:100%;margin:auto; overflow:hidden;margin-bottom: 20px;">
        <div class="rev_slider_wrapper">
            <div id="slider1" class="rev_slider"  data-version="5.0">
                <ul>
                    [LIMIT 0|100]
                    <li data-transition="[!S::SliderTransition!]">
                        <!-- MAIN IMAGE -->
                        <img src="/[!S::Image!].limit.1920x1080.jpg"  alt="" data-bgposition="center center"
                             [IF [!S::Panning!]]
                             data-kenburns="on" data-duration="10000" data-start=300 data-ease="Linear.easeNone" data-scalestart="100" data-scaleend="120" data-rotatestart="0" data-rotateend="0" data-offsetstart="0 -500" data-offsetend="0 500" data-bgparallax="10"
                             [/IF]
                                >
                        <!-- LAYER NR. 1 -->
                        [STORPROC Boutique/Promotion/[!S::Id!]/PromotionCalque|SC]
                        <div class="tp-caption [!SC::Police!]-[!SC::Type!] [!SC::Background!] [!SC::Class!]"
                             data-x="[!SC::PosX!]"
                             data-y="[!SC::PosY!]"
//                             [IF [!SC::CustomIn!]]data-transform_in="[!SC::CustomIn!]"[/IF]
//                             [IF [!SC::CustomOut!]]data-transform_out="[!SC::CustomOut!]"[/IF]
//                            data-speed="[!SC::Duration!]"
//                             data-start="[!SC::Delay!]"
//                             data-easing="[!SC::TransitionStart!]"
//                             data-endeasing="[!SC::TransitionEnd!]"
                        [SWITCH [!SC::TransitionType!]|=]
                            [CASE fromforeground]
                                data-transform_in="z:0;rX:0deg;rY:0;rZ:0;sX:1.5;sY:1.5;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeOut;"
                                data-transform_out="y:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/CASE]
                            [CASE frombackground]
                                data-transform_in="z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeInOut;"
                                data-transform_out="y:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/CASE]
                            [CASE fromtop]
                                data-transform_in="y:-100%;z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;s:1500;e:Power3.easeInOut;"
                                data-transform_out="y:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/CASE]
                            [CASE frombottom]
                                data-transform_in="y:100%;z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                                data-transform_out="y:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/CASE]
                            [CASE fromleft]
                                data-transform_in="x:-100%;z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                                data-transform_out="x:-100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/CASE]
                            [CASE fromright]
                                data-transform_in="x:100%;z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                                data-transform_out="x:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/CASE]
                            [DEFAULT]
                                data-transform_in="x:100%;z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                                data-transform_out="x:100%;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                            [/DEFAULT]
                        [/SWITCH]
                        >[!SC::Texte!]</div>
                        [/STORPROC]
                    </li>
                    [/LIMIT]
                </ul>
            </div><!-- END REVOLUTION SLIDER -->
        </div><!-- END OF SLIDER WRAPPER -->
    </div>

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
[/STORPROC]
