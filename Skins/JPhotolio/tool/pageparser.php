<?php

include_once "simple_html_dom.php";

function file_get_contents_curl($url) 
{
	$ch = curl_init();
			
	curl_setopt($ch, CURLOPT_USERAGENT, "UMozilla/5.0 (Windows NT 6.1; rv:10.0.1) Gecko/20100101 Firefox/10.0.1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_URL, $url);
	    
	$data = curl_exec($ch);
	curl_close($ch);
	     
	return $data;
}

/**
 * This function will parse HTML, find #main selector, title 
 * than build response (json) ajax page request need.
 * 
 * @param String $url
 */
function page_parser($url)
{
	$content = file_get_contents_curl($url);
		
	$html = new simple_html_dom();
	$html->load($content);
				
	$main = $html->find('#main',0)->innertext;		
	$title = $html->find('title',0)->plaintext;
				
	echo json_encode(array(
			'main'		=> $main,
			'title'		=> $title,
			'url'		=> $url,
			'id'		=> 1
	));		
}

page_parser('http://localhost/lab/html/index.html');
