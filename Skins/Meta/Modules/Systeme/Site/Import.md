<form enctype="multipart/form-data" action="Upload.json" method="post" name="import" id="import">
        <input type="file" id="Filedata" name="Filedata" class="fileupload">
        
        <input type="submit" value="go">
</form>

<script type="text/javascript">
        $(document).ready(function() {
                // Lorsque je soumets le formulaire
                $('#import').on('submit', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        var $this = $(this);
                        
                        var formData = new FormData($('#import')[0]);
                        [STORPROC [!Query!]|Site]
                        var site = '[!Site::Domaine!]';
                        [/STORPROC]
                    
                        $.ajax({
                            url: $this.attr('action'), 
                            type: $this.attr('method'), 
                            data: formData,
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(rdata) {
                                if(rdata.status){
                               
                                        $.ajax({
                                                url: 'TraitementCsv.json', 
                                                type: 'POST', 
                                                data: {url:rdata.url,site:site},
                                                cache: false,
                                                success: function(rdata2) { 
                                                     console.log(rdata2);
                                                }
                                        });
                                }
                            }
                        });
                }); 
        });
</script>