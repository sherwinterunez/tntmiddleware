<?php
/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App Module
*
* Date: November 27, 2015
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

global $apptemplate;

//echo '/* ';
//echo "app.mod.inc.js";
//echo ' */';

//echo "\n\n/*\n\n";
//pre($vars);
//echo "\n\n*/\n\n";

?>
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Javascript Utilities
*
* Created: November 27, 2015
*
*/

var loginForm = [
	{type: "settings", position: "label-left", labelWidth: 90, inputWidth: 220, offsetLeft: 10, offsetTop: 5},
	{type: "hidden", name: "user_hash", value: ""},
	{type: "input", label: "Username", value: "", offsetTop: 20, name:"username", required:true},
	{type: "password", label: "Password", value: "", name:"password", required:true},
	{type: "block", width: 320, blockOffset:0, list:[
	{type: "checkbox", label: "Remember me", checked: false, className:"loginRemember", offsetLeft:0, name:"remember"},
	{type: "newcolumn", offset:0},
	{type: "button", value: "Proceed", className:"loginProceed", offsetLeft:110, name:"proceed"},
	]},
];

srt.login = function() {
	dhxWins = new dhtmlXWindows();

	w1 = dhxWins.createWindow("w1", 20, 30, 350, 200);
	w1.setText("Login");
	w1.button("close").disable();
	w1.center();
	w1.denyPark();
	w1.denyResize();

	myForm = w1.attachForm();
	myForm.load(loginForm);

	myForm.enableLiveValidation(true);

	//myForm.setItemFocus('username');

	/*if(!$("input[name='username']").is(":focus")) {
		$("input[name='username']").focus();
	}*/

	myForm.attachEvent("onButtonClick", function(name){
		if(!this.validate()) {
			alert("Invalid username/password.");
			return false;
		}

		var user_hash = sha1(base64_encode(myForm.getItemValue('password')) + base64_encode(myForm.getItemValue('username')));

		myForm.setItemValue('user_hash',user_hash);

		$(this.base[0]).ajaxSubmit({
			url: "/"+settings.router_id+"/verify/",
			dataType: 'json',
			semantic: true,
			obj: {o:this,myForm:myForm},
			exclude: ['password'],
			success: function(data, statusText, xhr, $form, obj){
				var $ = jQuery;

				if(data.error_code&&data.error_message) {

					showAlertError('ERROR('+data.error_code+') '+data.error_message);

					if(settings.debug) {
						console.log(data.error_code+' => '+data.error_message);

						if(data.backtrace) {
							console.log(data.backtrace);
						}

						if(data.dberrors) {
							console.log(data.dberrors);
						}

						if(data.dbqueries) {
							console.log(JSON.stringify(data.dbqueries));
						}
					}
				}

				if(data.code==0&&data.message) {
					showMessage(data.message);
					setTimeout(function(){
						window.location = settings.site+'/app/';
					},500);
				}
			}
		});


	});
};

jQuery(document).ready(function($) {
	srt.login();
});
