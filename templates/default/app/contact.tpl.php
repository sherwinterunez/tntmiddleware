<?php
$moduleid = 'contact';
$submod = 'contact';
$templatemainid = $moduleid.'main';
$templatedetailid = $moduleid.'detail';

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method==$moduleid.'new'||$method==$moduleid.'edit') {
	$readonly = false;
}

if(!empty($vars['post']['wid'])) {
	$wid = $vars['post']['wid'];
} else {
	die('Invalid Window ID');
}

$myToolbar = array($moduleid.'new',$moduleid.'refresh');

//$myToolbar = array($moduleid.'new',$moduleid.'edit',$moduleid.'save',$moduleid.'cancel',$moduleid.'refresh');

//pre(array('$vars'=>$vars));
?>
<!--
<?php /*pre(array('$_SESSION'=>$_SESSION)); pre(array('$vars'=>$vars));*/ ?>
-->
<style>
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?> {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?>tabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% {
		padding: 0;
		/*border: 1px solid #f00;*/
		overflow: auto;
		/*overflow-y: scroll;*/
		margin-top: 3px;
	}
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .contact_grid_%formval%,
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .contact_grid_%formval% .dhxform_control {
		padding: 0;
	}
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .contact_grid_%formval% .dhxform_control .gridbox {
		border: none;
	}
	#<?php echo $wid; ?> .dhxtabbar_base_dhx_web div.dhx_cell_tabbar div.dhx_cell_cont_tabbar {
		display: none;
	}
	#<?php echo $wid; ?> .dhxtabbar_base_dhx_web div.dhxtabbar_tabs {
		border-top: none;
		border-left: none;
		border-right: none;
	}
	#<?php echo $wid; ?> .cls_bottomspace {
		display: block;
		/*height: 500px;*/
		border: 1px solid #f00;
		padding-bottom: 10px;
	}
</style>
<div id="<?php echo $wid; ?>">
	<div id="<?php echo $wid.$templatedetailid.$submod; ?>" class="navbar-default-bg">
		<div id="<?php echo $wid.$templatedetailid.$submod; ?>tabform_%formval%"></div>
		<div id="<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
