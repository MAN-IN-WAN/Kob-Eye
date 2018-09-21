<?php

/**
 * A Zimbra mail account.
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

class Ressource extends \Zimbra\ZCS\Entity
{

    static $statuses = array(
        'active' => 'Attivo',
        'closed' => 'Disattivo',
    );

    public function getStatus()
    {
        return self::$statuses[$this->get('zimbraAccountStatus')];
    }

    public static function getStatuses()
    {
        return self::$statuses;
    }

}
