<?php
class	xml2array {
	var $Tableau;
	/**
	*	constructor
	*/
	function xml2array($data, $WHITE=1)
	{
		if (!$data) return false;
			// check for file
		$Link = $data;
		if ( file_exists(ROOT_DIR.$data) ) {
			$data=implode ('', file (ROOT_DIR.$data));
		}
		$this->Test = $data;
    $data = trim($data);
    $vals = $index = $array = array();
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $WHITE);
    if ( !xml_parse_into_struct($parser, $data, $vals, $index) )
	    {
		$this->Error="AUCUNE DONNEE XML";
		klog::l("*********** Erreur ".xml_error_string(xml_get_error_code($parser)));
		/*die(sprintf("XML error in $Link : %s at line %d",
                    xml_error_string(xml_get_error_code($parser)),
                    xml_get_current_line_number($parser)));*/
		}
    xml_parser_free($parser);
    $i = 0; 
	$tagname = $vals[$i]['tag'];
    if ( isset ($vals[$i]['attributes'] ) )
    {
        $array[$tagname]['@'] = $vals[$i]['attributes'];
    } else {
        $array[$tagname]['@'] = array();
    }

    $array[$tagname]["#"] = $this->xml_depth($vals, $i);
	$this->Tableau=$array;
	}

function getResult() {
return $this->Tableau;
}



function xml_depth($vals, &$i) { 
    $children = array(); 

    if ( isset($vals[$i]['value']) )
    {
        array_push($children, $vals[$i]['value']);
    }

    while (++$i < count($vals)) { 

        switch ($vals[$i]['type']) { 

           case 'open': 

                if ( isset ( $vals[$i]['tag'] ) )
                {
                    $tagname = $vals[$i]['tag'];
                } else {
                    $tagname = '';
                }

                if ( isset ( $children[$tagname] ) )
                {
                    $size = sizeof($children[$tagname]);
                } else {
                    $size = 0;
                }

                if ( isset ( $vals[$i]['attributes'] ) ) {
                    $children[$tagname][$size]['@'] = $vals[$i]["attributes"];
                }

                $children[$tagname][$size]['#'] = $this->xml_depth($vals, $i);

            break; 


            case 'cdata':
                array_push($children, $vals[$i]['value']); 
            break; 

            case 'complete': 
                $tagname = $vals[$i]['tag'];

                if( isset ($children[$tagname]) )
                {
                    $size = sizeof($children[$tagname]);
                } else {
                    $size = 0;
                }

                if( isset ( $vals[$i]['value'] ) )
                {
                    $children[$tagname][$size]["#"] = $vals[$i]['value'];
                } else {
                    $children[$tagname][$size]["#"] = '';
                }

                if ( isset ($vals[$i]['attributes']) ) {
                    $children[$tagname][$size]['@'] = $vals[$i]['attributes'];
                }			

            break; 

            case 'close':
                return $children; 
            break;
        } 

    } 

	return $children;

}


/* function by acebone@f2s.com, a HUGE help!
 *
 * this helps you understand the structure of the array xmlize() outputs
 *
 * usage:
 * traverse_xmlize($xml, 'xml_');
 * print '<pre>' . implode("", $traverse_array . '</pre>';
 *
 *
 */ 

function traverse_xmlize($array, $arrName = "array", $level = 0) {

    foreach($array as $key=>$val)
    {
        if ( is_array($val) )
        {
            traverse_xmlize($val, $arrName . "[" . $key . "]", $level + 1);
        } else {
            $GLOBALS['traverse_array'][] = '$' . $arrName . '[' . $key . '] = "' . $val . "\"\n";
        }
    }

    return 1;

}
}


class array2xml
{

	public static function convertToXML( $array, $level = 0 ) {
		if(!is_array($array)) return !empty($array)?'<![CDATA['.$array.']]>':'';
		$str = ($level == 0) ? '' : "\r\n";
		foreach($array as $k => $data) :
			if($level == 0) :
				$str .= self::getTag( $k, $data, $level );
			else :
				foreach($data as $data2) $str .= self::getTag( $k, $data2, $level );
			endif;
		endforeach;
		return $str;
	}