</div>
<script>

	function <?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(myWinObj) {
		var dim = myWinObj.getDimension();

		//console.log('DIM: '+dim);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").height(dim[1]-103);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").width(dim[0]-16);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .contact_grid_%formval% .dhxform_container").height(dim[1]-103);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .contact_grid_%formval% .dhxform_container").width(dim[0]-24);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% div.dhxform_block").height(dim[1]-103);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% div.dhxform_block").width(dim[0]-24);

		/*$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_studentyearlevel_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_studentyearlevel_%formval% .dhxform_container").width(dim[0]-54);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeedepartment_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeedepartment_%formval% .dhxform_container").width(dim[0]-54);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeeposition_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeeposition_%formval% .dhxform_container").width(dim[0]-54);*/

		//$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_threshold_%formval% .dhxform_container").height(dim[1]-150);
		//$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_threshold_%formval% .dhxform_container").width(dim[0]-54);

		if(typeof(myWinObj.myGridContacts)!='undefined') {
			try {
				myWinObj.myGridContacts.setSizes();
			} catch(e) {}
		}

		/*if(typeof(myWinObj.myGridStudentYearlevel)!='undefined') {
			try {
				myWinObj.myGridStudentYearlevel.setSizes();
			} catch(e) {}
		}

		if(typeof(myWinObj.myGridEmployeeDepartment)!='undefined') {
			try {
				myWinObj.myGridEmployeeDepartment.setSizes();
			} catch(e) {}
		}

		if(typeof(myWinObj.myGridEmployeePosition)!='undefined') {
			try {
				myWinObj.myGridEmployeePosition.setSizes();
			} catch(e) {}
		}*/

		//if(typeof(myWinObj.myGridThreshold)!='undefined') {
		//	try {
		//		myWinObj.myGridThreshold.setSizes();
		//	} catch(e) {}
		//}
	}

	function <?php echo $wid.$templatedetailid.$submod; ?>_%formval%() {

		var that = this;

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myWinObj = srt.windows['<?php echo $wid; ?>'];

		var myWinToolbar = myWinObj.toolbar;

		var myToolbar = <?php echo json_encode($myToolbar); ?>;

		var myTabbar = new dhtmlXTabBar("<?php echo $wid.$templatedetailid.$submod; ?>tabform_%formval%");

		myTabbar.setArrowsMode("auto");

		myTabbar.addTab("tbContactRecords", "Contact Records");
		//myTabbar.addTab("tbLoginNotification", "Login Notification");

		myTabbar.tabs("tbContactRecords").setActive();

		myWinToolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 130, inputWidth: 200},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "<?php echo $moduleid; ?>"},
				{type: "hidden", name: "formid", value: "<?php echo $moduleid; ?>"},
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo !empty($vars['post']['rowid']) ? $vars['post']['rowid'] : ''; ?>"},
				{type: "hidden", name: "wid", value: "<?php echo !empty($vars['post']['wid']) ? $vars['post']['wid'] : ''; ?>"},
			]},
			{type: "block", name: "tbContactRecords", hidden:false, width: 1150, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbContactRecords']) ? json_encode($params['tbContactRecords']) : '[]'; ?>},
			/*{type: "label", label: ""}*/
		];

		if(typeof(myWinObj.form)!='undefined') {
			//try {
				console.log('Form unloaded!');
				myWinObj.form.unload();
			//} catch(e) {}
		}

		var myForm = myWinObj.form = new dhtmlXForm("<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=contacts&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridContacts)!='null'&&typeof(myWinObj.myGridContacts)!='undefined'&&myWinObj.myGridContacts!=null) {
				try {
					myWinObj.myGridContacts.destructor();
					myWinObj.myGridContacts = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridContacts = myWinObj.myGridContacts = new dhtmlXGridObject(myForm.getContainer('contact_grid'));

			myGridContacts.setImagePath("/codebase/imgs/")

			myGridContacts.setHeader("#master_checkbox, Seq, ID, School Year, Student No, RFID, First Name, Last Name, Middle Name, Year Level, Section, Guardian Name, Mobile No, Email");

			myGridContacts.setInitWidths("35,50,50,120,120,120,120,120,120,120,120,120,120,120");

			myGridContacts.setColAlign("center,center,center,left,left,left,left,left,left,left,left,left,left,left");

			myGridContacts.setColTypes("ch,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

			myGridContacts.setColSorting("int,int,int,str,str,str,str,str,str,str,str,str,str,str");

			myGridContacts.attachHeader("&nbsp;,&nbsp;,&nbsp;,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#combo_filter,#combo_filter,#text_filter,#text_filter,#text_filter");

			myGridContacts.init();

			myGridContacts.attachEvent("onRowDblClicked", function(rowId,cellIndex){

				var obj = {};
				obj.routerid = settings.router_id;
				obj.action = 'formonly';
				obj.formid = '<?php echo $templatedetailid.$submod; ?>';
				obj.module = '<?php echo $moduleid; ?>';
				obj.method = 'onrowselect';
				obj.rowid = rowId;
				obj.formval = '%formval%';
				obj.parentwid = '<?php echo $wid; ?>';

				//obj.title = 'Sim Cards / '+myGrid.cells(rowId,2).getValue()+' / '+myGrid.cells(rowId,3).getValue();

				obj.title = 'Student Profile';

				openWindow(obj, function(winobj,obj){
					console.log(obj);

					var myTab = srt.getTabUsingFormVal('%formval%');

					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {winobj:winobj,obj:obj},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.'studentprofile'; ?>&module=<?php echo $moduleid; ?>&method=onrowselect&rowid="+rowId+"&formval=%formval%&wid="+obj.wid,
					}, function(ddata,odata){
						if(ddata.toolbar) {
							console.log(ddata.toolbar);
							odata.winobj.toolbar = odata.winobj.attachToolbar({
								icons_path: settings.template_assets+"toolbar/",
							});
							odata.winobj.toolbar.toolbardata = ddata.toolbar;
							odata.winobj.toolbar.parentobj = winobj;
							odata.winobj.toolbar.owin = odata.obj;
							odata.winobj.toolbar.parenttoolbar = myWinToolbar;
							odata.winobj.toolbar.parentcontext = that;
							odata.winobj.toolbar.parentrefresh = function() {
								myWinToolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick.apply(myWinToolbar,['<?php echo $moduleid; ?>refresh',odata.obj.formval]);
							};
							odata.winobj.toolbar.tbRender(ddata.toolbar);
							odata.winobj.toolbar.attachEvent("onClick", function(id){
								showMessage("ToolbarOnClick: "+id,5000);

								var tdata = this.getToolbarData(id);

								if(!tdata) return false;

								if(typeof(tdata.onClick)=='function') {
									var ret = tdata.onClick.apply(this,[id,'%formval%',odata.obj.wid]);
									//showMessage('ret: '+ret,5000);

									return ret;
								}

								showMessage("Toolbar ID "+id+" not yet implemented!",10000);
								return false;
							});
						}
						if(ddata.html) {
							jQuery("#"+odata.obj.wid).html(ddata.html);
							//layout_resize_%formval%();
						}
					});
				});

				/*myTab.toolbar.disableAll();

				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method=onrowselect&rowid="+rowId+"&formval=%formval%",
				}, function(ddata,odata){
					if(ddata.html) {
						jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);
						layout_resize_%formval%();
					}
				});*/

			});

			try {
				myGridContacts.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridContacts.forEachRow(function(id){
						//myGridContacts.cells(id,1).setDisabled(true);
						//myGridContacts.cells(id,2).setDisabled(true);
						//myGridContacts.cells(id,3).setDisabled(true);
						//myGridContacts.cells(id,4).setDisabled(true);
						//myGridContacts.cells(id,5).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].yearlevel) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridContacts.getColumnCombo(3);

								myCombo.load(JSON.stringify(ddata.rows[x].yearlevel));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridContacts.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							/*if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridContacts.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}*/
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridContacts.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridContacts.getColumnCombo(4);

								myCombo.load(JSON.stringify(ddata.rows[x].discount));

								myCombo.enableFilteringMode(true);
							}
							*/
						}
					}
				},'json');
			} catch(e) {
				//console.log(e);
			}

		});

