<div class="row">
    [COUNT [!Query!]/Court|Nb]
    [IF [!Nb!]>=4]
        [!NbCol:=4!]
    [ELSE]
        [!NbCol:=[!Nb!]!]
    [/IF]
    [STORPROC [!Query!]/Court|C|0|10]
    <div class="col-md-[!12:/[!NbCol!]!]">
        <h3>[!C::Titre!]</h3>
        [STORPROC 12|H]
            <a href="" class="horaire-tennis">[!H:+9!]h</a>
        [/STORPROC]
    </div>
    [/STORPROC]
</div>
<style>
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: [!NbCol:*2!]0%;
        }
    }
</style>