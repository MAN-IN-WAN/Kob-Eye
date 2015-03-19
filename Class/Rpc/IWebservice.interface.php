<?php
Interface IWebservice {
	/**
	 * return wsdl
	 */
	function getwsdl($sys);
	function soapServer($sys);
}
