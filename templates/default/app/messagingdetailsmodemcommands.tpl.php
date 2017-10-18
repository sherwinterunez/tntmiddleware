<?php

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method=='messagingnew'||$method=='messagingedit') {
	$readonly = false;
}

if(!empty($vars['params']['modemcommandsinfo']['modemcommands_name'])) {
	$modemcommands_name = $vars['params']['modemcommandsinfo']['modemcommands_name'];
}

if(!empty($vars['params']['modemcommandsinfo']['modemcommands_type'])) {
	$modemcommands_type = $vars['params']['modemcommandsinfo']['modemcommands_type'];
}

if(!empty($vars['params']['modemcommandsinfo']['modemcommands_value'])) {
	$modemcommands_value = $vars['params']['modemcommandsinfo']['modemcommands_value'];
}

?>
<!--
<?php pre(array('$vars'=>$vars)); ?>
-->
<style>
	#formdiv_%formval% #messagingdetailsmodemcommands {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailsmodemcommands #messagingdetailsmodemcommandstabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailsmodemcommandsdetailsform_%formval% {
		padding: 10px;
		/*border: 1px solid #f00;*/
		overflow: scroll;
		/*overflow-y: scroll;*/
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
	#formdiv_%formval% #messagingdetailsmodemcommands div.cls_sherwin2 div.dhxform_block {
		margin-bottom: 50px;
	}
	#formdiv_%formval% #messagingdetailsmodemcommands div.cls_sherwin3 div.dhxform_block {
		/*border: 1px solid #f00;*/
	}
	#formdiv_%formval% #messagingdetailsmodemcommands div.cls_sherwin4 div.dhxform_block {
		/*border: 1px solid #f00;*/
	}
</style>
<div id="messagingdetails">
	<div id="messagingdetailsmodemcommands" class="navbar-default-bg">
		<div id="messagingdetailsmodemcommandstabform_%formval%"></div>
		<div id="messagingdetailsmodemcommandsdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php //pre(array('$vars'=>$vars)); ?>
