//[IF [!C_Valider!]]
    [STORPROC [!Query!]|Cat|0|1]
        [METHOD Cat|CategoryParserLafayette][/METHOD]
//        [METHOD Cat|ProductParser][/METHOD]
    [/STORPROC]
//[ELSE]
//    [TITLE]Admin Kob-Eye | Parser 1001Pharmacie [/TITLE]
//   [MODULE Systeme/Interfaces/FilAriane]
//
//  <div id="Container">
    //        <div id="Arbo">
    //            [BLOC Panneau]
    //            [/BLOC]
    //        </div>
    //        <div id="Data" style="overflow: auto;">
    //            [BLOC Panneau]
    //            <div style="margin:10px;font-size:15px;overflow: auto;">
    //                <form  action="/[!Lien!].csv" method="post" name="frm">
    //                    <p>Coller ici le menu 1001 Pharmacie pour parser les catégories et importer les produits</p>
    //                    <textarea name="data" cols="150" rows="30"></textarea>
    //                    <input type="submit" name="C_Valider" value="Parser" />
    //                </form>
    //            </div>
    //            [/BLOC]
    //      </div>
    //</div>
//[/IF]


