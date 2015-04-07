<?php
    include ('includes/application_top.php');
    $config =  dirname(__FILE__)."/feed/config/FeedConfig.php";
    $sPath =  dirname(__FILE__)."/feed/sdk/feed.php";
    require_once($config);

    if(file_exists($sPath)) {
        require_once($sPath);
        $sPluginName = "zen_modules";
        $sPluginPath = dirname(__FILE__).'/feed/plugin/'.$sPluginName.".php";
        /**
         * @var $oFeed Feed
         */
        $oFeed = Feed::getInstance($sPluginPath);
        $request = $_REQUEST['feed'];
        $oFeed->dispatch($request);
        exit();
    }
    header("HTTP/1.0 404 Not Found");
    exit();