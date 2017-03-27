<?php
class AbtelTools extends Module {
	function init (){
		parent::init();
	}
	
        /**
         * Surcharge de la fonction Check
         * Initialize les tables de communications entre la gestion et le parc.
         */
        function CheckBak () {
           parent::Check();

	   //Teste les tables de communication du parc.
	   $gp = genericClass::createInstance('AbtelTools','GestionParc');
	   $gp->check();
	}
	
}
?>
