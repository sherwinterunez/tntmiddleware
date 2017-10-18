<?php

global $applogin;

$access = $applogin->getAccess();

$savecancel = false;

$toolbars = array();

if(in_array('useraccountnewrole',$access)) {
	$toolbars[] = 'useraccountnewrole';
	$savecancel = true;
}

if(in_array('useraccounteditrole',$access)) {
	$toolbars[] = 'useraccountedit';
	$savecancel = true;
}

if(in_array('useraccountdeleterole',$access)) {
	$toolbars[] = 'useraccountdelete';
	$savecancel = true;
}

if(in_array('useraccountnewuser',$access)) {
	$toolbars[] = 'useraccountnewuser';
	$savecancel = true;
}

if(in_array('useraccountedituser',$access)) {
	if(!in_array('useraccountedit',$toolbars)) {
		$toolbars[] = 'useraccountedit';
	}
	$savecancel = true;
}

if(in_array('useraccountdeleteuser',$access)) {
	if(!in_array('useraccountdelete',$toolbars)) {
		$toolbars[] = 'useraccountdelete';
	}
	$savecancel = true;
}

if($savecancel) {
	$toolbars[] = 'useraccountsave';
	$toolbars[] = 'useraccountcancel';
}

?>
<style>
	#formdiv_%formval% #useraccountcontrol {
		height: 100%;
		width: 100%;
	}

	#formdiv_%formval% #useraccountcontrol_tree {
		overflow: none;
	}
</style>
<!--
<?php 

global $appaccess;

//$appaccess->showrules();

pre(array('$_SESSION'=>$_SESSION,'$vars'=>$vars)); 

?>
-->
<div id="useraccountcontrol">
	<div id="useraccountcontrol_tree"></div>
	<br style="clear:both;" />
</div>
<script>

	var myTree_%formval%;
	var myForm_%formval%;

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('b').hideArrow();

	$("#formdiv_%formval% #useraccountcontrol").parent().css({'overflow':'hidden'});

	function layout_resize_%formval%(f) {

		//showMessage('resized',5000);

		var $ = jQuery;
		var myTab = myTab_%formval% = srt.getTabUsingFormVal('%formval%');

		var laHeight = myTab.layout.cells('a').getHeight();
		var laWidth = myTab.layout.cells('a').getWidth();

		var lbHeight = myTab.layout.cells('b').getHeight();
		var lbWidth = myTab.layout.cells('b').getWidth();

////////

		if($("#formdiv_%formval% #useraccountcontrol").length) {

			//showMessage("#formdiv_%formval% #useraccountcontrol",5000);

			$("#formdiv_%formval% #useraccountcontrol").height(laHeight-30);
			$("#formdiv_%formval% #useraccountcontrol").width(laWidth-2);

			$("#formdiv_%formval% #useraccountcontrol_tree").height(laHeight-30);
			$("#formdiv_%formval% #useraccountcontrol_tree").width(laWidth-2);

		}

		if($("#formdiv_%formval% #useraccountrole").length) {

			//showMessage("#formdiv_%formval% #useraccountcontrol",5000);

			$("#formdiv_%formval% #useraccountrole").height(lbHeight-30);
			$("#formdiv_%formval% #useraccountrole").width(lbWidth-2);

			$("#formdiv_%formval% #useraccountroleform_%formval%").height(lbHeight-50);
			$("#formdiv_%formval% #useraccountroleform_%formval%").width(lbWidth-22);
		}

		if($("#formdiv_%formval% #useraccountuser").length) {

			//showMessage("#formdiv_%formval% #useraccountcontrol",5000);

			$("#formdiv_%formval% #useraccountuser").height(lbHeight-30);
			$("#formdiv_%formval% #useraccountuser").width(lbWidth-2);

			$("#formdiv_%formval% #useraccountuserform_%formval%").height(lbHeight-50);
			$("#formdiv_%formval% #useraccountuserform_%formval%").width(lbWidth-22);

		}


	}

	function doOnClick_%formval%(roleid,userid) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=formonly&formid="+(userid?'useraccountuser':'useraccountrole')+"&module=useraccount&method=useraccountview&formval=%formval%&roleid="+roleid+(userid?"&userid="+userid:''),
		}, function(ddata,odata){

			var $ = jQuery;

			$("#formdiv_%formval% #useraccountmain").parent().html(ddata.html);

			layout_resize_%formval%();

		});

	}

	function useraccount_tree_%formval%(){

		var myToolbar = <?php echo json_encode($toolbars); ?>

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myTree = myTree_%formval% = new dhtmlXTreeObject(jQuery("#formdiv_%formval% #useraccountcontrol_tree")[0],"100%","100%",0);

		myTree.setImagesPath(settings.template_assets+"imgs/dhxtree_skyblue/");

		if(typeof(myTab.layout.cells('a').ddata.xml)=='string') {
			myTree.parse(myTab.layout.cells('a').ddata.xml,"xml"); 
		}

		//myTab.toolbar.disableOnly(['useraccountnewuser','useraccountedit','useraccountdelete','useraccountsave']);

		myTab.toolbar.hideAll();

		myTab.toolbar.disableAll();

		myTab.toolbar.showOnly(myToolbar);

		myTab.layout.attachEvent("onCollapse", function(names){
			layout_resize_%formval%(true);
		});

		myTab.layout.attachEvent("onExpand", function(names){
			layout_resize_%formval%(true);
		});

		myTab.layout.attachEvent("onResizeFinish", function(names){
			layout_resize_%formval%(true);
		});

		myTab.layout.attachEvent("onPanelResizeFinish", function(names){
			layout_resize_%formval%(true);
		});

		layout_resize_%formval%();

		myTree_%formval%.attachEvent("onClick", function(id){

			//showMessage('onClicK: '+id,5000);

			var roleid = 0;
			var userid = 0;

			var myTab = srt.getTabUsingFormVal('%formval%');

			myTab.toolbar.disableAll();

			var arr = id.split('|');

			roleid = parseInt(arr[0]);

			if(roleid===0) {
				myTab.toolbar.enableItem('useraccountnewrole');
				return false;
			}

			if(arr[1]) {
				userid = parseInt(arr[1]);
			}

			if(userid===-1) {
				myTab.toolbar.enableItem('useraccountnewrole');
				myTab.toolbar.enableItem('useraccountnewuser');
				return false;
			}

			doOnClick_%formval%(roleid,userid);

			/*myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid="+(userid?'useraccountuser':'useraccountrole')+"&module=useraccount&method=useraccountview&formval=%formval%&roleid="+roleid+(userid?"&userid="+userid:''),
			}, function(ddata,odata){

				var $ = jQuery;

				$("#formdiv_%formval% #useraccountmain").parent().html(ddata.html);

				layout_resize_%formval%();

			});*/

/*
			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {o:this,roleid:roleid,userid:userid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=usermanagementmanage&module=user&formval=%formval%&roleid="+urlencode(roleid)+"&userid="+urlencode(userid),
			}, function(ddata,odata){
				$ = jQuery;
				//showMessage(ddata.html,5000);
				if(roleid==1&&!userid) {
					myTab.toolbar.disableOnly(['usermanagementedit','usermanagementdelete','usermanagementsave']);
				} else
				if(roleid==1&&userid==1) {
					myTab.toolbar.disableOnly(['usermanagementdelete','usermanagementsave']);
				} else {
					myTab.toolbar.disableOnly(['usermanagementsave']);					
				}
				$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			});


*/


		});

	}

	useraccount_tree_%formval%();

</script>
