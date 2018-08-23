## If logging is configured, start log
If ($LogPath)
{
    $LogFile = ("Hyper-V-Backup-{0:yyyy-MM-dd-HH-mm-ss}.log" -f (Get-Date))
    $Log = "$LogPath\$LogFile"

    ## If the log file already exists, clear it
    $LogT = Test-Path -Path $Log

    If ($LogT)
    {
        Clear-Content -Path $Log
    }

    Add-Content -Path $Log -Value "****************************************"
    Add-Content -Path $Log -Value "$(Get-Date -Format G) Log started"
    Add-Content -Path $Log -Value ""
}

## Set variables for computer name and get all running VMs-------------------------------------------------------
$Vs = $Env:ComputerName
$Vms = Get-VM | Where-Object {$_.State -eq 'Running'}

## Check to see if there are any running VMs---------------------------------------------------------------------
If ($Vms.count -ne 0)
{
    ##Remove-Item -path "\\SAMBA\intranet\listeVMTEMP.txt"
    ##New-Item -path "\\SAMBA\intranet" -NAME "listeVMTEMP.txt" -ItemType "file"

    ForEach ($Vm in $Vms){
        #Initialisation des variables----------------------------------------------------------------------------
        $VMName = $VM.name
        write-host($VMName)

        ##ADD-content -path "\\SAMBA\intranet\listeVMTEMP.txt" -value $VMName
    }
}

