<?php

global $applogin;

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method=='messagingnew'||$method=='messagingedit') {
	$readonly = false;
}

if(!empty($vars['params']['optionsinfo']['options_name'])) {
	$options_name = $vars['params']['optionsinfo']['options_name'];
}

if(!empty($vars['params']['optionsinfo']['options_type'])) {
	$options_type = $vars['params']['optionsinfo']['options_type'];
}

if(!empty($vars['params']['optionsinfo']['options_value'])) {
	$options_value = $vars['params']['optionsinfo']['options_value'];
}

?>
<!--
<?php pre(array('$vars'=>$vars)); ?>
-->
<style>
	#formdiv_%formval% #messagingdetailsoptions {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailsoptions #messagingdetailsoptionstabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailsoptionsdetailsform_%formval% {
		padding: 10px;
		/*border: 1px solid #f00;*/
		overflow: hidden;
		overflow-y: scroll;
	}
	#formdiv_%formval% .dhxtabbar_base_dhx_skyblue div.dhx_cell_tabbar div.dhx_cell_cont_tabbar {
		/*border: none;*/
		display: none;
	}
	#formdiv_%formval% .dhxtabbar_base_dhx_skyblue div.dhxtabbar_tabs {
		border-top: none;
		border-left: none;
		border-right: none;
	}
</style>
<div id="messagingdetails">
	<div id="messagingdetailsoptions" class="navbar-default-bg">
		<div id="messagingdetailsoptionstabform_%formval%"></div>
		<div id="messagingdetailsoptionsdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php /*pre(array('$vars'=>$vars));*/ ?>
</div>
<script>

	function messagingdetailsoptions_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingnew','messagingedit','messagingclone','messagingdelete','messagingsave','messagingcancel','messagingrefresh'];

		var myTabbar = new dhtmlXTabBar("messagingdetailsoptionstabform_%formval%");

		myTabbar.setArrowsMode("auto");

		myTabbar.addTab("a1", "Details");
		//myTabbar.addTab("a2", "Tab 1-2");
		//myTabbar.addTab("a3", "Tab 1-3");

		myTabbar.tabs("a1").setActive();

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 80, inputWidth: 500},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailsoptions"},
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "input", label: "Name", name: "txt_optionsname", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['optionsinfo']['options_name']) ? json_encode($vars['params']['optionsinfo']['options_name']) : '""'; ?>},
			<?php if($method=='messagingnew'||$method=='messagingedit') { ?>
			{type: "combo", label: "Type", name: "txt_optionstype", required: true, readonly: true, options:[
				{text: "STRING", value: "STRING"<?php echo !empty($options_type) && $options_type=='STRING' ? ', selected: true' : ''; ?>},
				{text: "NUMERIC", value: "NUMERIC"<?php echo !empty($options_type) && $options_type=='NUMERIC' ? ', selected: true' : ''; ?>},
				{text: "SETTING", value: "SETTING"<?php echo !empty($options_type) && $options_type=='SETTING' ? ', selected: true' : ''; ?>},
				{text: "NETWORK", value: "NETWORK"<?php echo !empty($options_type) && $options_type=='NETWORK' ? ', selected: true' : ''; ?>},
				{text: "REGEX", value: "REGEX"<?php echo !empty($options_type) && $options_type=='REGEX' ? ', selected: true' : ''; ?>},
				{text: "KEYCODE", value: "KEYCODE"<?php echo !empty($options_type) && $options_type=='KEYCODE' ? ', selected: true' : ''; ?>},
				{text: "PRODUCTCODE", value: "PRODUCTCODE"<?php echo !empty($options_type) && $options_type=='PRODUCTCODE' ? ', selected: true' : ''; ?>},
				{text: "ERRORMESSAGE", value: "ERRORMESSAGE"<?php echo !empty($options_type) && $options_type=='ERRORMESSAGE' ? ', selected: true' : ''; ?>},
				{text: "MESSAGE", value: "MESSAGE"<?php echo !empty($options_type) && $options_type=='MESSAGE' ? ', selected: true' : ''; ?>},
			]},
			<?php } else { ?>
			{type: "input", label: "Type", name: "txt_optionstype", readonly: true, value: <?php echo !empty($vars['params']['optionsinfo']['options_type']) ? json_encode($vars['params']['optionsinfo']['options_type']) : '""'; ?>},
			<?php } ?>
			<?php /*{type: "input", label: "Priority", name: "txt_optionspriority", readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo isset($vars['params']['optionsinfo']['options_priority']) ? json_encode($vars['params']['optionsinfo']['options_priority']) : '""'; ?>},*/ ?>
			{type: "input", label: "Value", name: "txt_optionsvalue", rows: 5, <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['optionsinfo']['options_value']) ? json_encode($vars['params']['optionsinfo']['options_value']) : '""'; ?>},
			<?php if($applogin->isSystemAdministrator()) { ?>
			{type: "checkbox", label:"Hidden", name: "txt_optionshidden", disabled: <?php echo $readonly?'true':'false'; ?>, checked: <?php echo !empty($vars['params']['optionsinfo']['options_hidden']) ? 'true' : 'false'; ?>},
			<?php } ?>
			//{type: "container", name: "messagingdetailscomposedetails",label: "Select Product",},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailsoptionsdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?>

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

		myForm.setItemFocus("txt_optionsname");

		<?php } else if($method=='messagingsave') { ?>

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['messagingsave','messagingcancel']);

		myTab.toolbar.showOnly(myToolbar);

		<?php } else { ?>

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['messagingsave','messagingcancel']);

		myTab.toolbar.showOnly(myToolbar);

		<?php } ?>

		setTimeout(function(){
			layout_resize_%formval%();
		},100);