///////////////////////////////////

		<?php if($method==$moduleid.'new') { ?>

		myWinToolbar.disableAll();

		myWinToolbar.enableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		//myForm.setItemFocus("simcard_name");

		myForm.enableLiveValidation(true);

		myWinToolbar.showOnly(myToolbar);

		<?php } else if($method==$moduleid.'edit') { ?>

		myWinToolbar.disableAll();

		myWinToolbar.enableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		//myForm.setItemFocus("simcard_name");

		myForm.enableLiveValidation(true);

		myWinToolbar.showOnly(myToolbar);

		<?php } else if($method==$moduleid.'save') { ?>

		myWinToolbar.disableAll();

		myWinToolbar.enableOnly(myToolbar);

		myWinToolbar.disableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		myWinToolbar.showOnly(myToolbar);

		<?php } else { ?>

		myWinToolbar.disableAll();

		myWinToolbar.enableOnly(myToolbar);

		myWinToolbar.disableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		<?php 	/*if(empty($vars['post']['rowid'])) {

		myWinToolbar.disableItem('<?php echo $moduleid; ?>edit');

		myWinToolbar.disableItem('<?php echo $moduleid; ?>delete');

		 	}*/ ?>

		myWinToolbar.showOnly(myToolbar);

		<?php } ?>

		//setTimeout(function(){
		//	layout_resize_%formval%();
		//},100);

///////////////////////////////////

		<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(myWinObj);

