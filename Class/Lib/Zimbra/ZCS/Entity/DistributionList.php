<?php

/**
 * A Zimbra distribution list.
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

class DistributionList extends \Zimbra\ZCS\Entity
{

    private $members = array();

    public function __construct($list)
    {
        parent::__construct($list);

        foreach ($list->children() as $lc){
            if($lc['n'] == 'zimbraMailForwardingAddress')
                $this->members[] = (string)$lc;
        }
        foreach ($list->children()->dlm as $data) {
            $this->members[] = (string) $data;
        }
        array_unique($this->members);
        $this->set('members', $this->members);
    }

    public function getMembers()
    {
        return $this->members;
    }

}
