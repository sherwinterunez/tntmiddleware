<?php

$readonly = true;

$contacts = false;

$groups = false;

$ports = false;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if(!empty($vars['params']['contacts'])) {
	$contacts = array();
	$contacts[] = array('text'=>'');
	//$contacts[] = array('text'=>'All Contacts');

	foreach($vars['params']['contacts'] as $k=>$v) {
		$contacts[] = array('text'=>$v['contact_number'].' / '.$v['contact_nick'],'value'=>$v['contact_number']);
	}

	if(!empty($contacts)) {
		$vars['contact_json'] = json_encode($contacts);
		$contacts = true;
	} else {
		$contacts = false;
	}
} else {
	$vars['contact_json'] = json_encode(array(array('text'=>"No Contacts Available")));
	$contacts = false;
}

if(!empty($vars['params']['groups'])) {
	$groups = array();
	$groups[] = array('text'=>'');

	//foreach($vars['params']['groups'] as $k=>$v) {
	//	$groups[] = array('text'=>$v);
	//}

	//if(!empty($groups)) {
		$vars['group_json'] = json_encode($vars['params']['groups']);
		$groups = true;
	//} else {
	//	$groups = false;		
	//}
} else {
	$vars['group_json'] = json_encode(array(array('text'=>"No Groups Available")));
	$groups = false;
}

if(!empty($vars['params']['ports'])) {
	$ports = array();
	$ports[] = array('text'=>'');

	$vars['port_json'] = json_encode($vars['params']['ports']);
	$ports = true;
} else {
	$vars['port_json'] = json_encode(array(array('text'=>"No Ports Available")));
	$ports = false;
}

?>
<style>
	#formdiv_%formval% #messagingmaincompose {
		display: block;
		height: 500px;
		width: 100%;
		padding: 5px;
	}
</style>
<div id="messagingmain" class="navbar-default-bg">
	<div id="messagingmaincompose">
		<div id="messagingmaincomposeform_%formval%" style="height:auto;width:auto;"></div>
		<?php //pre(array('$vars'=>$vars)); ?>
	</div>
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('b').setHeight(250);

	myTab.layout.cells('d').collapse();

	$("#formdiv_%formval% #messagingmain").parent().css({'overflow':'hidden'});

	function messagingmaincomposeform_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingsendtooutbox','messagingclear','messagingcancel'];

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.showOnly(myToolbar);	

		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 300},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "form"},
				{type: "hidden", name: "module", value: "messaging"},
				{type: "hidden", name: "buttonid", value: ""},
			]},
			//{type: "input", name: "to_number", label: "To Number", value: ""},
			//{type: "input", name: "to_groups", label: "To Groups", value: ""},
			//{type: "input", name: "subject", label: "Subject", value: ""},
			{type:"label",label:"To Number"},
			{type: "input", name: "txt_to_number", readonly:true, value: ""},
			{type:"multiselect",name:"to_number",disabled: <?php echo $contacts ? 'false' : 'true'; ?>,inputHeight:100,
				options: <?php echo $vars['contact_json']; ?>
			/*	options:[
				{text: "No Contacts Available"},
				{text: ""},
				{text: "All Contacts"},
				{text: "09287710253 / Sherwin", value: "09287710253"},
				{text: "09493621255 / Celeste", value: "09493621255"},
			]*/
			},
			{type: "newcolumn", offset:20},
			{type:"label",label:"To Groups"},
			{type: "input", name: "txt_to_groups", readonly:true, value: ""},
			{type:"multiselect",name:"to_groups",inputHeight:100,
				options: <?php echo $vars['group_json']; ?>
			},
			{type: "newcolumn", offset:20},
			{type:"label",label:"Ports"},
			{type: "input", name: "txt_ports", readonly:true, value: ""},
			{type:"multiselect",name:"ports",inputHeight:100,
				options: <?php echo $vars['port_json']; ?>
			},
		];

		var myForm = myForm_%formval% = new dhtmlXForm("messagingmaincomposeform_%formval%", formData_%formval%);

		myForm.attachEvent("onBeforeChange", function (name, old_value, new_value){
		    //showMessage("onBeforeChange: ["+name+"] "+name.length+" / {"+old_value+"} "+old_value.length,5000);
		    return true;
		});

		myForm.attachEvent("onChange", function (name, value){
			var x = value;
			//srt.dummy.apply(this,[value]);
		    //showMessage("onChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

		    if(value.length) {
		    	var t = '';
		    	for(var i=0;i<value.length;i++) {
		    		//myForm.setItemValue("txt_"+name, value);
		    		if(value[i]=='') continue;
		    		t = t + value[i] + ';';
		    	}
		    	myForm.setItemValue("txt_"+name, t);
				myChanged_%formval% = true;
		    }
		});

		myForm.attachEvent("onInputChange", function(name, value, form){
		    //showMessage("onInputChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

			myChanged_%formval% = true;
		});

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailscompose&module=messaging&formval=%formval%",
		}, function(ddata,odata){

			var $ = jQuery;

			$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

			layout_resize_%formval%();

		});

		try {

			clearInterval(mySetInterval_%formval%);

		} catch(e) {}

		//myForm.setItemWidth("to_number", myTab.layout.cells('b').getWidth()-150);
	}

	messagingmaincomposeform_%formval%();

</script>
