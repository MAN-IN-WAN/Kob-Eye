<?php
class Page extends genericClass {

	public function save () {
        $this->LastMod = date('c', time());
		$this->MD5 = md5($this->Url);
		return genericClass::Save();
	}
}
?>