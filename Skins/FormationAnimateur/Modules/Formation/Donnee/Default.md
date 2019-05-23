//<h1>Données participants</h1>
<div class="row">
    [INFO [!Query!]|I]
    [STORPROC [!I::LastDirect!]|S][/STORPROC]
   [STORPROC [!Query!]|CD|0|1][/STORPROC]
    <div class="col-md-3 naviquestion">
//        <h2>Liste des données</h2>
        <div class="row">
        [STORPROC Formation/Session/[!S::Id!]/Donnee|D]
            //get typequestion
            [STORPROC [!D::getParents(TypeQuestion)!]|TQ]
                [STORPROC [!TQ::getParents(Question)!]|Q]
                    [!BC:=[!Q::getCategoryBreadcrumb()!]!]
                    [!LASTCAT:=[![!Array::SizeOf([!BC!])!]:-2!]!]
                [/STORPROC]
            [/STORPROC]

            [!TEMPBC:=[!BC::[!LASTCAT!]!]!]
            [IF [!LASTBC::Id!]!=[!TEMPBC::Id!]]
                <ul class="catstats">
                    [STORPROC [!BC!]|BR]
                    [IF [!Pos!]<[!NbResult!]]
                        <li>[!BR::Nom!]</li>
                    [/IF]
                    [/STORPROC]
                </ul>
            [/IF]
            <div class="col-lg-12">
                <div class="panel">
                    <a id="question-[!D::Id!]" data-src="/Sessions/[!S::Id!]/Donnee/[!D::Id!]/Stats.htm" href="/Sessions/[!S::Id!]/Donnee/[!D::Id!]" class="[IF [!D::Id!]=[!CD::Id!]]active[/IF] Etape">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-map-marker fa-5x"></i>
                            </div>
                            <div class="col-xs-9">
                                <div class="huge">Question [!D::Numero!]</div>
                                <div>[IF [!Q::Prefixe!]!=]<em><b>- [!Q::Prefixe!] -</b></em> [/IF][!D::Titre!] </div>
                                <div>>> [!TQ::Nom!]</div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            [!LASTBC:=[!BC::[!LASTCAT!]!]!]
        [/STORPROC]
        </div>
    </div>
    <div class="col-md-9 stats">
        [MODULE Formation/Donnee/Stats?CD=[!CD!]&S=[!S!]]
    </div>
    <script>
        $('.Etape').click(function (e){
            e.preventDefault();
            $( '.active' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            $.ajax({
                url: $(this).attr('data-src'),
                context: $( '.stats' )
            }).done(function(data) {
                $( '.stats').html(data);
                $( this ).addClass( 'active' );
            });
        });
    </script>
</div>