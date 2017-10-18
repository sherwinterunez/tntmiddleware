<?php

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method=='messagingnew'||$method=='messagingedit') {
	$readonly = false;
}

if(!empty($vars['params']['smscommandsinfo']['smscommands_name'])) {
	$smscommands_name = $vars['params']['smscommandsinfo']['smscommands_name'];
}

if(!empty($vars['params']['smscommandsinfo']['smscommands_type'])) {
	$smscommands_type = $vars['params']['smscommandsinfo']['smscommands_type'];
}

if(!empty($vars['params']['smscommandsinfo']['smscommands_value'])) {
	$smscommands_value = $vars['params']['smscommandsinfo']['smscommands_value'];
}

?>
<!--
<?php pre(array('$vars'=>$vars)); ?>
-->
<style>
	#formdiv_%formval% #messagingdetailssmscommands {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailssmscommands #messagingdetailssmscommandstabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailssmscommandsdetailsform_%formval% {
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
	#formdiv_%formval% #messagingdetailssmscommands div.cls_sherwin div.dhxform_block {
		background-color: #fff;
		border:1px solid #a4bed4;
		overflow-y: scroll;
		height: 100px;
		margin-bottom: 10px;
	}

	#formdiv_%formval% #messagingdetailssmscommands div.cls_sherwin div.dhxform_block div.dhxform_item_label_right {
		padding: 0;
	}
	#formdiv_%formval% #messagingdetailssmscommands div.cls_sherwin2 div.dhxform_block {
		margin-bottom: 10px;
	}

</style>
<div id="messagingdetails">
	<div id="messagingdetailssmscommands" class="navbar-default-bg">
		<div id="messagingdetailssmscommandstabform_%formval%"></div>
		<div id="messagingdetailssmscommandsdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php //pre(array('$vars'=>$vars)); ?>
