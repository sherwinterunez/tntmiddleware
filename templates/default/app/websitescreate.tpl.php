
<div id="websitesmanage">
<div id="websitesmanageform_%formval%" style="height:auto;width:auto;"></div>
</div>

<script>

	var myForm_%formval%, formData_%formval%;

	function websitesmanagecreate_%formval%() {
		formData_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 100, inputWidth: 250},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "form"},
				{type: "hidden", name: "module", value: "web"},
			]},
			{type: "fieldset", name: "basicinfo", label: "Website", inputWidth: 500, list:[
				{type: "hidden", name: "website_id", value: "<?php if(!empty($vars['post']['roleid'])){echo $vars['post']['roleid'];} ?>"},
				{type: "input", name: "website_name", label: "Name", value: ""<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
				{type: "input", name: "website_domain", label: "Domain", value: ""<?php if(!$readonly){echo ', validate:"NotEmpty", required:true';}?><?php if($readonly){echo ', readonly:true';}?>},
				{type: "input", name: "website_desc", label: "Description", value: ""<?php if($readonly){echo ', readonly:true';}?>},
			]},
		];

		myForm_%formval% = new dhtmlXForm("websitesmanageform_%formval%", formData_%formval%);

		var myForm = myForm_%formval%;

	}

	websitesmanagecreate_%formval%();

</script>