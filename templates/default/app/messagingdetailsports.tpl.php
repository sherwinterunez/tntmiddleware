<?php

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method=='messagingnew'||$method=='messagingedit') {
	$readonly = false;
}

?>
<style>
	#formdiv_%formval% #messagingdetailsports {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailsports #messagingdetailsportstabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailsportsdetailsform_%formval% {
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
	<div id="messagingdetailsports" class="navbar-default-bg">
		<div id="messagingdetailsportstabform_%formval%"></div>
		<div id="messagingdetailsportsdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php //pre(array('$vars'=>$vars)); ?>
</div>
<script>

	function messagingdetailsports_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingnew','messagingedit','messagingdelete','messagingsave','messagingcancel','messagingrefresh'];

		var myTabbar = new dhtmlXTabBar("messagingdetailsportstabform_%formval%");

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
				{type: "hidden", name: "formid", value: "messagingdetailsports"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "input", label: "Device", name: "txt_portdevice", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['portinfo']['port_device']) ? $vars['params']['portinfo']['port_device'] : ''; ?>"},
			{type: "input", label: "Name", name: "txt_portname", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['portinfo']['port_name']) ? $vars['params']['portinfo']['port_name'] : ''; ?>"},
			{type: "input", label: "SIM Number", name: "txt_portsimnumber", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['portinfo']['port_simnumber']) ? $vars['params']['portinfo']['port_simnumber'] : ''; ?>"},
			<?php /*{type: "input", label: "Network", name: "txt_portnetwork", readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['portinfo']['port_network']) ? $vars['params']['portinfo']['port_network'] : ''; ?>"},*/ ?>
			{type: "input", label: "Description", name: "txt_portdesc", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['portinfo']['port_desc']) ? $vars['params']['portinfo']['port_desc'] : ''; ?>"},
			{type: "checkbox", label:"Disabled", name: "txt_portdisabled", disabled: <?php echo $readonly?'true':'false'; ?>, checked: <?php echo !empty($vars['params']['portinfo']['port_disabled']) ? 'true' : 'false'; ?>},

			//{type: "container", name: "messagingdetailscomposedetails",label: "Select Product",},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailsportsdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

		myForm.setItemFocus("txt_portdevice");

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

		//$("#messagingdetailsnetworksdetailsform_%formval% input[value='txt_networknumber']").focus();

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

			if(id=='txt_portdevice') {
				msg = 'Please enter Device (eg. /dev/cu.usbserial). This field is required.';
			} else
			if(id=='txt_portname') {
				msg = 'Please enter Name (eg. USBSERIAL #1). This field is required.';
			} else
			if(id=='txt_portsimnumber') {
				msg = 'Please enter SIM Number (eg. 09181234567). This field is required.';
			} else
			if(id=='txt_portdesc') {
				msg = 'Please enter Description (eg. USB Serial/SMART/09181234567). This field is required.';
			}

			this.setNote(id,{text:msg});

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			//this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsports&module=messaging&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('messagingedit').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsports&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					layout_resize_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingdelete').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			if(rowid) {
				showConfirmWarning('Are you sure you want to delete this Port?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsports&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainportsgrid_%formval%();
									showAlert(ddata.return_message);
								}
							}
						});
					}

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingsave').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var myForm = myForm2_%formval%;

			myForm.trimAllInputs();

			if(!myForm.validate()) return false; 

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			$("#messagingdetailsportsdetailsform_%formval% input[name='method']").val(id);

			showSaving();

			var obj = {o:this,id:id};

			$("#messagingdetailsportsdetailsform_%formval%").ajaxSubmit({
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



						layout_resize_%formval%($("#messagingdetailsportsdetailsform_%formval% input[name='rowid']").val());

					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {

							if(data.rowid) {
								layout_resize_%formval%();
								messagingmainportsgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("ports");

							}

							showAlert(data.return_message);
						}
					}

				}
			});

			return false;
		};

		myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("ports");
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			doSelect_%formval%("ports");
			showMessage("toolbar: "+id,5000);
		};

	}

	messagingdetailsports_%formval%();

</script>