</div>
<script>

	function messagingdetailssmscommands_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingnew','messagingedit','messagingclone','messagingdelete','messagingsave','messagingcancel','messagingrefresh'];

		var myTabbar = new dhtmlXTabBar("messagingdetailssmscommandstabform_%formval%");

		myTabbar.setArrowsMode("auto");
			
		myTabbar.addTab("a1", "Details");
		//myTabbar.addTab("a2", "Tab 1-2");
		//myTabbar.addTab("a3", "Tab 1-3");

		myTabbar.tabs("a1").setActive();

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 80, inputWidth: 200},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailssmscommands"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "block", width: 1500, blockOffset: 0, offsetTop:5, list:[
				{type: "input", label: "Priority", name: "txt_smscommands_priority", readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo isset($vars['params']['smscommandsinfo']['smscommands_priority']) ? json_encode($vars['params']['smscommandsinfo']['smscommands_priority']) : '""'; ?>},
				{type: "newcolumn", offset:20},
				{type: "checkbox", label:"Is Active?", name: "txt_smscommands_active", offsetTop:10, position:'label-right', disabled: false, readonly: <?php echo $readonly ? 'true' : 'false'; ?>, checked: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_active']) ? 'true' : 'false'; ?>},
			]},

			<?php for($i=0;$i<10;$i++) { ?>

			{type: "block", width: 1500, blockOffset: 0, offsetTop:5, list:[
				<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 
				{type: "combo", label: "Key #<?php echo ($i+1); ?>", name: "txt_smscommands_key<?php echo $i; ?>", required: true, readonly: true, options: <?php echo !empty($vars['params']['options'.($i+1).'_json']) ? $vars['params']['options'.($i+1).'_json'] : '[]'; ?>},
				<?php } else { ?>
				{type: "input", label: "Key #<?php echo ($i+1); ?>", name: "txt_smscommands_key<?php echo $i; ?>", readonly: true, value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_key'.$i]) ? json_encode($vars['params']['smscommandsinfo']['smscommands_key'.$i]) : '""'; ?>},
				<?php } ?>
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_smscommands_key<?php echo $i; ?>_1", readonly: true, validate: "NotEmpty", value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_key'.$i]) ? json_encode(getOption($vars['params']['smscommandsinfo']['smscommands_key'.$i])) : '""'; ?>},
				<?php /*
				{type: "newcolumn", offset:10},
				{type: "checkbox", label:"Optional", name: "txt_smscommands_key0_optional", position:'label-right', disabled: false, readonly: <?php echo $readonly ? 'true' : 'false'; ?>, checked: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_key0_optional']) ? 'true' : 'false'; ?>},
				*/ ?>
				{type: "newcolumn", offset:125},
				<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 
				{type: "combo", name: "txt_smscommands_key<?php echo $i; ?>_error", required: true, readonly: true, options: <?php echo !empty($vars['params']['errormessage'.($i+1).'_json']) ? $vars['params']['errormessage'.($i+1).'_json'] : '[]'; ?>},
				<?php } else { ?>
				{type: "input", name: "txt_smscommands_key<?php echo $i; ?>_error", readonly: true, value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_key'.$i.'_error']) ? json_encode($vars['params']['smscommandsinfo']['smscommands_key'.$i.'_error']) : '""'; ?>},
				<?php } ?>
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_smscommands_key<?php echo $i; ?>_error_1", readonly: true, value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_key'.$i.'_error']) ? json_encode(getOption($vars['params']['smscommandsinfo']['smscommands_key'.$i.'_error'])) : '""'; ?>},
			]},


			<?php } ?>

			{type: "block", width: 1500, blockOffset: 0, offsetTop:20, list:[
				<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 
				{type: "combo", label: "Action", name: "txt_smscommands_action0", inputWidth:404, required: false , options: <?php echo !empty($vars['params']['actions_json']) ? $vars['params']['actions_json'] : '[]'; ?>},
				<?php for($i=0;$i<10;$i++) { ?>
				{type: "input", label: "SMS #<?php echo ($i+1); ?>", name: "txt_smscommands_sendsms<?php echo $i; ?>", inputWidth:404, hidden:<?php echo (!empty($params['smscommandsinfo']['smscommands_action0'])&&($params['smscommandsinfo']['smscommands_action0']=='_SendSMS'||$params['smscommandsinfo']['smscommands_action0']=='_SendSMStoMobileNumber')) ? 'false' : 'true'; ?>, readonly: <?php echo $readonly?'true':'false'; ?>, rows:3, value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_sendsms'.$i]) ? json_encode($vars['params']['smscommandsinfo']['smscommands_sendsms'.$i]) : '""'; ?>},
				<?php } ?>
				<?php } else { ?>
				{type: "input", label: "Action", name: "txt_smscommands_action0", inputWidth:404, readonly: true, value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_action0']) ? json_encode($vars['params']['smscommandsinfo']['smscommands_action0']) : '""'; ?>},
				<?php for($i=0;$i<10;$i++) { ?>
				{type: "input", label: "SMS #<?php echo ($i+1); ?>", name: "txt_smscommands_sendsms<?php echo $i; ?>", inputWidth:404, hidden:<?php echo (!empty($params['smscommandsinfo']['smscommands_action0'])&&!empty($vars['params']['smscommandsinfo']['smscommands_sendsms'.$i])&&($params['smscommandsinfo']['smscommands_action0']=='_SendSMS'||$params['smscommandsinfo']['smscommands_action0']=='_SendSMStoMobileNumber')) ? 'false' : 'true'; ?>, readonly: <?php echo $readonly?'true':'false'; ?>, rows:3, value: <?php echo !empty($vars['params']['smscommandsinfo']['smscommands_sendsms'.$i]) ? json_encode($vars['params']['smscommandsinfo']['smscommands_sendsms'.$i]) : '""'; ?>},
				<?php } ?>
				<?php } ?>
			]},
			{type: "block", width: 800, blockOffset: 0, offsetTop:20, className:"cls_sherwin2", list:<?php echo !empty($params['sim_json']) ? $params['sim_json'] : '[]'; ?>},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailssmscommandsdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

		myForm.enableLiveValidation(true);

		myForm.setItemFocus("txt_smscommandsname");

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

		myForm.attachEvent("onChange", function (name, value, state){
		    //showMessage("onChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

		    if(typeof(name)!='string') return false;

		    showMessage("onChange: ["+name+"] ["+value+"]",5000);

		    var chkMatches = name.match(/chk_sim_(\d+)/);

		    if(chkMatches) {
			    showMessage('matches: '+chkMatches+' value: '+value+' state: '+state,5000);
			    if(state) {
			    	this.enableItem('txt_smsactions'+chkMatches[1]);
			    } else {
			    	this.disableItem('txt_smsactions'+chkMatches[1]);			    	
			    }
		    } else
		    if(name=='txt_smscommands_key0') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key1') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key2') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key3') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key4') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key0_error') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key1_error') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key2_error') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key3_error') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_key4_error') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_1', arr[1]);
		    } else
		    if(name=='txt_smscommands_action0'&&(value=='_SendSMS'||value=='_SendSMStoMobileNumber')) {
		    	this.showItem('txt_smscommands_sendsms0');
		    	this.showItem('txt_smscommands_sendsms1');
		    	this.showItem('txt_smscommands_sendsms2');
		    	this.showItem('txt_smscommands_sendsms3');
		    	this.showItem('txt_smscommands_sendsms4');
		    } else
		    if(name=='txt_smscommands_action0'&&(value!='_SendSMS'||value!='_SendSMStoMobileNumber')) {
		    	this.hideItem('txt_smscommands_sendsms0');
		    	this.hideItem('txt_smscommands_sendsms1');
		    	this.hideItem('txt_smscommands_sendsms2');
		    	this.hideItem('txt_smscommands_sendsms3');
		    	this.hideItem('txt_smscommands_sendsms4');
		    }

			myChanged_%formval% = true;
		});

		myForm.attachEvent("onInputChange", function(name, value, form){
		    //showMessage("onInputChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

			myChanged_%formval% = true;
		});

		myForm.attachEvent("onValidateError", function(id,value){
			var msg;

			/*if(id=='txt_smscommandsvalue') {
				msg = 'Please enter Value. This field is required.';
			} else
			if(id=='txt_smscommandsname') {
				msg = 'Please enter Name. This field is required.';
			} else
			if(id=='txt_smscommandstype') {
				msg = 'Please enter Type. This field is required.';
			}

			this.setNote(id,{text:msg});*/

			showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssmscommands&module=messaging&method="+id+"&formval=%formval%",
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
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssmscommands&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingclone').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssmscommands&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					if(ddata.rowid) {
						layout_resize_%formval%();
						messagingmainsmscommandsgrid_%formval%(ddata.rowid);
					} else {
						doSelect_%formval%("smscommands");
					}

					//$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					//layout_resize2_%formval%();

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
				showConfirmWarning('Are you sure you want to delete this SMS Command?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssmscommands&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainsmscommandsgrid_%formval%();
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

			//if(!myForm.validate()) return false; 

			showSaving();

			myForm.setItemValue('method', id);

			var obj = {o:this,id:id};

			$("#messagingdetailssmscommandsdetailsform_%formval%").ajaxSubmit({
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
						layout_resize_%formval%($("#messagingdetailssmscommandsdetailsform_%formval% input[name='rowid']").val());
					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {
	
							if(data.rowid) {
								layout_resize_%formval%();
								messagingmainsmscommandsgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("smscommands");
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
			doSelect_%formval%("smscommands");
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("smscommands");
		};


	}

	messagingdetailssmscommands_%formval%();

</script>