///////////////////////////////////


		if(typeof myWinObj.onCloseId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onCloseId);
			} catch(e) {}
		}

		myWinObj.onCloseId = myWinObj.attachEvent("onClose", function(win){
			console.log('onClose');
			win.form.unload();
			return true;
		});

		//console.log('eventId: '+srt.windows['<?php echo $wid; ?>'].onCloseId);

		if(typeof myWinObj.onResizeFinishId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onResizeFinishId);
			} catch(e) {}
		}

		myWinObj.onResizeFinishId = myWinObj.attachEvent("onResizeFinish", function(win){
			//win.form.unload();
			myTabbar.setSizes();
			//console.log(win.getId());
			//console.log(win.getDimension());

			<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(win);

			return true;
		});

		if(typeof myWinObj.onMaximizeId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onMaximizeId);
			} catch(e) {}
		}

		myWinObj.onMaximizeId = myWinObj.attachEvent("onMaximize", function(win){
			//win.form.unload();
			myTabbar.setSizes();
			//console.log(win.getId());
			//console.log(win.getDimension());

			<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(win);

			return true;
		});

		if(typeof myWinObj.onMinimizeId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onMinimizeId);
			} catch(e) {}
		}

		myWinObj.onMinimizeId = myWinObj.attachEvent("onMinimize", function(win){
			//win.form.unload();
			myTabbar.setSizes();
			//console.log(win.getId());
			//console.log(win.getDimension());

			<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(win);

			return true;
		});

///////////////////////////////////

		myTabbar.attachEvent("onTabClick", function(id, lastId){

			myTabbar.forEachTab(function(tab){
			    var tbId = tab.getId();

			    if(id==tbId) {
			    	srt.windows['<?php echo $wid; ?>'].form.showItem(tbId);
				    //myForm2_%formval%.showItem(tbId);
			    } else {
			    	srt.windows['<?php echo $wid; ?>'].form.hideItem(tbId);
				    //myForm2_%formval%.hideItem(tbId);
			    }
			});

		});

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

			/*if(id=='txt_optionsvalue') {
				msg = 'Please enter Value. This field is required.';
			} else
			if(id=='txt_optionsname') {
				msg = 'Please enter Name. This field is required.';
			} else
			if(id=='txt_optionstype') {
				msg = 'Please enter Type. This field is required.';
			}

			this.setNote(id,{text:msg});*/

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myForm.attachEvent("onBlur", function(name){
		    //showMessage("onBlur: ["+name+"] "+name.length,5000);

		    /*var mobileNo = myForm.getItemValue(name);
		    var provider;

		    if(name=='simcard_number') {
		    	if(provider=srt.ValidateMobileNo(mobileNo)) {
		    		myForm.setItemValue('simcard_provider',provider,true);
		    	} else {
		    		myForm.setItemValue('simcard_provider','',true);
		    	}
		    }*/
		});

