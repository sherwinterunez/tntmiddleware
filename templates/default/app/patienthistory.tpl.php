<h1>Patient History</h1>
<div id="formval_%formval%" style="height:auto;width:auto;"></div>
<br style="clear:both;" />

<?php 
//pre(array('$vars'=>$vars,'$post'=>$post,'$_SESSION'=>$_SESSION));

//if(!empty($form)) {
//	echo $form;
//}
?>

<script>
	var myForm_%formval%, formData_%formval%;

	function doOnLoad_%formval%() {
		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 130, inputWidth: 250},
			{type: "fieldset", label: "Welcome", inputWidth: 500, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "form"},
				{type: "hidden", name: "formid", value: ""},
				{type: "hidden", name: "buttonid", value: ""},
				{type: "radio", name: "type", label: "Already have account", labelWidth: "auto", position: "label-right", checked: true, value: 1, list:[
					{type: "input", name: "login", label: "Login", value: "p_rossi"},
					{type: "password", name: "password", label: "Password", value: "123"},
					{type: "checkbox", name: "remember", label: "Remember me", checked: true}
				]},
				{type: "radio", name: "type", label: "Not registered yet", labelWidth: "auto", position: "label-right", value: 2, list:[
					{type: "input", label: "Full Name", value: "Patricia D. Rossi"},
					{type: "input", label: "E-mail Address", value: "p_rossi@example.com"},
					{type: "input", label: "Login", value: "p_rossi"},
					{type: "password", label: "Password", value: "123"},
					{type: "password", label: "Confirm Password", value: "123"},
					{type: "checkbox", label: "Subscribe on news"}
				]},
				{type: "radio", name: "type", label: "Guest login", labelWidth: "auto", position: "label-right", value: 3, list:[
					{type: "select", label: "Account type", options:[
						{text: "Admin", value: "admin"},
						{text: "Organiser", value: "org"},
						{text: "Power User", value: "poweruser"},
						{text: "User", value: "user"}
					]},
					{type: "checkbox", label: "Show logs window"}
				]},
			]}
		];

		myForm_%formval% = new dhtmlXForm("formval_%formval%", formData_%formval%);

		var myTab = srt.getTabUsingFormVal('%formval%');

		myTab.toolbar.getToolbarData('newpatienthistory').onClick = function() {
			showMessage('myTab.formval: '+myTab.formval,5000);
			showMessage('newpatienthistory.func',5000);
			showMessage(arguments+', length: '+arguments.length,5000);

			for(var i=0;i<arguments.length;i++) {
				showMessage('arguments['+i+'] => '+arguments[i],5000);
			}

			return false;
		}

		//showMessage('myTab.formval: '+myTab.formval,5000);

		//srt.dummy.apply(this,[myTab,newpatienthistory]);
	}

	doOnLoad_%formval%();

</script>
