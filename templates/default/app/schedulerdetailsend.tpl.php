<?php
$moduleid = 'scheduler';
$submod = 'send';
$templatemainid = $moduleid.'main';
$templatedetailid = $moduleid.'detail';

$readonly = true;

$method = '';

if(!empty($vars['post']['method'])) {
	$method = $vars['post']['method'];
}

if($method==$moduleid.'new'||$method==$moduleid.'edit') {
	$readonly = false;
}

//$myToolbar = array($moduleid.'new',$moduleid.'edit',$moduleid.'delete',$moduleid.'save',$moduleid.'cancel',$moduleid.'refresh');

$myToolbar = array($moduleid.'send',$moduleid.'refresh');

/*if(!empty($vars['params']['optionsinfo']['options_name'])) {
	$options_name = $vars['params']['optionsinfo']['options_name'];
}

if(!empty($vars['params']['optionsinfo']['options_type'])) {
	$options_type = $vars['params']['optionsinfo']['options_type'];
}

if(!empty($vars['params']['optionsinfo']['options_value'])) {
	$options_value = $vars['params']['optionsinfo']['options_value'];
}*/

?>
<!--
<?php pre(array('$vars'=>$vars)); ?>
-->
<style>
	#formdiv_%formval% #<?php echo $templatedetailid.$submod; ?> {
		display: block;
		height: auto;
		width: 100%;
		border: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	#formdiv_%formval% #<?php echo $templatedetailid.$submod; ?> #<?php echo $templatedetailid.$submod; ?>tabform_%formval% {
		display: block;
		/*border: 1px solid #f00;*/
		border; none;
		height: 29px;
	}
	#formdiv_%formval% #<?php echo $templatedetailid.$submod; ?>detailsform_%formval% {
		padding: 10px;
		/*border: 1px solid #f00;*/
		overflow: hidden;
		overflow-y: scroll;
	}
	#formdiv_%formval% .dhxtabbar_base_dhx_skyblue div.dhx_cell_tabbar div.dhx_cell_cont_tabbar {
		display: none;
	}
	#formdiv_%formval% .dhxtabbar_base_dhx_skyblue div.dhxtabbar_tabs {
		border-top: none;
		border-left: none;
		border-right: none;
	}
	#formdiv_%formval% .cls_bottomspace {
		display: block;
		/*height: 500px;*/
		border: 1px solid #f00;
		padding-bottom: 10px;
	}
	#formdiv_%formval% #<?php echo $templatedetailid.$submod; ?> div.cls_sherwin div.dhxform_block {
		background-color: #fff;
		border:1px solid #a4bed4;
		overflow-y: scroll;
		height: 150px;
	}

	#formdiv_%formval% #<?php echo $templatedetailid.$submod; ?> div.cls_sherwin div.dhxform_block div.dhxform_item_label_right {
		padding: 0;
	}
</style>
<div id="<?php echo $templatedetailid; ?>">
	<div id="<?php echo $templatedetailid.$submod; ?>" class="navbar-default-bg">
		<div id="<?php echo $templatedetailid.$submod; ?>tabform_%formval%"></div>
		<div id="<?php echo $templatedetailid.$submod; ?>detailsform_%formval%"></div>
		<br style="clear:both;" />
	</div>
