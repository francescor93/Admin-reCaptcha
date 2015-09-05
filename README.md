Admin reCaptcha
====================

Plugin for [YOURLS](http://yourls.org) `v1.7 (possibly earlier, not tested) adding reCaptcha to private admin page`. 

Description
-----------
Spam protection for private YOURLS admin interface. Any unauthenticated user is required to pass a reCaptcha in order to login into Admin Interface

Installation
------------
1. 	Copy this folder in `/user/plugins`.
2. 	Go to the Plugins administration page ( *eg* `http://sho.rt/admin/plugins.php` ) and activate the plugin.
3. 	Sign up for a reCaptcha key at [Google](https://www.google.com/recaptcha/admin)
4. 	Go to Manage Plugins > Admin reCaptcha Settings and paste in your reCaptcha keys and save them.
5. 	Open `includes/functions-html.php` 
5.1 Paste `<script src='https://www.google.com/recaptcha/api.js'></script>` at the end of function
    `yourls_html_head` before ending of head tag i.e. before `</head>`
5.2 Paste `<div class="g-recaptcha" data-sitekey="YOUR SITE KEY FROM GOOGLE RECAPTCHA"></div>` in function `yourls_login_screen`
	before p tag surrounding submit button. i.e. before 
	`<p style="text-align: right;">
				<input type="submit" id="submit" name="submit" value="<?php yourls_e( 'Login' ); ?>" class="button" />
	</p>` 
5. Have fun!

License
-------
YOURLS' license, aka *"Do whatever the hell you want with it"*.

Contact
-------
Email: mail@armujahid.me
Website: http://armujahid.me