<?php
$moduleid = 'newmessage';
$submod = 'newmessage';
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

//$myToolbar = array($moduleid.'outbox',$moduleid.'now',$moduleid.'refresh');

$myToolbar = array($moduleid.'now',$moduleid.'refresh');

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
		padding: 10px;
		/*border: 1px solid #f00;*/
		overflow: auto;
		/*overflow-y: scroll;*/
		margin-top: 3px;
	}
	#<?php echo $wid; ?> #<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_blockcontacts_%formval% {
		/*border: 1px solid #f00;*/
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
		//var myForm = myWinObj.form;

		//console.log('DIM: '+dim);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").height(dim[1]-123);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").width(dim[0]-36);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .tbDetails_%formval%").width(dim[0]-36);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .tbDetails_%formval% div").first().width(dim[0]-36);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_contacts_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_contacts_%formval% .dhxform_container").width((dim[0]/3)-10);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_yearlevel_%formval% .dhxform_container").height((dim[1]/2)-80);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_yearlevel_%formval% .dhxform_container").width((dim[0]/3)-10);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_section_%formval% .dhxform_container").height((dim[1]/2)-80);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_section_%formval% .dhxform_container").width((dim[0]/3)-10);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% textarea[name='newmessage_sms']").height(dim[1]-258);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_blockcontacts_%formval%").height(dim[1]-140);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_blockcontacts_%formval%").width((dim[0]/3));

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_blockyearlevel_%formval%").height(dim[1]-140);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .newmessage_blockyearlevel_%formval%").width((dim[0]/3));

		//$("#"+myWinObj.form.getDOM('newmessage_sms')._rId).height(dim[1]-200);

		//$("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val(txt_optionnumber);

		/*if(typeof myWinObj.form != 'undefined') {
			//console.log(myWinObj.form);
			try {
				myWinObj.form.setItemHeight('newmessage_blockcontacts',dim[1]-804);
				//console.log('hello sherwin!',myWinObj.form.getDOM('newmessage_sms'));
				//console.log('hello sherwin!',myWinObj.form.getObj('newmessage_sms'));
			} catch(e) {
				console.log(e);
			}

		}*/

		//$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_threshold_%formval% .dhxform_container").height(dim[1]-150);
		//$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_threshold_%formval% .dhxform_container").width(dim[0]-54);

		if(typeof(myWinObj.myGridNewMessageContacts)!='undefined') {
			try {
				myWinObj.myGridNewMessageContacts.setSizes();
			} catch(e) {}
		}

		if(typeof(myWinObj.myGridNewMessageYearLevel)!='undefined') {
			try {
				myWinObj.myGridNewMessageYearLevel.setSizes();
			} catch(e) {}
		}

		if(typeof(myWinObj.myGridNewMessageSection)!='undefined') {
			try {
				myWinObj.myGridNewMessageSection.setSizes();
			} catch(e) {}
		}

		/*if(typeof(myWinObj.myGridEmployeePosition)!='undefined') {
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

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myWinObj = srt.windows['<?php echo $wid; ?>'];

		var myWinToolbar = myWinObj.toolbar;

		var myToolbar = <?php echo json_encode($myToolbar); ?>;

		var myTabbar = new dhtmlXTabBar("<?php echo $wid.$templatedetailid.$submod; ?>tabform_%formval%");

		myTabbar.setArrowsMode("auto");

		myTabbar.addTab("tbDetails", "Compose");
		///myTabbar.addTab("tbLoginNotification", "Login Notification");

		myTabbar.tabs("tbDetails").setActive();

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
			{type: "block", name: "tbDetails", className: "tbDetails_%formval%", hidden:false, width: 1500, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbDetails']) ? json_encode($params['tbDetails']) : '[]'; ?>},
			{type: "label", label: ""}
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

		//myForm.hideItem('tbLoginNotification');

///////////////////////////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=newmessagecontacts&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridNewMessageContacts)!='null'&&typeof(myWinObj.myGridNewMessageContacts)!='undefined'&&myWinObj.myGridNewMessageContacts!=null) {
				try {
					myWinObj.myGridNewMessageContacts.destructor();
					myWinObj.myGridNewMessageContacts = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridNewMessageContacts = myWinObj.myGridNewMessageContacts = new dhtmlXGridObject(myForm.getContainer('newmessage_contacts'));

			myGridNewMessageContacts.setImagePath("/codebase/imgs/")

			myGridNewMessageContacts.setHeader("#master_checkbox, ID, Mobile No, Student Name");

			myGridNewMessageContacts.setInitWidths("35,50,100,*");

			myGridNewMessageContacts.setColAlign("center,center,left,left");

			myGridNewMessageContacts.setColTypes("ch,ro,ro,ro");

			myGridNewMessageContacts.setColSorting("int,int,str,str");

			myGridNewMessageContacts.attachHeader("&nbsp;,&nbsp;,#text_filter,#text_filter");

			myGridNewMessageContacts.init();

			try {
				myGridNewMessageContacts.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridNewMessageContacts.forEachRow(function(id){
						//myGridNewMessageContacts.cells(id,1).setDisabled(true);
						//myGridNewMessageContacts.cells(id,2).setDisabled(true);
						//myGridNewMessageContacts.cells(id,3).setDisabled(true);
						//myGridNewMessageContacts.cells(id,4).setDisabled(true);
						//myGridNewMessageContacts.cells(id,5).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].yearlevel) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridNewMessageContacts.getColumnCombo(3);

								myCombo.load(JSON.stringify(ddata.rows[x].yearlevel));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridNewMessageContacts.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							/*if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageContacts.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}*/
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageContacts.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageContacts.getColumnCombo(4);

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

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=newmessageyearlevel&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridNewMessageYearLevel)!='null'&&typeof(myWinObj.myGridNewMessageYearLevel)!='undefined'&&myWinObj.myGridNewMessageYearLevel!=null) {
				try {
					myWinObj.myGridNewMessageYearLevel.destructor();
					myWinObj.myGridNewMessageYearLevel = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridNewMessageYearLevel = myWinObj.myGridNewMessageYearLevel = new dhtmlXGridObject(myForm.getContainer('newmessage_yearlevel'));

			myGridNewMessageYearLevel.setImagePath("/codebase/imgs/")

			myGridNewMessageYearLevel.setHeader("#master_checkbox, ID, Year Level");

			myGridNewMessageYearLevel.setInitWidths("35, 50,*");

			myGridNewMessageYearLevel.setColAlign("center,center,left");

			myGridNewMessageYearLevel.setColTypes("ch,ro,ro");

			myGridNewMessageYearLevel.setColSorting("int,int,str");

			myGridNewMessageYearLevel.attachHeader("&nbsp;,&nbsp;,#text_filter");

			myGridNewMessageYearLevel.init();

			try {
				myGridNewMessageYearLevel.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridNewMessageYearLevel.forEachRow(function(id){
						//myGridNewMessageYearLevel.cells(id,1).setDisabled(true);
						//myGridNewMessageYearLevel.cells(id,2).setDisabled(true);
						//myGridNewMessageYearLevel.cells(id,3).setDisabled(true);
						//myGridNewMessageYearLevel.cells(id,4).setDisabled(true);
						//myGridNewMessageYearLevel.cells(id,5).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].yearlevel) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridNewMessageYearLevel.getColumnCombo(3);

								myCombo.load(JSON.stringify(ddata.rows[x].yearlevel));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridNewMessageYearLevel.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							/*if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageYearLevel.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}*/
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageYearLevel.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageYearLevel.getColumnCombo(4);

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

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=newmessagesection&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridNewMessageSection)!='null'&&typeof(myWinObj.myGridNewMessageSection)!='undefined'&&myWinObj.myGridNewMessageSection!=null) {
				try {
					myWinObj.myGridNewMessageSection.destructor();
					myWinObj.myGridNewMessageSection = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridNewMessageSection = myWinObj.myGridNewMessageSection = new dhtmlXGridObject(myForm.getContainer('newmessage_section'));

			myGridNewMessageSection.setImagePath("/codebase/imgs/")

			myGridNewMessageSection.setHeader("#master_checkbox, ID, Section");

			myGridNewMessageSection.setInitWidths("35, 50,*");

			myGridNewMessageSection.setColAlign("center,center,left");

			myGridNewMessageSection.setColTypes("ch,ro,ro");

			myGridNewMessageSection.setColSorting("int,int,str");

			myGridNewMessageSection.attachHeader("&nbsp;,&nbsp;,#text_filter");

			myGridNewMessageSection.init();

			try {
				myGridNewMessageSection.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridNewMessageSection.forEachRow(function(id){
						//myGridNewMessageSection.cells(id,1).setDisabled(true);
						//myGridNewMessageSection.cells(id,2).setDisabled(true);
						//myGridNewMessageSection.cells(id,3).setDisabled(true);
						//myGridNewMessageSection.cells(id,4).setDisabled(true);
						//myGridNewMessageSection.cells(id,5).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].yearlevel) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridNewMessageSection.getColumnCombo(3);

								myCombo.load(JSON.stringify(ddata.rows[x].yearlevel));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridNewMessageSection.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							/*if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageSection.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}*/
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageSection.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridNewMessageSection.getColumnCombo(4);

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

			<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(this);

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

			<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(this);

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

			<?php echo $wid.$templatedetailid.$submod; ?>_resize_%formval%(this);

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

			if(name=='newmessage_sms') {
				var x = myForm.getItemValue(name);

				var l = x.length;

				var totalsms = '';

				if(l>160) {
					var smscnt = Math.ceil(l / 160);

					totalsms = ' ('+smscnt+' SMS)';
				}

				myForm.setItemValue('newmessage_totalchars','Characters: '+l+totalsms);
			}

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

			if(name=='newmessage_sms') {
				if(myForm.getItemValue(name)=='') {
					myForm.setItemValue(name,'Enter your message here');
				}

				var x = myForm.getItemValue(name);

				myForm.setItemValue('newmessage_totalchars','Characters: '+x.length);
			}

		});

		myForm.attachEvent("onFocus", function(name){
			if(name=='newmessage_sms') {
				if(myForm.getItemValue(name)=='Enter your message here') {
					myForm.setItemValue(name,'');
				}

				var x = myForm.getItemValue(name);

				myForm.setItemValue('newmessage_totalchars','Characters: '+x.length);
			}
			//console.log('name: '+name);
			//console.log(myForm.getItemValue(name));
		});

///////////////////////////////////

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>outbox').onClick = myWinToolbar.getToolbarData('<?php echo $moduleid; ?>now').onClick = function(id,formval) {
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

			myForm.trimAllInputs();

			console.log('method: '+myForm.getItemValue('method'));

			var newmessage_sms = myForm.getItemValue('newmessage_sms');

			if(newmessage_sms) {
			} else {
				showAlertError('Cannot send an empty message!');
				return false;
			}

			var newmessage_contacts = [];

			myWinObj.myGridNewMessageContacts.forEachRow(function(id){
				var checked = parseInt(myWinObj.myGridNewMessageContacts.cells(id,0).getValue());
				var val = myWinObj.myGridNewMessageContacts.cells(id,1).getValue();
				if(checked&&val) {
					newmessage_contacts.push(val);
				}
			});

			//console.log({newmessage_contacts:newmessage_contacts});

			var newmessage_yearlevel = [];

			myWinObj.myGridNewMessageYearLevel.forEachRow(function(id){
				var checked = parseInt(myWinObj.myGridNewMessageYearLevel.cells(id,0).getValue());
				var val = myWinObj.myGridNewMessageYearLevel.cells(id,1).getValue();
				if(checked&&val) {
					newmessage_yearlevel.push(val);
				}
			});

			console.log({newmessage_yearlevel:newmessage_yearlevel});

			var newmessage_section = [];

			myWinObj.myGridNewMessageSection.forEachRow(function(id){
				var checked = parseInt(myWinObj.myGridNewMessageSection.cells(id,0).getValue());
				var val = myWinObj.myGridNewMessageSection.cells(id,1).getValue();
				if(checked&&val) {
					newmessage_section.push(val);
				}
			});

			console.log({newmessage_section:newmessage_section});

			var newmessage_sendto = [];

			var to_number = myForm.getItemValue('newmessage_sendto');

			var anum = to_number.split(/,|;| /);

			for(var p in anum) {
				if(srt.ValidateMobileNo(anum[p])) {
					newmessage_sendto.push(anum[p]);
				}
			}

			console.log({newmessage_sendto:newmessage_sendto});

			//console.log(monumbers);

			var newmessage_sendpushnotification = 0;

			if(myForm.isItemChecked('newmessage_sendpushnotification')) {
				newmessage_sendpushnotification = 1;
			}

			//if((newmessage_sendto&&newmessage_sendto.length>0)||newmessage_contacts||newmessage_yearlevel||newmessage_section) {
			//} else {
			//	showAlertError('Please select or enter where to send the message.');
			//}

			var valid = false;

			if(newmessage_sendto&&newmessage_sendto.length>0) {
				valid = true;
			}

			if(newmessage_contacts&&newmessage_contacts.length>0) {
				valid = true;
			}

			if(newmessage_yearlevel&&newmessage_yearlevel.length>0) {
				valid = true;
			}

			if(newmessage_section&&newmessage_section.length>0) {
				valid = true;
			}

			if(valid) {
			} else {
				showAlertError('Please select or enter where to send the message.');
				return false;
			}

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {wid:wid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $moduleid; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval="+formval+"&wid="+wid+"&sms="+encodeURIComponent(newmessage_sms)+"&contacts="+encodeURIComponent(newmessage_contacts)+"&yearlevel="+encodeURIComponent(newmessage_yearlevel)+"&section="+encodeURIComponent(newmessage_section)+"&sendto="+encodeURIComponent(newmessage_sendto)+"&sendpushnotification="+newmessage_sendpushnotification,
			}, function(ddata,odata){
				//if(ddata.html) {
				//	jQuery("#formdiv_%formval% #<?php echo $wid; ?>").parent().html(ddata.html);
				//}
				if(ddata.error_code) {
					showAlertError(ddata.error_message);
				}
				if(ddata.return_code) {
					if(ddata.return_code=='SUCCESS') {

						//myWinToolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick.apply(obj.o,['<?php echo $moduleid; ?>refresh',obj.formval]);

						showMessage(ddata.return_message,5000);
					}
				}
			});
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
