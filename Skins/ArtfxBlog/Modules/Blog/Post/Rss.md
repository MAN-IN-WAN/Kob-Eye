<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>ArtFx Blog, le blog de la 3D et de l'image de synthèse</title>
        <link>http://blog.artfx.fr</link>
        //<description>Blog ArtFx Team</description>
	[STORPROC Blog/Post/Actif=1|Actus|0|20|tmsCreate|DESC]
		<item>
			<title>[!Utils::noHtml([!Actus::Titre!])!]</title>
			<link>http://blog.artfx.fr/Blog/Post/[!Actus::Link!]</link>
			<guid isPermaLink="true">http://blog.artfx.fr/Blog/Post/[!Actus::Id!]</guid>
				<description><![CDATA[
					<table>
						<tr>
							<td>
								[COUNT Blog/Post/[!Actus::Id!]/Fichier|Phot]
								[IF [!Phot!]]
									[STORPROC Blog/Post/[!Actus::Id!]/Fichier/Type=Image|Pho|0|1]
										<a href="http://blog.artfx.fr/Blog/Post/[!Actus::Link!]" title="Lire le detail de [!Actus::Titre!]">
											<img src="/[!Pho::Fichier!]" alt="[!Pho::Titre!]" width="350" border="5px"/>
										</a>
									[/STORPROC]
								[/IF]
							</td>
							<td>[SUBSTR 700| <a href="http://blog.artfx.fr/Blog/Post/[!Actus::Link!]" title="Lire la suite de [!Actus::Titre!]">[...]</a>][!Utils::noHtml([!Actus::Contenu!])!][/SUBSTR]</td>
						</tr>
						<tr>
							<td></td>
							<td>
								[COUNT Blog/Post/[!Actus::Id!]/Commentaire|Com]
								[IF [!Com!]=0]
									Il n'y a pas encore de commentaire.
								[/IF]
								[IF [!Com!]=1]
									Commentaire : [!Com!] 
								[/IF]
								[IF [!Com!]>1]
									Commentaires : [!Com!] 
								[/IF]
							</td>
						</tr>
					</table><hr />
				]]></description>
			<pubDate>[!Actus::Date!]</pubDate>
		</item>
	[/STORPROC]
    </channel>
</rss>
