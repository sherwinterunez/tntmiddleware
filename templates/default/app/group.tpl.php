<?php
$moduleid = 'group';
$submod = 'group';
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

$myToolbar = array($moduleid.'edit',$moduleid.'save',$moduleid.'cancel',$moduleid.'refresh');

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
	}
	#<?php echo $wid; ?> .dhxtabbar_base_dhx_skyblue div.dhx_cell_tabbar div.dhx_cell_cont_tabbar {
		display: none;
	}
	#<?php echo $wid; ?> .dhxtabbar_base_dhx_skyblue div.dhxtabbar_tabs {
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

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").height(dim[1]-123);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval%").width(dim[0]-36);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_studentsection_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_studentsection_%formval% .dhxform_container").width(dim[0]-54);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_studentyearlevel_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_studentyearlevel_%formval% .dhxform_container").width(dim[0]-54);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeedepartment_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeedepartment_%formval% .dhxform_container").width(dim[0]-54);

		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeeposition_%formval% .dhxform_container").height(dim[1]-150);
		$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_employeeposition_%formval% .dhxform_container").width(dim[0]-54);

		//$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_threshold_%formval% .dhxform_container").height(dim[1]-150);
		//$("#<?php echo $wid.$templatedetailid.$submod; ?>detailsform_%formval% .group_threshold_%formval% .dhxform_container").width(dim[0]-54);

		if(typeof(myWinObj.myGridStudentSection)!='undefined') {
			try {
				myWinObj.myGridStudentSection.setSizes();
			} catch(e) {}
		}

		if(typeof(myWinObj.myGridStudentYearlevel)!='undefined') {
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
		}

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

		myTabbar.addTab("tbStudentSection", "Student Section");
		myTabbar.addTab("tbStudentYearlevel", "Student Year Level");
		myTabbar.addTab("tbEmployeeDepartment", "Employee Department");
		myTabbar.addTab("tbEmployeePosition", "Employee Position");
		//myTabbar.addTab("tbThreshold", "Threshold");

		myTabbar.tabs("tbStudentSection").setActive();

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
			{type: "block", name: "tbStudentSection", hidden:false, width: 1150, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbStudentSection']) ? json_encode($params['tbStudentSection']) : '[]'; ?>},
			{type: "block", name: "tbStudentYearlevel", hidden:false, width: 1150, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbStudentYearlevel']) ? json_encode($params['tbStudentYearlevel']) : '[]'; ?>},
			{type: "block", name: "tbEmployeeDepartment", hidden: false, width: 1150, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbEmployeeDepartment']) ? json_encode($params['tbEmployeeDepartment']) : '[]'; ?>},
			{type: "block", name: "tbEmployeePosition", hidden: false, width: 1150, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbEmployeePosition']) ? json_encode($params['tbEmployeePosition']) : '[]'; ?>},
			//{type: "block", name: "tbThreshold", hidden: false, width: 1150, blockOffset: 0, offsetTop:0, list:<?php echo !empty($params['tbThreshold']) ? json_encode($params['tbThreshold']) : '[]'; ?>},
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

		//myTabbar.addTab("tbStudentSection", "Student Section");
		//myTabbar.addTab("tbStudentYearlevel", "Student Yearlevel");
		//myTabbar.addTab("tbEmployeeDepartment", "Employee Department");
		//myTabbar.addTab("tbEmployeePosition", "EmployeePosition");
		//myTabbar.addTab("tbThreshold", "Threshold");

		//myForm.hideItem('tbStudentSection');
		myForm.hideItem('tbStudentYearlevel');
		myForm.hideItem('tbEmployeeDepartment');
		myForm.hideItem('tbEmployeePosition');
		//myForm.hideItem('tbThreshold');

///////////////////////////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=studentsection&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridStudentSection)!='null'&&typeof(myWinObj.myGridStudentSection)!='undefined'&&myWinObj.myGridStudentSection!=null) {
				try {
					myWinObj.myGridStudentSection.destructor();
					myWinObj.myGridStudentSection = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridStudentSection = myWinObj.myGridStudentSection = new dhtmlXGridObject(myForm.getContainer('group_studentsection'));

			myGridStudentSection.setImagePath("/codebase/imgs/")

			myGridStudentSection.setHeader("ID, Seq, Section Name, Year Level, Start Time, End Time, &nbsp;");

			myGridStudentSection.setInitWidths("50,50,200,200,200,200,*");

			myGridStudentSection.setColAlign("center,center,left,left,left,left,left");

			myGridStudentSection.setColTypes("ro,ro,edtxt,combo,edtxt,edtxt,ro");

			myGridStudentSection.setColSorting("int,int,str,str,str,str,str");

			myGridStudentSection.setColumnHidden(0,true);

			myGridStudentSection.init();

			myGridStudentSection.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
				//showMessage('state=>'+stage+', rId=>'+rId+', cInd=>'+cInd+', nValue=>'+nValue+', oValue=>'+oValue,10000);

				//if(stage==1&&cInd==6) {
					//myGridStudentSection.cells(rId,cInd).inputMask({alias:'percentage',placeholder:'',min:-100,allowMinus:true,autoUnmask:false});
					//myGridStudentSection.cells(rId,cInd).inputMask('99999999999');
					//myGridStudentSection.cells(rId,cInd).numeric();
					//jQuery(myGridStudentSection.cells(rId,cInd).cell).first().numeric();
					//jQuery(myGridStudentSection.cells(rId,cInd).cell).first().attr('maxlength', 11);
				//} else
				if(stage==1&&(cInd==4||cInd==5)) {
					myGridStudentSection.cells(rId,cInd).inputMask({alias:'hh:mm:ss',prefix:'',placeholder:'',allowMinus:true,allowPlus:false,autoUnmask:false});
				} //else
				//if(stage==1&&(cInd==7||cInd==8)) {
				//	myGridStudentSection.cells(rId,cInd).inputMask({alias:'currency',prefix:'',placeholder:'',allowMinus:false,allowPlus:false,autoUnmask:false});
				//}

				return true;
			});

			try {
				myGridStudentSection.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridStudentSection.forEachRow(function(id){
						//myGridStudentSection.cells(id,1).setDisabled(true);
						myGridStudentSection.cells(id,2).setDisabled(true);
						myGridStudentSection.cells(id,3).setDisabled(true);
						myGridStudentSection.cells(id,4).setDisabled(true);
						myGridStudentSection.cells(id,5).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].yearlevel) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridStudentSection.getColumnCombo(3);

								myCombo.load(JSON.stringify(ddata.rows[x].yearlevel));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridStudentSection.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							/*if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridStudentSection.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}*/
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridStudentSection.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridStudentSection.getColumnCombo(4);

								myCombo.load(JSON.stringify(ddata.rows[x].discount));

								myCombo.enableFilteringMode(true);
							}
							*/
						}
					}
				},'json');
			} catch(e) {
				console.log(e);
			}

		});

