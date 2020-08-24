<?php
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data)) $data = $_GET;
if (isset($data['state'])) {
    $vars['state'] = $data['state'];
} else {
    $vars['state'] = 0;
}
$test = $date['state'];
if ($vars['state'] == 0) {

    $html = '<p>Choisissez un format :</p>
<div>
    <button ng-click="onExportMailStCult();">Structures culturelles</button><br>
    <button ng-click="onExportMailStSoc();">Structures sociales</button><br>
</div>';
    $ret = array(
        'html' => $html
    );
    $vars['return'] = json_encode($ret);
} elseif($vars['state'] == 2) {
    $vars['Organisation'] = Sys::getData('Reservation','Organisation',0,100000);
    include $_SERVER['DOCUMENT_ROOT']."/Class/Lib/HTML2PDF.class.php";
    ob_get_clean();
    $tes = new HTML2PDF();
    $struc = '';
    foreach ($vars['Organisation'] as $items){
        $struc .= '<tr style="width:200mm;font-size:10px;">
                    <td style="width:30%;font-size:14px;font-weight:bold;color:#ff0000;padding:20px;">'.$items->Nom.'</td>	
                    <td style="width:70%;font-weight:bold;padding-right:10px;font-size:16px;padding:20px;">'.$items->Mail.'</td>
                   </tr>';
    }
    $tes->writeHTML('<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
				<table cellspacing="0" cellspadding="0" border="1">
					<tr style="width:200mm;font-size:10px;background-color:#ccc;">
                                                <th style="font-size:14px;font-weight:bold;padding:20px;text-align:center;width:30%;">Structure Culturelle</th>
                                                <th style="font-size:14px;font-weight:bold;padding:20px;text-align:center;width:70%;">Mail(s)</th>
					</tr>'.$struc.'</table>
			</page>');
    $html = $tes->Output('MailsStructuresCulturelles.pdf');
    ob_end_clean();
//    $html = '<iframe src="/Reservation/Statistiques/ExportMail.pdf" frameborder="0" style="width:100%;height:900px;"></iframe>';
    $vars['return'] = $html;
}elseif($vars['state'] == 4) {
    $vars['Client'] = Sys::getData('Reservation','Client',0,100000);
    include $_SERVER['DOCUMENT_ROOT']."/Class/Lib/HTML2PDF.class.php";
    ob_get_clean();
    $tes = new HTML2PDF();
    $struc = '';
    foreach ($vars['Client'] as $items){
        $struc .= '<tr style="width:200mm;font-size:10px;">
                    <td style="width:30%;font-size:14px;font-weight:bold;color:#ff0000;padding:20px;">'.$items->Nom.'</td>	
                    <td style="width:70%;font-weight:bold;padding-right:10px;font-size:16px;padding:20px;">'.$items->Mail.'</td>
                   </tr>';
    }
    $tes->writeHTML('<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
				<table cellspacing="0" cellspadding="0" border="1">
					<tr style="width:200mm;font-size:10px;background-color:#ccc;">
                                                <th style="font-size:14px;font-weight:bold;padding:20px;text-align:center;width:30%;">Structure Sociales</th>
                                                <th style="font-size:14px;font-weight:bold;padding:20px;text-align:center;width:70%;">Mail(s)</th>
					</tr>'.$struc.'</table>
			</page>');
    $html = $tes->Output('MailsStructuresCulturelles.pdf');
    ob_end_clean();
//    $html = '<iframe src="/Reservation/Statistiques/ExportMail.pdf" frameborder="0" style="width:100%;height:900px;"></iframe>';
    $vars['return'] = $html;
}

