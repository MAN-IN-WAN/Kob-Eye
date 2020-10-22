<?php

class TypePaiement extends genericClass {

	/**
	 * Retourne un plugin Cadref / Instance
	 * @return	Implémentation d'interface
	 */
	public function getPlugin() {
		$plugin = Plugin::createInstance('Cadref','TypePaiement', $this->Plugin);
		$plugin->setConfig( $this->PluginConfig );
		return $plugin;
	}

	
}