[IF [!Systeme::User::Public!]]
[ELSE]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
				<table cellspacing="0" cellspadding="0" border="1">
					<tr style="width:200mm;font-size:10px;background-color:#ccc;">
                                                <th style="font-size:14px;font-weight:bold;padding:20xp;text-align:center;width:30%;">Structure Culturelle</th>
                                                <th style="font-size:14px;font-weight:bold;padding:20xp;text-align:center;width:70%;">Mail(s)</th>
					</tr>
                                        [STORPROC Reservation/Organisation|Orga]
                                                <tr style="width:200mm;font-size:10px;">
                                                        <td style="width:30%;font-size:14px;font-weight:bold;color:#ff0000;padding:20px;">[!Orga::Nom!]</td>	
                                                        <td style="width:70%;font-weight:bold;padding-right:10px;font-size:16px;padding:20px;">[!Orga::Mail!]</td>
                                                </tr>
                                        [/STORPROC]
				</table>
			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]

	[!html2pdf::Output(MailsStructuresCulturelles.pdf)!]
[/IF]