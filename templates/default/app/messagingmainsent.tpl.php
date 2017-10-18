<style>
	#formdiv_%formval% #messagingmain #messagingmainsentgridpaging {
		border-top: 1px solid #ccc;
		background: #f5f5f5;
		height: auto;
	}
	#formdiv_%formval% #messagingmain #messagingmainsentgridpaging #messagingmainsentgridrecinfoArea {
		float: left;
		padding-left: 5px;
	}
	#formdiv_%formval% #messagingmain #messagingmainsentgridpaging #messagingmainsentgridpagingArea {
		float: right;
		padding-right: 5px;
	}
</style>
<div id="messagingmain">
	<div id="messagingmainsentgrid" style="display:block;border:none;"></div>
	<div id="messagingmainsentgridpaging"><span id="messagingmainsentgridrecinfoArea"></span><span id="messagingmainsentgridpagingArea"></span><br style="clear:both;" /></div>
	<br style="clear:both;" />
</div>
<script>
	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('c').expand();

	myTab.layout.cells('b').setHeight(<?php echo getOption('$SENT_MAIN_HEIGHT',300); ?>);

	myTab.layout.cells('d').collapse();

	myTab.layout.cells('d').hideArrow();

	myTab.layout.cells('d').setText('');

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	$("#formdiv_%formval% #messagingmisc").parent().html('<div id="messagingmisc"></div>');

	function messagingmainsentgrid_%formval%(f) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		//var myToolbar = ['messagingresend','messagingforward','messagingdelete','messagingrefresh'];

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.hideAll();

		myTab.toolbar.disableAll();

		//myTab.toolbar.enableOnly(['messagingrefresh']);

		//myTab.toolbar.showOnly(myToolbar);	

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
				console.log(e);
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
			pdata: "routerid="+settings.router_id+"&action=grid&formid=messagingmainsentgrid&module=messaging&table=sent&formval=%formval%",
		}, function(ddata,odata){
			$ = jQuery;
			//$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			//alert(JSON.stringify(ddata));

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.destructor();
					myGrid_%formval% = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmainsentgrid');

			myGrid.setImagePath("/codebase/imgs/")

			myGrid.setHeader("#master_checkbox,ID, Name,Recipient, SIM, Part, Type, Message, Status, Date Created, Date Sent");

			myGrid.attachHeader("&nbsp;,&nbsp;,#combo_filter,#combo_filter,#combo_filter,&nbsp;,&nbsp;,#text_filter,&nbsp;,&nbsp;,&nbsp;");

			myGrid.setInitWidths("50,50,100,100,100,50,50,*,80,120,120");

			myGrid.setColAlign("center,center,left,left,left,center,center,left,center,left,left");

			myGrid.setColTypes("ch,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

			myGrid.setColSorting("int,int,str,str,str,str,str,str,str,str,str");

			myGrid.enablePaging(true,100,10,"messagingmainsentgridpagingArea",true,"messagingmainsentgridrecinfoArea");

			myGrid.init();

			myGrid.setSizes();

			$('#formdiv_%formval% #messagingmain #messagingmainsentgridpaging #messagingmainsentgridrecinfoArea').html('');
			$('#formdiv_%formval% #messagingmain #messagingmainsentgridpaging #messagingmainsentgridpagingArea').html('');

			try {

				if(ddata.rows[0].id) {

					myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){
					    //showMessage("Row with id="+rowId+" was selected",5000);

					    //myTab.toolbar.enableOnly(myToolbar);

						myTab.toolbar.disableAll();

						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssent&module=messaging&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							$ = jQuery;
							$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
							layout_resize_%formval%();
						});

					});

					myGrid.parse(ddata,function(){
						//alert('done!');

						if(typeof(f)!='undefined'&&rowid!=null) {
							myGrid.selectRowById(rowid,false,true,true);
						} else
						if(typeof(f)=='undefined'&&ddata.rows.length>0) {
							myGrid.selectRowById(ddata.rows[0].id,false,true,true);
						}

						if(ddata.inbox) {
							mySideBar_%formval%.items('inbox').setBubble(ddata.inbox);
						} else {
							mySideBar_%formval%.items('inbox').setBubble('');
						}

						if(ddata.outbox) {
							mySideBar_%formval%.items('outbox').setBubble(ddata.outbox);
						} else {
							mySideBar_%formval%.items('outbox').setBubble('');
						}

						if(ddata.sent) {
							mySideBar_%formval%.items('sent').setBubble(ddata.sent);
						} else {
							mySideBar_%formval%.items('sent').setBubble('');
						}

						/*if(ddata.contact) {
							mySideBar_%formval%.items('contacts').setBubble(ddata.contact);
						} else {
							mySideBar_%formval%.items('contacts').setBubble('');
						}*/

					},'json');
				}

			} catch(e) { 

				console.log('e => '+e); 

				$("#formdiv_%formval% #messagingmainsentgrid div.objbox").html('<span style="display:block;width:200px;margin:0 auto;"><center>Sent box is empty!</center></span>');

				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssent&module=messaging&method=nodata&formval=%formval%",
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

	messagingmainsentgrid_%formval%();

</script>
