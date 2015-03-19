
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]

	//CONFIG
	[IF [!page!]=][!page:=1!][/IF]
	[!NbLigneParPage:=5!]
	
	//REQUETE ET FILTRES
	[!REQUETE:=Murphy/Third/[!Th::Id!]/Enquiry!]
	[SWITCH [!filter!]|=]
		[CASE current]
			<section id="current">
			<h3>__LIST_ENQUIRY_CURRENT__</h3>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STE_CONFIRMED!]!]
	        	[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/CASE]
		[CASE completed]
			<section id="completed">
			<h3>__LISTE_ENQUIRY_COMPLETED__</h3>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STE_COMPLETED!]!]
	        	[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/CASE]
		[CASE closed]
			<section id="closed">
			<h3>__LISTE_ENQUIRY_CLOSED__</h3>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STE_CANCELLED!]+StatusId=[!CONF::MODULE::MURPHY::STE_COMPLETED!]!]
        		[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/CASE]

		[DEFAULT]
			<section>
			<h3>__LIST_WAITING_ENQUIRY__</h3>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STE_DRAFT!]+StatusId=[!CONF::MODULE::MURPHY::STE_HAGGLING!]!]
	        	[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/DEFAULT]
	[/SWITCH]
	
	//CALCUL PAGINATION
	[COUNT [!REQUETE!]|NB]
	[!NbPage:=[!NB:/[!NbLigneParPage!]!]!]
	[IF [!Math::Floor([!NbPage!])!]!=[!NbPage!]]
		[!NbPage:=[![!Math::Floor([!NbPage!])!]:+1!]!]
	[/IF]

    [STORPROC [!REQUETE!]|Enq|[![!page:-1!]:*[!NbLigneParPage!]!]|[!NbLigneParPage!]|Id|DESC]
//    	[MODULE Murphy/Enquiry/EtatList?Enq=[!Enq!]]
<form action="/[!Lien!]" method="post" class="form-horizontal" enctype="multipart/form-data">
    	[MODULE Murphy/Enquiry/EtatDetail?Id=[!Enq::Id!]&CONTROL=1]
</form>
        [NORESULT]
        	//<div class="alert alert-info">
        		[!MESG_NO_RESULT!]
        	//</div>
        [/NORESULT]
    [/STORPROC]    
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [/NORESULT]
    [IF [!NbPage!]>1]
        <div class="pagination  pagination-centered">
		    <ul>
			    <li class="[IF [!page:-1!]<1]disabled[/IF]"><a href="[IF [!page:-1!]>=1]?page=[!page:-1!][ELSE]#nogo[/IF]">Prev</a></li>
			    [STORPROC [!NbPage!]|P]
			    <li class="[IF [!P:+1!]=[!page!]]active[/IF]"><a href="?page=[!P:+1!]">[!P:+1!]</a></li>
			    [/STORPROC]
			    <li class="[IF [!page:+1!]>[!NbPage!]]disabled[/IF]"><a href="[IF [!page:+1!]<=[!NbPage!]]?page=[!page:+1!][ELSE]#nogo[/IF]">Next</a></li>
		    </ul>
	    </div>
	[/IF]
[/STORPROC]
</section>