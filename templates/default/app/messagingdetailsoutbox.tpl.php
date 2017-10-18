<?php

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

?>
<style>
	#formdiv_%formval% #messagingdetails {
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailsoutbox {
		display: block;
		height: 40px;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
	}
	#formdiv_%formval% #messagingdetailsoutboxdetails {
		display: block;
		height: 40px;
		width: 100%;
		border: 0;
		padding: 5px;
		margin: 0;
	}
	#formdiv_%formval% #messagingdetailsoutboxeditor {
		overflow: auto;
		display: block;
		height: 51px;
		width: 100%;
		border: 0;
		padding: 5px;
		margin: 0;
		border-top: 1px solid #ccc;
	}
</style>
<div id="messagingdetails">
	<div id="messagingdetailsoutbox">
		<div id="messagingdetailsoutboxdetails" class="navbar-default-bg">
			<div id="messagingdetailsoutboxdetailsform_%formval%" class="dhxform_obj_dhx_web">
				<div style="display:block;margin:10px 0 0 0;">
					<strong>Encoding:</strong>&nbsp;
					<span>GSM 7-bit</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<strong>Characters: </strong>
					<span id="charcount"><?php echo !empty($vars['params']['smsoutboxinfo']['smsoutbox_message']) ? strlen($vars['params']['smsoutboxinfo']['smsoutbox_message']) : ''; ?></span>
				</div>
			</div>
		</div>
		<div id="messagingdetailsoutboxeditor" class="dhxform_obj_dhx_web">
			<?php echo !empty($vars['params']['smsoutboxinfo']['smsoutbox_message']) ? str_replace("\n",'<br>',$vars['params']['smsoutboxinfo']['smsoutbox_message']) : ''; ?>
		</div>
		<?php /*pre(array('$vars'=>$vars));*/ ?>
	</div>
</div>
<script>

	function messagingdetailsoutbox_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		myTab.toolbar.resetAll();

		//var myToolbar = ['messagingsendstart','messagingforward','messagingdelete','messagingrefresh'];

		//var myToolbar = ['messagingsendstart','messagingsendstop','messagingresend','messagingforward','messagingdelete','messagingrefresh'];

		var myToolbar = ['messagingsendstart','messagingsendstop','messagingresend','messagingdelete','messagingrefresh'];

		myTab.toolbar.getToolbarData('messagingsendstart').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect2_%formval%("reply");
			//layout_resize_%formval%();

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			showConfirmWarning('Start sending SMS?',function(val){

				if(val) {

					var myTab = srt.getTabUsingFormVal('%formval%');

					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {rowid:rowid},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoutbox&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
					}, function(ddata,odata){
						if(ddata.return_code) {
							if(ddata.return_code=='SUCCESS') {
								if(ddata.rowid) {
									messagingmainoutboxgrid_%formval%(ddata.rowid);
								} else {
									messagingmainoutboxgrid_%formval%();
								}
								showAlert(ddata.return_message);
							}
						}
					});
				}

			});

		};

		myTab.toolbar.getToolbarData('messagingsendstop').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect2_%formval%("reply");
			//layout_resize_%formval%();

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			showConfirmWarning('Stop sending SMS?',function(val){

				if(val) {

					var myTab = srt.getTabUsingFormVal('%formval%');

					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {rowid:rowid},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoutbox&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
					}, function(ddata,odata){
						if(ddata.return_code) {
							if(ddata.return_code=='SUCCESS') {
								if(ddata.rowid) {
									messagingmainoutboxgrid_%formval%(ddata.rowid);
								} else {
									messagingmainoutboxgrid_%formval%();
								}
								showAlert(ddata.return_message);
							}
						}
					});
				}

			});

		};

		myTab.toolbar.getToolbarData('messagingresend').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect2_%formval%("reply");
			//layout_resize_%formval%();

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			showConfirmWarning('Resend SMS?',function(val){

				if(val) {

					var myTab = srt.getTabUsingFormVal('%formval%');

					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {rowid:rowid},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoutbox&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
					}, function(ddata,odata){
						if(ddata.return_code) {
							if(ddata.return_code=='SUCCESS') {
								if(ddata.rowid) {
									messagingmainoutboxgrid_%formval%(ddata.rowid);
								} else {
									messagingmainoutboxgrid_%formval%();
								}
								showAlert(ddata.return_message);
							}
						}
					});
				}

			});

		};

		myTab.toolbar.getToolbarData('messagingforward').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect2_%formval%("reply");
			//layout_resize_%formval%();

			var rowid = myGrid_%formval%.getSelectedRowId();

			var myTab = srt.getTabUsingFormVal('%formval%');

			myTab.layout.cells('b').setText('Forward');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingmainforward&module=messaging&from=outbox&rowid="+rowid+"&formval=%formval%",
			}, function(ddata,odata){
				$ = jQuery;
				$("#formdiv_%formval% #messagingmain").parent().html(ddata.html);
			});
		};

		myTab.toolbar.getToolbarData('messagingdelete').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect2_%formval%("reply");
			//layout_resize_%formval%();

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			if(rowid) {
				showConfirmWarning('Are you sure you want to delete this SMS?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsoutbox&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainoutboxgrid_%formval%();
									showAlert(ddata.return_message);
								}
							}
						});
					}

				});
			}

		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("outbox");
			//layout_resize_%formval%();

			try {
				var rowid = myGrid_%formval%.getSelectedRowId();
				messagingmainoutboxgrid_%formval%(rowid);
				//layout_resize_%formval%();
			} catch(e) {
				doSelect_%formval%("inbox");
			}

		};

		try {

			clearInterval(mySetInterval_%formval%);

			/*mySetInterval_%formval% = setInterval(function(){
				//doSelect_%formval%("inbox");
				var rowid = myGrid_%formval%.getSelectedRowId();
				messagingmainoutboxgrid_%formval%(rowid);
			},60000);*/

		} catch(e) {
			console.log(e);
		}

	}

	messagingdetailsoutbox_%formval%();

</script>
