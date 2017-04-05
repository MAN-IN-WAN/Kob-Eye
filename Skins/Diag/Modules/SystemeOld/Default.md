       
<h2 class="sub-header">Liste des domaines </h2>
       
          
<div class="table-responsive">
        <table class="table table-striped">
                <thead>
                        <tr>
                                <th>#</th>
                                <th>Domaine</th>
                                <th>Id</th>
                                <th>User Create</th>
                                <th>Client</th>
                        </tr>
                </thead>
                <tbody>
                [STORPROC Parc/Domain|D|0|1000]
                        <tr id="dom_[!D::Id!]">
                                <td>[!Pos!]</td>
                                <td>[!D::Url!]</td>
                                <td>[!D::Id!]</td>
                                [STORPROC Systeme/User/[!D::userCreate!]|U][/STORPROC]
                                <td>[!U::Login!]</td>
                                <td class="client"><img src="https://d13yacurqjgara.cloudfront.net/users/82092/screenshots/1073359/spinner.gif" style="width: 40px; height: 30px;"></td>
                        </tr>
                [/STORPROC]
                </tbody>
        </table>
</div>


<script type="text/javascript">
        $(document).on('ready',function(){
                $.getJSON( "/Systeme/Domain/getDomCli.json",function(data) {
                        $.each(data,function(i,v){
                                $("#dom_"+i+" .client" ).html(v);
                        });   
                });
        });
        
</script>
