<?php
namespace DDataACL;


use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use DDataACL\Acl\DDataAcl;

/**
 * Description of Module
 *
 */
class Module {
    
        public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        //listen to the route event
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this,'onRoute'));
        
        
    }
    
    
    public function onRoute(MvcEvent $e){
        $sm=$e->getApplication()->getServiceManager();
        $config = $sm->get('Config');
       $acl = New DDataAcl($config);
       
       //retrieve the current user role or guest/default
       $userRole = $this->getUserRole();
       //
       $controller = $e->getRouteMatch()->getParam('controller');
       $action = $e->getRouteMatch()->getParam('action');
       
       //test if user role is allowed on the resource
       //if not allowed fire the deny event.
       // the deny event is listened to an handled in DDataACL\Acl\DDataAcl::onDeny
       if(!$acl->isAllowed($userRole,$controller,$action))
       {
         $eventManager = $e->getApplication()->getEventManager(); 
         $eventManager->attach("deny",array($acl,'onDeny'));
         $eventManager->trigger("deny",$this,array('MvcEvent' => $e));
       }
       
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    
    protected function getUserRole(){
        /**
         * implement the login for retieving the user role
         */
        
        //bu default the DDataAcl::DEFAULT_ROLE is returned
        return DDataAcl::DEFAULT_ROLE;
    }
}
