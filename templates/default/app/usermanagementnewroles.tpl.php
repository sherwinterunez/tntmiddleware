<?php

global $appdb, $applogin;

$readonly = false;

//pre(array('$vars'=>$vars));

if(!empty($vars['params']['readonly'])) {
	$readonly = true;
}

if(!empty($vars['post']['roleid'])) {
	$roleid = $vars['post']['roleid'];
	if(!($result = $appdb->query("select * from tbl_roles where role_id=$roleid and flag=0"))) {
		json_encode_return(array('error_code'=>123,'error_message'=>'Error in SQL execution.','$appdb->lasterror'=>$appdb->lasterror,'$appdb->queries'=>$appdb->queries,'backtrace'=>debug_backtrace()));
		die;				
	}

	//pre($result);

	if(!empty($result['rows'][0]['role_id'])) {
		$role_name = $result['rows'][0]['role_name'];
		$role_desc = $result['rows'][0]['role_desc'];
	}
} else {
	$vars['post']['roleid'] = false;
}

?>
<div id="usermanagementmanage">
<?php //pre($vars); ?>
<div id="usermanagementmanageform_%formval%" style="height:auto;width:auto;"></div>
</div>

<script>
	var myForm_%formval%, formData_%formval%;

	function usermanagementmanagenewroles_%formval%() {
		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 250},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "form"},
				{type: "hidden", name: "module", value: "user"},
				{type: "hidden", name: "buttonid", value: ""},
			]},
			{type: "fieldset", name: "newrole", label: "Role", inputWidth: 500, list:[
				{type: "hidden", name: "role_id", value: "<?php if(!empty($vars['post']['roleid'])){echo $vars['post']['roleid'];} ?>"},
				{type: "input", name: "role_name", label: "Role", value: "<?php if(!empty($role_name)){echo $role_name;} ?>"<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
				{type: "input", name: "role_desc", label: "Description", value: "<?php if(!empty($role_name)){echo $role_desc;} ?>"<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
			]},
			<?php if($vars['post']['roleid']==1) { ?>
			<?php } else { ?>
			{type: "fieldset", name: "access1", label: "Select Access", inputWidth: 500, list:[
				{type: "checkbox", name: "access_one", label: "Access One", value: "one"<?php if($readonly){echo ', readonly:true';}?>},
				{type: "checkbox", name: "access_two", label: "Access Two", value: "two"<?php if($readonly){echo ', readonly:true';}?>},
				{type: "newcolumn", offset:100},
				{type: "checkbox", name: "access_three", label: "Access Three", value: "three"<?php if($readonly){echo ', readonly:true';}?>},
				{type: "checkbox", name: "access_four", label: "Access Four", value: "four"<?php if($readonly){echo ', readonly:true';}?>},
			]},
			<?php } ?>
			//{type: "fieldset", name: "access2", label: "Select Access", inputWidth: 160, list:[
			//	{type: "checkbox", name: "access_three", label: "Access Three", value: ""},
			//	{type: "checkbox", name: "access_four", label: "Access Four", value: ""},
			//]}
		];

		myForm_%formval% = new dhtmlXForm("usermanagementmanageform_%formval%", formData_%formval%);

		var myForm = myForm_%formval%;

		var $ = jQuery;

		//srt.dummy.apply(this,[myForm_%formval%]);

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

			if(id=='role_name') {
				msg = 'Please enter Role. This field is required.';
			} else if(id=='role_desc') {
				msg = 'Please enter Role Description. This field is required.';
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

			//showMessage('Validation: '+myForm.validate());

			var obj = {o:this,id:id};

			$("#usermanagementmanageform_"+formval).ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: obj,
				success: function(data, statusText, xhr, $form, obj){
					var $ = jQuery;

					//alert(obj.id);

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

						if(data.role_id) {
							myTree_%formval%.selectItem(data.role_id,true);
						}

						//myForm.setItemFocus('role_name');
						//myForm.setItemValue('role_name','');
						//myForm.setItemValue('role_desc','');
					}

					if(data.error_code) {
					} else 
					if(data.html) {
					}
				}
			});

			return false;
		}

		/*myTab.toolbar.getToolbarData('usermanagementedit').onClick = function(id,formval) {

			myTab.toolbar.disableOnly(['usermanagementnewroles','usermanagementnewuser','usermanagementedit','usermanagementdelete']);

			showMessage(id,5000);

			return false;
		}*/

	};

	usermanagementmanagenewroles_%formval%();

</script>
