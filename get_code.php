<?php
date_default_timezone_set('UTC');
require 'vendor/autoload.php';
use sandeepshetty\shopify_api; 
require 'help.php'; 
require 'conn-shopify.php';

$app_settings = db_fetch_row("SELECT * FROM tbl_appsettings WHERE id = $appId");

if (!empty($_GET['shop']) && !empty($_GET['code'])) {
    $shop = $_GET['shop']; //shop name
    //get permanent access token
    $access_token = shopify_api\oauth_access_token(
            $_GET['shop'], $app_settings['api_key'], $app_settings['shared_secret'], $_GET['code']
    );
    
    $installed = checkInstalled($shop, $appId);

    if($installed["installed"]){
        $date_installed = $installed["installed_date"];
        $data_user = [
            "access_token" => $access_token,
            "store_name" => $shop,
            "app_id" => $appId,
            "installed_date" => $date_installed,
            "confirmation_url" => ''
        ];
        db_insert("tbl_usersettings",$data_user);

        $date1 = new DateTime($installed["installed_date"]);
        $date2 = new DateTime("now");
        $interval = date_diff($date1, $date2);
        $diff = (int)$interval->format('%R%a');
        $trialTime = $trialTime - $diff;
        if($trialTime < 0){
            $trialTime = 0;
        }
    } else {
        $data_user = [
            "access_token" => $access_token,
            "store_name" => $shop,
            "app_id" => $appId,
            "installed_date" => date("Y-m-d H:i:s"),
            "confirmation_url" => ''
        ];
        db_insert("tbl_usersettings",$data_user);

        $data_shop = [
            "shop" => $shop,
            "app_id" => $appId,
            "date_installed" => date("Y-m-d H:i:s"),
        ];
        db_insert("shop_installed",$data_shop);
    }
    
    $shopify = shopifyInit($db, $shop, $appId);
    //insert demo data
    $now = date('Y-m-d H:i:s');
    $days = 20;
    $date_plus = date("Y-m-d H:i:s", strtotime("$now + $days day"));
    $data_insert = [
        'event_name'        => "Christmas Day",
        'choose_icons'      => '["snowflake-o"]',
        'choose_images'     => '[]',
        'custom_images'     => '[]',
        'frames'            => '',
        'start_date'        => $now,
        'end_date'          => $date_plus,
        'shop'              => $shop,
    ];
    $id = db_insert('holiday_events_effects', $data_insert);
    //charge fee
    $charge = array(
        "recurring_application_charge" => array(
            "name" => $chargeTitle,
            "price" => $price,
            "return_url" => "$rootLink/charge.php?shop=$shop",
            "test" => $testMode,
            "trial_days" => $trialTime
        )
    );
    if ($chargeType == "one-time") {
        $recu = $shopify("POST", "/admin/application_charges.json", $charge);
		if(is_array($recu) && isset($recu["confirmation_url"])) {
			$confirmation_url = $recu["confirmation_url"];
		} else {
			$confirmation_url = "";
		}
    } else {
        $recu = $shopify("POST", "/admin/recurring_application_charges.json", $charge);
		if(is_array($recu) && isset($recu["confirmation_url"])) {
			$confirmation_url = $recu["confirmation_url"];
		} else {
			$confirmation_url = "";
		}
    }
    $db->query("update tbl_usersettings set confirmation_url = '$confirmation_url' where store_name = '$shop' and app_id = $appId");
	 
    // Gui email cho customer khi cai dat
   // require 'email/install_email.php';
    //add js to shop
    $check = true;
    $putjs1 = $shopify('GET', '/admin/script_tags.json');
    if($putjs1){
        foreach ($putjs1 as $value) {
            if($value["src"] == $rootLink.'/holidayeffects.js'){
                $check = false;
            }
        }
    }
    if($check){
        $putjs = $shopify('POST', '/admin/script_tags.json', array('script_tag' => array('event' => 'onload', 'src' => $rootLink.'/holidayeffects.js')));
    }
    
    //hook when user remove app
    $webhook = $shopify('POST', '/admin/webhooks.json', array('webhook' => array('topic' => 'app/uninstalled', 'address' => $rootLink.'/uninstall.php', 'format' => 'json')));
    
    // webhook cho app
    $updateStorePlanUrl = $rootLink."/services.php?action=getStorePlanChange&shop=".$shop;
    $dataUpdateStorePlan = [
        "webhook" => [
            "topic" => "shop/update",
            "address" =>  $updateStorePlanUrl,
            "format" => "json",
        ]
    ];
    $shopify("POST","/admin/webhooks.json",$dataUpdateStorePlan);

    header('Location: '.$confirmation_url);
}
function checkInstalled($shop, $appId) {
    $query = db_fetch_row("select * from shop_installed where shop = '$shop' and app_id = $appId");
    if(!empty($query)> 0){
        $date_instaled = $query["date_installed"];
        $result = array(
            "installed_date" => $date_instaled,
            "installed" => true
        );
        return $result;
    } else {
        $result = array(
            "installed" => false
        );
        return $result;
    }
}