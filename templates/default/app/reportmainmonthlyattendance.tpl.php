<?php
$moduleid = 'report';
$submod = 'monthlyattendance';
$templatemainid = $moduleid.'main';
$templatedetailid = $moduleid.'detail';
$mainheight = 250;

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

//$myToolbar = array($moduleid.'refresh',$moduleid.'exportpdf',$moduleid.'print',$moduleid.'sep1',$moduleid.'from',$moduleid.'datefrom',$moduleid.'to',$moduleid.'dateto');

$myToolbar = array($moduleid.'refresh',$moduleid.'print',$moduleid.'sep1',$moduleid.'from',$moduleid.'datefrom',$moduleid.'to',$moduleid.'dateto');

?>
<!--
<?php /*print_r(array('$vars'=>$vars));*/ ?>
-->
<style>
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?> {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?> #<?php echo $templatemainid.$submod; ?>tabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% {
		padding: 10px;
		/*border: 1px solid #f00;*/
		overflow: hidden;
		overflow-y: scroll;
		margin-top: 3px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .schoolName_%formval% {
		font-size: 25px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .period_%formval% {
		font-size: 14px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .monthlyattendancereport_%formval% {
		font-size: 18px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .yearlevel_%formval% {
		font-size: 16px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .section_%formval% {
		font-size: 14px;
		font-weight: normal;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .studentName_%formval% {
		font-size: 12px;
		font-weight: normal;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .studentName_%formval% div.dhxform_txt_label2 {
		overflow: hidden;
		display: block;
		height: 20px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .block_%formval% {
		/*display: block;
		border: 1px solid #00f;*/
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .absent_%formval% {
		display: block;
		width: 32px;
		height: 1px;
		border-bottom: 20px solid #f00;
		margin-top: -3px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .present_%formval% {
		display: block;
		width: 32px;
		height: 1px;
		border-bottom: 20px solid #ffff0b;
		margin-top: -3px;
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .block_%formval% div.dhxform_txt_label2 {
		margin: 0;
		padding: 0;
		/*text-align: center;*/
	}
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .block_%formval% .ddmm_%formval% div.dhxform_txt_label2 {
		margin: 0;
		text-align: center;
	}
	#formdiv_%formval% .dhxtabbar_base_dhx_web div.dhx_cell_tabbar div.dhx_cell_cont_tabbar {
		display: none;
	}
	#formdiv_%formval% .dhxtabbar_base_dhx_web div.dhxtabbar_tabs {
		border-top: none;
		border-left: none;
		border-right: none;
	}
	#formdiv_%formval% .cls_bottomspace {
		display: block;
		/*height: 500px;*/
		border: 1px solid #f00;
		padding-bottom: 10px;
	}
</style>
<div id="<?php echo $templatemainid; ?>">
	<div id="<?php echo $templatemainid.$submod; ?>" class="navbar-default-bg">
		<div id="<?php echo $templatemainid.$submod; ?>tabform_%formval%"></div>
		<div id="<?php echo $templatemainid.$submod; ?>mainform_%formval%"></div>
		<br style="clear:both;" />
	</div>
</div>
<script>

	function <?php echo $wid.$templatemainid.$submod; ?>_resize_%formval%(myWinObj) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		var lbHeight = myTab.layout.cells('b').getHeight();
		var lbWidth = myTab.layout.cells('b').getWidth();

		var dim = myWinObj.getDimension();

		layout_resize_%formval%();

		console.log({lbHeight:lbHeight,lbWidth:lbWidth});

		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_contacts_%formval% .dhxform_container").height(lbHeight-140);
		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_contacts_%formval% .dhxform_container").width((lbWidth*.4)-30);

		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_yearlevel_%formval% .dhxform_container").height(lbHeight-140);
		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_yearlevel_%formval% .dhxform_container").width((lbWidth*.3)-30);

		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_section_%formval% .dhxform_container").height(lbHeight-140);
		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_section_%formval% .dhxform_container").width((lbWidth*.3)-30);

		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_blockcontacts_%formval%").height(lbHeight-140);
		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_blockcontacts_%formval%").width((lbWidth*.4)-30);

		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_blockyearlevel_%formval%").height(lbHeight-140);
		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_blockyearlevel_%formval%").width((lbWidth*.3)-30);

		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_blocksection_%formval%").height(lbHeight-140);
		$("#<?php echo $templatemainid.$submod; ?>mainform_%formval% .newmessage_blocksection_%formval%").width((lbWidth*.3)-30);

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

	}

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('b').hideArrow();

	jQuery("#formdiv_%formval% #<?php echo $templatemainid; ?>").parent().css({'overflow':'hidden'});

	function <?php echo $templatemainid.$submod; ?>grid_%formval%(f) {

		var myToolbar = <?php echo json_encode($myToolbar); ?>;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myWinObj = srt.windows['<?php echo $wid; ?>'];

		var myWinToolbar = myWinObj.toolbar;

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.hideAll();

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.showOnly(myToolbar);

		if(typeof(myWinObj.myTabbar)!='null'&&typeof(myWinObj.myTabbar)!='undefined'&&myWinObj.myTabbar!=null) {
			try {
				myWinObj.myTabbar.unload();
				myWinObj.myTabbar = null;
			} catch(e) {
				console.log(e);
			}
		}

		var myTabbar = myWinObj.myTabbar = new dhtmlXTabBar("<?php echo $templatemainid.$submod; ?>tabform_%formval%");

		myTabbar.setArrowsMode("auto");

		myTabbar.addTab("tbDetails", "Parameter");
		myTabbar.addTab("tbReports", "Generated Report");

		myTabbar.tabs("tbDetails").setActive();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 130, inputWidth: 200},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "<?php echo $moduleid; ?>"},
				{type: "hidden", name: "formid", value: "<?php echo $templatemainid.$submod; ?>"},
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method==$moduleid.'edit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "block", name: "tbDetails", hidden:false, width: 1500, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbDetails']) ? json_encode($params['tbDetails']) : '[]'; ?>},
			{type: "block", name: "tbReports", hidden:true, width: 1500, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbReports']) ? json_encode($params['tbReports']) : '[]'; ?>},
			{type: "label", label: ""}
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = myWinObj.form = new dhtmlXForm("<?php echo $templatemainid.$submod; ?>mainform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

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

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=newmessagecontacts&formval=%formval%",
		}, function(ddata,odata){

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

		myGridNewMessageYearLevel.init();

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=newmessageyearlevel&formval=%formval%",
		}, function(ddata,odata){

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

		myGridNewMessageSection.init();

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=newmessagesection&formval=%formval%",
		}, function(ddata,odata){

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

		if(typeof myWinObj.onResizeFinishId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onResizeFinishId);
			} catch(e) {}
		}

		myWinObj.onResizeFinishId = myWinObj.attachEvent("onResizeFinish", function(win){
			myTabbar.setSizes();

			<?php echo $wid.$templatemainid.$submod; ?>_resize_%formval%(this);

			return true;
		});

		if(typeof myWinObj.onMaximizeId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onMaximizeId);
			} catch(e) {}
		}

		myWinObj.onMaximizeId = myWinObj.attachEvent("onMaximize", function(win){
			myTabbar.setSizes();

			<?php echo $wid.$templatemainid.$submod; ?>_resize_%formval%(this);

			return true;
		});

		if(typeof myWinObj.onMinimizeId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onMinimizeId);
			} catch(e) {}
		}

		myWinObj.onMinimizeId = myWinObj.attachEvent("onMinimize", function(win){
			myTabbar.setSizes();

			<?php echo $wid.$templatemainid.$submod; ?>_resize_%formval%(this);

			return true;
		});

