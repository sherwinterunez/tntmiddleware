<div id="messagingmain">
	<div id="messagingmaintemplates">#messagingmaintemplates</div>
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	var myToolbar = ['messagingoptions'];

	//myTab.toolbar.hideAll();

	myTab.toolbar.disableAll();

	myTab.toolbar.enableOnly(myToolbar);

	myTab.toolbar.showOnly(myToolbar);	

	myTab.layout.cells('b').setHeight(300);

	//$("#formdiv_%formval% #messagingmaininboxgrid").height(myTab.layout.cells('b').getHeight()-60);
	//$("#formdiv_%formval% #messagingmaininboxgrid").width(myTab.layout.cells('b').getWidth());

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});
</script>