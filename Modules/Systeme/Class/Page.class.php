<?php
class Page extends genericClass {

	public function save () {
        $this->LastMod = date('c', time());
		if(!$this->Id) {
			$this->MD5 = md5($this->Url);
		}
		genericClass::Save();
    	}
}
?>