///////////////////////////////////

		myTabbar.attachEvent("onTabClick", function(id, lastId){

			//showMessage("onTabClick: "+id+", "+lastId,5000);

			if(id==lastId) {
				return true;
			}

			myTabbar.forEachTab(function(tab){
					var tbId = tab.getId();

					if(id==tbId) {

						if(tbId=='tbReports') {

							var contact = [];
							var yearlevel = [];
							var section = [];

							myWinObj.myGridNewMessageContacts.forEachRow(function(id){
								var checked = parseInt(myWinObj.myGridNewMessageContacts.cells(id,0).getValue());
								var val = myWinObj.myGridNewMessageContacts.cells(id,1).getValue();
								if(checked&&val) {
									contact.push(id);
								}
							});

							myWinObj.myGridNewMessageYearLevel.forEachRow(function(id){
								var checked = parseInt(myWinObj.myGridNewMessageYearLevel.cells(id,0).getValue());
								var val = myWinObj.myGridNewMessageYearLevel.cells(id,1).getValue();
								if(checked&&val) {
									yearlevel.push(id);
								}
							});

							myWinObj.myGridNewMessageSection.forEachRow(function(id){
								var checked = parseInt(myWinObj.myGridNewMessageSection.cells(id,0).getValue());
								var val = myWinObj.myGridNewMessageSection.cells(id,1).getValue();
								if(checked&&val) {
									section.push(id);
								}
							});

							//console.log({yearlevel:yearlevel,section:section,myWinObj:myWinObj});

							var datefrom = myTab.toolbar.getValue("<?php echo $moduleid; ?>datefrom");
							var dateto = myTab.toolbar.getValue("<?php echo $moduleid; ?>dateto");

							myTab.postData('/'+settings.router_id+'/json/', {
								//odata: {rowid:rowid},
								pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatemainid.$submod; ?>&module=<?php echo $moduleid; ?>&method=generatereport&contact="+contact+"&section="+section+"&yearlevel="+yearlevel+"&formval=%formval%&wid="+myWinObj._idd+"&datefrom="+datefrom+"&dateto="+dateto,
							}, function(ddata,odata){

								console.log({ddata:ddata});

								if(ddata.tbReports) {
									var tbReports = {type: "block", name: "tbReports", className: "clsReports", hidden: true, width: 1500, blockOffset: 0, offsetTop:0, list:ddata.tbReports};
								    myWinObj.form.removeItem("tbReports");
								    myWinObj.form.addItem(null, tbReports,1);
								    myWinObj.form.showItem(tbId);
								}

							});

						} else {

							myForm2_%formval%.showItem(tbId);

						}

					} else {
						myForm2_%formval%.hideItem(tbId);
					}
			});

		});

