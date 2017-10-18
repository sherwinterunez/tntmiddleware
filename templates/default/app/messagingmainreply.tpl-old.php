<style>
	#formdiv_%formval% #messagingmainreply {
		display: block;
		height: 500px;
		width: 100%;
		padding: 5px;
	}
</style>
<div id="messagingmain" class="navbar-default-bg">
	<div id="messagingmainreply">
		<div id="messagingmainreplyform_%formval%" style="height:auto;width:auto;"></div>
	</div>
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('b').setHeight(250);

	myTab.layout.cells('d').collapse();

	function messagingmainreplyform_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = ['messagingsendtooutbox','messagingclear','messagingcancel'];

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.showOnly(myToolbar);	


		/*formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 250},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "form"},
				{type: "hidden", name: "module", value: "user"},
				{type: "hidden", name: "buttonid", value: ""},
			]},
			{type: "fieldset", name: "newrole", label: "Role", inputWidth: 500, list:[
				{type: "hidden", name: "role_id", value: "1"},
				{type: "input", name: "role_name", label: "Role", value: "2"},
				{type: "input", name: "role_desc", label: "Description", value: ""},
			]},
		];*/

		// myTab.layout.cells('b').getWidth()-150

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
			{type:"multiselect",name:"to_number",inputHeight:100,
				options:[
				{text: ""},
				{text: "All Contacts"},
				{text: "09287710253 / Sherwin", value: "09287710253"},
				{text: "09493621255 / Celeste", value: "09493621255"},
			]},
			{type: "newcolumn", offset:20},
			{type:"label",label:"To Groups"},
			{type: "input", name: "txt_to_groups", readonly:true, value: ""},
			{type:"multiselect",name:"to_groups",inputHeight:100,
				options:[
				{text: ""},
				{text: "All Groups"},
				{text: "Globe/Touch Mobile"},
				{text: "Smart/Talk N Text"},
				{text: "Sun Cellular"},
			]},
			{type: "newcolumn", offset:20},
			{type:"label",label:"Ports"},
			{type: "input", name: "txt_ports", readonly:true, value: ""},
			{type:"multiselect",name:"ports",inputHeight:100,
				options:[
				{text: ""},
				{text: "All Ports"},
				{text: "USBSerial#1 / Smart/Talk N Text / 09498671493", value: "Option 1", selected: false},
				{text: "Power users", value: "Option 2", selected: false},
				{text: "Registered users", value: "Option 3"},
				{text: "Guests", value: "Option 4"},
				{text: "All users", value: "Option 5"},
			]},
		];

		var myForm = myForm_%formval% = new dhtmlXForm("messagingmainreplyform_%formval%", formData_%formval%);

		myForm.attachEvent("onChange", function (name, value){
			var x = value;
			//srt.dummy.apply(this,[value]);
		    showMessage("onChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

		    if(value.length) {
		    	var t = '';
		    	for(var i=0;i<value.length;i++) {
		    		//myForm.setItemValue("txt_"+name, value);
		    		if(value[i]=='') continue;
		    		t = t + value[i] + ';';
		    	}
		    	myForm.setItemValue("txt_"+name, t);
		    }
		});

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=formonly&formid=messagingdetailsreply&module=messaging&formval=%formval%",
		}, function(ddata,odata){

			var $ = jQuery;

			$("#formdiv_%formval% #messagingdetails").parent().html(ddata.html);

			layout_resize_%formval%();

		});

		//myForm.setItemWidth("to_number", myTab.layout.cells('b').getWidth()-150);
	}

	messagingmainreplyform_%formval%();

</script>