///////////////////////////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=studentyearlevel&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridStudentYearlevel)!='null'&&typeof(myWinObj.myGridStudentYearlevel)!='undefined'&&myWinObj.myGridStudentYearlevel!=null) {
				try {
					myWinObj.myGridStudentYearlevel.destructor();
					myWinObj.myGridStudentYearlevel = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridStudentYearlevel = myWinObj.myGridStudentYearlevel = new dhtmlXGridObject(myForm.getContainer('group_studentyearlevel'));

			myGridStudentYearlevel.setImagePath("/codebase/imgs/")

			myGridStudentYearlevel.setHeader("ID, Seq, Year Level Name, &nbsp;");

			myGridStudentYearlevel.setInitWidths("50,50,200,*");

			myGridStudentYearlevel.setColAlign("center,center,left,left");

			myGridStudentYearlevel.setColTypes("ro,ro,edtxt,ro");

			myGridStudentYearlevel.setColSorting("int,int,str,str");

			myGridStudentYearlevel.setColumnHidden(0,true);

			myGridStudentYearlevel.init();

			try {
				myGridStudentYearlevel.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridStudentYearlevel.forEachRow(function(id){
						myGridStudentYearlevel.cells(id,2).setDisabled(true);
						//myGridStudentYearlevel.cells(id,2).setDisabled(true);
						//myGridStudentYearlevel.cells(id,3).setDisabled(true);
						//myGridStudentYearlevel.cells(id,4).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].loadcommands) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridStudentYearlevel.getColumnCombo(1);

								myCombo.load(JSON.stringify(ddata.rows[x].loadcommands));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridStudentYearlevel.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridStudentYearlevel.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridStudentYearlevel.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridStudentYearlevel.getColumnCombo(4);

								myCombo.load(JSON.stringify(ddata.rows[x].discount));

								myCombo.enableFilteringMode(true);
							}
							*/
						}
					}
				},'json');
			} catch(e) {
				console.log(e);
			}

		});

