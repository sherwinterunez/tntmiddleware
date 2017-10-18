<?php

global $appdb, $applogin;

$readonly = false;
$userinfo = false;

if(!empty($vars['params']['readonly'])) {
	$readonly = true;
}

//pre(array('$vars'=>$vars));

if(!empty($vars['post']['roleid'])&&!empty($vars['post']['userid'])) {

	$roleid = $vars['post']['roleid'];

	//if(!($result = $appdb->query("select * from tbl_users where role_id=".$vars['post']['roleid']." and user_id=".$vars['post']['userid']." and flag=0"))) {
	//	json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	//}

	if(!($result = $appdb->query("select * from tbl_users where role_id=".$vars['post']['roleid']." and user_id=".$vars['post']['userid']))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries));
	}

	//pre(array('$result'=>$result));

	if(!empty($result['rows'][0]['user_id'])) {
		$userinfo = $result['rows'][0];
		if(!empty($userinfo['content'])) {
			$userinfo['content'] = json_decode($userinfo['content'],true);
		}
		//pre(array('$userinfo'=>$userinfo));			
	}

}

if(empty($vars['post']['userid'])) {
	$vars['post']['userid'] = false;
}

if($applogin->isSystemAdministrator()) {
	if(!($result = $appdb->query("select * from tbl_roles where flag=0 order by role_id asc"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries,'backtrace'=>debug_backtrace()));
	}
} else {
	if(!($result = $appdb->query("select * from tbl_roles where flag=0 and role_id!=1 order by role_id asc"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries,'backtrace'=>debug_backtrace()));
	}	
}

//pre(array('$result'=>$result));

$allroles = array();

if(!$readonly&&!($vars['post']['roleid']==1&&$vars['post']['userid']==1)) {
	foreach($result['rows'] as $k=>$v) {
		$ro = array('value'=>'roleid_'.$v['role_id'],'text'=>$v['role_name']);
		$allroles[] = $ro;
	}
} else {
	foreach($result['rows'] as $k=>$v) {
		if($v['role_id']==$userinfo['role_id']) {
			$ro = array('value'=>'roleid_'.$v['role_id'],'text'=>$v['role_name']);
			$allroles[] = $ro;					
		}
	}			
}


?>
<div id="usermanagementmanage">
<?php //pre($vars); ?>
<div id="usermanagementmanageform_%formval%" style="height:auto;width:auto;"></div>
</div>

