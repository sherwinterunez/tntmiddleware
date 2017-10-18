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

if(!empty($vars['params']['contacts'])) {
	$contacts = array();
	//$contacts[] = array('text'=>'');
	//$contacts[] = array('text'=>'All Contacts');

	foreach($vars['params']['contacts'] as $k=>$v) {
		$contacts[] = array('text'=>$v['contact_number'].' / '.$v['contact_nick'],'value'=>$v['contact_id']);
	}

	if(!empty($contacts)) {
		$vars['contact_json'] = json_encode($contacts);
		$contacts = true;
	}
} else {
	//$vars['contact_json'] = json_encode(array(array('text'=>"No Contacts Available")));
	$vars['contact_json'] = json_encode(array());
	$contacts = false;
}

if(!empty($vars['params']['groupmembers'])) {
	$members = array();
	//$contacts[] = array('text'=>'');
	//$contacts[] = array('text'=>'All Contacts');

	foreach($vars['params']['groupmembers'] as $k=>$v) {
		$members[] = array('text'=>$v['contact_number'].' / '.$v['contact_nick'],'value'=>$v['contact_id']);
	}

	if(!empty($members)) {
		$vars['groupmembers_json'] = json_encode($members);
		$members = true;
	}
} else {
	$vars['groupmembers_json'] = json_encode(array());
	$members = false;
}

$access = $applogin->getAccess();

$savecancel = false;

$toolbars = array('messagingcomposeto','messagingrefresh');

if(in_array('groupsnew',$access)) {
	$toolbars[] = 'messagingnew';
	$savecancel = true;
}

if(in_array('groupsedit',$access)) {
	$toolbars[] = 'messagingedit';
	$savecancel = true;
}

if(in_array('groupsdelete',$access)) {
	$toolbars[] = 'messagingdelete';
	$savecancel = true;
}

if($savecancel) {
	$toolbars[] = 'messagingsave';
	$toolbars[] = 'messagingcancel';
}

?>
<style>
	#formdiv_%formval% #messagingdetailsgroup {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailsgroup #messagingdetailsgrouptabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #messagingdetailsgroupdetailsform_%formval% {
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
	<div id="messagingdetailsgroup" class="navbar-default-bg">
		<div id="messagingdetailsgrouptabform_%formval%"></div>
		<div id="messagingdetailsgroupdetailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
	<?php //pre(array('$vars'=>$vars)); ?>