</div>
<script>

	function <?php echo $templatedetailid.$submod; ?>_%formval%() {

		var $ = jQuery;

		var myTab = srt.getTabUsingFormVal('%formval%');

		var myToolbar = <?php echo json_encode($myToolbar); ?>;

		var myTabbar = new dhtmlXTabBar("<?php echo $templatedetailid.$submod; ?>tabform_%formval%");

		myTabbar.setArrowsMode("auto");
			
		myTabbar.addTab("tbDetails", "Details");

		<?php /* ?>
		myTabbar.addTab("tbPayments", "Payments");
		myTabbar.addTab("tbMessage", "Message");
		myTabbar.addTab("tbHistory", "History");
		<?php */ ?>

		myTabbar.tabs("tbDetails").setActive();

		myTab.toolbar.resetAll();

		var formData2_%formval% = [
			{type: "settings", position: "label-left", labelWidth: 130, inputWidth: 200},
			{type: "fieldset", name: "settings", hidden: true, list:[
				{type: "hidden", name: "routerid", value: settings.router_id},
				{type: "hidden", name: "formval", value: "%formval%"},
				{type: "hidden", name: "action", value: "formonly"},
				{type: "hidden", name: "module", value: "<?php echo $moduleid; ?>"},
				{type: "hidden", name: "formid", value: "<?php echo $templatedetailid.$submod; ?>"},				
				{type: "hidden", name: "method", value: "<?php echo !empty($method) ? $method : ''; ?>"},
				{type: "hidden", name: "rowid", value: "<?php echo $method==$moduleid.'edit' ? $vars['post']['rowid'] : ''; ?>"},
			]},
			{type: "block", name: "tbDetails", hidden:false, width: 1500, blockOffset: 0, offsetTop:0, list:<?php echo json_encode($params['tbDetails']); ?>},
			<?php /* ?>
			{type: "block", name: "tbPayments", hidden: true, width: 1500, blockOffset: 0, offsetTop:0, list:[]},
			{type: "block", name: "tbMessage", hidden: true, width: 1500, blockOffset: 0, offsetTop:0, list:[]},
			{type: "block", name: "tbHistory", hidden: true, width: 1500, blockOffset: 0, offsetTop:0, list:[]},
			<?php */ ?>
			{type: "label", label: ""}
		];

		if(typeof(myForm2_%formval%)!='undefined') {
			try {
				myForm2_%formval%.unload();
			} catch(e) {}
		}

		var myForm = myForm2_%formval% = new dhtmlXForm("<?php echo $templatedetailid.$submod; ?>detailsform_%formval%",formData2_%formval%);

		myChanged_%formval% = false;

		myFormStatus_%formval% = '<?php echo $method; ?>';

///////////////////////////////////

		<?php if($method==$moduleid.'new'||$method==$moduleid.'edit') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		//myForm.setItemFocus("txt_optionsname");

		<?php } else if($method==$moduleid.'save') { ?> 

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		myTab.toolbar.showOnly(myToolbar);	

		<?php } else { ?>

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.disableOnly(['<?php echo $moduleid; ?>save','<?php echo $moduleid; ?>cancel']);

		if(myForm.getItemValue('scheduler_schedule')==null) {

			var date = new Date();

			myForm.setItemValue('scheduler_schedule',date);

			cal = myForm.getCalendar('scheduler_schedule');

			cal.setSensitiveRange(date, null);

		} else {

			var cal = myForm.getCalendar('scheduler_schedule');

			cal = myForm.getCalendar('scheduler_schedule');

			cal.setSensitiveRange(date, null);
		}

		<?php 	if(empty($vars['post']['rowid'])) { ?>

		myTab.toolbar.disableItem('<?php echo $moduleid; ?>edit');

		myTab.toolbar.disableItem('<?php echo $moduleid; ?>delete');

		<?php 	} ?>

///////////////////////////////////

		//myTab.toolbar.setValue("<?php echo $moduleid; ?>datefrom","<?php $dt=getDbDate(1); echo $dt['date']; ?> 00:00");
		//myTab.toolbar.setValue("<?php echo $moduleid; ?>dateto","<?php echo getDbDate(); ?>");

///////////////////////////////////

		myTab.toolbar.showOnly(myToolbar);	

		<?php } ?>

		setTimeout(function(){
			layout_resize_%formval%();
		},100);