///////////////////////////////////

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>new').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var winObj = this.parentobj;
			var myForm = winObj.form;

			var wid = winObj.getId();

			console.log('id: '+id);
			console.log('formval: '+formval);
			console.log('wid: '+wid);

			//console.log(this.parentobj.getId());
			console.log(this.parentobj);
			console.log(this.parentobj.form);

			console.log('method: '+myForm.getItemValue('method'));

			var obj = {};
			obj.routerid = settings.router_id;
			obj.action = 'formonly';
			obj.formid = '<?php echo $templatedetailid; ?>studentprofile';
			obj.module = '<?php echo $moduleid; ?>';
			obj.method = id;
			obj.rowid = 0;
			obj.formval = '%formval%';

			obj.title = 'New Student Profile';

			openWindow(obj, function(winobj,obj){
				console.log(obj);

				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {winobj:winobj,obj:obj},
					pdata: "routerid="+settings.router_id+"&action="+obj.action+"&formid="+obj.formid+"&module="+obj.module+"&method="+obj.method+"&rowid="+obj.rowid+"&formval="+obj.formval+"&wid="+obj.wid,
				}, function(ddata,odata){
					if(ddata.toolbar) {
						//console.log(ddata.toolbar);
						odata.winobj.toolbar = odata.winobj.attachToolbar({
							icons_path: settings.template_assets+"toolbar/",
						});
						odata.winobj.toolbar.toolbardata = ddata.toolbar;
						odata.winobj.toolbar.parentobj = winobj;
						odata.winobj.toolbar.owin = odata.obj;
						odata.winobj.toolbar.parenttoolbar = myWinToolbar;
						odata.winobj.toolbar.parentcontext = that;
						odata.winobj.toolbar.parentrefresh = function() {
							myWinToolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick.apply(myWinToolbar,['<?php echo $moduleid; ?>refresh',odata.obj.formval]);
						};
						odata.winobj.toolbar.tbRender(ddata.toolbar);
						odata.winobj.toolbar.attachEvent("onClick", function(id){
							showMessage("ToolbarOnClick: "+id,5000);

							var tdata = this.getToolbarData(id);

							if(!tdata) return false;

							if(typeof(tdata.onClick)=='function') {
								var ret = tdata.onClick.apply(this,[id,'%formval%',odata.obj.wid]);
								//showMessage('ret: '+ret,5000);

								return ret;
							}

							showMessage("Toolbar ID "+id+" not yet implemented!",10000);
							return false;
						});
					}
					if(ddata.html) {
						jQuery("#"+odata.obj.wid).html(ddata.html);
						//layout_resize_%formval%();
					}
				},function(ddata,odata){
					console.log('hello sherwin!');
					if(obj.wid) {
						closeWindow(obj.wid);
					}
				});
			});

			/*myTab.postData('/'+settings.router_id+'/json/', {
				odata: {wid:wid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $moduleid; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval="+formval+"&wid="+wid,
			}, function(ddata,odata){
				if(ddata.html) {
					jQuery("#formdiv_%formval% #<?php echo $wid; ?>").parent().html(ddata.html);
				}
			});*/
		};

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>edit').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var winObj = this.parentobj;
			var myForm = winObj.form;

			var wid = winObj.getId();

			console.log('id: '+id);
			console.log('formval: '+formval);
			console.log('wid: '+wid);

			//console.log(this.parentobj.getId());
			console.log(this.parentobj);
			console.log(this.parentobj.form);

			console.log('method: '+myForm.getItemValue('method'));

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {wid:wid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $moduleid; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval="+formval+"&wid="+wid,
			}, function(ddata,odata){
				if(ddata.html) {
					jQuery("#formdiv_%formval% #<?php echo $wid; ?>").parent().html(ddata.html);
				}
			});
		};

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>cancel').onClick = myWinToolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);

			var winObj = this.parentobj;
			var myForm = winObj.form;

			var wid = winObj.getId();

			console.log('id: '+id);
			console.log('formval: '+formval);
			console.log('wid: '+wid);

			//console.log(this.parentobj.getId());
			console.log(this.parentobj);
			console.log(this.parentobj.form);

			console.log('method: '+myForm.getItemValue('method'));

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {wid:wid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $moduleid; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval="+formval+"&wid="+wid,
			}, function(ddata,odata){
				if(ddata.html) {
					jQuery("#formdiv_%formval% #<?php echo $wid; ?>").parent().html(ddata.html);
				}
			});
		};

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>save').onClick = function(id,formval,wid) {
			//showMessage("toolbar: "+id,5000);

			var winObj = this.parentobj;
			var myForm = winObj.form;

			myForm.trimAllInputs();

			if(!myForm.validate()) return false;

			showSaving();

			myForm.setItemValue('method', id);

			var obj = {o:this,id:id,formval:formval};

			var extra = [];

			$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: obj,
				data: extra,
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

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {

							myWinToolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick.apply(obj.o,['<?php echo $moduleid; ?>refresh',obj.formval]);

							showMessage(data.return_message,5000);
						}
					}

				}
			});

			return false;
		};


	}

	<?php echo $wid.$templatedetailid.$submod; ?>_%formval%();

</script>
