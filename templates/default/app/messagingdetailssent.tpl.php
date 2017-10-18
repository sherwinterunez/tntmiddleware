 <?php

global $applogin;

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

$access = $applogin->getAccess();

$savecancel = false;

//$toolbars = array('messagingresend','messagingforward','messagingrefresh');

$toolbars = array('messagingrefresh');

if(in_array('sentdelete',$access)) {
	$toolbars[] = 'messagingdelete';
	$savecancel = true;
}

/*if(in_array('groupsedit',$access)) {
	$toolbars[] = 'messagingedit';
	$savecancel = true;
}

if(in_array('groupsdelete',$access)) {
	$toolbars[] = 'messagingdelete';
	$savecancel = true;
}

if($savecancel) {
	$toolbars[] = 'messagingsave';
	$toolbars[] = 'messagingcancel';
}*/

?>
<style>
	#formdiv_%formval% #messagingdetails {
		overflow: hidden;
	}
	#formdiv_%formval% #messagingdetailssent {
		display: block;
		height: 40px;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
	}
	#formdiv_%formval% #messagingdetailssentdetails {
		display: block;
		height: 40px;
		width: 100%;
		border: 0;
		padding: 5px;
		margin: 0;
	}
	#formdiv_%formval% #messagingdetailssenteditor {
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
	<div id="messagingdetailssent">
		<div id="messagingdetailssentdetails" class="navbar-default-bg">
			<div id="messagingdetailssentdetailsform_%formval%" class="dhxform_obj_dhx_web">
				<div style="display:block;margin:10px 0 0 0;">
					<strong>Encoding:</strong>&nbsp;
					<span>GSM 7-bit</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<strong>Characters: </strong>
					<span id="charcount"><?php echo !empty($vars['params']['smsoutboxinfo']['smsoutbox_message']) ? strlen($vars['params']['smsoutboxinfo']['smsoutbox_message']) : ''; ?></span>
				</div>
			</div>
		</div>
		<div id="messagingdetailssenteditor" class="dhxform_obj_dhx_web">
			<?php echo !empty($vars['params']['smsoutboxinfo']['smsoutbox_message']) ? str_replace("\n",'<br>',$vars['params']['smsoutboxinfo']['smsoutbox_message']) : ''; ?>
		</div>
		<?php /*pre(array('$vars'=>$vars));*/ ?>
	</div>
</div>
<script>

	function messagingdetailssent_%formval%() {

		<?php /* ?>
		//var myToolbar = ['messagingresend','messagingforward','messagingdelete','messagingrefresh'];
		<?php */ ?>

		var myToolbar = <?php echo json_encode($toolbars); ?>

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		myTab.toolbar.resetAll();

		<?php if(!empty($vars['post']['rowid'])) { ?>
		myTab.toolbar.enableOnly(myToolbar);
		<?php } else { ?>
		myTab.toolbar.enableOnly(['messagingrefresh']);
		<?php } ?>

		myTab.toolbar.showOnly(myToolbar);

		myTab.toolbar.getToolbarData('messagingresend').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			var myTab = srt.getTabUsingFormVal('%formval%');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssent&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
			}, function(ddata,odata){
				if(ddata.return_code) {
					if(ddata.return_code=='SUCCESS') {
						showAlert(ddata.return_message);
					}
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
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingmainforward&module=messaging&from=sent&rowid="+rowid+"&formval=%formval%",
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
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailssent&module=messaging&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									messagingmainsentgrid_%formval%();
									showAlert(ddata.return_message);
								}
							}
						});
					}

				});
			}

		};

		myTab.toolbar.getToolbarData('messagingrefresh').onClick = function(id,formval) {

			try {
				var rowid = myGrid_%formval%.getSelectedRowId();
				messagingmainsentgrid_%formval%(rowid);
				//layout_resize_%formval%();
			} catch(e) {
				doSelect_%formval%("sent");
			}

			//showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("sent");
			//layout_resize_%formval%();
		};

	}

	messagingdetailssent_%formval%();

</script>