///////////////////////////////////

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

		myForm.attachEvent("onValidateError", function(id,value){
			var msg;

			if(id=='txt_optionsvalue') {
				msg = 'Please enter Value. This field is required.';
			} else
			if(id=='txt_optionsname') {
				msg = 'Please enter Name. This field is required.';
			} else
			if(id=='txt_optionstype') {
				msg = 'Please enter Type. This field is required.';
			}

			this.setNote(id,{text:msg});

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoptions&module=messaging&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

				//layout_resize2_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('messagingedit').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoptions&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					//layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingclone').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoptions&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					if(ddata.rowid) {
						layout_resize_%formval%();
						messagingmainoptionsgrid_%formval%(ddata.rowid);
					} else {
						doSelect_%formval%("options");
					}

					//$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					//layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingdelete').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			if(rowid) {
				showConfirmWarning('Are you sure you want to delete this Option?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoptions&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainoptionsgrid_%formval%();
									showAlert(ddata.return_message);
								}
							}
						});
					}

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingsave').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm2_%formval%;

			var txt_optionnumber = parseInt($("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val());

			if(isNaN(txt_optionnumber)) {
				txt_optionnumber = '';
			}

			myForm.setItemValue('txt_optionnumber', txt_optionnumber);

			//$("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val(txt_optionnumber);

			myForm.trimAllInputs();

			if(!myForm.validate()) return false;

			showSaving();

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			myForm.setItemValue('method', id);

			//$("#messagingdetailsoptionsdetailsform_%formval% input[name='method']").val(id);

			var obj = {o:this,id:id};

			$("#messagingdetailsoptionsdetailsform_%formval%").ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: obj,
				success: function(data, statusText, xhr, $form, obj){
					var $ = jQuery;

					//alert(obj.id);

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
						layout_resize_%formval%($("#messagingdetailsoptionsdetailsform_%formval% input[name='rowid']").val());
					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {

							if(data.rowid) {
								layout_resize_%formval%();
								messagingmainoptionsgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("options");
							}

							showMessage(data.return_message,5000);
						}
					}

				}
			});

			return false;
		};

		myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			doSelect_%formval%("options");
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			doSelect_%formval%("options");
		};


	}

	messagingdetailsoptions_%formval%();

</script>