///////////////////////////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=employeedepartment&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridEmployeeDepartment)!='null'&&typeof(myWinObj.myGridEmployeeDepartment)!='undefined'&&myWinObj.myGridEmployeeDepartment!=null) {
				try {
					myWinObj.myGridEmployeeDepartment.destructor();
					myWinObj.myGridEmployeeDepartment = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridEmployeeDepartment = myWinObj.myGridEmployeeDepartment = new dhtmlXGridObject(myForm.getContainer('group_employeedepartment'));

			myGridEmployeeDepartment.setImagePath("/codebase/imgs/")

			myGridEmployeeDepartment.setHeader("ID, Seq, Department Name, Start Time, End Time, &nbsp;");

			myGridEmployeeDepartment.setInitWidths("50,50,200,200,200,*");

			myGridEmployeeDepartment.setColAlign("center,center,left,left,left,left");

			myGridEmployeeDepartment.setColTypes("ro,ro,edtxt,edtxt,edtxt,ro");

			myGridEmployeeDepartment.setColSorting("int,int,str,str,str,str");

			myGridEmployeeDepartment.setColumnHidden(0,true);

			myGridEmployeeDepartment.init();

			myGridEmployeeDepartment.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){

				if(stage==1&&(cInd==3||cInd==4)) {
					myGridEmployeeDepartment.cells(rId,cInd).inputMask({alias:'hh:mm:ss',prefix:'',placeholder:'',allowMinus:true,allowPlus:false,autoUnmask:false});
				}

				return true;
			});

			try {
				myGridEmployeeDepartment.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridEmployeeDepartment.forEachRow(function(id){
						myGridEmployeeDepartment.cells(id,2).setDisabled(true);
						myGridEmployeeDepartment.cells(id,3).setDisabled(true);
						myGridEmployeeDepartment.cells(id,4).setDisabled(true);
						//myGridEmployeeDepartment.cells(id,4).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].loadcommands) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridEmployeeDepartment.getColumnCombo(1);

								myCombo.load(JSON.stringify(ddata.rows[x].loadcommands));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridEmployeeDepartment.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridEmployeeDepartment.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridEmployeeDepartment.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridEmployeeDepartment.getColumnCombo(4);

								myCombo.load(JSON.stringify(ddata.rows[x].discount));

								myCombo.enableFilteringMode(true);
							}
							*/
						}
					}
				},'json');

			} catch(e) {
				console.log(e);
			}

		});

