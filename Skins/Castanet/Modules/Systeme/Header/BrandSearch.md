<select name="Marque" id="marqueSelect">
    <label>Recherche par marque</label>
    <option value="">Recherche par marque</option>
    [STORPROC Boutique/Marque/Actif=1|M|0|1000|Nom|ASC]
    <option value="[!M::Url!]">[!M::Nom!]</option>
    [/STORPROC]
</select>
<script>
    $('#marqueSelect').change(function() {
        console.log('selection d une marque',$(this).val());
        document.location.replace('/[!Sys::getMenu(Boutique/Marque)!]/'+$(this).val());
    })
</script>
