[IF [!SENS!]=][!SENS:=parent!][/IF]

[!REQ:=[!Chemin!]!]
[INFO [!REQ!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|OO]
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

    [STORPROC [!REQ!]|C|0|1000|tmsCreate|DESC]
    <tr>
        [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E]
            [MODULE Systeme/Utils/List/getElementType?E=[!E!]&C=[!C!]&NOLINK=1]
        [/STORPROC]
        <td width="250">
            [IF [!O::Id!]]
                [IF [!SENS!]=parent]
                    [COUNT [!P::objectModule!]/[!P::objectName!]/[!C::Id!]/[!O::ObjectType!]/[!O::Id!]|DF]
                [ELSE]
                    [COUNT [!P::objectModule!]/[!O::ObjectType!]/[!O::Id!]/[!C::ObjectType!]/[!C::Id!]|DF]
                [/IF]
            [/IF]
            <input type="checkbox" name="Form_[!P::name!][]" [IF [!DF!]]checked="checked"[/IF] class="switch " value="[!C::Id!]">
        </td>
    </tr>
    [/STORPROC]
    </tbody>
</table>
</div>