<?php
$moduleid = 'report';
$submod = 'individualattendance';
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

$myToolbar = array($moduleid.'refresh',$moduleid.'print',$moduleid.'sep1',$moduleid.'from',$moduleid.'datefrom',$moduleid.'to',$moduleid.'dateto');

?>
<!--
<?php print_r(array('$vars'=>$vars)); ?>
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
	#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>mainform_%formval% .dailyabsentreport_%formval% {
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

		myTabbar.addTab("tbDetails", "Generated Report");

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
			return true;
		});

		if(typeof myWinObj.onMaximizeId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onMaximizeId);
			} catch(e) {}
		}

		myWinObj.onMaximizeId = myWinObj.attachEvent("onMaximize", function(win){
			myTabbar.setSizes();
			return true;
		});

		if(typeof myWinObj.onMinimizeId != 'undefined') {
			try {
				myWinObj.detachEvent(myWinObj.onMinimizeId);
			} catch(e) {}
		}

		myWinObj.onMinimizeId = myWinObj.attachEvent("onMinimize", function(win){
			myTabbar.setSizes();
			return true;
		});

///////////////////////////////////

		layout_resize_%formval%();

  }

  <?php echo $templatemainid.$submod; ?>grid_%formval%();

</script>
