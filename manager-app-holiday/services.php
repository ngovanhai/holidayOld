<?php
    require '../conn-shopify.php'; 
    require '../help.php'; 
    require '../vendor/autoload.php';
    
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        if ($action == 'getAllStores') {
            $stores = db_fetch_array("select id, store_name, installed_date from tbl_usersettings where app_id = $appId AND status = 'active' ORDER BY id DESC");
            foreach ($stores as $key => &$value) {
               $value['status'] = 0;
               $value['status_js'] = 0;
               $value['status_cache'] = 0;
            }
            echo json_encode($stores);
            exit;
        }
        if ($action == 'updateDataCache') {
            $shop = $_GET['shop'];
            $shopify = shopifyInit($db, $shop, $appId);
            saveScriptTagId($shop, $shopify, $appId, 'tbl_scripttag');
            updateScriptTag($shop, $shopify, 'tbl_scripttag', $rootLink.'/holidayeffects.js');
            echo json_encode(true);
            exit;
        }
        if ($action == 'deleteDataCache') {
            $shop = $_GET['shop'];
            deleteDataCache(CACHE_PATH . $shop);
            echo json_encode(true);
            exit;
        }
        if ($action == 'updateAllStore') {
            $stores = db_fetch_array("select id, store_name, installed_date from tbl_usersettings where app_id = $appId AND status = 'active'");

            foreach ($stores as $key => $value) {
                $shop = $value['store_name'];
                $shopify = shopifyInit($db, $shop, $appId);
                saveScriptTagId($shop, $shopify, $appId, 'tbl_scripttag');
                updateScriptTag($shop, $shopify, 'tbl_scripttag', $rootLink.'/holidayeffects.js');
            }
            echo json_encode(true);
            exit;
        }
        if ($action == 'getSettingStore') {
            $shop = $_GET['shop'];
            $value = [];
            $value['setting'] = db_fetch_array("SELECT * FROM holiday_events_effects WHERE shop = '$shop'");
            print_r($value); die();
            echo json_encode($value);
            exit;
        }
    }
    
?>  