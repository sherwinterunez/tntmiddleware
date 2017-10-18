<?php

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

?>
<style>
	#formdiv_%formval% #messagingdetailsreply {
		display: block;
		height: 50px;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
	}
	#formdiv_%formval% #messagingdetailsreplyeditor {
		display: block;
		height: 50px;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		border-top:1px solid #ccc;
	}

	#formdiv_%formval% #messagingdetailsreplydetails {
		display: block;
		height: 40px;
		width: 100%;
		border: 0;
		padding: 5px;
		margin: 0;
	}

	#formdiv_%formval% #messagingdetailsreply .dhx_cell_editor .dhx_cell_stb {
		display: none;
	}
	
</style>
<div id="messagingdetails">
	<div id="messagingdetailsreply">
		<div id="messagingdetailsreplydetails" class="navbar-default-bg"><div id="messagingdetailsreplydetailsform_%formval%"></div></div>
		<div id="messagingdetailsreplyeditor"></div>
		<?php if(!empty($vars['post']['method'])&&$vars['post']['method']=='messagingsendtooutbox') { pre(array('$vars'=>$vars)); } ?>
	</div>
</div>
<script>

	function messagingdetailsreplydetails_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 80, inputWidth: 300},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailsreply"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
			]},
		];

		var myForm = new dhtmlXForm("messagingdetailsreplydetailsform_%formval%",[]);

		$('#messagingdetailsreplydetailsform_%formval% .dhxform_base').html('<div style="display:block;margin:10px 0 0 0;"><strong>Encoding:</strong>&nbsp;<span>GSM 7-bit</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Characters: </strong><span id="charcount"></span></div>');

		var myComposeEditor = myComposeEditor_%formval% = new dhtmlXEditor({parent:"messagingdetailsreplyeditor",toolbar:false});

		myComposeEditor.setContent('Enter SMS Message here');

		myComposeEditor.attachEvent("onAccess", function(eventName, evObj){
			var $ = jQuery;
			//showMessage("messagingdetailscomposedetails: "+eventName,5000);

			var cont = myComposeEditor_%formval%.getContent();

			var contlen = cont.length;

			var smscnt = 0;

			if(contlen>160) {
				smscnt = Math.ceil(contlen / 160);
			}

			if(smscnt>1) {
				$('#messagingdetailsreplydetailsform_%formval% #charcount').html(contlen+' used ('+smscnt+' SMS)');				
			} else {
				$('#messagingdetailsreplydetailsform_%formval% #charcount').html(contlen+' used');				
			}
		});

		myTab.toolbar.getToolbarData('messagingsendtooutbox').onClick = function(id,formval) {

			showMessage("toolbar: "+id,5000);
			//showMessage("content: "+content,5000);

			showConfirmWarning('Send this SMS to Outbox?',function(val){

				if(val) {
					var $ = jQuery;

					var content = myComposeEditor_%formval%.getContent();

					var tonumbers = $("#messagingmainreplyform_%formval% input[name='txt_to_number']").val();
					var togroups = $("#messagingmainreplyform_%formval% input[name='txt_to_groups']").val();
					var ports = $("#messagingmainreplyform_%formval% input[name='txt_ports']").val();

					tonumbers = tonumbers ? tonumbers : '';
					togroups = togroups ? togroups : '';
					ports = ports ? ports : '';

					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsreply&module=messaging&method="+id+"&formval=%formval%&content="+encodeURIComponent(content)+"&tonumbers="+encodeURIComponent(tonumbers)+"&togroups="+encodeURIComponent(togroups)+"&ports="+encodeURIComponent(ports),
					}, function(ddata,odata){

						var $ = jQuery;

						//$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

						//layout_resize2_%formval%();

							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									//doSelect_%formval%("compose");
									//layout_resize_%formval%();
									showAlert(ddata.return_message);
								}
							}

					});
				}

			});

		};

		myTab.toolbar.getToolbarData('messagingclear').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			myComposeEditor_%formval%.setContent('');
		};

		myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("inbox");
			//layout_resize2_%formval%();
		};

	}

	messagingdetailsreplydetails_%formval%();

</script>