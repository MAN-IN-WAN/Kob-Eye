<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
    <channel>
        <title>[!Domaine!]</title>
        <link>[!Domaine!]</link>
        <description>Les dernieres news sur la loi Scellier</description>
	
	[STORPROC Redaction/Categorie/155/*/Article|Actus|0|20|tmsCreate|DESC]
		<item>
			<title>[!Actus::Titre!]</title>
			<link>[!Domaine!]/News/Nouvelle/[!Actus::Id!]</link>
			<guid isPermaLink="true">[!Domaine!]/News/Nouvelle/[!Actus::Id!]/Complete</guid>
				<description>[UTIL SPECIALCHARS]
					<table>
						<tr>
							<td><img src="[!Domaine!]/[!Actus::Image!].mini.100x100.jpg" alt=""></td>
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

