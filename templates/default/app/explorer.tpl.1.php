<style>
	#formdiv_%formval% #explorer_tree {
		height: 100%;
		width: 100%;
	}

	#formdiv_%formval% #explorer_tree .containerTableStyle {
		overflow: none;
	}

	#formdiv_%formval% #explorermanage {
		height: 100%
		width: 100%;
		margin:10px;
	}
</style>

<div id="explorer_tree"></div>

<script>
	var myTree_%formval%;

	function explorer_tree_%formval%(){

		var myTree;

		myTree = myTree_%formval% = new dhtmlXTreeObject(jQuery("#formdiv_%formval% #explorer_tree")[0],"100%","100%",0);

		myTree.setImagesPath(settings.template_assets+"imgs/dhxtree_skyblue/");

		var myTab = srt.getTabUsingFormVal('%formval%');

		if(typeof(myTab.layout.cells('a').ddata.xml)=='string') {
			myTree.parse(myTab.layout.cells('a').ddata.xml,"xml"); 
		}

		myTree.showItemSign('menus',false);

		myTree.lockItem('menus',true);

		myTree.attachEvent("onClick", function(id){
			$ = jQuery;

			myTab.layout.cells('b').setText(myTree.getItemText(id));

			showMessage(id,5000);
		});
	};

	explorer_tree_%formval%();

</script>
