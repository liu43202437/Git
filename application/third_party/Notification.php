<?php
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');

class Notification {
	protected $iosAppkey				= NULL; 
	protected $iosAppMasterSecret		= NULL;
	protected $androidAppkey			= NULL;
	protected $androidAppMasterSecret	= NULL;
	
	protected $productionMode = "false";	// Set 'production_mode' to 'true' if your app is under production mode

	function __construct($iosKey, $iosSecret, $androidKey, $androidSecret) {
		$this->iosAppkey = $iosKey;
		$this->iosAppMasterSecret = $iosSecret;
		$this->androidAppkey = $androidKey;
		$this->androidAppMasterSecret = $androidSecret;
	}

	function sendIOSBroadcast($title, $content) {
		try {
			$brocast = new IOSBroadcast();
			$brocast->setAppMasterSecret($this->iosAppMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->iosAppkey);
			$brocast->setPredefinedKeyValue("timestamp",        strval(time()));
			$brocast->setPredefinedKeyValue("alert", $title);
			$brocast->setPredefinedKeyValue("production_mode", $this->productionMode);
			$brocast->setPredefinedKeyValue("description", $title);
			$brocast->setCustomizedField("content", $content);
			
			return $brocast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendIOSUnicast($deviceId, $title, $content) {
		try {
			$unicast = new IOSUnicast();
			$unicast->setAppMasterSecret($this->iosAppMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->iosAppkey);
			$unicast->setPredefinedKeyValue("timestamp",        strval(time()));
			$unicast->setPredefinedKeyValue("device_tokens",    $deviceId);		// Set your device tokens here 
			$unicast->setPredefinedKeyValue("alert", $title);
			$unicast->setPredefinedKeyValue("production_mode", $this->productionMode);
			$unicast->setPredefinedKeyValue("description", $title);
			$unicast->setCustomizedField("content", $content);
			
			return $unicast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}
	
	function sendIOSGroupcast($where, $title, $content) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"iostest"}
    	 	 *		]
		 	 *	}
		 	 */
			$filter = array(
				"where" => 	array(
					"and" 	=>  array($where)
				)
			);
					  
			$groupcast = new IOSGroupcast();
			$groupcast->setAppMasterSecret($this->iosAppMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",		$this->iosAppkey);
			$groupcast->setPredefinedKeyValue("timestamp",	strval(time()));
			$groupcast->setPredefinedKeyValue("filter",		$filter);		// Set the filter condition
			$groupcast->setPredefinedKeyValue("alert", 		$title);
			$groupcast->setPredefinedKeyValue("production_mode", $this->productionMode);
			$groupcast->setPredefinedKeyValue("description", $title);
			$groupcast->setCustomizedField("content", $content);
			
			return $groupcast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendIOSFilecast() {
		try {
			$filecast = new IOSFilecast();
			$filecast->setAppMasterSecret($this->iosAppMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->iosAppkey);
			$filecast->setPredefinedKeyValue("timestamp",        strval(time()));

			$filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
			$filecast->setPredefinedKeyValue("badge", 0);
			$filecast->setPredefinedKeyValue("production_mode", $this->productionMode);
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents("aa"."\n"."bb");
			
			return $filecast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendIOSCustomizedcast() {
		try {
			$customizedcast = new IOSCustomizedcast();
			$customizedcast->setAppMasterSecret($this->iosAppMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->iosAppkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        strval(time()));

			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue("alias", "xx");
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue("alias_type", "xx");
			$customizedcast->setPredefinedKeyValue("alert", "IOS 个性化测试");
			$customizedcast->setPredefinedKeyValue("badge", 0);
			$customizedcast->setPredefinedKeyValue("production_mode", $this->productionMode);
			
			return $customizedcast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}
	
	
	// android
	function sendAndroidBroadcast($title, $content) {
		try {
			$brocast = new AndroidBroadcast();
			$brocast->setAppMasterSecret($this->androidAppMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->androidAppkey);
			$brocast->setPredefinedKeyValue("timestamp",        strval(time()));
			$brocast->setPredefinedKeyValue("ticker",           "Android broadcast ticker");
			$brocast->setPredefinedKeyValue("title",            $title);
			$brocast->setPredefinedKeyValue("text",             $content);
			$brocast->setPredefinedKeyValue("after_open",       "go_app");
			$brocast->setPredefinedKeyValue("production_mode",	$this->productionMode);
			$brocast->setExtraField("content", $content);					// [optional]Set extra fields

			return $brocast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendAndroidUnicast($deviceId, $title, $content) {
		try {
			$unicast = new AndroidUnicast();
			$unicast->setAppMasterSecret($this->androidAppMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->androidAppkey);
			$unicast->setPredefinedKeyValue("timestamp",        strval(time()));
			$unicast->setPredefinedKeyValue("device_tokens",    $deviceId);					// Set your device tokens here 
			$unicast->setPredefinedKeyValue("ticker",           "Android unicast ticker");
			$unicast->setPredefinedKeyValue("title",            $title);
			$unicast->setPredefinedKeyValue("text",             $content);
			$unicast->setPredefinedKeyValue("after_open",       "go_app");
			$unicast->setPredefinedKeyValue("production_mode",	$this->productionMode);
			$unicast->setExtraField("content", $content);				// Set extra fields

			return $unicast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}
	
	function sendAndroidGroupcast($where, $title, $content) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"test"},
      	 	 *			{"tag":"Test"}
    	 	 *		]
		 	 *	}
		 	 */
			$filter = 	array(
				"where" => array(
					"and" => array($where)
				)
			);
					  
			$groupcast = new AndroidGroupcast();
			$groupcast->setAppMasterSecret($this->androidAppMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->androidAppkey);
			$groupcast->setPredefinedKeyValue("timestamp",        strval(time()));
			$groupcast->setPredefinedKeyValue("filter",           $filter);						// Set the filter condition
			$groupcast->setPredefinedKeyValue("ticker",           "Android groupcast ticker");
			$groupcast->setPredefinedKeyValue("title",            $title);
			$groupcast->setPredefinedKeyValue("text",             $content);
			$groupcast->setPredefinedKeyValue("after_open",       "go_app");
			$groupcast->setPredefinedKeyValue("production_mode",  $this->productionMode);
			$groupcast->setExtraField("content", $content);				// Set extra fields

			return $groupcast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}

	function sendAndroidFilecast() {
		try {
			$filecast = new AndroidFilecast();
			$filecast->setAppMasterSecret($this->androidAppMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->androidAppkey);
			$filecast->setPredefinedKeyValue("timestamp",        strval(time()));
			$filecast->setPredefinedKeyValue("ticker",           "Android filecast ticker");
			$filecast->setPredefinedKeyValue("title",            "Android filecast title");
			$filecast->setPredefinedKeyValue("text",             "Android filecast text");
			$filecast->setPredefinedKeyValue("after_open",       "go_app");  //go to app
			print("Uploading file contents, please wait...\r\n");
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents("aa"."\n"."bb");
			return $filecast->send();
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
			return false;
		}
	}
}
