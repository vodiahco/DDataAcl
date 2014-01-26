DDataAcl
========

DDataACL is an MVC based ACL for zend framework 2. It takes advantage to the MVC events to perform access control.
Simply configure your roles and resources in the config and DDataACL will automatically build the ACL tree.

## Requirements
The requirements of this module is listed in composer.json.
## Installation
1. require "DomainData/DDataAcl" : "dev-master", in your composer.json and run composer update
2. Enable the DData module in config/application.config.php file

        "require": {
            "php": ">=5.3.3",
            "zendframework/zendframework": "2.2.*",
            "DomainData/DDataAcl" : "dev-master"

        },
        "repositories": [
            {
            "type":"vcs",
            "url": "https://github.com/vodiahco/DDataAcl.git"
            }
        ]

## Usage 
Simply configure the acl key in the configuration, add the roles and resources and the DDataACL will automatically build the ACL tree.

Please note that when access to a resource is denied by DDAtaACL, a deny event is fired. By default, this event is handled in the DDataACL::onDeny method. This method receives the Zend\Mvc\MvcEvent
as part of the params. You can then implement custom redirect in the DDataACL::onDeny method.


