<div id="messagingmain">
	<div id="messagingmaincontactsgrid" style="display:block;border:none;"></div>
	<br style="clear:both;" />
</div>
<script>
	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('c').expand();

	myTab.layout.cells('b').setHeight(<?php echo getOption('$CONTACTS_MAIN_HEIGHT',300); ?>);

	myTab.layout.cells('d').expand();

	//myTab.layout.cells('d').collapse();

	myTab.layout.cells('d').showArrow();

	myTab.layout.cells('d').setText('History');

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	$("#formdiv_%formval% #messagingmisc").parent().html('<div id="messagingmisc"></div>');

	function messagingmaincontactsgrid_%formval%(f) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.hideAll();

		myTab.toolbar.disableAll();

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

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=messagingmaincontactsgrid&module=messaging&table=contact&formval=%formval%",
		}, function(ddata,odata){

			$ = jQuery;

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.destructor();
					myGrid_%formval% = null;
				} catch(e) {}
			}

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmaincontactsgrid');

			myGrid.setImagePath("/codebase/imgs/")

			myGrid.setHeader("#master_checkbox, ID, Name, Number, Network, Group, Registered, Updated");

			myGrid.attachHeader("&nbsp;,&nbsp;,#text_filter,#text_filter,#combo_filter,#text_filter,&nbsp;,&nbsp;");

			myGrid.setInitWidths("50, 50,120,120,*,*,120,120");

			myGrid.setColAlign("center, center,left,left,left,left,left,left");

			myGrid.setColTypes("ch,ro,ro,ro,ro,ro,ro,ro");

			myGrid.setColSorting("int,int,str,str,str,str,str,str");

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
							odata: {rowid:rowId},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscontact&module=messaging&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							$ = jQuery;
							$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
							layout_resize_%formval%();
						});

						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingmisccontacts&module=messaging&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							$ = jQuery;
							$("#formdiv_%formval% #messagingmisc").parent().html(ddata.html);
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

						if(ddata.contact) {
							mySideBar_%formval%.items('contacts').setBubble(ddata.contact);
						} else {
							mySideBar_%formval%.items('contacts').setBubble('');
						}

					},'json');
				}

			} catch(e) { 

				console.log('e => '+e); 

				$("#formdiv_%formval% #messagingmaincontactsgrid div.objbox").html('Data Not Available');

				myTab.postData('/'+settings.router_id+'/json/', {
					/*odata: {rowid:rowId},*/
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscontact&module=messaging&method=nodata&formval=%formval%",
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

	messagingmaincontactsgrid_%formval%();

</script>
