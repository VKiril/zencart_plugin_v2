<?php
require('includes/application_top.php');

$mainPath = dirname(dirname(__FILE__));
$sPath = $mainPath."/feed/config/FeedConfig.php";
require($sPath);

$feedifyConfig = new FeedConfig();

$attributesToDisplay = array(
    "FEED_FIELD_TAX_RATE"      => "• Tax Rate (Default):",
    "FEED_FIELD_SHIPPING_COST" => "• Shipping Cost (Default):",
    "FEED_FIELD_COUPON"        => "• Coupon:",
    "FEED_FIELD_COLOR"         => "• Color:",
    "FEED_FIELD_SIZE"          => "• Size:",
    "FEED_FIELD_GENDER"        => "• Gender:",
    "FEED_FIELD_MATERIAL"      => "• Material:",
    "FEED_FIELD_EAN_1"         => "• Ean Field:",
    "FEED_FIELD_GOOGLE"        => "• Google Field:",
    "FEED_FIELD_SUBTITLE"      => "• Subtitle Field:",
    "FEED_FIELD_ISBN"          => "• ISBN Field:",
    "FEED_FIELD_BASE_UNIT"     => "• Base Unit Field:",
    "FEED_FIELD_BASE_PRICE"    => "• Base Price Field:",
    "FEED_FIELD_UVP"           => "• Manufacturer recommended price:",
    "FEED_FIELD_YATEGOO"       => "• Yategoo Category Field:"
);

$dbProductsColumns = $feedifyConfig->getDatabaseColumns("'".TABLE_PRODUCTS."', '".TABLE_PRODUCTS_DESCRIPTION."', '".TABLE_CATEGORIES."'");
$feedifyFields = array_merge($feedifyConfig::$gReturn, $feedifyConfig->getQueryFields());

$zones = $feedifyConfig->getTaxZones();
$attributes = $feedifyConfig->getAttributesGroups();

asort($dbProductsColumns);
asort($zones);
asort($attributes);

$temp = array();
foreach ($attributes as $elements) {
    $temp[$elements['products_options_id']] = $elements;
}
$attributes = $temp;


//beginning connection to feed
if(isset($_POST['FEED_USER']) && isset($_POST['FEED_PASS']) && isset($_POST['FEED_SECRET'])){

    $feedifyConfig->remove();
    $feedifyConfig->install();

    $sPath =  $mainPath."/feed/sdk/feed.php";
    if(!file_exists($sPath)) {
        $blSetShopModuleError = true;
    }
    require_once($sPath);

    $sPluginName = "zen_modules";
    $sPluginPath = $mainPath."/feed/plugin/".$sPluginName.".php";
    $oRegisterEvent = new FeedEvent();
    $oNewsEvent = new FeedNewsEvent();

    Feed::getInstance($sPluginPath)->eventManager->dispatchEvent("onRegisterFeed", $oRegisterEvent);
    Feed::getInstance($sPluginPath)->eventManager->dispatchEvent("onNewsFeed", $oNewsEvent);

    if($oRegisterEvent->getResponse()->getStatus() == 204) {
        $blCheckOK = true;
        $FeedifyNews = $oNewsEvent->getNews();
        $response_form = 'succes';
    } else {
        $response_form = 'error';
        $blCheckError = true;
        $FeedifyError = $oRegisterEvent->getResponse()->getStatusMsg();
    }
} else {
    $error = 'error';
    $response_form = '';
    $blCheckError = false;
}

