<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Toutes les actualit&#233;s de l'[!CONF::MODULE::SYSTEME::SOCIETE!]</title>
        <link>[!Domaine!]</link>
        <description>Toutes les actualit&#233;s concert de l'[!CONF::MODULE::SYSTEME::SOCIETE!]</description>
	[STORPROC News/Nouvelle|Actus|0|20|tmsCreate|DESC]
		<item>
			<title>[!Actus::Titre!]</title>
			<link>[!Domaine!]/Actualites/Nouvelle/[!Actus::Url!]</link>
			<guid isPermaLink="true">[!Domaine!]/Actualites/Nouvelle/[!Actus::Url!]</guid>
				<description>[UTIL SPECIALCHARS]
					<table>
						<tr>
							<td><img src="[!Domaine!]/[!Actus::Image!].limit.100x100.jpg" alt=""></td>
							<td>[!Actus::Contenu!]</td>
						</tr>
					</table>
						[/UTIL]
				</description>
			<pubDate>[!Actus::Date!]</pubDate>
		</item>
	[/STORPROC]
    </channel>
</rss>

