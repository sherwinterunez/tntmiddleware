<?php

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

?>
<style>
	#formdiv_%formval% #messagingdetailscompose {
		display: block;
		height: 50px;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
	}
	#formdiv_%formval% #messagingdetailscomposeeditor {
		display: block;
		height: 50px;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		border-top:1px solid #ccc;
	}

	#formdiv_%formval% #messagingdetailscomposedetails {
		display: block;
		height: 40px;
		width: 100%;
		border: 0;
		padding: 5px;
		margin: 0;
	}

	#formdiv_%formval% #messagingdetailscompose .dhx_cell_editor .dhx_cell_stb {
		display: none;
	}
	
</style>
<div id="messagingdetails">
	<div id="messagingdetailscompose">
		<div id="messagingdetailscomposedetails" class="navbar-default-bg"><div id="messagingdetailscomposedetailsform_%formval%"></div></div>
		<div id="messagingdetailscomposeeditor"></div>
		<?php /*if(!empty($vars['post']['method'])&&$vars['post']['method']=='messagingsendtooutbox') { pre(array('$vars'=>$vars)); } */ ?>
	</div>
</div>
<script>

	function messagingdetailscomposedetails_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 80, inputWidth: 300},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "formid", value: "messagingdetailscompose"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
			]},
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("messagingdetailscomposedetailsform_%formval%",[]);

		$('#messagingdetailscomposedetailsform_%formval% .dhxform_base').html('<div style="display:block;margin:10px 0 0 0;"><strong>Encoding:</strong>&nbsp;<span>GSM 7-bit</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Characters: </strong><span id="charcount"></span></div>');

		var myComposeEditor = myComposeEditor_%formval% = new dhtmlXEditor({parent:"messagingdetailscomposeeditor",toolbar:false});

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
				$('#messagingdetailscomposedetailsform_%formval% #charcount').html(contlen+' used ('+smscnt+' SMS)');				
			} else {
				$('#messagingdetailscomposedetailsform_%formval% #charcount').html(contlen+' used');				
			}
		});

		myTab.toolbar.getToolbarData('messagingsendtooutbox').onClick = myTab.toolbar.getToolbarData('messagingsendnow').onClick = function(id,formval) {

			//showMessage("toolbar: "+id,5000);
			//showMessage("content: "+content,5000);

			var content = trim(myComposeEditor_%formval%.getContent());

			if(content.length<1) {
				showAlertError('Cannot send empty SMS to Outbox!');
				return false;
			}

			var tonumbers = [];

			myToNumberGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myToNumberGrid_%formval%.cells(id,0).getValue());
				if(val) {
					tonumbers.push(id);
				}
			});


			//var tonumbers = trim($("#messagingmaincomposeform_%formval% input[name='txt_to_number']").val());

			var togroups = trim($("#messagingmaincomposeform_%formval% input[name='txt_to_groups']").val());

			/*if(tonumbers.length<1&&togroups.length<1) {
				showAlertError('Please select a Number or Group!');
				return false;
			}*/

			if(tonumbers.length<1&&togroups.length<1) {
				showAlertError('Please select a Number or Group!');
				return false;
			}

			var ports = trim($("#messagingmaincomposeform_%formval% input[name='txt_ports']").val());

			if(ports.length<1) {
				showAlertError('Please select SIM to use!');
				return false;
			}

			//showConfirmWarning((id=='messagingsendtooutbox' ? 'Send this SMS to Outbox?' : 'Send this SMS now?'),function(val){

				//if(val) {
					//var $ = jQuery;

					var content = trim(myComposeEditor_%formval%.getContent());

					//var tonumbers = $("#messagingmaincomposeform_%formval% input[name='txt_to_number']").val();
					var togroups = $("#messagingmaincomposeform_%formval% input[name='txt_to_groups']").val();
					var ports = $("#messagingmaincomposeform_%formval% input[name='txt_ports']").val();

					//tonumbers = tonumbers ? tonumbers : '';
					togroups = togroups ? togroups : '';
					ports = ports ? ports : '';

					myTab.postData('/'+settings.router_id+'/json/', {
						odata: {},
						pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscompose&module=messaging&method="+id+"&formval=%formval%&content="+encodeURIComponent(content)+"&tonumbers="+encodeURIComponent(tonumbers)+"&togroups="+encodeURIComponent(togroups)+"&ports="+encodeURIComponent(ports),
					}, function(ddata,odata){

						var $ = jQuery;

						//$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

						//layout_resize2_%formval%();

							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									//doSelect_%formval%("compose");
									//layout_resize_%formval%();
									//showAlert(ddata.return_message);
									showMessage(ddata.return_message,5000);
								}
							}

					});
				//}

			//});

		};

		myTab.toolbar.getToolbarData('messagingclear').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			myComposeEditor_%formval%.setContent('');
		};

		/*myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			showMessage("toolbar: "+id,5000);
			doSelect_%formval%("compose");
			//layout_resize2_%formval%();
		};*/

		myTab.toolbar.getToolbarData('messagingcancel').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("compose");

			<?php if(!empty($vars['post']['from'])) { ?>
			doSelect_%formval%('<?php echo $vars['post']['from']; ?>');
			<?php } else { ?>
			doSelect_%formval%("compose");
			<?php } ?>

		};

	}

	messagingdetailscomposedetails_%formval%();

</script>