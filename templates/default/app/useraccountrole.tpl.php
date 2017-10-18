<?php

global $applogin;

$readonly = true;

$method = false;

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method=='useraccountedit'||$method=='useraccountnewrole') {
	$readonly = false;
}

$sysadmin = false;

if($vars['params']['rolesinfo']['role_id']==1) {
	$sysadmin = true;
}

$access = $applogin->getAccess();

$savecancel = false;

$toolbars = array();
$disabledtb = array();

if(in_array('useraccountnewrole',$access)) {
	$toolbars[] = 'useraccountnewrole';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountnewrole';
}

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

if(in_array('useraccountnewuser',$access)) {
	$toolbars[] = 'useraccountnewuser';
	$savecancel = true;
} else {
	$disabledtb[] = 'useraccountnewuser';		
}

/*
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
*/

if($savecancel) {
	$toolbars[] = 'useraccountsave';
	$toolbars[] = 'useraccountcancel';
} else {
	//$disabledtb[] = 'useraccountsave';
	//$disabledtb[] = 'useraccountcancel';	
}

?>
<style>
	#formdiv_%formval% #useraccountrole {
		display: block;
		/*border: 1px solid #f00;*/
	}
	#formdiv_%formval% #useraccountroleform_%formval% {
		display: block;
		padding: 10px;
		overflow: hidden;
		overflow-y: scroll;
		/*border: 1px solid #f00;*/
	}
</style>
<div id="useraccountmain">
	<div id="useraccountrole" class="navbar-default-bg">
		<div id="useraccountroleform_%formval%"></div>
	</div>
</div>
<!--
<?php 

pre(array('$_SESSION'=>$_SESSION));

pre(array('$vars'=>$vars)); 

?>
-->
<script>

	$("#formdiv_%formval% #useraccountmain").parent().css({'overflow':'hidden'});

	function useraccountrole_%formval%() {

		var myToolbar = <?php echo json_encode($toolbars); ?>

		//var disabledtb = <?php echo json_encode($disabledtb); ?>

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		myTab.toolbar.resetAll();

		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 250},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "useraccount"},
				{type: "hidden", name: "formid", value: "useraccountrole"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "roleid", value: <?php echo !empty($params['rolesinfo']['role_id']) ? json_encode($params['rolesinfo']['role_id']) : '""'; ?>},
			]},
			{type: "fieldset", name: "newrole", label: "Role", inputWidth: 500, list:[
				{type: "input", name: "role_name", label: "Role", validate:"NotEmpty", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($vars['params']['rolesinfo']['role_name']) ? json_encode($vars['params']['rolesinfo']['role_name']) : '""'; ?>},
				{type: "input", name: "role_desc", label: "Description", validate:"NotEmpty", required:<?php echo !$readonly ? 'true' : 'false'; ?>, readonly:<?php echo $readonly ? 'true' : 'false'; ?>, value: <?php echo !empty($vars['params']['rolesinfo']['role_desc']) ? json_encode($vars['params']['rolesinfo']['role_desc']) : '""'; ?>},
			]},
			<?php if(!$sysadmin) { ?>
			{type: "block", width: 1500, blockOffset: 0, offsetTop:5, list:<?php echo !empty($params['rules']) ? json_encode($params['rules']) : '[]'; ?>}
			<?php } ?>			
		];

		if(typeof(myForm_%formval%)!='null'&&typeof(myForm_%formval%)!='undefined'&&myForm_%formval%!=null) {
			try {
				myForm_%formval%.unload();
				myForm_%formval% = undefined;
			} catch(e) {
				console.log(e);
			}
		}

		var myForm = myForm_%formval% = new dhtmlXForm("useraccountroleform_%formval%", formData_%formval%);

		<?php if($readonly) { ?>

			<?php if($sysadmin) { ?>
			myTab.toolbar.disableOnly(['useraccountedit','useraccountdelete','useraccountsave','useraccountcancel']);
			<?php } else { ?>
			myTab.toolbar.disableOnly(['useraccountsave','useraccountcancel']);

				<?php if(!in_array('useraccounteditrole',$access)) { ?>
					myTab.toolbar.disableItem('useraccountedit');
				<?php } ?>

				<?php if(!in_array('useraccountdeleterole',$access)) { ?>
					myTab.toolbar.disableItem('useraccountdelete');
				<?php } ?>

			<?php } ?>

		//myTab.toolbar.disableOnly(['useraccountdelete','useraccountsave','useraccountcancel']);
		<?php } else { ?>
		myTab.toolbar.enableOnly(['useraccountsave','useraccountcancel']);
		myForm.setItemFocus('role_name');
		<?php } ?>

		//myTab.toolbar.showOnly(myToolbar);	

		//myTab.toolbar.disableItems(disabledtb);

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

			if(id=='role_name') {
				msg = 'Please enter Role. This field is required.';
			} else if(id=='role_desc') {
				msg = 'Please enter Role Description. This field is required.';
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
			var userid = 0;

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=useraccountrole&module=useraccount&method="+id+"&formval=%formval%&roleid="+roleid,
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #useraccountmain").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('useraccountsave').onClick = function(id,formval) {
			//showMessage("toolbarx: "+id,5000);

			var myForm = myForm_%formval%;

			myForm.trimAllInputs();

			if(!myForm.validate()) return false; 

			/*var user_hash = computeHash(myForm.getItemValue('user_pass2'), myForm.getItemValue('user_login'));

			if(computeHash('*',myForm.getItemValue('user_login'))!=myForm.getItemValue('user_pass2')) {
				myForm.setItemValue('new_hash',computeHash(myForm.getItemValue('user_pass2'),myForm.getItemValue('user_login')));
			}*/

			showSaving();

			//showMessage('Validation: '+myForm.validate());

			myForm.setItemValue('method', id);

			var obj = {o:this,id:id};

			$("#useraccountroleform_%formval%").ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: obj,
				exclude: [],
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
	
							var treeid = data.treeid ? data.treeid : 0;

							if(data.xml) {
								myTree_%formval%.deleteItem('0|0');

								myTree_%formval%.parse(data.xml,"xml"); 

								myTree_%formval%.selectItem(treeid,true);								
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

			if(roleid&&!userid) {
				var si = explode(',',myTree_%formval%.getSubItems(treeid));
				//showMessage("subitems: "+si,5000);

				if(si.length==1) {
					var ur = explode('|',si[0]);

					if(parseInt(ur[1])==-1) {
						candelete=true;
					}
				}

				if(!candelete) {
					showAlertError('Cannot delete Role with users under it.');
					return false;
				}
			}

			showConfirmWarning('Are you sure you want to delete this Role?',function(val){

				if(val) {
					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=useraccountrole&module=useraccount&method="+id+"&formval=%formval%&roleid="+roleid,
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
			myTree_%formval%.selectItem(myTree_%formval%.getSelectedItemId(),true);
		};

	}

	useraccountrole_%formval%();

</script>