[SWITCH [!E::type!]|=]
        [CASE boolean]
<td>[IF [!C::[!E::name!]!]]
    <h4>[IF [!NOLINK!]=]<a href="?search=[!search!]&Page=[!Page!]&action=edit&prop=[!E::name!]&value=0&id=${id}">[/IF]<span class="label label-success"><i class="fa fa-check"></i></span>[IF [!NOLINK!]=]</a>[/IF]</h4>
    [ELSE]
    <h4>[IF [!NOLINK!]=]<a href="?search=[!search!]&Page=[!Page!]&action=edit&prop=[!E::name!]&value=1&id=${id}">[/IF]<span class="label label-danger"><i class="fa fa-times"></i></span>[IF [!NOLINK!]=]</a>[/IF]</h4>
    [/IF]
</td>
        [/CASE]
        [CASE price]
<td><h4><span class="label label-primary">${[!E::name!]} €</span></h4></td>
        [/CASE]
        [CASE int]
<td><h4><span class="label label-warning">${[!E::name!]}</span></h4></td>
        [/CASE]
        [CASE fkey]
<td><strong>${[!E::name!]}</strong></td>
        [/CASE]
        [CASE date]
<td><h4><span class="label label-primary">${[!E::name!]}</span></h4></td>
        [/CASE]
        [CASE datetime]
<td><h4><span class="label label-info">${[!E::name!]}</span></h4></td>
        [/CASE]
        [CASE image]
<td><img src="/${[!E::name!]}.mini.200x50.jpg" class="img-responsive" /></td>
        [/CASE]
        [DEFAULT]
<td>
[IF [!NOLINK!]=]<a href="/[!Sys::getMenu([!C::Module!]/[!C::ObjectType!])!]/${id}[IF [!Popup!]]/Form[/IF]" class="[IF [!Popup!]]popup[/IF]">[/IF]
    [IF [!Pos!]=1]<strong>[/IF]
    ${label}
    [IF [!Pos!]=1]</strong>[/IF]
    [IF [!NOLINK!]=]</a>[/IF]
</td>
        [/DEFAULT]
[/SWITCH]
