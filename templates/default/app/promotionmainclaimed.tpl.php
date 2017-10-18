<?php
$moduleid = 'promotion';
$submod = 'claimed';
$templatemainid = $moduleid.'main';
$templatedetailid = $moduleid.'detail';
$mainheight = 250;
?>
<!--
<?php print_r(array('$vars'=>$vars)); ?>
-->
<div id="<?php echo $templatemainid; ?>">
	<div id="<?php echo $templatemainid.$submod; ?>grid" style="display:block;border:none;"></div>
	<br style="clear:both;" />
</div>
<script>

	var myTab = srt.getTabUsingFormVal('%formval%');

	myTab.layout.cells('c').expand();

	myTab.layout.cells('b').setHeight(<?php echo $mainheight; ?>);

	//myTab.layout.cells('d').collapse();

	//myTab.layout.cells('d').hideArrow();

	//myTab.layout.cells('d').setText('');

	jQuery("#formdiv_%formval% #<?php echo $templatemainid; ?>").parent().css({'overflow':'hidden'});

	jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html('<div id="<?php echo $templatedetailid; ?>"></div>');

	function <?php echo $templatemainid.$submod; ?>grid_%formval%(f) {

		var myTab = srt.getTabUsingFormVal('%formval%');

		myChanged_%formval% = false;

		myFormStatus_%formval% = '';

		myTab.toolbar.hideAll();

		myTab.toolbar.disableAll();

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

			myGrid.setHeader("#master_checkbox,ID, Promo Code, Contact, Title, Description, Date Claimed");

			myGrid.setInitWidths("50,70,120,120,*,*,120");

			myGrid.setColAlign("center,center,left,left,left,left,left");

			myGrid.setColTypes("ch,ro,ro,ro,ro,ro,ro");

			myGrid.setColSorting("int,int,str,str,str,str,str");

			myGrid.init();

			myGrid.setSizes();

			try {

				if(ddata.rows[0].id) {

					myGrid.attachHeader("&nbsp;,&nbsp;,#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;");

					myGrid.attachEvent("onBeforeSelect", function(new_row,old_row,new_col_index){

						var method = myFormStatus_%formval%;

						if(method=='messagingedit'||method=='messagingnew') {
							return false;
						}

						return true;
					});

					myGrid.attachEvent("onRowSelect",function(rowId,cellIndex){

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

					});

					myGrid.parse(ddata,function(){

						if(typeof(f)!='undefined'&&rowid!=null) {
							myGrid.selectRowById(rowid,false,true,true);
						} else
						if(typeof(f)=='undefined'&&ddata.rows.length>0) {
							myGrid.selectRowById(ddata.rows[0].id,false,true,true);
						}

					},'json');
				}

			} catch(e) { 

				//alert(typeof(rowId));

				console.log('e => '+e); 

				jQuery("#formdiv_%formval% #<?php echo $templatemainid.$submod; ?>grid div.objbox").html('<span style="display:block;width:150px;margin:0 auto;"><center>Data not yet available!</center></span>');

				myTab.postData('/'+settings.router_id+'/json/', {
					odata: {},
					pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatedetailid.$submod; ?>&module=<?php echo $moduleid; ?>&method=nodata&formval=%formval%",
				}, function(ddata,odata){
					if(ddata.html) {
						jQuery("#formdiv_%formval% #<?php echo $templatedetailid; ?>").parent().html(ddata.html);
						layout_resize_%formval%();						
					}
				});

			}

		});

		try {

			clearInterval(mySetInterval_%formval%);

		} catch(e) {}

	}

	<?php echo $templatemainid.$submod; ?>grid_%formval%();

</script>