</div>
<script>

	function messagingdetailsmodemcommands_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingnew','messagingedit','messagingclone','messagingdelete','messagingsave','messagingcancel','messagingrefresh','messaginginsertrow'];

		var myTabbar = new dhtmlXTabBar("messagingdetailsmodemcommandstabform_%formval%");

		myTabbar.setArrowsMode("auto");
			
		myTabbar.addTab("a1", "Details");
		//myTabbar.addTab("a2", "Tab 1-2");
		//myTabbar.addTab("a3", "Tab 1-3");

		myTabbar.tabs("a1").setActive();

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 150},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailsmodemcommands"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "block", width: 2200, blockOffset: 0, offsetTop:5, list:[
				{type: "input", label: "Name", name: "txt_modemcommandsname", inputWidth:500, <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['modemcommandsinfo']['modemcommands_name']) ? json_encode($vars['params']['modemcommandsinfo']['modemcommands_name']) : '""'; ?>},
				{type: "input", label: "Description", name: "txt_modemcommandsdesc", inputWidth:500, <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['modemcommandsinfo']['modemcommands_desc']) ? json_encode($vars['params']['modemcommandsinfo']['modemcommands_desc']) : '""'; ?>},
			]},
			{type: "block", width: 2200, blockOffset: 0, offsetTop:10, className:"cls_sherwin3", list:[
				{type:"label",labelWidth:20,label:"ID"},
				{type: "newcolumn", offset:0},
				{type:"label",labelWidth:150,label:"AT Command"},
				{type: "newcolumn", offset:0},
				{type:"label",labelWidth:150,label:"RegX #1"},
				{type: "newcolumn", offset:300},
				{type:"label",labelWidth:150,label:"RegX #2"},
				{type: "newcolumn", offset:300},
				{type:"label",labelWidth:150,label:"RegX #3"},
				{type: "newcolumn", offset:300},
				{type:"label",labelWidth:150,label:"Result Index"},
				{type: "newcolumn", offset:0},
				{type:"label",labelWidth:150,label:"Expected Result"},
				{type: "newcolumn", offset:0},
				{type:"label",labelWidth:150,label:"Repeat"},
			]},

			<?php 

			$cntr = 20;

			if($readonly) {
				$cntr = count($vars['params']['atcommandsinfo']);
			}

			for($i=0;$i<$cntr;$i++) { ?>

			{type: "block", width: 2200, blockOffset: 0, offsetTop:0, className:"cls_sherwin3", list:[
				{type: "input", label:"<?php echo ($i+1); ?>.", labelWidth:20, name: "txt_atcommands_at[<?php echo $i; ?>]", <?php echo $readonly?'':'validate: "NotEmpty", required: false, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_at']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_at']) : '""'; ?>},
				{type: "newcolumn", offset:0},
				<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 
				{type: "combo", name: "txt_atcommands_regx0[<?php echo $i; ?>]", required: false, options: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['options0_json']) ? $vars['params']['atcommandsinfo'][$i]['options0_json'] : '[]'; ?>},
				<?php } else { ?>
				{type: "input", name: "txt_atcommands_regx0[<?php echo $i; ?>]", readonly: true, value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_regx0']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_regx0']) : '""'; ?>},
				<?php } ?>
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_regxA0_<?php echo $i; ?>", readonly: true, validate: "NotEmpty", value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_regx0']) ? json_encode(getOption($vars['params']['atcommandsinfo'][$i]['atcommands_regx0'])) : '""'; ?>},
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_param0[<?php echo $i; ?>]", readonly: <?php echo $readonly?'true':'false'; ?>, validate: "NotEmpty", value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_param0']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_param0']) : '""'; ?>},
				{type: "newcolumn", offset:0},
				<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 
				{type: "combo", name: "txt_atcommands_regx1[<?php echo $i; ?>]", required: false, options: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['options1_json']) ? $vars['params']['atcommandsinfo'][$i]['options1_json'] : '[]'; ?>},
				<?php } else { ?>
				{type: "input", name: "txt_atcommands_regx1[<?php echo $i; ?>]", readonly: true, value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_regx1']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_regx1']) : '""'; ?>},
				<?php } ?>
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_regxA1_<?php echo $i; ?>", readonly: true, validate: "NotEmpty", value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_regx1']) ? json_encode(getOption($vars['params']['atcommandsinfo'][$i]['atcommands_regx1'])) : '""'; ?>},
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_param1[<?php echo $i; ?>]", readonly: <?php echo $readonly?'true':'false'; ?>, validate: "NotEmpty", value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_param1']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_param1']) : '""'; ?>},
				{type: "newcolumn", offset:0},
				<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 
				{type: "combo", name: "txt_atcommands_regx2[<?php echo $i; ?>]", required: false, options: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['options2_json']) ? $vars['params']['atcommandsinfo'][$i]['options2_json'] : '[]'; ?>},
				<?php } else { ?>
				{type: "input", name: "txt_atcommands_regx2[<?php echo $i; ?>]", readonly: true, value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_regx2']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_regx2']) : '""'; ?>},
				<?php } ?>
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_regxA2_<?php echo $i; ?>", readonly: true, validate: "NotEmpty", value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_regx2']) ? json_encode(getOption($vars['params']['atcommandsinfo'][$i]['atcommands_regx2'])) : '""'; ?>},
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_param2[<?php echo $i; ?>]", readonly: <?php echo $readonly?'true':'false'; ?>, validate: "NotEmpty", value: <?php echo !empty($vars['params']['atcommandsinfo'][$i]['atcommands_param2']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_param2']) : '""'; ?>},
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_resultindex[<?php echo $i; ?>]", <?php echo $readonly?'':'validate: "NotEmpty", required: false, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo isset($vars['params']['atcommandsinfo'][$i]['atcommands_resultindex']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_resultindex']) : '""'; ?>},
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_expectedresult[<?php echo $i; ?>]", <?php echo $readonly?'':'validate: "NotEmpty", required: false, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo isset($vars['params']['atcommandsinfo'][$i]['atcommands_expectedresult']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_expectedresult']) : '""'; ?>},
				{type: "newcolumn", offset:0},
				{type: "input", name: "txt_atcommands_repeat[<?php echo $i; ?>]", <?php echo $readonly?'':'validate: "NotEmpty", required: false, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, value: <?php echo isset($vars['params']['atcommandsinfo'][$i]['atcommands_repeat']) ? json_encode($vars['params']['atcommandsinfo'][$i]['atcommands_repeat']) : '""'; ?>},
			]},

			<?php } ?>

			{type: "block", width: 1550, blockOffset: 0, offsetTop:0, className:"cls_sherwin2", list:[]},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailsmodemcommandsdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

		myForm.setItemFocus("txt_modemcommandsname");

		<?php } else if($method=='messagingsave') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['messagingsave','messagingcancel']);

		myTab.toolbar.showOnly(myToolbar);	

		<?php } else { ?>

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['messagingsave','messagingcancel','messaginginsertrow']);

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

		    if(typeof(name)!='string') return false;

		    showMessage("onChangeX: ["+name+"] ["+value+"]",5000);

		    //var chkMatches = name.match(/txt\_atcommands\_regx\d+\[\d+\]/);


		    //var chkMatches = name.match(/chk_sim_(\d+)/);

		    //var chkMatches = name.match(/txt_atcommands_regx(.+)/);

		    var chkMatches = name.match(/txt\_atcommands\_regx(\d+)\[(\d+)\]/);

		    if(chkMatches) {
			    //showMessage('matches: '+chkMatches+' value: '+value+' state: '+state,5000);
			    //alert(chkMatches);
		    	var arr = value.split('|');
		    	this.setItemValue('txt_atcommands_regxA'+chkMatches[1]+'_'+chkMatches[2], arr[1]);
			} else
		    if(name=='txt_modemcommands_regx0') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx1') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx2') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx3') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx4') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx5') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx6') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx7') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx8') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    } else
		    if(name=='txt_modemcommands_regx9') {
		    	var arr = value.split('|');
			    //showMessage("arr: "+arr,5000);
			    this.setItemValue(name+'_0', arr[1]);
		    }

			myChanged_%formval% = true;

		});

		myForm.attachEvent("onInputChange", function(name, value, form){
		    //showMessage("onInputChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

			myChanged_%formval% = true;
		});

		myForm.attachEvent("onValidateError", function(id,value){
			var msg;

			if(id=='txt_modemcommandsvalue') {
				msg = 'Please enter Value. This field is required.';
			} else
			if(id=='txt_modemcommandsname') {
				msg = 'Please enter Name. This field is required.';
			} else
			if(id=='txt_modemcommandstype') {
				msg = 'Please enter Type. This field is required.';
			}

			this.setNote(id,{text:msg});

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myForm.attachEvent("onFocus", function(name, value){

		    var chkMatches = name.match(/txt\_atcommands\_at\[(\d+)\]/);

		    if(chkMatches) {
			    //showMessage('matches: '+chkMatches+' value: '+value+' state: '+state,5000);
			    //alert(chkMatches);
		    	//var arr = value.split('|');
		    	//this.setItemValue('txt_atcommands_regxA'+chkMatches[1]+'_'+chkMatches[2], arr[1]);
				showMessage("onFocus: name => "+name+", value => "+chkMatches[1],5000);

				this.MyInsertRow = parseInt(chkMatches[1])+1;

				if(myFormStatus_%formval%=='messagingnew'||myFormStatus_%formval%=='messagingedit') {
					myTab.toolbar.enableItem('messaginginsertrow');
				}

			}


		});

		myForm.attachEvent("onBlur", function(name){
		    //your code here
			if(myFormStatus_%formval%=='messagingnew'||myFormStatus_%formval%=='messagingedit') {
				myTab.toolbar.disableItem('messaginginsertrow');
			}
		});

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsmodemcommands&module=messaging&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

				//layout_resize2_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('messagingedit').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsmodemcommands&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					//layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingclone').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsmodemcommands&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					if(ddata.rowid) {
						layout_resize_%formval%();
						messagingmainmodemcommandsgrid_%formval%(ddata.rowid);
					} else {
						doSelect_%formval%("modemcommands");
					}

					//$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					//layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messaginginsertrow').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			var myForm = myForm2_%formval%;

			showMessage("MyInsertRow: "+myForm.MyInsertRow,5000);

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsmodemcommands&module=messaging&method="+myFormStatus_%formval%+"&rowid="+rowid+"&insertrow="+myForm.MyInsertRow+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

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
				showConfirmWarning('Are you sure you want to delete this MODEM Commands?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsmodemcommands&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainmodemcommandsgrid_%formval%();
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

			var txt_optionnumber = parseInt($("#messagingdetailsmodemcommandsdetailsform_%formval% input[name='txt_optionnumber']").val());
			
			if(isNaN(txt_optionnumber)) {
				txt_optionnumber = '';
			}

			myForm.setItemValue('txt_optionnumber', txt_optionnumber);

			//$("#messagingdetailsmodemcommandsdetailsform_%formval% input[name='txt_optionnumber']").val(txt_optionnumber);

			myForm.trimAllInputs();

			//if(!myForm.validate()) return false; 

			//showSaving();

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			myForm.setItemValue('method', id);

			//$("#messagingdetailsmodemcommandsdetailsform_%formval% input[name='method']").val(id);

			var obj = {o:this,id:id};

			$("#messagingdetailsmodemcommandsdetailsform_%formval%").ajaxSubmit({
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
						layout_resize_%formval%($("#messagingdetailsmodemcommandsdetailsform_%formval% input[name='rowid']").val());
					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {
	
							if(data.rowid) {
								layout_resize_%formval%();
								messagingmainmodemcommandsgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("modemcommands");
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
			doSelect_%formval%("modemcommands");
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("modemcommands");
		};


	}

	messagingdetailsmodemcommands_%formval%();

</script>