</div>
<script>

	function messagingdetailsgroup_%formval%() {

		<?php /* ?>
		var myToolbar = ['messagingcomposeto','messagingnew','messagingedit','messagingdelete','messagingsave','messagingcancel','messagingrefresh'];
		<?php */ ?>

		var myToolbar = <?php echo json_encode($toolbars); ?>

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myTabbar = new dhtmlXTabBar("messagingdetailsgrouptabform_%formval%");

		myTabbar.setArrowsMode("auto");
			
		myTabbar.addTab("a1", "Details");
		//myTabbar.addTab("a2", "Tab 1-2");
		//myTabbar.addTab("a3", "Tab 1-3");

		myTabbar.tabs("a1").setActive();

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 80, inputWidth: 300},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailsgroups"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method=='messagingedit' ? $vars['post']['rowid'] : ''; ?>"},
				{type: "hidden", name: "groupmembers", value: ""},				
			]},
			{type: "block", width: 1000, list:[
				{type: "input", label: "Name", name: "txt_groupname", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, inputWidth: 200, value: "<?php echo !empty($vars['params']['groupinfo']['group_name']) ? $vars['params']['groupinfo']['group_name'] : ''; ?>"},
				{type: "newcolumn", offset: 20},
				{type: "input", label: "Description", name: "txt_groupdesc", <?php echo $readonly?'':'validate: "NotEmpty", required: true, '; ?>readonly: <?php echo $readonly?'true':'false'; ?>, inputWidth: 400, value: "<?php echo !empty($vars['params']['groupinfo']['group_desc']) ? $vars['params']['groupinfo']['group_desc'] : ''; ?>"},
			]},
			{type: "block", width: 1000, list:[
				{type: "label", label: "Non Member"},
				{type: "multiselect", name: "nonmember", disabled: <?php echo $readonly?'true':'false'; ?>, inputHeight: 100, 
					options: <?php echo $vars['contact_json']; ?>
				},
				{type: "newcolumn", offset: 20},
				{type: "block", width: 150, list:[
					{type: "button", name: "add", disabled: <?php echo $readonly?'true':'false'; ?>, value: ">>", offsetLeft: 25, offsetTop: 60},
					{type: "button", name: "remove", disabled: <?php echo $readonly?'true':'false'; ?>, value: "<<", offsetLeft: 25}
				]},
				{type: "newcolumn", offset: 20},
				{type: "label", label: "Members"},
				{type: "multiselect", name: "members", disabled: <?php echo $readonly?'true':'false'; ?>, inputHeight: 100, 
					options: <?php echo $vars['groupmembers_json']; ?>
				},
			]},

			//{type: "container", name: "messagingdetailscomposedetails",label: "Select Product",},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailsgroupdetailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method=='messagingnew'||$method=='messagingedit') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['messagingsave','messagingcancel']);

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

		myForm.attachEvent("onButtonClick",function(name){
		    //showMessage("onButtonClick: ["+name+"]",5000);

		    if(name=='add') {

			    var va = myForm.getItemValue("nonmember");
			    var sa = myForm.getSelect("nonmember");
			    var sb = myForm.getSelect("members");

			    if(typeof(va)!=undefined&&va.length>0) {
			    } else return false;

			    //eval("var k={"+va.join(":true,")+":true};");

				//alert(myForm.getItemValue("nonmember")+" / "+myForm.getItemValue("nonmember").length);

		    	//showMessage(JSON.stringify(k),5000);

			    for(var i=0;i<sa.options.length;i++) {
			    	//if(k[sa.options[i].value]) {
				    //	showMessage(i+' => '+sa.options[i].value,5000);
			    	//}
			    	for(var j=0;j<va.length;j++) {
			    		if(sa.options[i].value==va[j]) {
			    			//showMessage(i+' => '+sa.options[i].value,5000);
			    			sb.options.add(new Option(sa.options[i].text,sa.options[i].value));
			    			sa.options.remove(i);
			    		}
			    	}
			    }

			    //for(var i=0;i<va.length;i++) {
			    //	showMessage(i+' => '+va[i],5000);
			    //}

		    } else if(name=='remove') {
			    var va = myForm.getItemValue("members");
			    var sa = myForm.getSelect("members");
			    var sb = myForm.getSelect("nonmember");

			    if(typeof(va)!=undefined&&va.length>0) {
			    } else return false;

			    //eval("var k={"+va.join(":true,")+":true};");

				//alert(myForm.getItemValue("nonmember")+" / "+myForm.getItemValue("nonmember").length);

		    	//showMessage(JSON.stringify(k),5000);

			    for(var i=0;i<sa.options.length;i++) {
			    	//if(k[sa.options[i].value]) {
				    //	showMessage(i+' => '+sa.options[i].value,5000);
			    	//}
			    	for(var j=0;j<va.length;j++) {
			    		if(sa.options[i].value==va[j]) {
			    			//showMessage(i+' => '+sa.options[i].value,5000);
			    			sb.options.add(new Option(sa.options[i].text,sa.options[i].value));
			    			sa.options.remove(i);
			    		}
			    	}
			    }

		    }

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

			if(id=='txt_groupname') {
				msg = 'Please enter Name (eg. Group #1). This field is required.';
			} else
			if(id=='txt_groupdesc') {
				msg = 'Please enter Description (eg. Clients). This field is required.';
			}

			this.setNote(id,{text:msg});

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('messagingcomposeto').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			var myTab = srt.getTabUsingFormVal('%formval%');

			myTab.layout.cells('b').setText('Compose To');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingmaincompose&module=messaging&from=groups&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
			}, function(ddata,odata){
				$ = jQuery;
				$("#formdiv_%formval% #messagingmain").parent().html(ddata.html);
			});
		};

		myTab.toolbar.getToolbarData('messagingnew').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsgroups&module=messaging&method="+id+"&formval=%formval%",
			}, function(ddata,odata){

				$ = jQuery;

				$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

				layout_resize_%formval%();

			});
		};

		myTab.toolbar.getToolbarData('messagingedit').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//showMessage("rowId: "+myGrid_%formval%.getSelectedRowId(),5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsgroups&module=messaging&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					$ = jQuery;

					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

					layout_resize_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('messagingdelete').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("groups");

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			if(rowid) {
				showConfirmWarning('Are you sure you want to delete this Group?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsgroups&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmaingroupsgrid_%formval%();
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

			showSaving();

		    var sa = myForm.getSelect("members");

		    if(sa.options.length>0) {

		    	var gm = [];

			    for(var i=0;i<sa.options.length;i++) {

			    	gm.push(sa.options[i].value);

			    	//if(k[sa.options[i].value]) {
				    //	showMessage(i+' => '+sa.options[i].value,5000);
			    	//}
			    	//for(var j=0;j<va.length;j++) {
			    	//	if(sa.options[i].value==va[j]) {
			    	//		showMessage(i+' => '+sa.options[i].value,5000);
			    	//		sb.options.add(new Option(sa.options[i].text,sa.options[i].value));
			    	//		sa.options.remove(i);
			    	//	}
			    	//}
			    }

			    if(gm.length>0) {
					$("#messagingdetailsgroupdetailsform_%formval% input[name='groupmembers']").val(JSON.stringify(gm));			    	
			    }
		    }

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			$("#messagingdetailsgroupdetailsform_%formval% input[name='method']").val(id);

			var obj = {o:this,id:id};

			$("#messagingdetailsgroupdetailsform_%formval%").ajaxSubmit({
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



						layout_resize_%formval%($("#messagingdetailsgroupdetailsform_%formval% input[name='rowid']").val());

					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {
							if(data.rowid) {
								layout_resize_%formval%();
								messagingmaingroupsgrid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("groups");								
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
			doSelect_%formval%("groups");
			//layout_resize_%formval%();
		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			doSelect_%formval%("groups");
			//layout_resize_%formval%();
		};
	}

	messagingdetailsgroup_%formval%();

</script>