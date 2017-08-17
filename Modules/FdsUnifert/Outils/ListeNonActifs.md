<button onclick="PrintElem('tableContainer',event);" style="float-left;">Imprimer</button>
<button onclick="toggleDispo(event);" style="float:right;">Filtrer</button>
<div id="tableContainer" style="overflow: auto;height: 100%;">
    [COUNT FdsUnifert/Client/Actif=0|CCli]
    <h1 style="text-align: center">Client n'ayant pas activé leurs comptes ([!CCli!])</h1>

<table border="1" style="border:1px solid #0a4b3e;border-collapse: collapse;width: 900px;text-align: center;font-size: 18px;margin:auto;">
    <tr style="background-color:#00CB98;">
        <th>Id</th>
        <th>Société</th>
        <th>Code</th>
        <th>Mail</th>
    </tr>
[STORPROC FdsUnifert/Client/Actif=0|Cli|0|10000|Id|ASC]
    [!Obli:=0!]
    [STORPROC FdsUnifert/Fds/Client/[!Cli::Id!]|F]
        [IF [!F::Obligatoire!]]
            [!Obli:=1!]
        [/IF]
    [/STORPROC]

    <tr style="[IF [!Utils::modulo([!Key!],2)!]]background-color:#c2ffc2;[/IF] border-top: 3px solid #000;" [IF [!Obli!]=0]class="notobli"[/IF]>
        <td>[!Cli::Id!]</td>
        <td [IF [!Obli!]]style="font-weight:600;font-style:italic;color:#ff0000;"[/IF]>[!Cli::Societe!]</td>
        <td><a href="/FdsUnifert/Client/[!Cli::Id!].htm" target="_blank"><strong style="color: #5a0009;">[!Cli::Code!]</strong></a></td>
        <td>[!Cli::Mail!]</td>
    </tr>
    [STORPROC FdsUnifert/Client/[!Cli::Id!]/Contact|Ct]
    <tr [IF [!Utils::modulo([!Key!],2)!]]style="background-color:#c2ffc2;"[/IF] [IF [!Obli!]=0]class="notobli"[/IF]>
        <td colspan="4">
            <h3 style="text-align: center;margin: 10px;">Contacts:</h3>
            <table border="1" style="border:1px solid #ddd;border-collapse: collapse;width: 750px;margin: auto;background-color:#fff;margin-bottom: 5px;">
                <tr style="background-color:#fff;color: #888;">
                    <th>Nom Prénom</th>
                    <th>Mail</th>
                    <th>Contact Fds</th>
                </tr>
                [LIMIT 0|100]
                <tr [IF [!Ct::Fds!]]style="color:#00CB98"[/IF]>
                    <td>[!Ct::Nom!] [!Ct:Prenom!]</td>
                    <td>[!Ct::Mail!]</td>
                    <td>[IF [!Ct::Fds!]] Oui [ELSE] Non [/IF]</td>
                </tr>
                [/LIMIT]
            </table>
        </td>
    </tr>
    [/STORPROC]


[/STORPROC]
</table>
</div>
<script type="text/javascript">

    function PrintElem(elem,event)
    {
        event.stopPropagation();
        event.preventDefault();

        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }

    function toggleDispo(event){
        event.stopPropagation();
        event.preventDefault();

        var elems = document.getElementsByClassName('notobli');
        for (var n in elems){
            var elem = elems[n];
            if(elem.offsetParent === null){
                elem.style.display = '';
            }else{
                elem.style.display = 'none';
            }
        }
    }
</script>