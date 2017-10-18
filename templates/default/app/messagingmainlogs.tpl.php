<div id="messagingmain">
	<div id="messagingmainlogsgrid" style="display:block;border:none;"></div>
	<br style="clear:both;" />
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	//var myToolbar = ['messagingnew','messagingedit','messagingsave','messagingcancel','messagingoptions'];

	var myToolbar = ['messagingexport'];

	//myTab.toolbar.hideAll();

	myChanged_%formval% = false;

	myTab.toolbar.disableAll();

	myTab.toolbar.enableOnly(myToolbar);

	myTab.toolbar.showOnly(myToolbar);	

	myTab.layout.cells('b').setHeight(250);

	myTab.layout.cells('d').collapse();

	//$("#formdiv_%formval% #messagingmaininboxgrid").height(myTab.layout.cells('b').getHeight()-60);
	//$("#formdiv_%formval% #messagingmaininboxgrid").width(myTab.layout.cells('b').getWidth());

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	layout_resize_%formval%();

	function messagingmainlogsgrid_%formval%(f) {

		if(typeof(f)!='undefined') {
			var rowid = myGrid_%formval%.getSelectedRowId();
			//showMessage(rowid,5000);
			myGrid_%formval%.destructor();
		}

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=messagingmainlogsgrid&module=messaging&table=log&formval=%formval%",
		}, function(ddata,odata){
			$ = jQuery;
			//$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			//alert(JSON.stringify(ddata));

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmainlogsgrid');

			myGrid.setImagePath("/codebase/imgs/")

			myGrid.setHeader("ID, Text, Result, Port");

			myGrid.setInitWidths("70,*,*,*");

			myGrid.setColAlign("center,left,left,left");

			myGrid.setColTypes("ro,ro,ro,ro");

			myGrid.setColSorting("int,str,str,str");

			myGrid.init();

			myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){
			    showMessage("Row with id="+rowId+" was selected",5000);

				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailslogs&module=messaging&rowid="+rowId+"&formval=%formval%",
				}, function(ddata,odata){
					$ = jQuery;
					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
					layout_resize2_%formval%();
				});

			});

			//myGrid.load("/common/grid.xml");

			//alert(JSON.stringify(ddata));

			//srt.dummy.apply(this,[ddata]);

			myGrid.parse(ddata,'json');

			if(typeof(f)!='undefined'&&rowid!=null) {
				setTimeout(function(){
					myGrid.selectRowById(rowid,false,true,true);
				},100);
			} else
			if(typeof(f)=='undefined'&&ddata.rows.length>0) {
				setTimeout(function(){
					myGrid.selectRowById(ddata.rows[0].id,false,true,true);
				},100);
			}

		});

		/*
		var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmainlogsgrid');

		myGrid.setImagePath("/codebase/imgs/")

		myGrid.load("/common/grid.xml");		

		if(typeof(f)!='undefined'&&rowid!=null) {
			setTimeout(function(){
				myGrid.selectRowById(rowid);
			},100);
		}
		*/

	}
</script>