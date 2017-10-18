<div id="messagingmain">
	<div id="messagingmainportsgrid" style="display:block;border:none;"></div>
	<br style="clear:both;" />
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	//myTab.layout.cells('b').setHeight(250);

	myTab.layout.cells('c').collapse();

	myTab.layout.cells('c').hideArrow();

	myTab.layout.cells('c').setText('');

	myTab.layout.cells('d').collapse();

	myTab.layout.cells('d').hideArrow();

	myTab.layout.cells('d').setText('');

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	$("#formdiv_%formval% #messagingmisc").parent().html('<div id="messagingmisc"></div>');

	$("#formdiv_%formval% #messagingdetails").parent().html('<div id="messagingdetails"></div>');

	function messagingmainportsgrid_%formval%(f) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.disableAll();

		//myTab.toolbar.enableOnly('messagingnew');

		//myTab.toolbar.hideAll();

///////////////

		if(typeof(f)!='undefined'&&typeof(myGrid_%formval%)!='undefined') {
			try {
				var rowid = myGrid_%formval%.getSelectedRowId();

				if(typeof(f)=='boolean') {
				} else
				if(typeof(f)=='number'||typeof(f)=='string') {
					rowid = parseInt(f);

					if(isNaN(rowid)) {
						rowid = 1;
					}
				}
			} catch(e) {
				var rowid = 1;
			}
		}

		if(typeof(myForm_%formval%)!='null'&&typeof(myForm_%formval%)!='undefined'&&myForm_%formval%!=null) {
			try {
				myForm_%formval%.unload();
				myForm_%formval% = undefined;
			} catch(e) {
				console.log(e);
			}
		}

///////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=messagingmainportsgrid&module=messaging&table=port&formval=%formval%",
		}, function(ddata,odata){
			$ = jQuery;

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.destructor();
					myGrid_%formval% = null;
				} catch(e) {}
			}

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmainportsgrid');

			myGrid.setImagePath("/codebase/imgs/")

			//myGrid.setHeader("#master_checkbox,ID, Device, Name, SIM Number, Network, Description, Status");

			myGrid.setHeader("#master_checkbox,ID, Device, SIM Number, Network");

			myGrid.setInitWidths("50,70,150,150,*");

			myGrid.setColAlign("center,center,left,left,left");

			myGrid.setColTypes("ch,ro,ro,ro,ro");

			myGrid.setColSorting("int,int,str,str,str");

			myGrid.init();

			myGrid.setSizes();

			try {

				if(ddata.rows[0].id) {

					/*myGrid.attachEvent("onBeforeSelect", function(new_row,old_row,new_col_index){

						var method = myFormStatus_%formval%;

						if(method=='messagingedit'||method=='messagingnew') {
							return false;
						}

						return true;
					});

					myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){

						myTab.toolbar.disableAll();

						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsports&module=messaging&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							$ = jQuery;
							$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
							layout_resize_%formval%();
						});

					});*/

					myGrid.parse(ddata,function(){

						var myTab = srt.getTabUsingFormVal('%formval%');

						if(typeof(f)!='undefined'&&rowid!=null) {
							myGrid.selectRowById(rowid,false,true,true);
						} else
						if(typeof(f)=='undefined'&&ddata.rows.length>0) {
							myGrid.selectRowById(ddata.rows[0].id,false,true,true);
						}

						setTimeout(function(){
							layout_resize_%formval%();
							myTab.toolbar.enableOnly(['messagingrefresh']);
							myTab.toolbar.showOnly(['messagingrefresh']);
						},100);

						myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
							doSelect_%formval%("ports");
							showMessage("toolbar: "+id,5000);
						};

					},'json');
				}

			} catch(e) { 

				console.log('e => '+e); 

				$("#formdiv_%formval% #messagingmainportsgrid div.objbox").html('Data Not Available');

				setTimeout(function(){
					layout_resize_%formval%();
				},100);

				/*myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowId},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsports&module=messaging&method=nodata&formval=%formval%",
				}, function(ddata,odata){
					$ = jQuery;
					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
					layout_resize_%formval%();
				});*/

			}

		});

		try {

			clearInterval(mySetInterval_%formval%);

		} catch(e) {}

	}

	messagingmainportsgrid_%formval%();

</script>