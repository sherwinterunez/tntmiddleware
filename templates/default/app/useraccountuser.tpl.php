<?php

global $applogin;

$readonly = true;

$method = false;

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method=='useraccountedit'||$method=='useraccountnewuser') {
	$readonly = false;
}

$sysadmin = false;

if($params['userinfo']['user_id']==1) {
	$sysadmin = true;
}

$access = $applogin->getAccess();

$savecancel = false;

$toolbars = array();

if(in_array('useraccountnewrole',$access)) {
	$toolbars[] = 'useraccountnewrole';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountnewrole';
}

/*
if(in_array('useraccounteditrole',$access)) {
	$toolbars[] = 'useraccountedit';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountedit';	
}

if(in_array('useraccountdeleterole',$access)) {
	$toolbars[] = 'useraccountdelete';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountdelete';		
}
*/

if(in_array('useraccountnewuser',$access)) {
	$toolbars[] = 'useraccountnewuser';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountnewuser';		
}

if(in_array('useraccountedituser',$access)) {
	$toolbars[] = 'useraccountedit';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountedit';
}

if(in_array('useraccountdeleteuser',$access)) {
	$toolbars[] = 'useraccountdelete';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountdelete';	
}

if($savecancel) {
	$toolbars[] = 'useraccountsave';
	$toolbars[] = 'useraccountcancel';
} else {
	//$disabledtb[] = 'useraccountsave';
	//$disabledtb[] = 'useraccountcancel';	
}

?>
<style>
	#formdiv_%formval% #useraccountuser {
		display: block;
		/*border: 1px solid #f00;*/
	}
	#formdiv_%formval% #useraccountuserform_%formval% {
		display: block;
		padding: 10px;
		overflow: hidden;
		overflow-y: scroll;
		/*border: 1px solid #f00;*/
	}
</style>
<div id="useraccountmain">
	<div id="useraccountuser" class="navbar-default-bg">
		<div id="useraccountuserform_%formval%"></div>
	</div>
