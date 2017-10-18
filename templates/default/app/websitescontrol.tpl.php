<style>
	#formdiv_%formval% #websitescontrol_tree {
		height: 100%;
		width: 100%;
	}

	#formdiv_%formval% #websitescontrol_tree .containerTableStyle {
		overflow: none;
	}

	#formdiv_%formval% #websitesmanage {
		height: 100%
		width: 100%;
		margin:10px;
	}
</style>

<div id="websitescontrol_tree"></div>

<?php /*
<div id="websitescontrol_tree">hello!<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />world!</div>
*/ ?>

<script>
	var myTree_%formval%;

	function websitescontrol_tree_%formval%(){

		var myTree;
		
		myTree = myTree_%formval% = new dhtmlXTreeObject({
			parent: jQuery("#formdiv_%formval% #websitescontrol_tree")[0],
			skin: "dhx_skyblue",
			checkbox: true,
			image_path: settings.template_assets+"imgs/dhxtree_skyblue/",
			xml: "/common/tree.xml"
		});

		var myTab = srt.getTabUsingFormVal('%formval%');

		//if(typeof(myTab.layout.cells('a').ddata.xml)=='string') {
		//	myTree_%formval%.parse(myTab.layout.cells('a').ddata.xml,"xml"); 
		//}

		myTab.toolbar.disableOnly(['websitesaddpage','websitesedit','websitesdelete','websitessave']);

		myTab.toolbar.getToolbarData('websitescreate').onClick = function() {

			srt.dummy.apply(this,[this]);

			$("#formdiv_%formval% #websitesmanage").html('Loading...');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: this,
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=websitescreate&module=web&formval=%formval%",
			}, function(ddata,odata){
				$ = jQuery;
				myTab.toolbar.disableOnly(['websitescreate','websitesaddpage','websitesedit','websitesdelete']);
				$("#formdiv_%formval% #websitesmanage").parent().html(ddata.html);
			});

			return false;
		};

	}

	websitescontrol_tree_%formval%();
</script>