///////////////////////////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&method=<?php echo $method; ?>&table=employeeposition&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myWinObj.myGridEmployeePosition)!='null'&&typeof(myWinObj.myGridEmployeePosition)!='undefined'&&myWinObj.myGridEmployeePosition!=null) {
				try {
					myWinObj.myGridEmployeePosition.destructor();
					myWinObj.myGridEmployeePosition = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridEmployeePosition = myWinObj.myGridEmployeePosition = new dhtmlXGridObject(myForm.getContainer('group_employeeposition'));

			myGridEmployeePosition.setImagePath("/codebase/imgs/")

			myGridEmployeePosition.setHeader("ID, Seq, Position Name, &nbsp;");

			myGridEmployeePosition.setInitWidths("50,50,200,*");

			myGridEmployeePosition.setColAlign("center,center,left,left");

			myGridEmployeePosition.setColTypes("ro,ro,edtxt,ro");

			myGridEmployeePosition.setColSorting("int,int,str,str");

			myGridEmployeePosition.setColumnHidden(0,true);

			myGridEmployeePosition.init();

			try {
				myGridEmployeePosition.parse(ddata,function(){

					<?php if(!($method==$moduleid.'new'||$method==$moduleid.'edit')) { ?>

					myGridEmployeePosition.forEachRow(function(id){
						myGridEmployeePosition.cells(id,2).setDisabled(true);
						//myGridEmployeePosition.cells(id,2).setDisabled(true);
						//myGridEmployeePosition.cells(id,3).setDisabled(true);
						//myGridEmployeePosition.cells(id,4).setDisabled(true);
					});

					<?php } ?>

					var x;

					if(ddata.rows&&ddata.rows.length>0) {
						for(x in ddata.rows) {
							if(ddata.rows[x].loadcommands) {
								//alert(JSON.stringify(ddata.rows[x].type));
								var myCombo = myGridEmployeePosition.getColumnCombo(1);

								myCombo.load(JSON.stringify(ddata.rows[x].loadcommands));

								//myCombo.setComboText(ddata.rows[x].simcardfunctions_loadcommandid);

								myCombo.enableFilteringMode(true);

								//myGridEmployeePosition.cells(ddata.rows[x].id,1).setValue(ddata.rows[x].simcardfunctions_loadcommandid);

								//myCombo.setComboValue(ddata.rows[x].data[1]);
							}
							if(ddata.rows[x].modemcommands) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridEmployeePosition.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].modemcommands));

								myCombo.enableFilteringMode(true);
							}
							break;
							/*
							if(ddata.rows[x].category) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridEmployeePosition.getColumnCombo(2);

								myCombo.load(JSON.stringify(ddata.rows[x].category));

								myCombo.enableFilteringMode(true);
							}
							if(ddata.rows[x].discount) {
								//alert(JSON.stringify(ddata.rows[x].options));
								var myCombo = myGridEmployeePosition.getColumnCombo(4);

								myCombo.load(JSON.stringify(ddata.rows[x].discount));

								myCombo.enableFilteringMode(true);
							}
							*/
						}
					}
				},'json');

			} catch(e) {
				console.log(e);
			}

		});