</div>
<!--
<?php pre(array('$vars'=>$vars)); ?>
-->
<script>

	$("#formdiv_%formval% #useraccountmain").parent().css({'overflow':'hidden'});

	function validatepass_%formval%(data,name) {
		var myform = this.obj().getForm();
		var user_pass1 = myform.getItemValue('user_pass1');
		var user_pass2 = myform.getItemValue('user_pass2');

		if(user_pass1!=user_pass2) {
			return false;
		}

		return true;
	}

	function useraccountuser_%formval%() {

		var myToolbar = <?php echo json_encode($toolbars); ?>

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		<?php /*if($sysadmin) { ?>
		myTab.toolbar.disableOnly(['useraccountedit','useraccountdelete','useraccountsave']);
		<?php } else { ?>
		myTab.toolbar.disableOnly(['useraccountdelete','useraccountsave']);
		<?php }*/ ?>

		myTab.toolbar.resetAll();

		//alert('hello, sherwin!');

		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 150, inputWidth: 250},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "useraccount"},
				{type: "hidden", name: "formid", value: "useraccountuser"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "roleid", value: <?php echo !empty($params['userinfo']['role_id']) ? json_encode($params['userinfo']['role_id']) : '""'; ?>},
				{type: "hidden", name: "userid", value: <?php echo !empty($params['userinfo']['user_id']) ? json_encode($params['userinfo']['user_id']) : '""'; ?>},
			]},
			{type: "fieldset", name: "userrole", label: "User Role", inputWidth: 500, list:[
				<?php if($method=='useraccountedit'&&!in_array('useraccountchangerole',$access)) { ?>
				{type: "combo", name: "user_role", label: "Role", required:false, readonly:true, filtering:true, options: <?php echo !empty($params['allroles']) ? json_encode($params['allroles']) : '[]'; ?>},
				<?php } else { ?>
				{type: "combo", name: "user_role", label: "Role", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:true, filtering:true, options: <?php echo !empty($params['allroles']) ? json_encode($params['allroles']) : '[]'; ?>},
				<?php } ?>
			]},
			{type: "fieldset", name: "newuser", label: "New User", inputWidth: 500, list:[
				{type: "hidden", name: "user_hash", value: <?php echo !empty($params['userinfo']['user_hash']) ? json_encode($params['userinfo']['user_hash']) : '""'; ?>},
				{type: "hidden", name: "new_hash", value: ""},
				<?php if($method=='useraccountedit'&&!in_array('useraccountchangeuserlogin',$access)) { ?>
				{type: "input", name: "user_login", label: "User Login", required:false, readonly:true, value: <?php echo !empty($params['userinfo']['user_login']) ? json_encode($params['userinfo']['user_login']) : '""';  ?>},
				<?php } else { ?>
				{type: "input", name: "user_login", label: "User Login", validate:"NotEmpty", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($params['userinfo']['user_login']) ? json_encode($params['userinfo']['user_login']) : '""';  ?>},
				<?php } ?>
				{type: "password", name: "user_pass1", label: "Password", validate:"NotEmpty", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: ""},
				<?php if(!$readonly) { ?>
				{type: "password", name: "user_pass2", label: "Confirm Password", validate:"NotEmpty", validate:"validatepass_%formval%", required:<?php echo !$readonly ? 'true' : 'false'; ?>, value: ""},
				<?php } ?>
				{type: "input", name: "user_email", label: "Email Address", validate:"ValidEmail", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($params['userinfo']['user_email']) ? json_encode($params['userinfo']['user_email']) : '""';  ?>},
			]},
			{type: "fieldset", name: "userinfo", label: "User Information", inputWidth: 500, list:[
				{type: "input", name: "user_fname", label: "First Name", validate:"NotEmpty", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($params['userinfo']['content']['user_fname']) ? json_encode($params['userinfo']['content']['user_fname']) : '""'; ?>},
				{type: "input", name: "user_mname", label: "Middle Name", readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($params['userinfo']['content']['user_mname']) ? json_encode($params['userinfo']['content']['user_mname']) : '""'; ?>},
				{type: "input", name: "user_lname", label: "Last Name", validate:"NotEmpty", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($params['userinfo']['content']['user_lname']) ? json_encode($params['userinfo']['content']['user_lname']) : '""'; ?>},
			]},
		];

		if(typeof(myForm_%formval%)!='null'&&typeof(myForm_%formval%)!='undefined'&&myForm_%formval%!=null) {
			try {
				myForm_%formval%.unload();
				myForm_%formval% = undefined;
			} catch(e) {
				console.log(e);
			}
		}

		var myForm = myForm_%formval% = new dhtmlXForm("useraccountuserform_%formval%", formData_%formval%);

		<?php if($method!='useraccountnewuser') { ?>
		myForm.setItemValue('user_pass1',computeHash('*',myForm.getItemValue('user_login')));
		<?php } ?>

		<?php if($readonly) { ?>

			<?php if($sysadmin) { ?>
			myTab.toolbar.disableOnly(['useraccountdelete','useraccountsave','useraccountcancel']);
			<?php } else { ?>
			myTab.toolbar.disableOnly(['useraccountsave','useraccountcancel']);

				<?php if(!in_array('useraccountedituser',$access)) { ?>
					myTab.toolbar.disableItem('useraccountedit');
				<?php } ?>

				<?php if(!in_array('useraccountdeleteuser',$access)) { ?>
					myTab.toolbar.disableItem('useraccountdelete');
				<?php } ?>

			<?php } ?>

		<?php } else { ?>
		myForm.setItemValue('user_pass2',myForm.getItemValue('user_pass1'));
		myTab.toolbar.enableOnly(['useraccountsave','useraccountcancel']);
		<?php } ?>

		//myTab.toolbar.showOnly(myToolbar);	

		//myTab.toolbar.disableOnly(disabledtb);

		myForm.enableLiveValidation(true);

		myForm.attachEvent("onBeforeChange", function (name, old_value, new_value){
		    //showMessage("onBeforeChange: ["+name+"] "+name.length+" / {"+old_value+"} "+old_value.length,5000);
		    return true;
		});

		myForm.attachEvent("onChange", function (name, value){
		    //showMessage("onChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

			myChanged_%formval% = true;

		});

		myForm.attachEvent("onInputChange", function(name, value, form){
		    //showMessage("onInputChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

			myChanged_%formval% = true;
		});

		myForm.attachEvent("onFocus", function(id,value){
			//showMessage("onFocus: "+id,5000);
		});

		myForm.attachEvent("onValidateError", function(id,value){
			var msg;

			if(id=='user_login') {
				msg = 'Please enter User Login. This field is required.';
			} else if(id=='user_pass1') {
				msg = 'Please enter Password. This field is required.';
			} else if(id=='user_pass2') {
				if(typeof(value)=='string' && value!='') {
					msg = 'Please make sure the password is the same.';					
				} else {
					msg = 'Please enter Confirm Password. This field is required. ';					
				}
			} else if(id=='user_email') {
				msg = 'Please enter proper Email Address (eg. joshua@yahoo.com). This field is required.';
			} else if(id=='user_fname') {
				msg = 'Please enter First Name. This field is required.';
			} else if(id=='user_lname') {
				msg = 'Please enter Last Name. This field is required.';
			}

			this.setNote(id,{text:msg});

			//showErrorMessage(msg,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			//this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('useraccountnewrole').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm_%formval%;

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=useraccountrole&module=useraccount&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #useraccountmain").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('useraccountnewuser').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm_%formval%;

			var roleid = 0;

			var userid = 0;

			var treeid = myTree_%formval%.getSelectedItemId();

			var arr = treeid.split('|');

			roleid = parseInt(arr[0]);

			if(arr[1]) {
				userid = parseInt(arr[1]);
			}

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=useraccountuser&module=useraccount&method="+id+"&formval=%formval%&roleid="+roleid,
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #useraccountmain").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('useraccountedit').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm_%formval%;

			var roleid = myForm.getItemValue('roleid');
			var userid = myForm.getItemValue('userid');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=useraccountuser&module=useraccount&method="+id+"&formval=%formval%&roleid="+roleid+"&userid="+userid,
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #useraccountmain").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('useraccountsave').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm_%formval%;

			myForm.trimAllInputs();

			if(!myForm.validate()) return false; 

			var user_hash = computeHash(myForm.getItemValue('user_pass2'), myForm.getItemValue('user_login'));

			if(computeHash('*',myForm.getItemValue('user_login'))!=myForm.getItemValue('user_pass2')) {
				myForm.setItemValue('new_hash',computeHash(myForm.getItemValue('user_pass2'),myForm.getItemValue('user_login')));
			}

			showSaving();

			//showMessage('Validation: '+myForm.validate());

			myForm.setItemValue('method', id);

			var obj = {o:this,id:id};

			$("#useraccountuserform_%formval%").ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: obj,
				exclude: ['user_pass1','user_pass2','user_hash','user_role_new_value'],
				success: function(data, statusText, xhr, $form, obj){
					var $ = jQuery;

					hideSaving();

					if(data.error_code&&data.error_message) {

						//hideSaving();

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

						if(data.error_code==255) {
							setTimeout(function(){
								window.location = settings.site+'/login/';
							},2000);
						}
					}

					if(data.error_code) {
					} else 
					if(data.html) {
					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {
	
							var roleid = data.roleid ? data.roleid : 0;
							var userid = data.userid ? data.userid : 0;

							if(data.xml) {
								myTree_%formval%.deleteItem('0|0');

								myTree_%formval%.parse(data.xml,"xml"); 

								myTree_%formval%.selectItem(roleid+'|'+userid,true);
							}

							showAlert(data.return_message);
						}
					}

				}
			});

			return false;
		};

		myTab.toolbar.getToolbarData('useraccountdelete').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			
			var roleid = 0;

			var userid = 0;

			var candelete = false;

			var treeid = myTree_%formval%.getSelectedItemId();

			var myForm = myForm_%formval%;

			var arr = treeid.split('|');

			roleid = parseInt(arr[0]);

			if(arr[1]) {
				userid = parseInt(arr[1]);
			}

			if(userid==1) {
				showAlertError('Cannot delete System Administrator\'s account.');
				return false;
			}

			showConfirmWarning('Are you sure you want to delete this User?',function(val){

				if(val) {
					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=useraccountuser&module=useraccount&method="+id+"&formval=%formval%&userid="+userid,
					}, function(ddata,odata){

						if(ddata.return_code) {
							if(ddata.return_code=='SUCCESS') {

								if(ddata.xml) {
									myTree_%formval%.deleteItem('0|0');

									myTree_%formval%.parse(ddata.xml,"xml"); 

									myTree_%formval%.selectItem(1,true);									
								}

								showAlert(ddata.return_message);

							}
						}

					});
				}
			});

		};

		myTab.toolbar.getToolbarData('useraccountcancel').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm_%formval%;

			var treeid = myTree_%formval%.getSelectedItemId();

			myTree_%formval%.selectItem(treeid,true);
		};

	}

	useraccountuser_%formval%();

</script>