<?php

namespace DDataACL\Acl\Base;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\EventManager\Event;
/**
 * AbstractDDataAcl
 * Abstract class for the ACL classes
 *
 * @author Victor Odiah (vodiahco1@gmail.com)
 */
class AbstractDDataAcl extends Acl implements DDataAclInterface {
    protected $rolesConfig;
    protected $resourceConfig;
    protected $redirectRoute;
    
    const ROLES_KEY = "roles";
    const RESOURCE_KEY = "resources";
    const ACL_KEY = "acl";
    const DEFAULT_ROLE = "guest";
    const REDIRECT_ROUTE_KEY = "redirect_route";
    
    
    public function __construct($config) {
        $this->prepareConfig($config);
        $this->buildAclTree();
    }
    
    /**
     * This method attemps to load the ACL configuration 
     * @param array $config
     * @throws \Exception
     */
    protected function prepareConfig($config)
    {
        $config=$this->prePrepareConfig($config);
        if(is_array($config) && array_key_exists(self::ACL_KEY, $config))
        {
          $this->prepareConfigFromArray($config);  
        } 
        else{
        throw new \Exception('Invalid Config, no '.self::ACL_KEY.' key found');    
        }
            
    }

    /**
     * This method is called before prepareConfig(). 
     * This where you will prepare your config to array, if the config is in a different format
     * @param array $config
     * @return type
     */
    protected function prePrepareConfig($config){
        return $config;
    }
    
    
    /**
     * This method loads the array config
     * @param array $config
     * @throws \Exception
     */
    protected function prepareConfigFromArray($config){
         if (!isset($config[self::ACL_KEY][self::ROLES_KEY]) || !isset($config[self::ACL_KEY][self::RESOURCE_KEY])) {
            throw new \Exception('Invalid ACL Config');
        }
       $rolesConfig = $config[self::ACL_KEY][self::ROLES_KEY];
       $resourceConfig = $config[self::ACL_KEY][self::RESOURCE_KEY];
       $redirectRoute = (isset($config[self::ACL_KEY][self::REDIRECT_ROUTE_KEY])?$config[self::ACL_KEY][self::REDIRECT_ROUTE_KEY] : array());
       $this->setRolesConfig($rolesConfig); 
       $this->setResourceConfig($resourceConfig); 
       $this->setRedirectRoute($redirectRoute);
       $this->buildAclTree();
    }
    
    /**
     * This method builds the ACL tree based on the config
     */
    protected function buildAclTree(){
        $this->buildRoles()
             ->buildRecources();
    }
    
    /**
     * This method builds the specified roles from the config
     * @return \DDataACL\Acl\Base\AbstractDDataAcl
     */
    protected function buildRoles(){  
        $rolesConfig = $this->getRolesConfig();
        foreach ($rolesConfig as $name => $parent) {
            if (!$this->hasRole($name)) {
                if (empty($parent)) {
                    $parent = array();
                } else {
                    
                    $parent = (is_array($parent) && count($parent)>0)? $parent : explode(',', $parent);
                }

                $this->addRole(new Role($name), $parent);
            }
        }

        return $this;
    }
    
    /**
     * This method builds the resources from the config
     * @return \DDataACL\Acl\Base\AbstractDDataAcl
     * @throws \Exception
     */
    protected function buildRecources(){
        $resourceConfig = $this->getResourceConfig();
        foreach ($resourceConfig as $permission => $controllers) {
            foreach ($controllers as $controller => $actions) {
                if ('all' == $controller) {
                    $controller = null;
                } else {
                    if (!$this->hasResource($controller)) {
                        $this->addResource(new Resource($controller));
                    }
                }

                foreach ($actions as $action => $role) {
                    if ('all' == $action) {
                        $action = null;
                    }

                    if ('allow' == $permission) {
                        $this->allow($role, $controller, $action);
                    } elseif ('deny' == $permission) {
                        $this->deny($role, $controller, $action);
                    } else {
                        throw new \Exception('Invalid permission: ' . $permission);
                    }
                }
            }
        }

        return $this;
    }
    
    /**
     * This method returns the roles config
     * @return array
     */
    public function getRolesConfig() {
        return $this->rolesConfig;
    }

    
    /**
     * This method sets the roles config
     * @param array $rolesConfig
     */
    protected function setRolesConfig($rolesConfig) {
        $this->rolesConfig = $rolesConfig;
    }

    /**
     * This method returns the resources config
     * @return array
     */
    public function getResourceConfig() {
        return $this->resourceConfig;
    }

    /**
     * This method sets the resources config
     * @param array $resourceConfig
     */
    protected function setResourceConfig($resourceConfig) {
        $this->resourceConfig = $resourceConfig;
    }
    
    /**
     * This method returns the redirect route from the config
     * @return array
     */
    public function getRedirectRoute() {
        return $this->redirectRoute;
    }

    /**
     * This method sets the redirect route config
     * @param array $redirectRoute
     */
    protected function setRedirectRoute($redirectRoute) {
        $this->redirectRoute = $redirectRoute;
    }

    
    /**
     * This method is the listener for the deny event.
     * The deny event is called when access to a resource is denied by the ACL.
     * 
     * @param \Zend\EventManager\Event $event
     * @throws \Exception
     */
    public function onDeny(Event $event) {
        $mvcEvent = $event->getParam('MvcEvent');
        $routeArray=  $this->getRedirectRoute();
        if(!isset($routeArray['params']) || !isset($routeArray['options']))
             throw new \Exception('Invalid redirection route');
            
        $params = $routeArray['params'];
        $options = $routeArray['options'];
      $response = $mvcEvent->getResponse();
      $router = $mvcEvent->getRouter();
      $url=$router->assemble($params,$options);
      $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
   
    }
    
    /**
     * This method is a proxy for getRedirectRoute()
     * @return array
     */
    protected function getRedirectParams(){
        return $this->getRedirectRoute();
    }

}
