<?php
$moduleid = 'load';
$submod = 'retail';
$templatemainid = $moduleid.'main';
$templatedetailid = $moduleid.'detail';
$mainheight = 250;

$myToolbar = array($moduleid.'refresh',$moduleid.'sep1',$moduleid.'from',$moduleid.'datefrom',$moduleid.'to',$moduleid.'dateto',$moduleid.'filter');

?>
<!--
<?php print_r(array('$vars'=>$vars)); ?>
-->
<style>
	#formdiv_%formval% #<?php echo $templatemainid; ?> #<?php echo $templatemainid.$submod; ?>gridpaging {
		border-top: 1px solid #ccc;
		background: #f5f5f5;
		height: auto;
	}
	#formdiv_%formval% #<?php echo $templatemainid; ?> #<?php echo $templatemainid.$submod; ?>gridpaging #<?php echo $templatemainid.$submod; ?>gridrecinfoArea {
		float: left;
		padding-left: 5px;
	}
	#formdiv_%formval% #<?php echo $templatemainid; ?> #<?php echo $templatemainid.$submod; ?>gridpaging #<?php echo $templatemainid.$submod; ?>gridpagingArea {
		float: right;
		padding-right: 5px;
	}
</style>
<div id="<?php echo $templatemainid; ?>">
	<div id="<?php echo $templatemainid.$submod; ?>grid" style="display:block;border:none;"></div>
	<div id="<?php echo $templatemainid.$submod; ?>gridpaging"><span id="<?php echo $templatemainid.$submod; ?>gridrecinfoArea"></span><span id="<?php echo $templatemainid.$submod; ?>gridpagingArea"></span><br style="clear:both;" /></div>
	<br style="clear:both;" />
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('c').collapse();

	myTab.layout.cells('c').hideArrow();

	myTab.layout.cells('c').setText('');

	myTab.layout.cells('b').hideArrow();

	//myTab.layout.cells('b').setHeight(<?php echo $mainheight; ?>);

	//myTab.layout.cells('d').collapse();

	//myTab.layout.cells('d').hideArrow();

	//myTab.layout.cells('d').setText('');

	jQuery("#formdiv_%formval% #<?php echo $templatemainid; ?>").parent().css({'overflow':'hidden'});

	jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html('<div id="<?php echo $templatedetailid; ?>"></div>');

	function <?php echo $templatemainid.$submod; ?>grid_%formval%(f) {

		var myToolbar = <?php echo json_encode($myToolbar); ?>;

		var myTab = srt.getTabUsingFormVal('%formval%');

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.hideAll();

		myTab.toolbar.disableAll();

		myTab.toolbar.enableOnly(myToolbar);

		myTab.toolbar.showOnly(myToolbar);

///////////////

		if(typeof(f)!='undefined'&&typeof(myGrid_%formval%)!='undefined') {
			try {
				var rowid = myGrid_%formval%.getSelectedRowId();

				if(typeof(f)=='boolean') {
				} else
				if(typeof(f)=='number'||typeof(f)=='string') {
					rowid = parseInt(f);

					if(isNaN(rowid)) {
						rowid = 1;
					}
				}
			} catch(e) {
				var rowid = 1;
			}
		}

		if(typeof(myForm_%formval%)!='null'&&typeof(myForm_%formval%)!='undefined'&&myForm_%formval%!=null) {
			try {
				myForm_%formval%.unload();
				myForm_%formval% = undefined;
			} catch(e) {
				console.log(e);
			}
		}

///////////////

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=grid&formid=<?php echo $templatemainid.$submod; ?>grid&module=<?php echo $moduleid; ?>&table=<?php echo $submod; ?>&formval=%formval%",
		}, function(ddata,odata){

			if(typeof(myGrid_%formval%)!='null'&&typeof(myGrid_%formval%)!='undefined'&&myGrid_%formval%!=null) {
				try {
					myGrid_%formval%.destructor();
					myGrid_%formval% = null;
				} catch(e) {
					console.log(e);
				}
			}

			var myGrid = myGrid_%formval% = new dhtmlXGridObject('<?php echo $templatemainid.$submod; ?>grid');

			myGrid.setImagePath("/codebase/imgs/")

			myGrid.setHeader("#master_checkbox, ID, Receipt Date, Receipt No, Provider, SIM, Customer Name, Recipient No., Item, Quantity, Discount, Amnt Due, Status");

			myGrid.setInitWidths("50,50,120,120,120,120,120,120,80,80,80,80,150");

			myGrid.setColAlign("center,center,left,left,left,center,left,center,center,right,right,right,left");

			myGrid.setColTypes("ch,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

			myGrid.setColSorting("int,int,str,str,str,str,str,str,str,str,str,str,str");

			myGrid.enablePaging(true,100,10,"<?php echo $templatemainid.$submod; ?>gridpagingArea",true,"<?php echo $templatemainid.$submod; ?>gridrecinfoArea");

			//myGrid.setPagingSkin("toolbar");

			myGrid.init();

			myGrid.setSizes();

			$('#formdiv_%formval% #<?php echo $templatemainid; ?> #<?php echo $templatemainid.$submod; ?>gridpaging #<?php echo $templatemainid.$submod; ?>gridrecinfoArea').html('');
			$('#formdiv_%formval% #<?php echo $templatemainid; ?> #<?php echo $templatemainid.$submod; ?>gridpaging #<?php echo $templatemainid.$submod; ?>gridpagingArea').html('');

			try {

				if(ddata.rows[0].id) {

					myGrid.attachHeader("&nbsp;,&nbsp;,#text_filter,#text_filter,#combo_filter,#combo_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#combo_filter,#combo_filter");

					myGrid.attachEvent("onBeforeSelect", function(new_row,old_row,new_col_index){

						var method = myFormStatus_%formval%;

						if(method=='<?php echo $moduleid; ?>edit'||method=='<?php echo $moduleid; ?>new') {
							return false;
						}

						return true;
					});

					/*myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){

						myTab.toolbar.disableAll();

						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.html) {
								jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);
								layout_resize_%formval%();
							}
						});

					});*/


					myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){
						layout_resize_%formval%();
					});

					myGrid.attachEvent("onRowDblClicked", function(rowId,cellIndex){

						var obj = {};
						obj.routerid = settings.router_id;
						obj.action = 'formonly';
						obj.formid = '<?php echo $templatedetailid.$submod; ?>';
						obj.module = '<?php echo $moduleid; ?>';
						obj.method = 'onrowselect';
						obj.rowid = rowId;
						obj.formval = '%formval%';

						obj.title = 'Load Retail / '+myGrid.cells(rowId,3).getValue()+' / '+myGrid.cells(rowId,5).getValue();

						openWindow(obj, function(winobj,obj){
							console.log(obj);

							myTab.postData('/'+settings.router_id+'/json/', {
								odata: {winobj:winobj,obj:obj},
								pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method=onrowselect&rowid="+rowId+"&formval=%formval%&wid="+obj.wid,
							}, function(ddata,odata){
								if(ddata.toolbar) {
									console.log(ddata.toolbar);
									odata.winobj.toolbar = odata.winobj.attachToolbar({
										icons_path: settings.template_assets+"toolbar/",
									});
									odata.winobj.toolbar.toolbardata = ddata.toolbar;
									odata.winobj.toolbar.tbRender(ddata.toolbar);
									odata.winobj.toolbar.attachEvent("onClick", function(id){
										showMessage("ToolbarOnClick: "+id,5000);

										var tdata = this.getToolbarData(id);

										if(!tdata) return false;

										if(typeof(tdata.onClick)=='function') {
											var ret = tdata.onClick.apply(this,[id,'%formval%',odata.obj.wid]);
											//showMessage('ret: '+ret,5000);

											return ret;
										}

										showMessage("Toolbar ID "+id+" not yet implemented!",10000);
										return false;
									});
								}
								if(ddata.html) {
									jQuery("#"+odata.obj.wid).html(ddata.html);
									//layout_resize_%formval%();
								}
							});
						});

						/*myTab.toolbar.disableAll();

						myTab.postData('/'+settings.router_id+'/json/', {
							odata: {},
							pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method=onrowselect&rowid="+rowId+"&formval=%formval%",
						}, function(ddata,odata){
							if(ddata.html) {
								jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);
								layout_resize_%formval%();
							}
						});*/

					});

					myGrid.parse(ddata,function(){

						if(typeof(f)!='undefined'&&rowid!=null) {
							myGrid.selectRowById(rowid,false,true,true);
						} else
						if(typeof(f)=='undefined'&&ddata.rows.length>0) {
							myGrid.selectRowById(ddata.rows[0].id,false,true,true);
						}

						<?php /*
						if(ddata.rows.length>0) {

							for(var i=0;i<ddata.rows.length;i++) {
								//var cell = myGrid_%formval%.cells(ddata.rows[i].id,0);

								var o = myGrid.cells(ddata.rows[i].id,0).getRowObj();

								if(ddata.rows[i].unread&&parseInt(ddata.rows[i].unread)===1) {
									o.style.fontWeight = 'bold';
									//o.style.color = '#f00';
								} else {
									o.style.fontWeight = 'normal';
								}
							}
						}
						*/ ?>

					},'json');
				}

			} catch(e) {

				//alert(typeof(rowId));

				console.log('e => '+e);

				jQuery("#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>grid div.objbox").html('<span style="display:block;width:150px;margin:0 auto;"><center>Data not yet available!</center></span>');

				<?php /*myTab.postData('/'+settings.router_id+'/json/', {
					odata: {},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method=nodata&formval=%formval%",
				}, function(ddata,odata){
					if(ddata.html) {
						jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);
						layout_resize_%formval%();
					}
				});*/ ?>

			}

		});

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

		try {

			clearInterval(mySetInterval_%formval%);

		} catch(e) {}

	}

	<?php echo $templatemainid.$submod; ?>grid_%formval%();

</script>
