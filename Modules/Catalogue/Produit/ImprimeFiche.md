// Fiche Pdf d'un produit
[STORPROC [!Query!]|P|0|1]
	[IF [!P::Publier!]!=1]
		[REDIRECT]/Boutique[/REDIRECT]
	[ELSE]
        [STORPROC Catalogue/Categorie/Produit/[!P::Id!]|Cat|0|1][/STORPROC]
        [!Infos:=0!]
        [LIB HTML2PDF|html2pdf]
        [METHOD html2pdf|writeHTML]
            [PARAM]
                <style type="text/css">
                    body {font-family:verdana;font-size:12px; }
                    table.page_desc { width:200mm; border: none; }
                        table.page_footer { width:200mm; border: none; border-top: solid 1mm #536281; }
                    ul.bb_ul {overflow:auto;margin:0;padding:0;list-style-type:none;}
                    li.bb_li {
                        display:block;
                        margin-bottom:5px;
                        padding-left:12px;
                        list-style:circle;
                    }
                    .bb_bold { font-weight:bold;}
                    .bb_ul { list-style-type:square; }
                    .bb_li {padding-left:10px; }
                </style>
                <page  pageset="old" backtop="14mm" backbottom="10mm" backleft="0" backright="0" >
                    <table class="page_desc" >
                        <tr style="padding-top:5px;margin:5px;">
                            <td style="width:80mm;text-align:left;vertical-align:middle;">
                                <img src="Skins/Public2015/Img/bando-mail.jpg"  />
                            </td>
                            <td style="color:#536281;text-transform:none;font-size:14px;padding:20px;text-align:center;font-size:20px;">Garantir votre sécurité et votre confort</td>
                        </tr>
                        <tr style="padding-top:5px;margin:5px 0;">
                            <td style="width:100%;" colspan="2"><hr style="color:#536281;width:100%;padding:0;margin:0;" /></td>
                        </tr>
                    </table>
                    <page_footer>
                        <table class="page_footer">
                            <tr>
                                <td style="width:100%;text-align:right;color:#536281;">Axenergie Gaz Service</td>

                            </tr>
                        </table>
                    </page_footer>
                    <table class="page_desc" >
                        <tr style="padding-top:5px;margin:5px;border-bottom:1px dotted #536281;height:390px;vertical-align:top;">
                            <td style="width:80mm;text-align:left">
                                [IF [!P::Image!]!=]<img src="[!P::Image!]" width="256" />[/IF]
                            </td>
                            <td style="width:109mm;text-align:left;">
                                <div style="color:#af0410;font-weight:bold;font-variant:small-caps;font-size:14px;">[!Cat::Nom!]</div>
                                [IF [!P::Fabricant!]!=]
                                    [STORPROC Catalogue/Fabricant/[!P::Fabricant!]|F|0|1]
                                        <div style="color:#000;font-weight:bold;font-variant:small-caps;font-size:14px;height:20px;">[!F::Nom!]</div>
                                    [/STORPROC]
                                    <hr style="color:#536281;" />
                                [/IF]
                                [IF [!P::Titre!]!=]<div style="color:#536281;font-weight:bold;font-size:14px;height:20px;">[!P::Titre!]</div>[/IF]

                                [IF [!P::Dimensions!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;">
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Dimensions : </span>[!P::Dimensions!]
                                    </div>
                                [/IF]
                                [IF [!P::SolMurale!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;" >
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Pose : </span>[!P::SolMurale!]
                                    </div>
                                [/IF]
                                [IF [!P::Service!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;" >
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Service : </span>[!P::Service!]
                                    </div>
                                [/IF]
                                [IF [!P::Evacuation!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;" >
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Evacuation : </span>
                                        [SWITCH [!P::Evacuation!]|=]
                                            [CASE CF]
                                                - Conduit Fumée
                                            [/CASE]
                                            [CASE FF]
                                                - Flux forcé
                                            [/CASE]
                                            [CASE VMC]
                                                - VMC
                                            [/CASE]
                                        [/SWITCH]
                                    </div>
                                [/IF]
                                [IF [!P::Puissance!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;" >
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Puissance : </span>[!P::Puissance!]
                                    </div>
                                [/IF]
                                [IF [!P::Sanitaire!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;" >
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Type sanitaire : </span>[!P::Sanitaire!]
                                    </div>
                                [/IF]
                                [IF [!P::DebitSanitaire!]!=]
                                    [!Infos:=1!]
                                    <div style="color:#000;font-size:14px;padding-top:10px;" >
                                        <span style="color:#000;font-size:14px;font-weight:bold;"> Débit sanitaire : </span>[!P::DebitSanitaire!]
                                    </div>
                                [/IF]
                                [IF [!Infos!]=0]
                                    <hr style="color:#536281;" />
                                    [IF [!P::Description!]!=]
                                        <div style="color:#536281;font-weight:bold;text-transform:capitalize;font-size:14px;">Description</div>
                                        <div style="color:#000;font-size:14px;text-align:justify;margin-top:20px;width:109mm;">
                                            [!P::Description!]
                                        </div>
                                    [/IF]
                                    [IF [!P::Avantages!]!=]
                                        <div style="color:#536281;font-weight:bold;text-transform:uppercase;font-size:14px;">Avantages produits</div>
                                        <div style="color:#000;font-size:14px;text-align:justify;margin-top:20px;">[!P::Avantages!]</div>
                                    [/IF]
                                [/IF]
                                [IF [!F::Logo!]!=]
                                    <div style="text-align:right;padding:30px 0 0 0 ;width:109mm;">
                                        <img src="[!F::Logo!]" style="max-width:100px;" />
                                    </div>
                                [/IF]
                            </td>
                        </tr>
                    </table>
                    [IF [!Infos!]=1]
                        <table class="page_desc" >
                            <tr style="width:185mm;padding-top:5px;margin:5px">
                                <td style="width:185mm;text-align:left"  >
                                    [IF [!P::Description!]!=]
                                        <div style="color:#536281;font-weight:bold;text-transform:uppercase;font-size:14px;">Description</div>
                                        <div style="color:#000;font-size:14px;text-align:justify;">
                                            [!P::Description!]
                                        </div>
                                    [/IF]
                                    [IF [!P::Avantages!]!=]
                                        <div style="color:#536281;font-weight:bold;text-transform:uppercase;font-size:14px;">Avantages produits</div>
                                        <div  style="color:#000;font-size:14px;text-align:justify;">[!P::Avantages!]</div>
                                    [/IF]
                                </td>
                            </tr>
                        </table>
                    [/IF]
                    //[COUNT Catalogue/PictoProduit/Produit.PictoProduitId(Id=[!P::Id!])|NbPpA]
                    //[IF [!NbPpA!]]
                    //	<table class="page_desc" ><tr style="width:185mm;padding-top:5px;margin:5px"><td style="width:185mm;text-align:left"  >
                    //		[STORPROC Catalogue/PictoProduit/Position=Avantages&&Produit.PictoProduitId(Id=[!P::Id!])|PpA]
                    //			<img src="[!PpA::Picto!]" alt="[!PpA::Titre!]" title="[!PpA::Titre!]" style="float:left;padding-right:10px;" />
                    //		[/STORPROC]
                    //	</td></tr></table>
                    //[/IF]



                </page>
            [/PARAM]
            [PARAM][/PARAM]
        [/METHOD]

        [!html2pdf::Output!]
    [/IF]
[/STORPROC]
	
	
