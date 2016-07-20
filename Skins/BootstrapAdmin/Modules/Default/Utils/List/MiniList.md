[IF [!SENS!]=][!SENS:=parent!][/IF]

[!REQ:=[!Chemin!]!]
[INFO [!REQ!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|OO]
<div class="table-responsive">
<table class="table table-striped">
    <thead>
    <tr>

        [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E]
        <th>[IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::description!][/IF]</th>
        [/STORPROC]
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>

    [STORPROC [!REQ!]|C|0|1000]
    <tr>
        [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E]
            [MODULE Systeme/Utils/List/getElementType?E=[!E!]&C=[!C!]]
        [/STORPROC]
        <td width="250">
            [IF [!O::Id!]]
                [IF [!SENS!]=parent]
                    [COUNT [!P::objectModule!]/[!P::objectName!]/[!C::Id!]/[!O::ObjectType!]/[!O::Id!]|DF]
                [ELSE]
                    [COUNT [!P::objectModule!]/[!O::ObjectType!]/[!O::Id!]/[!C::ObjectType!]/[!C::Id!]|DF]
                [/IF]
            [/IF]
        </td>
    </tr>
    [/STORPROC]
    </tbody>
</table>
</div>