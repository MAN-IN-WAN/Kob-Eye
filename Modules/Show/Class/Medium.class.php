<?php

class Medium extends genericClass {
	
	function Delete() {
		if(substr($m->Medium, 0, 5) == 'Home/') unlink((getcwd()."/".$m->Medium));
		return parent::Delete();
	}
}