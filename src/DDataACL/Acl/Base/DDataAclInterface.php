<?php

namespace DDataACL\Acl\Base;
use Zend\EventManager\Event;

/**
 *
 * @author Victor Odiah (vodiahco1@gmail.com)
 */
interface DDataAclInterface {
   
    public function onDeny(Event $event);

}
