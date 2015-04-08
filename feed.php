<?php
    if (!isset($_REQUEST['dataFeed'])) {
        if (isset($_REQUEST['dataExport'])) {
            header('Location: http://daily-feed.com/export/' . $_REQUEST['dataExport']);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
        }
    } else {

        include ('includes/application_top.php');
        $config1 =  dirname(__FILE__)."/feed/config/FeedConfig.php";
        $config2 =  dirname(__FILE__)."/feed/config/ShopConfig.php";
        $sPath   =  dirname(__FILE__)."/feed/sdk/feed.php";
        require_once($config1);
        require_once($config2);
        if(file_exists($sPath)) {
            require_once($sPath);
            $sPluginName = "zen_modules";
            $sPluginPath = dirname(__FILE__).'/feed/plugin/'.$sPluginName.".php";
            /**
             * @var $oFeed Feed
             */
            $oFeed = Feed::getInstance($sPluginPath);
            $request = $_REQUEST['dataFeed'];
            $oFeed->dispatch($request);
            exit();
        }
        header("HTTP/1.0 404 Not Found");
        exit();
}