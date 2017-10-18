<div id="messagingmain">
	<div id="messagingmaincontactsgrid" style="display:block;border:none;"></div>
	<br style="clear:both;" />
</div>
<script>
	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('b').setHeight(250);

	//myTab.layout.cells('d').expand();

	myTab.layout.cells('d').collapse();

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	layout_resize_%formval%();

	function messagingmaincontactsgrid_%formval%(f) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingcomposeto','messagingnew','messagingedit','messagingdelete','messagingsave','messagingcancel','messagingrefresh'];

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.disableAll();

		//myTab.toolbar.enableOnly(['messagingnew','messagingedit','messagingdelete','messagingrefresh']);

		myTab.toolbar.enableOnly(['messagingnew','messagingrefresh']);

		myTab.toolbar.showOnly(myToolbar);	

		if(typeof(f)!='undefined'&&typeof(myGrid_%formval%)!='undefined') {
			var rowid = myGrid_%formval%.getSelectedRowId();

			if(typeof(f)=='boolean') {
			} else
			if(typeof(f)=='number'||typeof(f)=='string') {
				rowid = parseInt(f);

				if(isNaN(rowid)) {
					rowid = 1;
				}
			}

			//showMessage(rowid+", "+typeof(f)+", "+f ,5000);
			myGrid_%formval%.destructor();
		}

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=messagingmaincontactsgrid&module=messaging&table=contact&formval=%formval%",
		}, function(ddata,odata){
			$ = jQuery;
			//$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			//alert(JSON.stringify(ddata));

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('messagingmaincontactsgrid');

			myGrid.setImagePath("/codebase/imgs/")

			myGrid.setHeader("ID, Nick, Number, Network, Group, Date Subscribed, Date Updated");

			myGrid.setInitWidths("50,150,150,150,*,50,*");

			myGrid.setColAlign("center,left,left,left,left,left,left");

			myGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");

			myGrid.setColSorting("int,str,str,str,str,str,str");

			myGrid.init();

			myGrid.attachEvent("onBeforeSelect", function(new_row,old_row,new_col_index){
				//var method = $("#messagingdetailsgroupdetailsform_%formval% input[name='method']").val();
				var method = myFormStatus_%formval%;

				//showMessage("new_row="+new_row+", old_row="+old_row+", new_col_index="+new_col_index+", method="+method,5000);

				if(method=='messagingedit'||method=='messagingnew') {
					return false;
				}

				return true;
			});

			myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){
			    //showMessage("Row with id="+rowId+" was selected",5000);

				myTab.toolbar.enableOnly(['messagingcomposeto','messagingnew','messagingedit','messagingdelete','messagingrefresh']);

				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowId},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscontact&module=messaging&method=onrowselect&rowid="+rowId+"&formval=%formval%",
				}, function(ddata,odata){
					$ = jQuery;
					$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);
					layout_resize2_%formval%();
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

			},'json');

			/*myGrid.parse(ddata,'json');

			if(typeof(f)!='undefined'&&rowid!=null) {
				setTimeout(function(){
					myGrid.selectRowById(rowid,false,true,true);
				},100);
			} else
			if(typeof(f)=='undefined'&&ddata.rows.length>0) {
				setTimeout(function(){
					myGrid.selectRowById(ddata.rows[0].id,false,true,true);
				},100);
			}*/

		});

		try {

			clearInterval(mySetInterval_%formval%);

		} catch(e) {}

	}

	//messagingmaincontactsgrid_%formval%();

</script>
