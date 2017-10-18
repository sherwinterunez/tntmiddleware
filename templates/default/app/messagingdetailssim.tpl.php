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

$access = $applogin->getAccess();

$savecancel = false;

$toolbars = array('messagingrefresh');

if(in_array('simedit',$access)) {
	$toolbars[] = 'messagingedit';
	$savecancel = true;
}

if(in_array('simdelete',$access)) {
	$toolbars[] = 'messagingdelete';
	$savecancel = true;
}

if($savecancel) {
	$toolbars[] = 'messagingsave';
	$toolbars[] = 'messagingcancel';
}

?>
<style>
	#formdiv_%formval% #messagingdetailssim {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailssim #messagingdetailssimtabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailssimdetailsform_%formval% {
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
	#formdiv_%formval% .dhxform_obj_dhx_skyblue div.disabled div.dhxform_label div.dhxform_label_nav_link {
		font-weight: bold;
		color: #000;
	}
	#formdiv_%formval% .dhxform_obj_dhx_skyblue div.dhxform_label div.dhxform_label_nav_link,
	#formdiv_%formval% .dhxform_obj_dhx_skyblue div.dhxform_label div.dhxform_label_nav_link:visited,
	#formdiv_%formval% .dhxform_obj_dhx_skyblue div.dhxform_label div.dhxform_label_nav_link:active,
	#formdiv_%formval% .dhxform_obj_dhx_skyblue div.dhxform_label div.dhxform_label_nav_link:hover {
	    color: inherit;
	    cursor: default;
	    outline: medium none;
	    overflow: hidden;
	    text-decoration: none;
	    white-space: normal;
	    font-weight: bold;
	}
</style>
<div id="messagingdetails">
	<div id="messagingdetailssim" class="navbar-default-bg">
		<div id="messagingdetailssimtabform_%formval%"></div>
		<div id="messagingdetailssimdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php /*pre(array('$vars'=>$vars));*/ ?>
</div>
<script>

	function messagingdetailssim_%formval%() {

		<?php /* ?>
		var myToolbar = ['messagingedit','messagingdelete','messagingsave','messagingcancel','messagingrefresh'];
		<?php */ ?>

		var myToolbar = <?php echo json_encode($toolbars); ?>

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myTabbar = new dhtmlXTabBar("messagingdetailssimtabform_%formval%");

		myTabbar.setArrowsMode("auto");

		myTabbar.addTab("a1", "Details");
		//myTabbar.addTab("a2", "Tab 1-2");
		//myTabbar.addTab("a3", "Tab 1-3");

		myTabbar.tabs("a1").setActive();

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 500},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailssim"},
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "input", name: "txt_simdevice", hidden: true, value: <?php echo !empty($vars['params']['siminfo']['sim_device']) ? json_encode($vars['params']['siminfo']['sim_device']) : '""'; ?>},
			{type: "input", label: "Number", name: "txt_simnumber", hidden: false, inputWidth: 200, <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['siminfo']['sim_number']) ? json_encode($vars['params']['siminfo']['sim_number']) : '""'; ?>},
			{type: "input", label: "Name", name: "txt_simname", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['siminfo']['sim_name']) ? json_encode($vars['params']['siminfo']['sim_name']) : '""'; ?>},
			{type: "input", label: "Description", name: "txt_simdesc", readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['siminfo']['sim_desc']) ? json_encode($vars['params']['siminfo']['sim_desc']) : '""'; ?>},
			{type: "checkbox", label:"Disabled", name: "txt_simdisabled", disabled: <?php echo $readonly?'true':'false'; ?>, checked: <?php echo !empty($vars['params']['siminfo']['sim_disabled']) ? 'true' : 'false'; ?>},
			{type: "checkbox", label:"Hotline", name: "txt_simhotline", disabled: <?php echo $readonly?'true':'false'; ?>, checked: <?php echo !empty($vars['params']['siminfo']['sim_hotline']) ? 'true' : 'false'; ?>},
			{type: "checkbox", label:"Menu", name: "txt_simmenu", disabled: <?php echo $readonly?'true':'false'; ?>, checked: <?php echo !empty($vars['params']['siminfo']['sim_menu']) ? 'true' : 'false'; ?>},
			{type: "checkbox", label:"eLoad", name: "txt_simeload", disabled: <?php echo $readonly?'true':'false'; ?>, checked: <?php echo !empty($vars['params']['siminfo']['sim_eload']) ? 'true' : 'false'; ?>},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailssimdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?>

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

		//myForm.setItemFocus("txt_simname");

		myForm.setItemFocus("txt_simnumber");

		jQuery("#messagingdetailssimdetailsform_%formval% input[name='txt_simnumber']").numeric();

		//srt.dummy.apply(jQuery("#messagingdetailssimdetailsform_%formval% input[name='txt_simnumber']"));

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

			/*if(id=='user_login') {
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
			}*/

			if(id=='txt_simname') {
				msg = 'Please enter Name (eg. SIM1234567890). This field is required.';
			} //else
			//if(id=='txt_simdesc') {
			//	msg = 'Please enter Description (eg. USB Serial/SMART/09181234567). This field is required.';
			//}

			this.setNote(id,{text:msg});

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			//this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssim&module=messaging&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('messagingedit').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssim&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					layout_resize_%formval%();

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
				showConfirmWarning('Are you sure you want to delete this SIM?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssim&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainsimgrid_%formval%();
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

			myForm.trimAllInputs();

			if(!myForm.validate()) return false;

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			$("#messagingdetailssimdetailsform_%formval% input[name='method']").val(id);

			showSaving();

			var obj = {o:this,id:id};

			$("#messagingdetailssimdetailsform_%formval%").ajaxSubmit({
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

					/*if(typeof(data.xml)=='string') {
						myTree_%formval%.deleteItem('roleid_0');
						myTree_%formval%.parse(data.xml,"xml");

						if(data.role_id) {
							myTree_%formval%.selectItem(data.role_id,true);
						}

						//myForm.setItemFocus('role_name');
						//myForm.setItemValue('role_name','');
						//myForm.setItemValue('role_desc','');
					}*/

					if(data.error_code) {
					} else
					if(data.html) {

						//$("#formdiv_%formval% #messagingdetails").parent().html(data.html);

						//layout_resize2_%formval%();



						layout_resize_%formval%($("#messagingdetailssimdetailsform_%formval% input[name='rowid']").val());

					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {

							if(data.rowid) {
								layout_resize_%formval%();
								messagingmainsimgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("sim");

							}

							showAlert(data.return_message);
						}
					}

				}
			});

			return false;
		};

		myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			doSelect_%formval%("sim");
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			doSelect_%formval%("sim");
			//showMessage("toolbar: "+id,5000);
		};

	}

	messagingdetailssim_%formval%();

</script>
