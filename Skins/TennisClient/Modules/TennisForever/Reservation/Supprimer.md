[STORPROC [!Query!]|R|0|1]
    [IF [!action!]=Je confirme]
        [IF [!R::Valide!]&&[!R::getTotal()!]>0]
            [REDIRECT][!Sys::getMenu(TennisForever/Reservation)!]?msg=La réservation ne peut être supprimée&action=danger[/REDIRECT]
        [ELSE]
            [!R::Delete()!]
            <div class="alert alert-success">
                La réservation a été supprimée avec succès.
            </div>
            //Suppression de la réservation
            [!R::Delete()!]
            [REDIRECT][!Sys::getMenu(TennisForever/Reservation)!]?msg=La réservation a été supprimée avec succes&action=success[/REDIRECT]
        [/IF]
    [/IF]
<div class="row">
    <div class="col-md-12">
        <form action="" method="POST">
            <h1>Supprimer la réservation</h1>
            Êtes vous sur de vouloir la réservation ?
            <input type="submit" class="btn btn-success btn-block" name="action" value="Je confirme"/>
            <a href="/[!Sys::getMenu(TennisForever/Reservation)!]/[!R::Id!]" class="btn btn-danger btn-block">Annuler</a>
        </form>
    </div>
</div>

[/STORPROC]