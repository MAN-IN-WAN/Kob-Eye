<?php
class Illustration extends genericClass {
    /**
     *replace the src attribute to data-src attribute
     *to prevent the iframe to load before the complete loading of the page
     */
    public function getIframe() {
        return str_replace("src="," data-src=",$this->Iframe);
    }
}
