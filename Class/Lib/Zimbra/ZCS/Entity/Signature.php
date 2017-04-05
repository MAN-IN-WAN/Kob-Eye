<?php

/**
 * A Signature
 *
 * @author LiberSoft <info@libersoft.it>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

class Signature extends \Zimbra\ZCS\Entity
{

    public function __construct($object)
    {
        parent::__construct($object);
        
        if(!$object->count) return false;
        $content = $object->children()->content;
        
        foreach($content->attributes() as $a=>$b){
                $this->set((string) $a, (string) $b);
        }
        $this->set('content', (string)$content[0]);
    }

}