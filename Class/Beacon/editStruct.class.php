<?php
class editStruct extends Beacon {
    var $Data = '';

	function editStruct() {
	}

	function Generate() {
		parent::Generate();
		switch ($this->Beacon) {
			case "HEADER":
				switch($this->Vars) {
					case 'CSS' :
						if (is_object($GLOBALS["Systeme"]->Header)) {
						    $GLOBALS["Systeme"]->Header->addCss($this->getString());
                        			}
					break;
					case 'JS' :
			                        if (is_object($GLOBALS["Systeme"]->Header)) {
						    $GLOBALS["Systeme"]->Header->addJs($this->getString());
                        			}
					break;
					case '404' :
						$GLOBALS["Systeme"]->redirectionErreur404();
					break;
					default:
						if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->Add($this->getString(),"");
					break;
				}
			break;
			case "REDIRECT":
			break;
			case "DIE":
			break;
			case "CLOSE":
			break;
			case "CONNEXION":
				$this->Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$this->Vars);
				$Login = $Vars[0];
				$Pass = $Vars[1];
                                //echo( $Login . "..." . $Pass );
				$GLOBALS["Systeme"]->Connection->Connect($Login,$Pass);
			break;
			case "TITLE":
				if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setTitle($this->getString());
			break;
			case "BODY":
				if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setBody($this->getString());
			break;
            case "HTML":
                if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setHtml($this->getString());
                break;
			case "KEYWORDS":
				if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setKeywords($this->getString());
			break;
			case "REPLYTO":
				if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setReplyTo($this->getString());
			break;
			case "DESCRIPTION":
				if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setDescription(preg_replace("#<(.*?)>#","",html_entity_decode($this->getString())));
			break;
			case "CLASSIFICATION":
				if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->setClassification($this->getString());
			break;
			case "INI":
				ini_set ( $this->Vars, $this->getString() );
				$this->ChildObjects="";
			break;
		}
		return ;
	}

	function getString() {
		//$this->Data = Parser::getContent($this->ChildObjects);
		return $this->Data;
	}

	function Affich() {
		switch ($this->Beacon) {
			CASE "REDIRECT":
				$p = $this->getString();
				if (preg_match("#^http#",$p)){
					header("Location:".$p);
				}
				else {
                    if(preg_match("#\.htm#",$_SERVER["REQUEST_URI"]) )
                    {
                        $T = explode("?",$p);
                        if ( sizeof($T) > 1 ) {
                            $pt = $T[0] . ".htm";
                            $p = $pt . "?". $T[1];
                        }else {
                            $pt = $p . ".htm";
                            $p = $pt."?";
                        }
                    }
                    header("Location: http://".$_SERVER["HTTP_HOST"]."/".$p);
                }
				$GLOBALS["Systeme"]->Close();
				die();
				break;                          
			CASE "DIE":
				$p = $this->getString();
				die($p);
			break;
			CASE "CLOSE":
				$GLOBALS["Systeme"]->close();
				$p = $this->getString();
				die($p);
			break;
			CASE "CONNEXION":
/*				$Vars = explode("|",$this->Vars);
				$Login = $Vars[0];
				$Pass = $Vars[1];
                                //echo( $Login . "..." . $Pass );
				$GLOBALS["Systeme"]->Connection->Connect($Login,$Pass);*/
				break;
		}
	}

}


?>