<?php
$moduleid = 'referral';
$templatemainid = $moduleid.'main';
$templatedetailid = $moduleid.'detail';
?>
<!--
<?php 

global $appaccess;
global $applogin;

$access = $applogin->getAccess();

//$appaccess->showrules(); 

pre(array('$_SESSION'=>$_SESSION));

?>
-->
<script>
	var myChanged_%formval% = false;
	var myForm_%formval%;
	var myForm2_%formval%;
	var myFormStatus_%formval%;
	var formData_%formval%;
	var mySideBar_%formval%;
	var myGrid_%formval%;
	var myTab_%formval%;
	var myComposeEditor_%formval%;
	var mySetInterval_%formval%;
	var myCalendar1_%formval%;
	var myCalendar2_%formval%;

	function layout_resize_%formval%(f) {
		var $ = jQuery;
		var myTab = myTab_%formval% = srt.getTabUsingFormVal('%formval%');
		var mySideBar = mySideBar_%formval%;

		var lbHeight = myTab.layout.cells('b').getHeight();
		var lbWidth = myTab.layout.cells('b').getWidth();

		var lcHeight = myTab.layout.cells('c').getHeight();
		var lcWidth = myTab.layout.cells('c').getWidth();

		//var ldHeight = myTab.layout.cells('d').getHeight();
		//var ldWidth = myTab.layout.cells('d').getWidth();

		//showMessage("f => "+f,5000);

		mySideBar.setSideWidth(myTab.layout.cells('a').getWidth());

////////

		if($("#formdiv_%formval% #<?php echo $templatemainid; ?>promosgrid").length) {

			$("#formdiv_%formval% #<?php echo $templatemainid; ?>promosgrid").height(lbHeight-90);
			$("#formdiv_%formval% #<?php echo $templatemainid; ?>promosgrid").width(lbWidth);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #<?php echo $templatedetailid; ?>promos").length) {

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>promos").height(lcHeight-2);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>promos").width(lcWidth-2);

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>promosdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>promosdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #<?php echo $templatemainid; ?>referralgrid").length) {

			$("#formdiv_%formval% #<?php echo $templatemainid; ?>referralgrid").height(lbHeight-90);
			$("#formdiv_%formval% #<?php echo $templatemainid; ?>referralgrid").width(lbWidth);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #<?php echo $templatedetailid; ?>referral").length) {

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>referral").height(lcHeight-2);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>referral").width(lcWidth-2);

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>referraldetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>referraldetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #<?php echo $templatemainid; ?>templategrid").length) {

			$("#formdiv_%formval% #<?php echo $templatemainid; ?>templategrid").height(lbHeight-90);
			$("#formdiv_%formval% #<?php echo $templatemainid; ?>templategrid").width(lbWidth);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #<?php echo $templatedetailid; ?>template").length) {

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>template").height(lcHeight-2);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>template").width(lcWidth-2);

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>templatedetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>templatedetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #<?php echo $templatemainid; ?>claimedgrid").length) {

			$("#formdiv_%formval% #<?php echo $templatemainid; ?>claimedgrid").height(lbHeight-90);
			$("#formdiv_%formval% #<?php echo $templatemainid; ?>claimedgrid").width(lbWidth);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #<?php echo $templatedetailid; ?>claimed").length) {

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>claimed").height(lcHeight-2);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>claimed").width(lcWidth-2);

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>claimeddetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>claimeddetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #<?php echo $templatemainid; ?>fundtransfergrid").length) {

			$("#formdiv_%formval% #<?php echo $templatemainid; ?>fundtransfergrid").height(lbHeight-90);
			$("#formdiv_%formval% #<?php echo $templatemainid; ?>fundtransfergrid").width(lbWidth);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtransfer").length) {

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtransfer").height(lcHeight-2);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtransfer").width(lcWidth-2);

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtransferdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtransferdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #<?php echo $templatemainid; ?>fundtochildgrid").length) {

			$("#formdiv_%formval% #<?php echo $templatemainid; ?>fundtochildgrid").height(lbHeight-90);
			$("#formdiv_%formval% #<?php echo $templatemainid; ?>fundtochildgrid").width(lbWidth);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtochild").length) {

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtochild").height(lcHeight-2);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtochild").width(lcWidth-2);

			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtochilddetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #<?php echo $templatedetailid; ?>fundtochilddetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmaininboxgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmaininboxgrid",5000);

			//$("#formdiv_%formval% #messagingmaininboxgrid").height(lbHeight-60);

			$("#formdiv_%formval% #messagingmaininboxgrid").height(lbHeight-90);
			$("#formdiv_%formval% #messagingmaininboxgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmaininboxgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmaininboxgrid div.objbox").width(lbWidth);

			//messagingmaininboxgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsinbox").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			//$("#formdiv_%formval% #messagingdetailsinbox").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsinbox").width(lcWidth-12);

			$("#formdiv_%formval% #messagingdetailsinboxdetails").width(lcWidth-12);
			//$("#formdiv_%formval% #messagingdetailsinboxdetails").height(lcHeight-63);

			$("#formdiv_%formval% #messagingdetailsinboxeditor").height(lcHeight-64);
			$("#formdiv_%formval% #messagingdetailsinboxeditor").width(lcWidth-12);

		}

		if($("#formdiv_%formval% #messagingmiscinbox").length) {
			$("#formdiv_%formval% #messagingmiscinbox").height(ldHeight-40);
		}

		if($("#formdiv_%formval% #messagingmisccontacts").length) {
			$("#formdiv_%formval% #messagingmisccontacts").height(ldHeight-40);
		}

