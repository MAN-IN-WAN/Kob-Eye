[IF [!modImg!]]
        [IF [!Form_PubSignature_Upload::error!]=0]
                [OBJ Abtel|Entite|E]
                [!E::initFromId(5)!]
                [!E::Save()!]
        [ELSE]
                <div class="error">Aucun Fichier envoyé</div>
        [/IF]
[/IF]


//Gestion des images de la pub pour les signatures
<form action="" method="post" enctype="multipart/form-data" >
        <input type="hidden" name="modImg" id="modImg" value="1">
        <input type="file" name="Form_PubSignature_Upload" id="Form_PubSignature_Upload">
        <input type="submit" name="submitPub" id="submitPub">
        
        <a href="#" id="resetPub">Réinitialiser</a>
</form>

<script type="text/javascript">
         $(document).on('ready',function(){
                $('#resetPub').on('click',function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        
                        $.ajax({
                                url: "/Abtel/Signature/ResetPub.json",
                                method: "POST",
                                success: function(result){
                                        img = $("#pubSig img")[0] ;
                                        img.src= img.src+3;
                                }
                        });
                });
         });
</script>