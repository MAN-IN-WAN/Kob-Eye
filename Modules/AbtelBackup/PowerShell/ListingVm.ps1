## Set variables for computer name and get all running VMs-------------------------------------------------------
$Vs = $Env:ComputerName
$Vms = Get-VM | Where-Object {$_.State -eq 'Running'}

## Check to see if there are any running VMs---------------------------------------------------------------------
If ($Vms.count -ne 0)
{
        Remove-Item -path "\\SAMBA\intranet\listeVMTEMP.txt"
        New-Item -path "\\SAMBA\intranet" -NAME "listeVMTEMP.txt" -ItemType "file"
   
        ForEach ($Vm in $Vms)
        {
        
             #Initialisation des variables----------------------------------------------------------------------------
             $VMName = $VM.name 
             $VMVersion = $VM.version 
             $VMGeneration = $VM.Generation
             $VMId = $VM.Id 
             $Temp = get-VM -VMName $VMName | select-object VMId | Get-VHD
             $vhdsize = $temp.filesize

              #Concaténation et affichage----------------------------------------------------------------------------
		write-host($VMName + "(*.*)" + $VMVersion + "(*.*)" + $VMGeneration + "(*.*)" + $VMId + "(*.*)" + $vhdsize)
       
}
}

       
       
       
       
       
        