////////

		if($("#formdiv_%formval% #messagingmainoutboxgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainoutboxgrid",5000);

			$("#formdiv_%formval% #messagingmainoutboxgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainoutboxgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainoutboxgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainoutboxgrid div.objbox").width(lbWidth);

			//messagingmainoutboxgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsoutbox").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			//$("#formdiv_%formval% #messagingdetailsoutbox").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsoutbox").width(lcWidth-12);

			//$("#formdiv_%formval% #messagingdetailsoutboxsms").width(lcWidth-12);
			//$("#formdiv_%formval% #messagingdetailsoutboxsms").height(lcHeight-63);

			$("#formdiv_%formval% #messagingdetailsoutboxdetails").width(lcWidth-12);
			//$("#formdiv_%formval% #messagingdetailsinboxdetails").height(lcHeight-63);

			$("#formdiv_%formval% #messagingdetailsoutboxeditor").height(lcHeight-64);
			$("#formdiv_%formval% #messagingdetailsoutboxeditor").width(lcWidth-12);
		}

////////

		if($("#formdiv_%formval% #messagingmainsentgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainsentgrid",5000);

			//$("#formdiv_%formval% #messagingmainsentgrid").height(lbHeight-60);

			$("#formdiv_%formval% #messagingmainsentgrid").height(lbHeight-90);
			$("#formdiv_%formval% #messagingmainsentgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainsentgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainsentgrid div.objbox").width(lbWidth);

			//messagingmainsentgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailssent").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			//$("#formdiv_%formval% #messagingdetailssent").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailssent").width(lcWidth-12);

			//$("#formdiv_%formval% #messagingdetailssentsms").width(lcWidth-12);
			//$("#formdiv_%formval% #messagingdetailssentsms").height(lcHeight-63);

			$("#formdiv_%formval% #messagingdetailssentdetails").width(lcWidth-12);
			//$("#formdiv_%formval% #messagingdetailsinboxdetails").height(lcHeight-63);

			$("#formdiv_%formval% #messagingdetailssenteditor").height(lcHeight-64);
			$("#formdiv_%formval% #messagingdetailssenteditor").width(lcWidth-12);
		}

////////

		if($("#formdiv_%formval% #messagingmaingroupsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmaingroupsgrid",5000);

			$("#formdiv_%formval% #messagingmaingroupsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmaingroupsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmaingroupsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmaingroupsgrid div.objbox").width(lbWidth);

			//messagingmaingroupsgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsgroup").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailsgroup").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsgroup").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsgroupdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailsgroupdetailsform_%formval%").width(lcWidth-22);

			if(typeof(myForm2_%formval%)!='undefined') {
				try {
					myForm2_%formval%.setItemWidth("groupdetails", lcWidth-50);
				} catch(e) {}
			}
		}