///////////////////////////////////

			<?php /* ?>

			if(typeof(myWinObj.myGridThreshold)!='null'&&typeof(myWinObj.myGridThreshold)!='undefined'&&myWinObj.myGridThreshold!=null) {
				try {
					myWinObj.myGridThreshold.destructor();
					myWinObj.myGridThreshold = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGridThreshold = myWinObj.myGridThreshold = new dhtmlXGridObject(myForm.getContainer('group_threshold'));

			myGridThreshold.setImagePath("/codebase/imgs/")

			myGridThreshold.setHeader("ID,Date/Time, SMS Date/Time, Date, Time, Receipt No., Customer Name, Reference No., Mobile No., Transaction Type, Status, Service Charge, Transfer Fee, Processing Fee, In, Out, Balance, Running Balance");

			myGridThreshold.setInitWidths("50,120,100,70,60,110,150,110,100,100,100,100,100,100,100,100,100,100");

			myGridThreshold.setColAlign("center,left,left,left,left,left,left,left,left,left,left,right,right,right,right,right,right,right");

			myGridThreshold.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

			myGridThreshold.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
			myGridThreshold.init();

			<?php */ ?>

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

		<?php 	/*if(empty($vars['post']['rowid'])) { ?>

		myWinToolbar.disableItem('<?php echo $moduleid; ?>edit');

		myWinToolbar.disableItem('<?php echo $moduleid; ?>delete');

		<?php 	}*/ ?>

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

			//var txt_optionnumber = parseInt($("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val());

			//if(isNaN(txt_optionnumber)) {
			//	txt_optionnumber = '';
			//}

			//myForm.setItemValue('txt_optionnumber', txt_optionnumber);

			//$("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val(txt_optionnumber);

			myForm.trimAllInputs();

			if(!myForm.validate()) return false;

			/*jQuery('<div>Saving in progress. Please wait...</div>').modal({
				escapeClose: false,
				clickClose: false,
				showClose: false
			});*/

			showSaving();

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			myForm.setItemValue('method', id);

			//$("#messagingdetailsoptionsdetailsform_%formval% input[name='method']").val(id);

			var obj = {o:this,id:id,formval:formval};

			var extra = [];

			/*winObj.myGridSMSFunction.forEachRow(function(id){
				var m = winObj.myGridSMSFunction.cells(id,1).getValue();
				var n = winObj.myGridSMSFunction.cells(id,2).getValue();
				if(m&&n) {
					extra['simcardfunctions_loadcommandid['+id+']'] = m;
					extra['simcardfunctions_modemcommandsname['+id+']'] = n;
				}
			});*/

			winObj.myGridStudentSection.forEachRow(function(id){
				var k = winObj.myGridStudentSection.cells(id,0).getValue();
				var l = winObj.myGridStudentSection.cells(id,1).getValue();
				var m = winObj.myGridStudentSection.cells(id,2).getValue();
				var n = winObj.myGridStudentSection.cells(id,3).getValue();
				var o = winObj.myGridStudentSection.cells(id,4).getValue();
				var p = winObj.myGridStudentSection.cells(id,5).getValue();
				if(n&&o&&p) {
					extra['studentsection_id['+id+']'] = k;
					extra['studentsection_seq['+id+']'] = l;
					extra['studentsection_sectionname['+id+']'] = m;
					extra['studentsection_yearlevel['+id+']'] = n;
					extra['studentsection_starttime['+id+']'] = o;
					extra['studentsection_endtime['+id+']'] = p;
				}
			});

			winObj.myGridStudentYearlevel.forEachRow(function(id){
				var k = winObj.myGridStudentYearlevel.cells(id,0).getValue();
				var l = winObj.myGridStudentYearlevel.cells(id,1).getValue();
				var m = winObj.myGridStudentYearlevel.cells(id,2).getValue();
				//if(m) {
					extra['studentyearlevel_id['+id+']'] = k;
					extra['studentyearlevel_seq['+id+']'] = l;
					extra['studentyearlevel_yearlevel['+id+']'] = m;
				//}
			});

			winObj.myGridEmployeeDepartment.forEachRow(function(id){
				var k = winObj.myGridEmployeeDepartment.cells(id,0).getValue();
				var l = winObj.myGridEmployeeDepartment.cells(id,1).getValue();
				var m = winObj.myGridEmployeeDepartment.cells(id,2).getValue();
				var n = winObj.myGridEmployeeDepartment.cells(id,3).getValue();
				var o = winObj.myGridEmployeeDepartment.cells(id,4).getValue();
				if(n&&o) {
					extra['employeedepartment_id['+id+']'] = k;
					extra['employeedepartment_seq['+id+']'] = l;
					extra['employeedepartment_departmentname['+id+']'] = m;
					extra['employeedepartment_starttime['+id+']'] = n;
					extra['employeedepartment_endtime['+id+']'] = o;
				}
			});

			winObj.myGridEmployeePosition.forEachRow(function(id){
				var k = winObj.myGridEmployeePosition.cells(id,0).getValue();
				var l = winObj.myGridEmployeePosition.cells(id,1).getValue();
				var m = winObj.myGridEmployeePosition.cells(id,2).getValue();
				//if(m) {
					extra['employeeposition_id['+id+']'] = k;
					extra['employeeposition_seq['+id+']'] = l;
					extra['employeeposition_positionname['+id+']'] = m;
				//}
			});

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