<script>
	var myForm_%formval%, formData_%formval%;

	function validatepass_%formval%(data,name) {
		var myform = this.obj().getForm();
		var user_pass1 = myform.getItemValue('user_pass1');
		var user_pass2 = myform.getItemValue('user_pass2');
		//alert('validatepass!');
		//srt.dummy.apply(this,[this.obj()]);
		//srt.dummy.apply(this,[this.obj().getForm()]);
		//showMessage(this.obj().getItemValue('user_pass1'),5000);
		//this.obj().getForm().forEachItem(function(name){
		//this.obj().getForm().forEachItem(function(name){
			//showMessage(name+', '+this.getItemValue(name),5000);
			//showMessage(name,5000);
			//srt.dummy.apply(this,[this.getForm()]);
		//});
		//showMessage(name+', '+myform.getItemValue(name),5000);
		//showMessage('user_pass1, '+myform.getItemValue('user_pass1'),5000);

		if(user_pass1!=user_pass2) {
			//myform.setNote('user_pass2',{text:'Password is not the same.'});
			return false;
		}

		return true;
	}

	function usermanagementmanagenewuser_%formval%() {
		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 150, inputWidth: 250},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "form"},
				{type: "hidden", name: "module", value: "user"},
				{type: "hidden", name: "buttonid", value: ""},
			]},
			/*{type: "fieldset", name: "userrole", label: "User Role", inputWidth: 500, list:[
				{type: "combo", name: "user_role", label: "Role", required:true, filtering:true, options:[
				    {value: "roleid_1", text: "Administrator", selected:true},
				    {value: "roleid_2", text: "Employee"}
				]},
			]},*/
			{type: "fieldset", name: "userrole", label: "User Role", inputWidth: 500, list:[
				{type: "combo", name: "user_role", label: "Role"<?php if(!$readonly&&$vars['post']['userid']!=1) {echo ', required:true';} ?>, filtering:true, options:<?php echo json_encode($allroles); ?>},
			]},
			{type: "fieldset", name: "newuser", label: "New User", inputWidth: 500, list:[
				<?php if(!empty($vars['params']['edit'])) { ?>
				{type: "hidden", name: "update", value: true},
				{type: "hidden", name: "newuser_hash", value: ""},
				{type: "hidden", name: "user_id", value: "<?php if(!empty($userinfo['user_id'])){echo $userinfo['user_id'];} ?>"},
				<?php } ?>
				{type: "hidden", name: "user_hash", value: "<?php if(!empty($userinfo['user_hash'])){echo $userinfo['user_hash'];} ?>"},
				{type: "input", name: "user_login", label: "User Login", value: "<?php if(!empty($userinfo['user_login'])){echo $userinfo['user_login'];} ?>"<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
				{type: "password", name: "user_pass1", label: "Password", value: "<?php if(!empty($userinfo['user_login'])){echo sha1('*');} ?>"<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
				<?php if(!$readonly){ ?>{type: "password", name: "user_pass2", label: "Confirm Password", value: "<?php if(!empty($userinfo['user_login'])){echo sha1('*');} ?>"<?php if(!$readonly){echo ', validate:"validatepass_%formval%", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},<?php } ?>
				{type: "input", name: "user_email", label: "Email Address", value: "<?php if(!empty($userinfo['user_email'])){echo $userinfo['user_email'];} ?>"<?php if(!$readonly){echo ', validate:"ValidEmail", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
			]},
			{type: "fieldset", name: "userinfo", label: "User Information", inputWidth: 500, list:[
				{type: "input", name: "user_fname", label: "First Name", value: "<?php if(!empty($userinfo['content']['user_fname'])){echo $userinfo['content']['user_fname'];} ?>"<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
				{type: "input", name: "user_mname", label: "Middle Name", value: "<?php if(!empty($userinfo['content']['user_mname'])){echo $userinfo['content']['user_mname'];} ?>"<?php if($readonly){echo ', readonly:true';}?>},
				{type: "input", name: "user_lname", label: "Last Name", value: "<?php if(!empty($userinfo['content']['user_lname'])){echo $userinfo['content']['user_lname'];} ?>"<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
			]},
		];

		myForm_%formval% = new dhtmlXForm("usermanagementmanageform_%formval%", formData_%formval%);

		var myForm = myForm_%formval%;

		var $ = jQuery;

		var role_combo = myForm.getCombo('user_role');

		/*role_combo.addOption("roleid_1","Administrator");
		role_combo.addOption("roleid_2","Clerk");
		role_combo.setComboValue("roleid_1");*/

		//alert(settings.isSystemAdministrator);

		<?php if(!empty($vars['post']['roleid'])) { ?>
		role_combo.setComboValue("roleid_<?php echo $vars['post']['roleid']; ?>");
		<?php } ?>

		role_combo.readonly(true);

		//srt.dummy.apply(this,[myForm_%formval%]);

		//srt.dummy.apply(role_combo,[role_combo]);

		//myForm_%formval%.hideItem('access_four');

		//alert(myForm_%formval%.getDom('access2'));

		myForm.enableLiveValidation(true);

		//$(myForm.getDOM('access1')).attr('style','clear:left;float:left;');
		//$(myForm.getDOM('access2')).attr('style','float:left;margin-left:10px;clear:right;');

		//$("#usermanagementmanagenewrolesform_%formval% input[name='role_name']").numeric();

		//srt.dummy.apply(this,[myForm_%formval%.getDOM('access_four')]);

		var myTab = srt.getTabUsingFormVal('%formval%');

		//myForm.forEachItem(function(name){
			//showMessage(name+', '+this.getItemType(name),5000);
			//if(!this.isItemHidden(name)) {
				//if(this.getItemType(name)=='fieldset') {
					//showMessage('disabling: '+name,5000);
					//this.disableItem(name);
					//this.enableItem(name);
				//}
			//}
		//});

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
			this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('usermanagementsave').onClick = function(id,formval) {
			$ = jQuery;


			/*myForm.forEachItem(function(name){
				if(this.getItemType(name)=='input') {
					//srt.dummy.apply(this,[this.getDOM(name)]);
					oval = this.getItemValue(name);
					nval = trim(oval);
					showMessage('oval:['+oval+'], nval:['+nval+']',5000);
					if(oval!=nval) {
						this.setItemValue(name,nval);
					}

				}
				//showMessage(name+', '+this.getItemType(name),5000);
				//if(!this.isItemHidden(name)) {
					//if(this.getItemType(name)=='fieldset') {
						//showMessage('disabling: '+name,5000);
						//this.disableItem(name);
						//this.enableItem(name);
					//}
				//}
			});*/

			myForm.trimAllInputs();

			if(!myForm.validate()) return false; 

			$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			var user_hash = sha1(base64_encode(myForm.getItemValue('user_pass2')) + base64_encode(myForm.getItemValue('user_login')));

			<?php if(!empty($vars['params']['edit'])) { ?>

			var edit_hash = sha1(base64_encode(sha1('*')) + base64_encode('<?php echo $userinfo['user_login']; ?>'));

			if(user_hash!=edit_hash) {
				$("#usermanagementmanageform_"+formval+" input[name='newuser_hash']").val(user_hash);
			}

			//showMessage('edit_hash: '+edit_hash+', user_hash: '+user_hash,10000);

			<?php } ?>

			//showMessage(user_hash,5000);

			//showMessage(myForm.getItemValue('user_hash'),5000);

			//user_hash = 

			$("#usermanagementmanageform_"+formval+" input[name='user_hash']").val(user_hash);

			//$("#usermanagementmanageform_"+formval+" input[name='user_pass1']").val(user_hash);
			//$("#usermanagementmanageform_"+formval+" input[name='user_pass2']").val(user_hash);

			//showMessage('Validation: '+myForm.validate());

			$("#usermanagementmanageform_"+formval).ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: {o:this,id:id,myForm:myForm,formval:formval},
				exclude: ['user_pass1','user_pass2'],
				success: function(data, statusText, xhr, $form, obj){
					var $ = jQuery;

					if(data.error_code&&data.error_message) {

						hideLoader();

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

					if(typeof(data.xml)=='string') {
						myTree_%formval%.deleteItem('roleid_0');
						myTree_%formval%.parse(data.xml,"xml"); 

						if(data.user_id) {
							myTree_%formval%.selectItem(data.user_id,true);
						}
					}

				}
			});

			return false;
		}

		/*myTab.toolbar.getToolbarData('usermanagementedit').onClick = function(id,formval) {

			myTab.toolbar.disableOnly(['usermanagementnewroles','usermanagementnewuser','usermanagementedit','usermanagementdelete']);

			showMessage(id,5000);

			myForm.forEachItem(function(name){
				showMessage(name+', '+this.getItemType(name),5000);
				//if(!this.isItemHidden(name)) {
					//if(this.getItemType(name)=='fieldset') {
						//showMessage('disabling: '+name,5000);
						//this.disableItem(name);
						//this.enableItem(name);
					//}
				//}
			});

			return false;
		}*/

	};

	usermanagementmanagenewuser_%formval%();

</script>
