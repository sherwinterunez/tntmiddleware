<div id="messagingmain">
	<div id="messagingmainsimgrid" style="display:block;border:none;"></div>
	<br style="clear:both;" />
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('c').expand();

	myTab.layout.cells('b').setHeight(<?php echo getOption('$SIM_MAIN_HEIGHT',300); ?>);

	myTab.layout.cells('d').collapse();

	myTab.layout.cells('d').hideArrow();

	myTab.layout.cells('d').setText('');

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	$("#formdiv_%formval% #messagingmisc").parent().html('<div id="messagingmisc"></div>');

	function messagingmainsimgrid_%formval%(f) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.disableAll();

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
			pdata: "routerid="+settings.router_id+"&action=grid&formid=messagingmainsimgrid&module=messaging&table=sim&formval=%formval%",
		}, function(ddata,odata){
			$ = jQuery;

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.destructor();
					myGrid_%formval% = null;
				} catch(e) {}
			}

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmainsimgrid');

			myGrid.setImagePath("/codebase/imgs/")

			myGrid.setHeader("#master_checkbox, ID, Name, Number, Network, Signal, Device, IP, Status, Online");

			myGrid.attachHeader("&nbsp;,&nbsp;,#text_filter,#text_filter,#combo_filter,#text_filter,#text_filter,#text_filter,#combo_filter,#combo_filter");

			//myGrid.setInitWidths("50,70,150,150,150,150,*,90");

			myGrid.setInitWidths("50,70,150,120,150,120,100,100,100,100");

			myGrid.setColAlign("center,center,left,left,left,left,left,left,left,left");

			myGrid.setColTypes("ch,ro,ro,ro,ro,ro,ro,ro,ro,ro");

			myGrid.setColSorting("int,int,str,str,str,str,str,str,str,str");

			myGrid.init();

			myGrid.setSizes();

			try {

				if(ddata.rows[0].id) {

					myGrid.attachEvent("onBeforeSelect", function(new_row,old_row,new_col_index){

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
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssim&module=messaging&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							$ = jQuery;
							$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
							layout_resize_%formval%();
						});

					});

					myGrid.parse(ddata,function(){

						if(typeof(f)!='undefined'&&rowid!=null) {
							myGrid.selectRowById(rowid,false,true,true);
						} else
						if(typeof(f)=='undefined'&&ddata.rows.length>0) {
							myGrid.selectRowById(ddata.rows[0].id,false,true,true);
						}

					},'json');
				}

			} catch(e) {

				console.log('e => '+e);

				$("#formdiv_%formval% #messagingmainsimgrid div.objbox").html('<span style="display:block;width:200px;margin:0 auto;"><center>Data Not Available!</center></span>');

				myTab.postData('/'+settings.router_id+'/json/', {
					//odata: {rowid:rowId},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssim&module=messaging&method=nodata&formval=%formval%",
				}, function(ddata,odata){
					$ = jQuery;
					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
					layout_resize_%formval%();
				});

			}

		});

		try {

			clearInterval(mySetInterval_%formval%);

		} catch(e) {}

	}

	messagingmainsimgrid_%formval%();

</script>