///////////////////////////////////

		//myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick = function(id,formval,wid) {

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick = function(id,formval,wid) {
			showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("retail");

			var winObj = this.parentobj;
			var myForm = winObj.form;

			var wid = winObj.getId();

			//console.log('id: '+id);
			//console.log('formval: '+formval);
			//console.log('wid: '+wid);

			//console.log(this.parentobj.getId());
			//console.log(this.parentobj);
			//console.log(this.parentobj.form);

			/*try {
				var rowid = myGrid_%formval%.getSelectedRowId();
				<?php echo $templatemainid.$submod; ?>grid_%formval%(rowid);
			} catch(e) {
				doSelect_%formval%("<?php echo $submod; ?>");
			}*/

			var datefrom = myTab.toolbar.getValue("<?php echo $moduleid; ?>datefrom");
			var dateto = myTab.toolbar.getValue("<?php echo $moduleid; ?>dateto");

			myTab.postData('/'+settings.router_id+'/json/', {
				//odata: {rowid:rowid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatemainid.$submod; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval=%formval%&datefrom="+encodeURIComponent(datefrom)+"&dateto="+encodeURIComponent(dateto)+"&wid="+wid,
			}, function(ddata,odata){

				jQuery("#formdiv_%formval% #<?php echo $templatemainid; ?>").parent().html(ddata.html);

			});

		};

		myWinToolbar.getToolbarData('<?php echo $moduleid; ?>print').onClick = function(id,formval,wid) {
			showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("retail");

			var winObj = this.parentobj;
			var myForm = winObj.form;

			var wid = winObj.getId();

			//console.log('id: '+id);
			//console.log('formval: '+formval);
			//console.log('wid: '+wid);

			//console.log(this.parentobj.getId());
			//console.log(this.parentobj);
			//console.log(this.parentobj.form);

			/*try {
				var rowid = myGrid_%formval%.getSelectedRowId();
				<?php echo $templatemainid.$submod; ?>grid_%formval%(rowid);
			} catch(e) {
				doSelect_%formval%("<?php echo $submod; ?>");
			}*/

			var contact = [];
			var yearlevel = [];
			var section = [];

			myWinObj.myGridNewMessageContacts.forEachRow(function(id){
				var checked = parseInt(myWinObj.myGridNewMessageContacts.cells(id,0).getValue());
				var val = myWinObj.myGridNewMessageContacts.cells(id,1).getValue();
				if(checked&&val) {
					contact.push(id);
				}
			});

			myWinObj.myGridNewMessageYearLevel.forEachRow(function(id){
				var checked = parseInt(myWinObj.myGridNewMessageYearLevel.cells(id,0).getValue());
				var val = myWinObj.myGridNewMessageYearLevel.cells(id,1).getValue();
				if(checked&&val) {
					yearlevel.push(id);
				}
			});

			myWinObj.myGridNewMessageSection.forEachRow(function(id){
				var checked = parseInt(myWinObj.myGridNewMessageSection.cells(id,0).getValue());
				var val = myWinObj.myGridNewMessageSection.cells(id,1).getValue();
				if(checked&&val) {
					section.push(id);
				}
			});

			if(contact.length>0||yearlevel.length>0||yearlevel.length>0) {
			} else {
				showAlertError('Please specify parameters to generate report.');
				return false;
			}

			var datefrom = myTab.toolbar.getValue("<?php echo $moduleid; ?>datefrom");
			var dateto = myTab.toolbar.getValue("<?php echo $moduleid; ?>dateto");

			myTab.postData('/'+settings.router_id+'/json/', {
				//odata: {rowid:rowid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatemainid.$submod; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval=%formval%&datefrom="+encodeURIComponent(datefrom)+"&dateto="+encodeURIComponent(dateto)+"&wid="+wid+"&contact="+contact+"&yearlevel="+yearlevel+"&section="+section,
			}, function(ddata,odata){

				//jQuery("#formdiv_%formval% #<?php echo $templatemainid; ?>").parent().html(ddata.html);

				//window.open('/'+settings.router_id+'/app/print/sample');

				var win = window.open('/'+settings.router_id+'/print/'+ddata.topost,"win","status=yes,scrollbars=yes,toolbar=no,menubar=yes,height=650,width=1200");

				//var win = window.open('/'+settings.router_id+'/print/'+ddata.topost,"_blank");

			});

		};

		<?php echo $wid.$templatemainid.$submod; ?>_resize_%formval%(myWinObj);

  }

  <?php echo $templatemainid.$submod; ?>grid_%formval%();

</script>