////////

		if($("#formdiv_%formval% #messagingmaincontactsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmaincontactsgrid",5000);

			$("#formdiv_%formval% #messagingmaincontactsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmaincontactsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmaincontactsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmaincontactsgrid div.objbox").width(lbWidth);

			//messagingmaincontactsgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailscontact").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailscontact").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailscontact").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailscontactdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailscontactdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainnetworksgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainnetworksgrid",5000);

			$("#formdiv_%formval% #messagingmainnetworksgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainnetworksgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainnetworksgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainnetworksgrid div.objbox").width(lbWidth);

			//messagingmainnetworksgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsnetworks").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailsnetworks").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsnetworks").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsnetworksdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailsnetworksdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainoptionsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainoptionsgrid",5000);

			$("#formdiv_%formval% #messagingmainoptionsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainoptionsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainoptionsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainoptionsgrid div.objbox").width(lbWidth);

			//messagingmainoptionsgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsoptions").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailsoptions").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsoptions").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsoptionsdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailsoptionsdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainsmscommandsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainsmscommandsgrid",5000);

			$("#formdiv_%formval% #messagingmainsmscommandsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainsmscommandsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainsmscommandsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainsmscommandsgrid div.objbox").width(lbWidth);

			//messagingmainsmscommandsgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailssmscommands").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailssmscommands").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailssmscommands").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailssmscommandsdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailssmscommandsdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainmodemcommandsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainmodemcommandsgrid",5000);

			$("#formdiv_%formval% #messagingmainmodemcommandsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainmodemcommandsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainmodemcommandsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainmodemcommandsgrid div.objbox").width(lbWidth);

			//messagingmainmodemcommandsgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsmodemcommands").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailsmodemcommands").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsmodemcommands").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsmodemcommandsdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailsmodemcommandsdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainportsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainportsgrid",5000);

			$("#formdiv_%formval% #messagingmainportsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainportsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainportsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainportsgrid div.objbox").width(lbWidth);

			//messagingmainportsgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailsports").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailsports").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailsports").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsportsdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailsportsdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainsimgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainsimgrid",5000);

			$("#formdiv_%formval% #messagingmainsimgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainsimgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainportsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainportsgrid div.objbox").width(lbWidth);

			//messagingmainsimgrid_%formval%(f);

			if(typeof(myGrid_%formval%)!='undefined') {
				try {
					myGrid_%formval%.setSizes();
				} catch(e) {}
			}
		}

		if($("#formdiv_%formval% #messagingdetailssim").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingdetailssim").height(lcHeight-2);
			$("#formdiv_%formval% #messagingdetailssim").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailssimdetailsform_%formval%").height(lcHeight-51);
			$("#formdiv_%formval% #messagingdetailssimdetailsform_%formval%").width(lcWidth-22);
		}

