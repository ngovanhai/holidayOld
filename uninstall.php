<?php

require 'conn-shopify.php';
session_start();

unset($_SESSION['shop']);
$webhookContent = "";

$webhook = fopen('php://input', 'rb');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}

fclose($webhook);
$webhookContent = json_decode($webhookContent);
if (isset($webhookContent->myshopify_domain)) {
    $shop = $webhookContent->myshopify_domain;
    $db->query('DELETE FROM tbl_usersettings WHERE store_name = "' . $shop . '" and app_id = ' . $appId);

	// Gui email cho customer khi uninstalled
	//  require 'email/uninstall_email.php';	
} else if (isset($webhookContent->domain)) {
    $shop = $webhookContent->domain;
    $db->query('DELETE FROM tbl_usersettings WHERE store_name = "' . $shop . '" and app_id = ' . $appId);

    // Gui email cho customer khi uninstalled
	//  require 'email/uninstall_email.php';	
}


