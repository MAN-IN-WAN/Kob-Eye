[CmdletBinding()]
Param(
    [parameter(Mandatory=$True)]
    [alias("BackupTo")]
    $Backup,
    [alias("VMToBackup")]
    $ListeVM
    )


## Obtenir les VM actives et création des variables de temps et d'emplacement------------------------------------
$Vs = $Env:ComputerName
$Vms = Get-VM | Where-Object {$_.State -eq 'Running'}
$date = Get-Date -Uformat "%Y-%m-%d-%H-%M"
$PathTest = "$Backup\Backup.$date.h"



#Vérification de l'existence du chemin et création sinon.--------------------------------------------------------

        If ($PathTest -eq $False)
        {
            New-Item "$Backup\Backup.$date.h" -ItemType Directory -Force
        }
        
#Traitement de la liste des VMs----------------------------------------------------------------------------------

Write-Host " VM en cours de sauvegarde :  $ListeVM "

If ($Vms.count -ne 0)
{   
        ForEach ($Vm in $Vms)
        {
        
        
        #Initialisation des variables----------------------------------------------------------------------------
        $VMName = $VM.name
        If ($arr -contains $ListeVM){
            $SnapshotName = "$VM.name-Backup.$date"
        
        #Création et export du Snapshot en tant que VM---------------------------------------------------------------------
             Write-Host "création snapshot"
             $t = Checkpoint-VM -Name $VM.name -SnapshotName $SnapshotName
             new-job Export-VMSnapshot  -Name $SnapshotName -VMName $VMName  -Path "$Backup\Backup.$date.h" 
        
        #Suppression du Snapshot---------------------------------------------------------------------------------
             Get-VM $Vm.name | Remove-VMSnapshot -Name $SnapshotName
             Write-Host "snapshot supprimé"
      
			
     
  
       
       
       
       
       
        }
        }
        }

        