////////

		if($("#formdiv_%formval% #messagingmainlogsgrid").length) {

			//showMessage("#formdiv_%formval% #messagingmainlogsgrid",5000);

			$("#formdiv_%formval% #messagingmainlogsgrid").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainlogsgrid").width(lbWidth);

			//$("#formdiv_%formval% #messagingmainlogsgrid div.objbox").height(lbHeight-92);
			//$("#formdiv_%formval% #messagingmainlogsgrid div.objbox").width(lbWidth);

			messagingmainlogsgrid_%formval%(f);
		}

		if($("#formdiv_%formval% #messagingmaincompose").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingmaincompose").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmaincompose").width(lbWidth);

			var composewidth = (lbWidth / 3) - 35;

			$("#formdiv_%formval% #messagingmaincompose div.cls_sherwin div.dhxform_block").height(lbHeight-120);
			$("#formdiv_%formval% #messagingmaincompose div.cls_sherwin div.dhxform_block").width(composewidth);

			//myForm_%formval%.setItemWidth("to_number", lbWidth-150);
			//myForm_%formval%.setItemWidth("to_groups", lbWidth-150);
			//myForm_%formval%.setItemWidth("subject", lbWidth-150);

			//myForm_%formval%.setItemHeight("to_number", lbHeight-60);

			$("#formdiv_%formval% #messagingdetailscompose").height(lcHeight-30);
			$("#formdiv_%formval% #messagingdetailscompose").width(lcWidth-12);

			$("#formdiv_%formval% #messagingdetailscomposeeditor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailscomposeeditor").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailscomposeeditor .dhx_cell_editor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailscomposeeditor .dhx_cell_editor").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailscomposeeditor .dhx_cell_editor .dhx_cell_cont_editor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailscomposeeditor .dhx_cell_editor .dhx_cell_cont_editor").width(lcWidth-2);

			//$("#formdiv_%formval% #messagingdetailscomposeeditor iframe .dhxeditor_mainiframe").height(lcHeight-80);
			$("#formdiv_%formval% #messagingdetailscomposeeditor .dhxeditor_mainiframe").width(lcWidth-7);
		}

		if($("#formdiv_%formval% #messagingmainreply").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingmainreply").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainreply").width(lbWidth);

			//myForm_%formval%.setItemWidth("to_number", lbWidth-150);
			//myForm_%formval%.setItemWidth("to_groups", lbWidth-150);
			//myForm_%formval%.setItemWidth("subject", lbWidth-150);

			//myForm_%formval%.setItemHeight("to_number", lbHeight-60);

			$("#formdiv_%formval% #messagingdetailsreply").height(lcHeight-30);
			$("#formdiv_%formval% #messagingdetailsreply").width(lcWidth-12);

			$("#formdiv_%formval% #messagingdetailsreplyeditor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailsreplyeditor").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsreplyeditor .dhx_cell_editor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailsreplyeditor .dhx_cell_editor").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsreplyeditor .dhx_cell_editor .dhx_cell_cont_editor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailsreplyeditor .dhx_cell_editor .dhx_cell_cont_editor").width(lcWidth-2);

			//$("#formdiv_%formval% #messagingdetailsreplyeditor iframe .dhxeditor_mainiframe").height(lcHeight-80);
			$("#formdiv_%formval% #messagingdetailsreplyeditor .dhxeditor_mainiframe").width(lcWidth-7);
		}

		if($("#formdiv_%formval% #messagingmainforward").length) {

			//showMessage("#formdiv_%formval% #messagingmaincompose",5000);

			$("#formdiv_%formval% #messagingmainforward").height(lbHeight-60);
			$("#formdiv_%formval% #messagingmainforward").width(lbWidth);

			//myForm_%formval%.setItemWidth("to_number", lbWidth-150);
			//myForm_%formval%.setItemWidth("to_groups", lbWidth-150);
			//myForm_%formval%.setItemWidth("subject", lbWidth-150);

			//myForm_%formval%.setItemHeight("to_number", lbHeight-60);

			$("#formdiv_%formval% #messagingdetailsforward").height(lcHeight-30);
			$("#formdiv_%formval% #messagingdetailsforward").width(lcWidth-12);

			$("#formdiv_%formval% #messagingdetailsforwardeditor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailsforwardeditor").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsforwardeditor .dhx_cell_editor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailsforwardeditor .dhx_cell_editor").width(lcWidth-2);

			$("#formdiv_%formval% #messagingdetailsforwardeditor .dhx_cell_editor .dhx_cell_cont_editor").height(lcHeight-54);
			$("#formdiv_%formval% #messagingdetailsforwardeditor .dhx_cell_editor .dhx_cell_cont_editor").width(lcWidth-2);

			//$("#formdiv_%formval% #messagingdetailsforwardeditor iframe .dhxeditor_mainiframe").height(lcHeight-80);
			$("#formdiv_%formval% #messagingdetailsforwardeditor .dhxeditor_mainiframe").width(lcWidth-7);
		}

	}

	function explorer_sidebar_%formval%(){

		var mySideBar;

		var myTab = myTab_%formval% = srt.getTabUsingFormVal('%formval%');

		mySideBar = mySideBar_%formval% = myTab.layout.cells('a').attachSidebar({
			icons_path: "/common/win_16x16/",
			width: myTab.layout.cells('a').getWidth(),
			items: [
					{id: "template", text: "Template", icon: "downloads.png"},
					//{id: "activetemplate", text: "Active Template", icon: "downloads.png"},
					{id: "referral", text: "Referrals", icon: "desktop.png"},
					{id: "claimed", text: "Claimed", icon: "music.png"},

					<?php /* ?>
					{id: "transaction", text: "Transactions", icon: "downloads.png"},
					{id: "product", text: "Products", icon: "recent.png"},
					{id: "fundreload", text: "Fund Reload", icon: "downloads.png"},
					{id: "fundtransfer", text: "Fund Transfer", icon: "documents.png"},
					{id: "fundtochild", text: "Fund to Child", icon: "music.png"},
					<?php //if(in_array('compose',$access)) { ?>
					{id: "compose", text: "Compose", icon: "recent.png"},
					<?php //} ?>
					<?php //if(in_array('contacts',$access)) { ?>
					{id: "contacts", text: "Contacts", icon: "desktop.png"},
					<?php //} ?>
					<?php //if(in_array('groups',$access)) { ?>
					{id: "groups", text: "Groups", icon: "downloads.png"},
					<?php //} ?>
					{type: "separator"},
					<?php //if(in_array('inbox',$access)) { ?>
					{id: "inbox", text: "Inbox", icon: "documents.png"},
					<?php //} ?>
					<?php //if(in_array('outbox',$access)) { ?>
					{id: "outbox", text: "Outbox", icon: "music.png"},
					<?php //} ?>
					<?php //if(in_array('sent',$access)) { ?>
					{id: "sent", text: "Sent", icon: "pictures.png"},
					<?php //} ?>
					{type: "separator"},
					<?php //if(in_array('networks',$access)) { ?>
					{id: "networks", text: "Networks", icon: "documents.png"},
					<?php //} ?>
					<?php //if(in_array('sim',$access)) { ?>
					{id: "sim", text: "SIM", icon: "documents.png"},
					<?php //} ?>
					//{id: "ports", text: "Ports", icon: "documents.png"},
					//{id: "device", text: "Devices", icon: "documents.png"},
					//{id: "logs", text: "Logs", icon: "documents.png"},
					{type: "separator"},
					<?php //if(in_array('options',$access)) { ?>
					{id: "options", text: "Options", icon: "documents.png"},
					<?php //} ?>
					<?php //if(in_array('smscommands',$access)) { ?>
					{id: "smscommands", text: "SMS Commands", icon: "documents.png"},
					<?php //} ?>
					<?php //if(in_array('modemcommands',$access)) { ?>
					{id: "modemcommands", text: "MODEM Commands", icon: "documents.png"},
					<?php //} */ ?>
				]		
		});

		myTab.layout.cells('c').hideHeader();

		//myTab.layout.cells('a').hideArrow();
		//myTab.layout.cells('b').hideArrow();

		myTab.layout.attachEvent("onCollapse", function(names){
			layout_resize_%formval%(true);
		});

		myTab.layout.attachEvent("onExpand", function(names){
			layout_resize_%formval%(true);
		});

		myTab.layout.attachEvent("onResizeFinish", function(names){
			layout_resize_%formval%(true);
		});

		myTab.layout.attachEvent("onPanelResizeFinish", function(names){
			layout_resize_%formval%(true);
		});

		mySideBar.attachEvent("onSelect", function(id, lastId){
			doSelect_%formval%(id, lastId);
		});

		<?php /* ?>
		var input_from = myTab.toolbar.getInput("<?php echo $moduleid; ?>datefrom");
		input_from.setAttribute("readOnly", "true");
		//input_from.onclick = function(){ setSens(input_till,"max"); }

		var input_till = myTab.toolbar.getInput("<?php echo $moduleid; ?>dateto");
		input_till.setAttribute("readOnly", "true");
		//input_till.onclick = function(){ setSens(input_from,"min"); }

		myCalendar1_%formval% = new dhtmlXCalendarObject([input_from]);
		myCalendar1_%formval%.setDateFormat("%m-%d-%Y %H:%i");

		myCalendar2_%formval% = new dhtmlXCalendarObject([input_till]);
		myCalendar2_%formval%.setDateFormat("%m-%d-%Y %H:%i");

		myTab.toolbar.setValue("<?php echo $moduleid; ?>datefrom","<?php $dt=getDbDate(1); echo $dt['date']; ?> 00:00");
		myTab.toolbar.setValue("<?php echo $moduleid; ?>dateto","<?php echo getDbDate(); ?>");

		var cdt = myCalendar1_%formval%.getDate();

		myCalendar2_%formval%.setSensitiveRange(cdt, null);

		myCalendar1_%formval%.attachEvent("onBeforeChange", function(date){

			var cdt = myCalendar2_%formval%.getDate();

			myCalendar2_%formval%.setSensitiveRange(date, null);

			var edt = myCalendar2_%formval%.getDate();

			if(date.getTime()>=edt.getTime()) {
				edt.setDate(date.getDate());
				//myForm.setItemValue('promos_enddate',edt);

				var mm, dd, yy, hh, mn, dt;

				if(edt.getMonth()<10) {
					mm = '0' + (edt.getMonth()+1);
				} else {
					mm = edt.getMonth() + 1;
				}

				dd = edt.getDate();
				yy = edt.getFullYear();

				hh = edt.getHours();
				mn = edt.getMinutes();

				dt = mm+'-'+dd+'-'+yy+' '+hh+':'+mn;

				myTab.toolbar.setValue("<?php echo $moduleid; ?>dateto",dt);
			}

			return true;
		});


		myCalendar2_%formval%.attachEvent("onBeforeChange", function(date){

			var date = myCalendar1_%formval%.getDate();

			myCalendar2_%formval%.setSensitiveRange(date, null);

			var edt = myCalendar2_%formval%.getDate();

			if(date.getTime()>=edt.getTime()) {
				edt.setDate(date.getDate());
				//myForm.setItemValue('promos_enddate',edt);

				var mm, dd, yy, hh, mn, dt;

				if(edt.getMonth()<10) {
					mm = '0' + (edt.getMonth()+1);
				} else {
					mm = edt.getMonth() + 1;
				}

				dd = edt.getDate();
				yy = edt.getFullYear();

				hh = edt.getHours();
				mn = edt.getMinutes();

				dt = mm+'-'+dd+'-'+yy+' '+hh+':'+mn;

				myTab.toolbar.setValue("<?php echo $moduleid; ?>dateto",dt);
			}

			return true;
		});

		<?php */ ?>

		myTab.onTabClose = function(id,formval) {
			//alert('onTabClose: '+id+', '+formval);

			if(typeof(myForm_%formval%)!='null'&&typeof(myForm_%formval%)!='undefined'&&myForm_%formval%!=null) {
				try {
					myForm_%formval%.unload();
					myForm_%formval% = undefined;
				} catch(e) {
					console.log(e);
				}
			}

			if(typeof(myForm2_%formval%)!='null'&&typeof(myForm2_%formval%)!='undefined'&&myForm2_%formval%!=null) {
				try {
					myForm2_%formval%.unload();
					myForm2_%formval% = undefined;
				} catch(e) {
					console.log(e);
				}
			}

			if(typeof(myGrid_%formval%)!='null'&&typeof(myGrid_%formval%)!='undefined'&&myGrid_%formval%!=null) {
				try {
					myGrid_%formval%.destructor();
					myGrid_%formval% = null;
				} catch(e) {
					console.log(e);
				}
			}

			try {
				clearInterval(mySetInterval_%formval%);
			} catch(e) {
				console.log(e);
			}
		}

		setTimeout(function(){
			doSelect_%formval%("referral");
		},100);

	};

	function doSelect_%formval%(id, lastId){
		var mySideBar = mySideBar_%formval%;
		var myTab = myTab_%formval%;
		var t = mySideBar.cells(id).getText();

		//showMessage('id => '+id+', lastId => '+lastId,5000);

		mySideBar.items(id).setActive();

		//showMessage(t.text,5000);

		myTab.layout.cells('b').setText(t.text);

		myTab.postData('/'+settings.router_id+'/json/', {
			odata: {},
			pdata: "routerid="+settings.router_id+"&action=formonly&formid=<?php echo $templatemainid; ?>"+id+"&module=<?php echo $moduleid; ?>&formval=%formval%",
		}, function(ddata,odata){
			if(ddata.html) {
				jQuery("#formdiv_%formval% #<?php echo $templatemainid; ?>").parent().html(ddata.html);	
				<?php /*
				myTab.toolbar.setValue("<?php echo $moduleid; ?>datefrom","<?php $dt=getDbDate(1); echo $dt['date']; ?> 00:00");
				myTab.toolbar.setValue("<?php echo $moduleid; ?>dateto","<?php echo getDbDate(); ?>");		
				*/ ?>	
			}
		});

	};

	explorer_sidebar_%formval%();

</script>
