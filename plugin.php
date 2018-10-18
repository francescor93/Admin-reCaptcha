<?php
/*
Plugin Name: Admin reCaptcha Invisible
Plugin URI: https://github.com/francescor93/Admin-reCaptcha.git
Description: New version of armujahid's "Admin reCaptcha". It uses reCaptcha V2 Invisible and cURL to send request.
Version: 1.0
Author: Francesco Rega
Author URI: https://www.francescorega.eu
*/

// Die if not requiring from YOURLS
if (!defined('YOURLS_ABSPATH')) { die; exit; }

// Add custom action to pre login
yourls_add_action('pre_login_username_password', 'ReFraReCaptcha_validate');

// Function to check if reCaptcha is valid
function ReFraReCaptcha_validate() {

	// Get private key
	$privKey = yourls_get_option('ReFraReCaptcha_privKey');

	// Prepare cURL request
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$params = http_build_query(array(
		'secret' => $privKey, 
		'response' => $_POST['g-recaptcha-response'],
		'remoteip' => $_SERVER['REMOTE_ADDR']
	));	
	$request = curl_init();
	curl_setopt($request, CURLOPT_URL, $url);
	curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($request, CURLOPT_POST, true);
	curl_setopt($request, CURLOPT_POSTFIELDS, $params);
	
	// Send cURL request
	$response = curl_exec($request);
	
	// If response is available
	if ($response) {
		
		// Decode JSON
		$json = json_decode($response);
		
		// If having "success" key return true
		if ($json->success) {
			return true;
		}
		
		// Else return false and terminates
		else {
			yourls_do_action('login_failed');
			yourls_login_screen($error_msg = 'reCaptcha validation failed');
			exit;
			return false;
		}
	}
	else {
		yourls_do_action('login_failed');
		yourls_login_screen($error_msg = 'reCaptcha validation failed');
		exit;
		return false;
	}
}

// Add custom action after loading plugins
yourls_add_action('plugins_loaded', 'ReFraReCaptcha_init');

// Register plugin page in administrative area
function ReFraReCaptcha_init() {
    yourls_register_plugin_page('adminReCaptchaInvisible', 'Admin reCaptcha Invisible Settings', 'ReFraReCaptcha_config' );
}

// Define plugin page
function ReFraReCaptcha_config() {

	// After clicking Save
	if (isset($_POST['ReFraReCaptcha_save'])) {
			
		// Verify nonce
		yourls_verify_nonce( 'ReFraReCaptcha_nonce' );
		
		// Get values from form
		$pubkey = $_POST['ReFraReCaptcha_publicKey'];
		$privkey = $_POST['ReFraReCaptcha_privateKey'];
		
		// If public key option already exists update it, else add it
		if (yourls_get_option('ReFraReCaptcha_pubKey') !== false) {
			yourls_update_option('ReFraReCaptcha_pubKey', $pubkey);
		} 
		else {
			yourls_add_option('ReFraReCaptcha_pubKey', $pubkey);
		}
		
		// If private key option already exists update it, else add it
		if (yourls_get_option('ReFraReCaptcha_privKey') !== false) {
			yourls_update_option('ReFraReCaptcha_privKey', $privkey);
		} 
		else {
			yourls_add_option('ReFraReCaptcha_privKey', $privkey);
		}
		
		// Show confirm message
		echo "Saved";
	}
    
	// Get form values
    $nonce = yourls_create_nonce('ReFraReCaptcha_nonce');
    $pubkey = yourls_get_option('ReFraReCaptcha_pubKey');
    $privkey = yourls_get_option('ReFraReCaptcha_privKey', "" );
	
	// Show form
    echo '
	<h2>Admin reCaptcha Invisible plugin settings</h2>
	<form method="post">
		<input type="hidden" name="nonce" value="' . $nonce . '">
		<p>
			<label for="ReFraReCaptcha_publicKey">reCaptcha site key: </label>
			<input type="text" id="ReFraReCaptcha_publicKey" name="ReFraReCaptcha_publicKey" value="' . $pubkey . '">
		</p>
		<p>
			<label for="ReFraReCaptcha_privateKey">reCaptcha secret key: </label>
			<input type="text" id="ReFraReCaptcha_privateKey" name="ReFraReCaptcha_privateKey" value="' . $privkey . '">
		</p>
		<input type="submit" name="ReFraReCaptcha_save" value="Save">
	</form>';
}

// Add custom action in html head tag
yourls_add_action('html_head', 'ReFraReCaptcha_addJS');

// Add the JavaScript for reCaptcha widget
function ReFraReCaptcha_addJS() {
	?>
	<script>
		function sendForm() {
			$("#login form").submit();
		}
		$(document).ready(function() {
			if ($("#login").length) {
				$("#submit").addClass("g-recaptcha");
				$("#submit").attr('data-sitekey','<?php echo yourls_get_option('ReFraReCaptcha_pubKey'); ?>');
				$("#submit").attr('data-callback','sendForm');
				$("#submit").attr('name','btnsubmit');
				$("#submit").attr('id','btnsubmit');
				$.getScript("https://www.google.com/recaptcha/api.js");
			}
		});
	</script>
	<?php
}
?>
