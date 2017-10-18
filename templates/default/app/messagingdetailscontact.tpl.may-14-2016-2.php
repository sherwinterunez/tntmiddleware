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
	#formdiv_%formval% #messagingdetailscontact {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailscontact #messagingdetailscontacttabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailscontactdetailsform_%formval% {
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
	<div id="messagingdetailscontact" class="navbar-default-bg">
		<div id="messagingdetailscontacttabform_%formval%"></div>
		<div id="messagingdetailscontactdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php //pre(array('$vars'=>$vars)); ?>
</div>
<script>

	function messagingdetailscontact_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myTabbar = new dhtmlXTabBar("messagingdetailscontacttabform_%formval%");

		myTabbar.setArrowsMode("auto");
			
		myTabbar.addTab("a1", "Details");
		//myTabbar.addTab("a2", "Tab 1-2");
		//myTabbar.addTab("a3", "Tab 1-3");

		myTabbar.tabs("a1").setActive();

		myTab.toolbar.resetAll();

		var myToolbar = ['messagingnew','messagingedit','messagingdelete','messagingsave','messagingcancel'];

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 80, inputWidth: 300},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailscontact"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},

			]},
			{type: "input", label: "Number", name: "txt_number", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $method=='messagingnew' ? 'false' : 'true'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_number']) ? $vars['params']['contactinfo']['contact_number'] : ''; ?>"},
			<?php /*if($method!='messagingnew') { ?>
			{type: "input", label: "Network", name: "txt_network", readonly: true, value: "<?php echo !empty($vars['params']['contactinfo']['contact_network']) ? $vars['params']['contactinfo']['contact_network'] : ''; ?>"},
			<?php }*/ ?>
			{type: "input", label: "Nick", name: "txt_nick", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_nick']) ? $vars['params']['contactinfo']['contact_nick'] : ''; ?>"},
			{type: "input", label: "First Name", name: "txt_fname", readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_fname']) ? $vars['params']['contactinfo']['contact_fname'] : ''; ?>"},
			{type: "input", label: "Last Name", name: "txt_lname", readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_lname']) ? $vars['params']['contactinfo']['contact_lname'] : ''; ?>"},
			{type: "newcolumn", offset:20},
			{type: "input", label: "Address", rows: 2, name: "txt_address", readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_address']) ? $vars['params']['contactinfo']['contact_address'] : ''; ?>"},
			{type: "input", label: "City", name: "txt_city", readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_city']) ? $vars['params']['contactinfo']['contact_city'] : ''; ?>"},
			{type: "input", label: "Country", name: "txt_country", readonly: <?php echo $readonly?'true':'false'; ?>, value: "<?php echo !empty($vars['params']['contactinfo']['contact_country']) ? $vars['params']['contactinfo']['contact_country'] : ''; ?>"},

			//{type: "container", name: "messagingdetailscomposedetails",label: "Select Product",},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailscontactdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

		myForm.enableLiveValidation(true);

		myForm.setItemFocus("txt_number");

		$("#messagingdetailscontactdetailsform_%formval% input[name='txt_number']").numeric();

		<?php } else ?>

		<?php if($method=='messagingsave') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['messagingsave','messagingcancel']);

		myTab.toolbar.showOnly(myToolbar);	

		<?php } ?>

		setTimeout(function(){
			layout_resize_%formval%();
		},100);

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

			if(id=='txt_number') {
				msg = 'Please enter Number (eg. 09191234567) Numeric Only. This field is required.';
			} else
			if(id=='txt_nick') {
				msg = 'Please enter Nick (eg. Superman). This field is required.';
			}

			this.setNote(id,{text:msg});

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('messagingcomposeto').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
		};

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscontact&module=messaging&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

				layout_resize2_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('messagingedit').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscontact&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingdelete').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				showConfirmWarning('Are you sure you want to delete this Contact?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscontact&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmaincontactsgrid_%formval%();
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

			$("#messagingdetailscontactdetailsform_%formval% input[name='method']").val(id);

			var obj = {o:this,id:id};

			showSaving();

			$("#messagingdetailscontactdetailsform_%formval%").ajaxSubmit({
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



						layout_resize_%formval%($("#messagingdetailscontactdetailsform_%formval% input[name='rowid']").val());

					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {

							if(data.rowid) {
								layout_resize_%formval%();
								messagingmaincontactsgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("contacts");
							}

							//doSelect_%formval%("contacts");
							//showAlert(data.return_message+", rowid: "+data.rowid);
							showAlert(data.return_message);
						}
					}

				}
			});

			return false;
		};

		myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("contacts");
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("contacts");
		};

	}

	messagingdetailscontact_%formval%();

</script>