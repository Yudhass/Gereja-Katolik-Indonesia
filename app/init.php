<?php 

if(!session_id()) session_start();

require_once 'core/Logger.php';
require_once 'core/App.php';
require_once 'core/Router.php'; 
require_once 'core/Controller.php';
require_once 'core/Config.php';
require_once 'core/Database.php';
require_once 'core/Validator.php';
require_once 'core/Helper.php';
require_once 'core/SessionManager.php';
// require_once 'core/Flasher.php';