	public static function getTag( $tag, $data, $level ) {
		// Ouverture balise
		$str = '';
		for($i=0; $i<$level; $i++) $str .= "\t";
		$str .= '<' . $tag;
		if(isset($data['@'])) foreach($data['@'] as $attr => $val) $str .= ' ' . $attr . '="' . $val . '"';
		$str .= '>';
		// Données
		$str .= self::convertToXML( $data['#'], $level + 1 );
		// Fermeture balise
		if(is_array($data['#'])) for($i=0; $i<$level; $i++) $str .= "\t";
		$str .= '</' . $tag . '>';
		if($level != 0) $str .= "\r\n";
		return $str;
	}

	var $config	=	array(
		'encoding'	=>	'ISO-8859-15',
		'xmlns'	=>	array(
			'ino'	=>	'http://namespaces.softwareag.com/tamino/response2'
		)
	);
	function array2xml( $array )
	{
		if (!is_array($array)) return false;
		$this->array = $array;
		$this->dom = domxml_new_doc("1.0");
	}
	
	function setEncoding( $enc )
	{
		$this->config['encoding'] = ( $enc != '' )	?	$enc	:	$this->config['encoding'];
	}
	
	function addNamespaces( $assoc )
	{
		$this->config['xmlns'] = array_merge($this->config['xmlns'], $assoc);
	}
	
	function getResult($format = TRUE)
	{
		$doc_root = array_shift( array_keys($this->array) );
		$root_element = $this->dom->create_element($doc_root);
		$this->_recArray2Node($root_element, $this->array[$doc_root]);
		$this->dom->append_child($root_element);

		// check for namespaces ? add each to doc
		if ( is_array($this->used_namespaces) )
			foreach ($this->used_namespaces as $ns)
				$root_element->add_namespace($this->config["xmlns"][ $ns ], $ns);

		// <b>Warning</b>:  dump_mem(): xmlDocDumpFormatMemoryEnc:  Failed to identify encoding handler for character set 'ISO-8859-15'
		return $this->dom->dump_mem($format,$this->config['encoding']);
	}
	
	function _recArray2Node( $parent_node, $array )
	{
		foreach ($array as $key => $value)
		{
			$org_key = $key;
			list( $ns, $key ) = split( ':', str_replace("@","",$org_key) );
			if ( !$key )
				$key = $ns;
			elseif ($ns == "xmlns")
			{
				$this->addNamespaces( array($key => $value) );
				break;
			}else{
				if ( $this->config["xmlns"][ $ns ] )
				{
					$this->used_namespaces[] = $ns;
					$key = $ns.":".$key;
				}
				else
					die("Namespace for $ns does not exist! Use obj->addNamespaces( \$assoc ) for adding.");
			}
			
			if (substr($org_key, 0, 1) == '@')
			{
				// attribute
				$parent_node->set_attribute( $key, $value );
				continue;
			}
			else if ( $key == '#text' || !is_array($value) )
			{
				// text node
				// check if valid text & not empty
				if ( $value=='0' | !empty($value) )
				{
					$element = $this->dom->create_cdata_section($value);
					$parent_node->append_child($element);
				}
				continue;
			} else	{
				// child node
				// check for enumeration
				$enum = FALSE;
				while (list( $k, $v ) = each( $value ))
				{
					if ( is_numeric($k) )
					{
						// enumeration of multiple nodes
						$enum = TRUE;
						$element = $this->dom->create_element($key);
						$this->_recArray2Node($element, $v);
						$parent_node->append_child($element);
					}
				}

				// check for enumeration
				if (  $enum == FALSE )
				{
					$element = $this->dom->create_element($key);
					$this->_recArray2Node($element, $value);
					$parent_node->append_child($element);
				}
			}
		}
	}
}




?>