?>
    <!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
        <script language="javascript" src="includes/menu.js"></script>
        <script language="javascript" src="includes/general.js"></script>
        <script type="text/javascript">
            <!--
            function init()
            {
                cssjsmenu('navbar');
                if (document.getElementById)
                {
                    var kill = document.getElementById('hoverJS');
                    kill.disabled = true;
                }
            }
            // -->
        </script>
    </head>
    <body onload="init()" style="background-image: url('../images/backgroung/back-gnd.jpg')">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
    <!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" >

    <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0"  >
    <form name="myedit" action="feed_view.php" method="post" >
    <table>
    <tr>
        <td>• Username:</td>
        <td><input type="text" name="FEED_USER" style="margin-left: 2px; width: 130px;" value="<?php echo $feedifyConfig->getConfig('FEED_USER');?>"></td>
    </tr>
    <tr>
        <td>• Password:</td>
        <td><input type="password" name="FEED_PASS" style="margin-left: 2px; width: 130px;" value="<?php echo $feedifyConfig->getConfig('FEED_PASS');?>"></td>
    </tr>
    <tr>
        <td>• Secret:</td>
        <td><input type="text" name="FEED_SECRET" style="margin-left: 2px; width: 130px;" value="<?php echo $feedifyConfig->getConfig('FEED_SECRET');?>"><br/></td>
    </tr>
    <tr>
        <td>• Tax Zone:</td>
        <td>
            <select name="FEED_TAX_ZONE"  style="width: 130px; margin-left: 2px">
                <?php
                foreach($zones as $key=>$zone) {
                    echo '<option value='.$key; if ($feedifyConfig->getConfig('FEED_TAX_ZONE') == $key ) {echo " selected ";} echo '>'.$zone.'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>


    <tr><td><br></td></tr>

    <tr>
        <td colspan="2"><h1 style="color:#3f608b">- Export Configurations:</h1></td>
    </tr>

    <?php foreach ($attributesToDisplay as $key=>$item) { ?>
        <tr>
            <td><?php echo $item ?></td>
            <td>
                <select name="<?php echo $key.'_1' ?>"  style="width: 130px; margin-left: 2px">
                    <option value="N" <?php if ($feedifyConfig->getConfig($key.'_1') == 'N') echo "selected"; ?> >-- empty --</option>
                    <?php
                    foreach($dbProductsColumns as $key_2=>$column) {
                        echo '<option value='.$column['table_name'].';'.$column['column_name'];
                        if ($feedifyConfig->getConfig($key.'_1') == $column['table_name'].';'.$column['column_name']) {
                            echo " selected ";
                        }
                        echo'>'.$column['column_name'].' ('.$column['table_name'].')</option>';
                    }
                    ?>
                </select>
            </td>
            <td> or
                <select name="<?php echo $key.'_2';?>"  style="width: 130px; margin-left: 2px" >
                    <option value="N" <?php if ($feedifyConfig->getConfig($key.'_2') == 'N') echo "selected"; ?> >-- empty --</option>
                    <?php
                    foreach($attributes as $attribute) {
                        echo '<option value='.$attribute['products_options_id'];
                        if ($feedifyConfig->getConfig($key.'_2') == $attribute['products_options_id']) {
                            echo " selected ";
                        }
                        echo'>'.$attribute['products_options_name'].'</option>';
                    }
                    ?>
                </select>
            </td>
            <td> or <input type="text" name="<?php echo $key.'_3';?>" style="margin-left: 2px; width: 130px;" value="<?php echo $feedifyConfig->getConfig($key.'_3');?>"></td>
        </tr>
    <?php } ?>

    <br/>

    <tr>
        <td>• Packet Size:</td>
        <td>
            <select name="FEED_FIELD_PACKET_SIZE_1"  style="width: 130px; margin-left: 2px">
                <option value="N" <?php if ($feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_1') == 'N') echo "selected"; ?> >-- empty --</option>
                <?php
                foreach($dbProductsColumns as $key=>$column) {
                    echo '<option value='.$column['table_name'].';'.$column['column_name'];
                    if ($feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_1') == $column['table_name'].';'.$column['column_name']) {
                        echo " selected ";
                    }
                    echo'>'.$column['column_name'].' ('.$column['table_name'].')</option>';
                }
                ?>
            </select>
        </td>
        <td >
            or
            <select name="FEED_FIELD_PACKET_SIZE_2"  style="width: 130px; margin-left: 2px" >
                <option value="N" <?php if ($feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_2') == 'N') echo "selected"; ?> >-- empty --</option>
                <?php
                foreach($attributes as $attribute) {
                    echo '<option value='.$attribute['products_options_id'];
                    if ($feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_2') == $attribute['products_options_id']) {
                        echo " selected ";
                    }
                    echo'>'.$attribute['products_options_name'].'</option>';
                }
                ?>
            </select>

        </td>
        <td>  or  </td>

    </tr>
    <tr>
        <tr>
            <td style="text-align: right">
                - width &nbsp; :
            </td>
            <td>
                <input style="width: 130px;" type="text" name="FEED_FIELD_PACKET_SIZE_WIDTH" size="number" value="<?php echo $feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_WIDTH');?>"> cm
            </td>
        </tr>
        <tr>
            <td style="text-align: right">
                - length :
            </td>
            <td>
                <input style="width: 130px;" type="text" name="FEED_FIELD_PACKET_SIZE_LENGTH" size="number" value="<?php echo $feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_LENGTH');?>"> cm
            </td>
        </tr>
        <tr>
            <td style="text-align: right">
                - height :
            </td>
            <td>
                <input style="width: 130px;" type="text" name="FEED_FIELD_PACKET_SIZE_HEIGHT" size="number" value="<?php echo $feedifyConfig->getConfig('FEED_FIELD_PACKET_SIZE_HEIGHT');?>"> cm
            </td>
        </tr>
    </tr>

    <tr>
        <td>
            • Delivery Time:
        </td>
        <td>
            <select name="FEED_DTIME_1"  style="width: 130px; margin-left: 2px">
                <option value="N" <?php if ($feedifyConfig->getConfig('FEED_DTIME_1') == 'N') echo "selected"; ?> >-- empty --</option>
                <?php
                foreach($dbProductsColumns as $key=>$column) {
                    echo '<option value='.$column['table_name'].';'.$column['column_name'];
                    if ($feedifyConfig->getConfig('FEED_DTIME_1') == $column['table_name'].';'.$column['column_name']) {
                        echo " selected ";
                    }
                    echo'>'.$column['column_name'].' ('.$column['table_name'].')</option>';
                }
                ?>
            </select>
        </td>
        <td >
            or
            <select name="FEED_DTIME_2"  style="width: 130px; margin-left: 2px" >
                <option value="N" <?php if ($feedifyConfig->getConfig('FEED_DTIME_2') == 'N') echo "selected"; ?> >-- empty --</option>
                <?php
                foreach($attributes as $attribute) {
                    echo '<option value='.$attribute['products_options_id'];
                    if ($feedifyConfig->getConfig('FEED_DTIME_2') == $attribute['products_options_id']) {
                        echo " selected ";
                    }
                    echo'>'.$attribute['products_options_name'].'</option>';
                }
                ?>
            </select>

        </td>
        <td>  or  </td>
    </tr>
    <tr>
        <td style="text-align: right"> - from:  </td><td> <input style="width: 130px;" type="text" name="FEED_DTIME_FROM" size="number" value="<?php echo $feedifyConfig->getConfig('FEED_DTIME_FROM');?>"></td>
    </tr>
    <tr>
        <td style="text-align: right"> - to:  </td><td> <input style="width: 130px;" type="text" name="FEED_DTIME_TO" size="number" value="<?php echo $feedifyConfig->getConfig('FEED_DTIME_TO');?>"></td>
    </tr>
    <tr>
        <td style="text-align: right"> - type:</td>
        <td><select name="FEED_DTIME_TYPE" style="width: 130px; margin-left: 2px">
                <option value="D" <?php if ($feedifyConfig->getConfig('FEED_DTIME_TYPE') == 'D') echo "selected"; ?> >days   </option>
                <option value="W" <?php if ($feedifyConfig->getConfig('FEED_DTIME_TYPE') == 'W') echo "selected"; ?> >weeks  </option>
                <option value="M" <?php if ($feedifyConfig->getConfig('FEED_DTIME_TYPE') == 'M') echo "selected"; ?> >months </option>
            </select>
        </td>
    </tr>

<!----------------------------------------------------------------------------------------------------------------->

<?php
$shippingParameters = array(
    "FEED_SHIPPING_ADDITION"       =>  "• shipping outside the EU:",
    "FEED_SHIPPING_PAYPAL_OST"     =>  "• Shipping costs for paypal Austria:",
    "FEED_SHIPPING_COD"            =>  "• Shipping cost for Cash on Deliver:",
    "FEED_SHIPPING_CREDIT"         =>  "• Shipping cost for Creditcard:",
    "FEED_SHIPPING_PAYPAL"         =>  "• Shipping costs for paypal:",
    "FEED_SHIPPING_TRANSFER"       =>  "• Shipping costs Ready for Transfer:",
    "FEED_SHIPPING_DEBIT"          =>  "• Shipping costs ELV:",
    "FEED_SHIPPING_ACCOUNT"        =>  "• Shipping costs for purchase orders:",
    "FEED_SHIPPING_MONEYBOOKERS"   =>  "• Shipping costs at Moneybookers:",
    "FEED_SHIPPING_CLICK_BUY"      =>  "• Shipping costs Click & Buy:",
    "FEED_SHIPPING_GIROPAY"        =>  "• Shipping costs Giropay:"
);
?>

    <tr>
        <td colspan="2"><h1 style="color:#3f608b">- Special Delivery Prices:</h1></td>
    </tr>
    <?php
    foreach ($shippingParameters as $shippingKey=>$shippingItem) {
        ?>
        <tr>
            <td>•<?php echo $shippingItem ?></td>
            <td>
                <select name="<?php echo $shippingKey.'_1'?>"  style="width: 130px; margin-left: 2px">
                    <option value="N" <?php if ($feedifyConfig->getConfig( $shippingKey.'_1') == 'N') echo "selected"; ?> >-- empty --</option>
                    <?php
                    foreach($dbProductsColumns as $key=>$column) {
                        echo '<option value='.$column['table_name'].';'.$column['column_name'];
                        if ($feedifyConfig->getConfig($shippingKey.'_1') == $column['table_name'].';'.$column['column_name']) {
                            echo " selected ";
                        }
                        echo'>'.$column['column_name'].' ('.$column['table_name'].')</option>';
                    }
                    ?>
                </select>
            </td>
            <td>
                or
                <input type="text" name="<?php echo $shippingKey.'_2'?>"
                       style="margin-left: 2px; width: 130px;"
                       value="<?php echo $feedifyConfig->getConfig($shippingKey.'_2');?>">
                <br/>
            </td>
        </tr>
        <?php
    }
    ?>

    <tr>
        <td>• Delivery comment:</td>
        <td><textarea name="FEED_SHIPPING_COMMENT" style="margin-left: 2px; width: 130px;"><?php echo $feedifyConfig->getConfig('FEED_SHIPPING_COMMENT');?></textarea><br/></td>
    </tr>

    <tr><td><br></td></tr>

<!-------------------------------------------------------------------------------------------------------------------->

    <tr>
        <td><h1 style="color:#3f608b">- Attributes Extra:</h1></td>
    </tr>
    <tr>
        <td>• Condition:</td>
        <td><select name="FEED_FIELD_CONDITION_1"  style="width: 130px; margin-left: 2px" >
                <option value="N" <?php if ($feedifyConfig->getConfig('FEED_FIELD_CONDITION_1') == 'N') echo "selected"; ?> >-- empty --</option>
                <option value="1" <?php if ($feedifyConfig->getConfig('FEED_FIELD_CONDITION_1') == '1') echo "selected"; ?> >new</option>
                <option value="2" <?php if ($feedifyConfig->getConfig('FEED_FIELD_CONDITION_1') == '2') echo "selected"; ?> >used</option>
                <!--<option value="3" <?php /*if ($feedifyConfig->getConfig('FEED_FIELD_CONDITON_1') == '3') echo "selected"; */?> >used</option>-->
            </select>
        </td>
        <td>
            or
            <input type="text" name="FEED_FIELD_CONDITION_2" style="margin-left: 2px; width: 130px;" value="<?php echo $feedifyConfig->getConfig('FEED_FIELD_CONDITION_2');?>"/>
            <br/>
        </td>
    <tr>
        <td><h1 style="color:#3f608b">- Tracking Pixel:</h1></td>
    </tr>
    <tr>
        <td>• Client Id:</td>
        <td><input type="text" name="FEED_CLIENT_ID" style="margin-left: 2px; width: 130px;" value="<?php echo $feedifyConfig->getConfig('FEED_CLIENT_ID');?>"></td>
    </tr>

    <tr style="display: none">
        <td>• Products Id field</td>
        <td><select name="FEED_TRACKING_PRODUCTS_ID"  style="width: 130px; margin-left: 2px">
                <option value="ModelOwn" selected >ModelOwn </option>
                <?php
                /*echo '<option value='.$key; if ($feedifyConfig->getConfig('FEED_TRACKING_PRODUCTS_ID') == $key) {echo " selected ";} echo '>'.$column.'</option>';
                foreach($feedifyFields as $key=>$column) {
                    echo '<option value='.$key; if ($feedifyConfig->getConfig('FEED_TRACKING_PRODUCTS_ID') == $key) {echo " selected ";} echo '>'.$column.'</option>';
                }*/
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <td>• Tracking Pixel Enable:</td>
        <td>
            <input type="checkbox" name="FEED_TRACKING_PIXEL_STATUS" value="Y" <?php if ($feedifyConfig->getConfig('FEED_TRACKING_PIXEL_STATUS') == 'Y') {echo " checked ";}?> >
        </td>
    </tr>
    <tr>
        <td><br><input name="chek" value="Connect" type="submit" style="margin-left: 30px;font-size: 15px;" ></td>
    </tr>
    </table>
    </form>
    </table></td>
    </tr>
    </table></td>
    <!-- body_text_eof //-->
    </tr>
    </table>
    <?php
    if($response_form == 'succes') echo '<h1 style="color:#3f608b;margin-left: 30px">• Succes!</h1>';
    else if($response_form == 'error') echo '<h1 style="color:#3f608b;margin-left: 30px">• Something goes wrong on connection to feed</h1>';
    if($connectionValidate === false) { echo '<h1 style="color:#3f608b;margin-left: 30px">• Some Fields are Required!</h1>'; }
    ?>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
    <br>

    </body>
    </html>

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>