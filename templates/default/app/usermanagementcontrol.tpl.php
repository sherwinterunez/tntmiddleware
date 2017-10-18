<style>
	#formdiv_%formval% #usermanagement_tree {
		height: 100%;
		width: 100%;
	}

	#formdiv_%formval% #usermanagement_tree .containerTableStyle {
		overflow: none;
	}

	#formdiv_%formval% #usermanagementmanage {
		height: 100%
		width: 100%;
		margin:10px;
	}
</style>

<div id="usermanagement_tree"></div>

<script>
	var myTree_%formval%;

	function usermanagement_tree_%formval%(){

		var myTree;
		/*myTree_%formval% = new dhtmlXTreeObject({
			parent: jQuery("#formdiv_%formval% #usermanagement_tree")[0],
			skin: "dhx_skyblue",
			checkbox: true,
			image_path: settings.template_assets+"imgs/dhxtree_skyblue/",
			xml: "/common/tree.xml"
		});*/

		myTree = myTree_%formval% = new dhtmlXTreeObject(jQuery("#formdiv_%formval% #usermanagement_tree")[0],"100%","100%",0);

		//myTree_%formval%.load("/common/tree.xml");
		myTree_%formval%.setImagesPath(settings.template_assets+"imgs/dhxtree_skyblue/");

		//myTree_%formval%.openAllItems('books');

		//myTree_%formval%.openItem('books');

		var myTab = srt.getTabUsingFormVal('%formval%');

		//srt.dummy.apply(myTab,[]);

		//srt.dummy.apply(myTab.layout.cells('a'),[]);

		if(typeof(myTab.layout.cells('a').ddata.xml)=='string') {
			//alert('Hello!');
			myTree_%formval%.parse(myTab.layout.cells('a').ddata.xml,"xml"); 
		}

		//myTree_%formval%.load();

		myTab.toolbar.disableOnly(['usermanagementnewuser','usermanagementedit','usermanagementdelete','usermanagementsave']);

		myTree_%formval%.attachEvent("onClick", function(id){
			$ = jQuery;

			//srt.dummy.apply(this,[id]);

			//showMessage(id,5000);

			//this.openAllItems(id);

			//$("#formdiv_%formval% #usermanagementmanage").html(id+': '+this.getItemText(id));

			//myTab.postData('/'+settings.router_id+'/json/','router_id='+settings.router_id)

			$("#formdiv_%formval% #usermanagementmanage").html('Loading...');

			var ru = explode('_',id);
			var roleid = '';
			var userid = '';

			if(in_array('roleid',ru)) {
				roleid = ru[1];
			}

			if(in_array('userid',ru)) {
				userid = ru[3];
			}

			if(roleid==0) {
				myTab.toolbar.disableOnly(['usermanagementnewuser','usermanagementedit','usermanagementdelete','usermanagementsave']);
				$("#formdiv_%formval% #usermanagementmanage").html('&nbsp;');
				return false;
			}

			//if(roleid==1) {
			//	myTab.toolbar.disableOnly(['usermanagementedit','usermanagementdelete','usermanagementsave']);
			//}

			//srt.dummy.apply(this,[roles]);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {o:this,roleid:roleid,userid:userid},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=usermanagementmanage&module=user&formval=%formval%&roleid="+urlencode(roleid)+"&userid="+urlencode(userid),
			}, function(ddata,odata){
				$ = jQuery;
				//showMessage(ddata.html,5000);
				if(roleid==1&&!userid) {
					myTab.toolbar.disableOnly(['usermanagementedit','usermanagementdelete','usermanagementsave']);
				} else
				if(roleid==1&&userid==1) {
					myTab.toolbar.disableOnly(['usermanagementdelete','usermanagementsave']);
				} else {
					myTab.toolbar.disableOnly(['usermanagementsave']);					
				}
				$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			});

		});


		myTab.toolbar.getToolbarData('usermanagementnewroles').onClick = function() {
			/*showMessage('myTab.formval: '+myTab.formval,5000);
			showMessage('usermanagementnewroles.func',5000);
			showMessage(arguments+', length: '+arguments.length,5000);

			for(var i=0;i<arguments.length;i++) {
				showMessage('arguments['+i+'] => '+arguments[i],5000);
			}*/

			//var alltb = myTab.toolbar.getAllToolbarData();

			//srt.dummy.apply(this,[alltb]);

			//for(var i=0;i<alltb.length;i++) {
			//	showMessage(alltb[i].id,5000);
			//}

			//myTab.toolbar.disableAll();

			$("#formdiv_%formval% #usermanagementmanage").html('Loading...');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: this,
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=usermanagementnewroles&module=user&formval=%formval%",
			}, function(ddata,odata){
				$ = jQuery;
				myTab.toolbar.disableOnly(['usermanagementnewroles','usermanagementnewuser','usermanagementedit','usermanagementdelete']);
				$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			});

			return false;
		};

		myTab.toolbar.getToolbarData('usermanagementnewuser').onClick = function(id,formval) {

			//showMessage(myTree.getSelectedItemId(),5000);

			/*showMessage('myTab.formval: '+myTab.formval,5000);
			showMessage('usermanagementnewuser.func',5000);
			showMessage(arguments+', length: '+arguments.length,5000);

			for(var i=0;i<arguments.length;i++) {
				showMessage('arguments['+i+'] => '+arguments[i],5000);
			}*/

			$("#formdiv_%formval% #usermanagementmanage").html('Loading...');

			var ru = explode('_',myTree.getSelectedItemId());
			var roleid = '';

			if(in_array('roleid',ru)) {
				roleid = ru[1];
			}

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: this,
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=usermanagementnewuser&module=user&formval=%formval%&roleid="+roleid,
			}, function(ddata,odata){
				$ = jQuery;
				myTab.toolbar.disableOnly(['usermanagementnewroles','usermanagementnewuser','usermanagementedit','usermanagementdelete']);
				$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			});

			return false;
		};

		myTab.toolbar.getToolbarData('usermanagementedit').onClick = function() {

			/*showMessage('myTab.formval: '+myTab.formval,5000);
			showMessage('usermanagementedit.func',5000);
			showMessage(arguments+', length: '+arguments.length,5000);

			for(var i=0;i<arguments.length;i++) {
				showMessage('arguments['+i+'] => '+arguments[i],5000);
			}*/

			var ru = explode('_',myTree.getSelectedItemId());

			var roleid = '';
			var userid = '';

			if(in_array('roleid',ru)) {
				roleid = ru[1];
			}

			if(in_array('userid',ru)) {
				userid = ru[3];
			}

			$("#formdiv_%formval% #usermanagementmanage").html('Loading...');

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: this,
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=usermanagementedit&module=user&formval=%formval%&roleid="+urlencode(roleid)+"&userid="+urlencode(userid),
			}, function(ddata,odata){
				$ = jQuery;
				myTab.toolbar.disableOnly(['usermanagementnewroles','usermanagementnewuser','usermanagementedit','usermanagementdelete']);
				$("#formdiv_%formval% #usermanagementmanage").parent().html(ddata.html);
			});

			return false;
		};

		myTab.toolbar.getToolbarData('usermanagementdelete').onClick = function() {

			/*showMessage('myTab.formval: '+myTab.formval,5000);
			showMessage('usermanagementedit.func',5000);
			showMessage(arguments+', length: '+arguments.length,5000);

			for(var i=0;i<arguments.length;i++) {
				showMessage('arguments['+i+'] => '+arguments[i],5000);
			}*/


			var ru = explode('_',myTree.getSelectedItemId());

			var roleid = '';
			var userid = '';
			var candelete = false;

			if(in_array('roleid',ru)) {
				roleid = ru[1];
			}

			if(in_array('userid',ru)) {
				userid = ru[3];
			}

			if(roleid&&!userid) {
				var si = explode(',',myTree.getSubItems(myTree.getSelectedItemId()));

				if(si.length>0) {
					var ur = explode('_',si[0]);

					if(ur[0]=='nousers') {
						candelete=true;
					}
				}

				if(!candelete) {
					showAlertError('Cannot delete Role with users under it.');
					return false;
				}
			} else {
				candelete=true;
			}

			if(candelete) {
				showConfirmWarning('Are you sure you want to delete this item?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: this,
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=usermanagementdelete&module=user&formval=%formval%&roleid="+urlencode(roleid)+"&userid="+urlencode(userid),
						}, function(ddata,odata){
							$ = jQuery;
							myTab.toolbar.disableOnly(['usermanagementnewroles','usermanagementnewuser','usermanagementedit','usermanagementdelete']);

							if(typeof(ddata.xml)=='string') {
								myTree_%formval%.deleteItem('roleid_0');
								myTree_%formval%.parse(ddata.xml,"xml"); 
								myTree_%formval%.selectItem('roleid_0',true);
							}

						});
					}
				});
			}

			return false;
		};

	};

	usermanagement_tree_%formval%();
</script>
