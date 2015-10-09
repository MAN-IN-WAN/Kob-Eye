<div class="row">
    <div class="col-lg-12">
        <h2>Fichiers et vidéos</h2>
    </div>
    [STORPROC [!CurrentProjet::getChildren(Fichier)!]|F]
    <div class="col-md-6">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="cold-md-6">
                        <div class="row">
                            <div class="col-xs-5">
                                <a href="/[!F::Fichier!].download">
                                    [SWITCH [!F::Type!]|=]
                                    [CASE doc]
                                    <i class="fa fa-file-word-o fa-5x"></i>
                                    [/CASE]
                                    [CASE excell]
                                    <i class="fa fa-file-excell-o fa-5x"></i>
                                    [/CASE]
                                    [CASE powerpoint]
                                    <i class="fa fa-file-powerpoint-o fa-5x"></i>
                                    [/CASE]
                                    [CASE zip]
                                    <i class="fa fa-file-zip-o fa-5x"></i>
                                    [/CASE]
                                    [CASE image]
                                    <i class="fa fa-file-image-o fa-5x"></i>
                                    [/CASE]
                                    [CASE text]
                                    <i class="fa fa-file-text fa-5x"></i>
                                    [/CASE]
                                    [CASE video]
                                    <i class="fa fa-file-video-o fa-5x"></i>
                                    [/CASE]
                                    [CASE pdf]
                                    <i class="fa fa-file-pdf-o fa-5x"></i>
                                    [/CASE]
                                    [DEFAULT]
                                    <i class="fa fa-file-o fa-5x"></i>
                                    [/DEFAULT]
                                    [/SWITCH]
                                </a>
                            </div>
                            <div class="col-xs-7">
                                <a href="/[!F::Fichier!].download">
                                    <div class="huge">[!F::Nom!]</div>
                                    <div>[!F::Type!]</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Remplacer le fichier</label>
                        <input id="input-[!F::Id!]" type="file" multiple=false class="file-loading">
                        <script>
                            $(document).on('ready', function() {
                                $("#input-[!F::Id!]").fileinput({showCaption: false, showPreview: false, language: 'fr', uploadUrl: '/Formation/Fichier/[!F::Id!]/Upload.htm', dropZoneEnabled: false});
                            });
                            $('#input-[!F::Id!]').on('filebatchuploadcomplete', function(event, file, previewId, index) {
                                console.log('document upload ', event);
                                document.location = '/[!Lien!]';
                            });

                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    [/STORPROC]
</div>
