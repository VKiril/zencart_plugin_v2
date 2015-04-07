1. Copy all files from the "copy_this" folder and put it in shop directory


2. Open the file: '[your ZenCart shop directory]\includes\filenames.php'
	- and add following line :
		define('FILENAME_FEED','feed_view.php');

3. Open the file '[your ZenCart shop directory]\[admin directory]\includes\languages\english.php'
	- and add following line :
		define('BOX_MODULES_FEED', 'Feed');

4. Open the file '[your ZenCart shop directory]\includes\templates\template_default\templates\tpl_product_info_display.php'
	- and add following line :
		require(dirname(dirname($check_path))."/feed/tracking_pixel/tp_product_info.php");

5. Open the file '[your ZenCart shop directory]\includes\templates\template_default\templates\tpl_checkout_success_default.php'
	- and add following line :
		require (dirname(dirname($check_path)).'/feed/tracking_pixel/tp_checkout_success.php');

6. Go to your "ZenCart Admin Page" and select: Admin Access Management -> Admin Page Registration.
	Type following in the corresponding fields :
		*Page Key  		=>  feed
		*Page Name  		=>  BOX_MODULES_FEED
		*Page Filename  	=>  FILENAME_FEED
		*Menu 			=>  Modules
		*Display on menu? 	=>  'true'
	then press "insert" button

7.  Now in Modules will appear a new item (Feed).
##############################################################################
################################For Testing  ####################################

 [yourshopname]/feed.php?feed[secret]=secret_and_username_hashed_with_md5&feed[fnc]=getFeed
 [yourshopname]/feed.php?feed[secret]=secret_and_username_hashed_with_md5&feed[fnc]=getOrderProducts&feed[args][id]=1
 [yourshopname]/feed.php?feed[secret]=secret_and_username_hashed_with_md5&feed[fnc]=getProduct&feed[args][id]=1_3-5_4-1

 secret_and_username_hashed_with_md5 = md5(getApiSecret() . getApiUsername())