///////////////////////////////////

		myTabbar.attachEvent("onTabClick", function(id, lastId){

			myTabbar.forEachTab(function(tab){
			    var tbId = tab.getId();

			    if(id==tbId) {
				    myForm2_%formval%.showItem(tbId);
			    } else {
				    myForm2_%formval%.hideItem(tbId);
			    }
			});
 
		});

		myForm.attachEvent("onBeforeChange", function (name, old_value, new_value){
		    //showMessage("onBeforeChange: ["+name+"] "+name.length+" / {"+old_value+"} "+old_value.length,5000);
		    return true;
		});

		myForm.attachEvent("onChange", function (name, value, state){
		    //showMessage("onChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

		    showMessage('onChange: '+name+', value: '+value+', state: '+state,5000);

			myChanged_%formval% = true;

			if(typeof(name)!='string') return false;

			if(typeof(this.to_number)=='undefined') {
				this.to_number = [];
				this.to_groups = [];
				this.to_sims = [];
			}

			if(name.substring(0,9)=='to_number') {

				var tname = name.substring(0,9);

				if(name=='to_number_0') {
					this.forEachItem(function(name){
					//    showMessage('name: '+name,5000);
						if(name!='to_number_0'&&name.substring(0,9)=='to_number') {
							if(state) {
								this.disableItem(name);
								this.uncheckItem(name);
							} else {
								this.enableItem(name);								
							}
						}
					});


					if(state) {
						myForm.setItemValue("txt_"+tname, 'All Contacts');
						myChanged_%formval% = true;
					} else {
						myForm.setItemValue("txt_"+tname, '');
						myChanged_%formval% = true;
					}

					return true;
				}

				//alert('name: '+name.substring(0,9)+'/'+typeof(name)+', value: '+value+', state: '+state);
				if(state) {
					if(!in_array(value,this.to_number)) {
						this.to_number.push(value);
					}
				} else {
					var key = '';
					for (key in this.to_number) {
						if (this.to_number[key] == value) {
							this.to_number.splice(key,1);
						}
					}
				}

				var t = '';

				for (key in this.to_number) {
					t = t + this.to_number[key] + ';';
				}

				myForm.setItemValue("txt_"+tname, t);
				myChanged_%formval% = true;
			} else

			if(name.substring(0,9)=='to_groups') {

				var tname = name.substring(0,9);

				if(name=='to_groups_0') {
					this.forEachItem(function(name){
					//    showMessage('name: '+name,5000);
						if(name!='to_groups_0'&&name.substring(0,9)=='to_groups') {
							if(state) {
								this.disableItem(name);
								this.uncheckItem(name);
							} else {
								this.enableItem(name);								
							}
						}
					});

					if(state) {
						myForm.setItemValue("txt_"+tname, 'All Groups');
						myChanged_%formval% = true;
					} else {
						myForm.setItemValue("txt_"+tname, '');
						myChanged_%formval% = true;
					}

					return true;
				}

				//alert('name: '+name.substring(0,9)+'/'+typeof(name)+', value: '+value+', state: '+state);
				if(state) {
					if(!in_array(value,this.to_groups)) {
						this.to_groups.push(value);
					}
				} else {
					var key = '';
					for (key in this.to_groups) {
						if (this.to_groups[key] == value) {
							this.to_groups.splice(key,1);
						}
					}
				}

				var t = '';

				for (key in this.to_groups) {
					t = t + this.to_groups[key] + ';';
				}

				myForm.setItemValue("txt_"+tname, t);
				myChanged_%formval% = true;
			} else

			if(name.substring(0,7)=='to_sims') {

				var tname = name.substring(0,7);

				if(name=='to_sims_0') {
					this.forEachItem(function(name){
					//    showMessage('name: '+name,5000);
						if(name!='to_sims_0'&&name.substring(0,7)=='to_sims') {
							if(state) {
								this.disableItem(name);
								this.uncheckItem(name);
							} else {
								this.enableItem(name);								
							}
						}
					});

					if(state) {
						myForm.setItemValue("txt_"+tname, 'All SIMs');
						myChanged_%formval% = true;
					} else {
						myForm.setItemValue("txt_"+tname, '');
						myChanged_%formval% = true;
					}

					return true;
				}

				//alert('name: '+name.substring(0,9)+'/'+typeof(name)+', value: '+value+', state: '+state);
				if(state) {
					if(!in_array(value,this.to_sims)) {
						this.to_sims.push(value);
					}
				} else {
					var key = '';
					for (key in this.to_sims) {
						if (this.to_sims[key] == value) {
							this.to_sims.splice(key,1);
						}
					}
				}

				var t = '';

				for (key in this.to_sims) {
					t = t + this.to_sims[key] + ';';
				}

				myForm.setItemValue("txt_"+tname, t);
				myChanged_%formval% = true;
			}

		});

		myForm.attachEvent("onInputChange", function(name, value, form){
		    //showMessage("onInputChange: ["+name+"] "+name.length+" / {"+value+"} "+value.length,5000);

			myChanged_%formval% = true;
		});

		myForm.attachEvent("onValidateError", function(id,value){
			var msg;

			/*if(id=='txt_optionsvalue') {
				msg = 'Please enter Value. This field is required.';
			} else
			if(id=='txt_optionsname') {
				msg = 'Please enter Name. This field is required.';
			} else
			if(id=='txt_optionstype') {
				msg = 'Please enter Type. This field is required.';
			}

			this.setNote(id,{text:msg});*/

			//showErrorMessage('Error: '+id,60000,id);
		});

		myForm.attachEvent("onValidateSuccess", function(id,value){
			this.clearNote(id);
		});

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>send').onClick = function(id,formval) {

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(!rowid) {
				showAlertError('Invalid Row ID. Please refresh browser.');
				return false;
			}

			var tonumbers = myForm.getItemValue('txt_to_number');
			var togroups = myForm.getItemValue('txt_to_groups');

			var date = myForm.getItemValue('scheduler_schedule');

			var scheduler_schedule = (date.getMonth()+1) + '-' + date.getDate() + '-' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes();

			//alert(scheduler_schedule);

			if(tonumbers.length<1&&togroups.length<1) {
				showAlertError('Please select a Number or Group!');
				return false;
			}

			var tosims = myForm.getItemValue('txt_to_sims');

			if(tosims.length<1) {
				showAlertError('Please select SIM to use!');
				return false;
			}

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval=%formval%&tonumbers="+encodeURIComponent(tonumbers)+"&togroups="+encodeURIComponent(togroups)+"&tosims="+encodeURIComponent(tosims)+"&rowid="+rowid+"&scheduler_schedule="+encodeURIComponent(scheduler_schedule),
			}, function(ddata,odata){

				if(ddata.return_code) {
					if(ddata.return_code=='SUCCESS') {
						showMessage(ddata.return_message,5000);
					}
				}

			});

		};

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>new').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			myTab.postData('/'+settings.router_id+'/json/', {
				odata: {},
				pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method="+id+"&formval=%formval%",
			}, function(ddata,odata){
				if(ddata.html) {
					jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);					
				}
			});
		};

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>edit').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			if(rowid) {
				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {rowid:rowid},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method="+id+"&rowid="+rowid+"&formval=%formval%",
				}, function(ddata,odata){

					var $ = jQuery;

					$("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);

					//layout_resize2_%formval%();

				});
			}
		};

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>delete').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var rowid = myGrid_%formval%.getSelectedRowId();

			var rowids = [];

			myGrid_%formval%.forEachRow(function(id){
				var val = parseInt(myGrid_%formval%.cells(id,0).getValue());
				if(val) {
					rowids.push(id);
				}
			});

			if(rowid) {
				showConfirmWarning('Are you sure you want to delete the item(s)?',function(val){

					if(val) {
						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {rowid:rowid},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method="+id+"&rowid="+rowid+"&rowids="+rowids+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.return_code) {
								if(ddata.return_code=='SUCCESS') {
									<?php echo $templatemainid.$submod; ?>grid_%formval%();
									showAlert(ddata.return_message);
								}
							}
						});
					}

				});
			}
		};

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>save').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);

			var myForm = myForm2_%formval%;

			var txt_optionnumber = parseInt($("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val());
			
			if(isNaN(txt_optionnumber)) {
				txt_optionnumber = '';
			}

			myForm.setItemValue('txt_optionnumber', txt_optionnumber);

			//$("#messagingdetailsoptionsdetailsform_%formval% input[name='txt_optionnumber']").val(txt_optionnumber);

			myForm.trimAllInputs();

			if(!myForm.validate()) return false; 

			showSaving();

			//$("#usermanagementmanageform_"+formval+" input[name='buttonid']").val(id);

			//showMessage('Validation: '+myForm.validate());

			myForm.setItemValue('method', id);

			//$("#messagingdetailsoptionsdetailsform_%formval% input[name='method']").val(id);

			var obj = {o:this,id:id};

			$("#<?php echo $templatedetailid.$submod; ?>detailsform_%formval%").ajaxSubmit({
				url: "/"+settings.router_id+"/json/",
				dataType: 'json',
				semantic: true,
				obj: obj,
				success: function(data, statusText, xhr, $form, obj){
					var $ = jQuery;

					//alert(obj.id);

					hideSaving();

					if(data.error_code&&data.error_message) {

						//hideSaving();

						showAlertError('ERROR('+data.error_code+') '+data.error_message);

						if(settings.debug) {
							console.log(data.error_code+' => '+data.error_message);

							if(data.backtrace) {
								console.log(data.backtrace);
							}

							if(data.dberrors) {
								console.log(data.dberrors);
							}

							if(data.dbqueries) {
								console.log(JSON.stringify(data.dbqueries));
							}
						}

						if(data.error_code==255) {
							setTimeout(function(){
								window.location = settings.site+'/login/';
							},2000);
						}
					}

					if(data.return_code) {
						if(data.return_code=='SUCCESS') {
	
							if(data.rowid) {
								layout_resize_%formval%();
								<?php echo $templatemainid.$submod; ?>grid_%formval%(data.rowid);
							} else {
								doSelect_%formval%("<?php echo $submod; ?>");
							}

							showMessage(data.return_message,5000);
						}
					}

				}
			});

			return false;
		};

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>cancel').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("retail");
			doSelect_%formval%("<?php echo $submod; ?>");
		};

		myTab.toolbar.getToolbarData('<?php echo $moduleid; ?>refresh').onClick = function(id,formval) {
			//showMessage("toolbar: "+id,5000);
			//doSelect_%formval%("retail");

			try {
				var rowid = myGrid_%formval%.getSelectedRowId();
				<?php echo $templatemainid.$submod; ?>grid_%formval%(rowid);
			} catch(e) {
				doSelect_%formval%("<?php echo $submod; ?>");
			}

		};

	}

	<?php echo $templatedetailid.$submod; ?>_%formval%();

</script>