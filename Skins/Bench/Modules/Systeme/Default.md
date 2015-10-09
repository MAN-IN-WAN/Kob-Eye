[!DEBUG::Sys::Skin!]
//SIMPLE PARENT 0,1
[STORPROC Systeme/Group/User/[!Systeme::User::Id!]|G|0|1]
    <div style="color: green">OK</div>
[/STORPROC]

//SIMPLE PARENT 0,N
[STORPROC Systeme/User/Menu/8|G|0|1]OK[/STORPROC]

//RECURSIV PARENT 0,1
[STORPROC Systeme/Group/*/Group/User/[!Systeme::User::Id!]|G|0|1]OK[/STORPROC]