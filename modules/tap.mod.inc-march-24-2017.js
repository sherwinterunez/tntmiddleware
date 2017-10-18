<?php
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* App Module
*
* Date: November 27, 2015
*
*/

if(!defined('APPLICATION_RUNNING')) {
	header("HTTP/1.0 404 Not Found");
	die('access denied');
}

if(defined('ANNOUNCE')) {
	echo "\n<!-- loaded: ".__FILE__." -->\n";
}

global $apptemplate;

//echo '/* ';
//echo "app.mod.inc.js";
//echo ' */';

//echo "\n\n/*\n\n";
//pre($vars);
//echo "\n\n*/\n\n";

?>
/*
*
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Javascript Utilities
*
* Created: November 27, 2015
*
*/

var loginForm = [
	{type: "settings", position: "label-left", labelWidth: 90, inputWidth: 220, offsetLeft: 10, offsetTop: 5},
	{type: "input", label: "rfid", value: "", offsetTop: 20, name:"rfid", required:true},
	{type: "input", label: "unixtime", value: "", offsetTop: 20, name:"unixtime", required:true},
	{type: "hidden", value: "350", name:"imagesize"},
];

srt.checkFocus = function() {
	setTimeout(function(){
		if(!jQuery("input[name='rfid']").is(":focus")) {
			jQuery("input[name='rfid']").focus();
			//console.log('set focus');
		}
		srt.checkFocus();
		//console.log('Checking focus...');
	},100);
};

//srt.checkTime = function() {
//	setTimeout(function(){
//		srt.myForm.setItemValue('unixtime',moment().format('X'));
//		srt.checkTime();
//	},1000);
//}

srt.etap = function() {

	srt.myForm = myForm = new dhtmlXForm("myForm", loginForm);

	myForm.enableLiveValidation(true);

	srt.checkFocus();
	//srt.checkTime();

	myForm.attachEvent("onEnter", function(){
		var rfid = myForm.getItemValue("rfid");
		var unixtime = myForm.getItemValue("unixtime");
		var imagesize = myForm.getItemValue("imagesize");
	    console.log("Enter key has been pressed!");
	    console.log("Value: "+rfid);
	    myForm.setItemValue("rfid","");

	    //console.log($(this.base));

		postData('/'+settings.router_id+'/tapped/','rfid='+rfid+'&unixtime='+unixtime+'&imagesize='+imagesize,function(data){
			//if(data.return_code) {
			//	if(data.return_code=='SUCCESS') {
			//		showMessage(data.return_message,5000);
			//	}
			//}

			//if(data) {

			//}

			//console.log(typeof(data.db));

			//console.log(data.in);

			if(typeof(data)!='object') {
				return false;
			}

			if(typeof data.db != 'undefined' ) {
				jQuery('#db').html(data.db);
			}

			if(typeof data.in != 'undefined' ) {
				jQuery('#in').html(data.in);
			}

			if(typeof data.out != 'undefined' ) {
				jQuery('#out').html(data.out);
			}

			if(typeof data.late != 'undefined' ) {
				jQuery('#late').html(data.late);
			}

			if(typeof data.type != 'undefined' ) {
				jQuery('#type').html(data.type);
			}

			if(typeof data.image != 'undefined' ) {
				//console.log(typeof(data.image));
				jQuery('#studentphoto').html('<img src="'+data.image+'" />');
			}

			if(typeof data.fullname != 'undefined' ) {
				//console.log(typeof(data.image));
				jQuery('#studentname').html(data.fullname);
			}

			var yearlevelsection = '';

			if(typeof data.yearlevel != 'undefined' ) {
				//console.log(typeof(data.image));
				//jQuery('#studentname').html(data.fullname);
				yearlevelsection += data.yearlevel + ' - ';
			}

			if(typeof data.section != 'undefined' ) {
				//console.log(typeof(data.image));
				//jQuery('#studentname').html(data.fullname);
				yearlevelsection += data.section;
			}

			jQuery('#studentyearsection').html(yearlevelsection);

			if(typeof data.remarks != 'undefined' ) {
				//console.log(typeof(data.image));
				jQuery('#studentremarks').html(data.remarks);
			}

			if(typeof data.previous == 'object' &&  data.previous.length>0 ) {

				var obj = [];
				var max = 0;
				var ctr = 0;

				jQuery(".studentprev").each(function(idx){
					obj[max] = this;
					max++;
				});

				for(var prop in data.previous) {
					if(typeof obj[ctr] == 'object') {
						jQuery(obj[ctr]).html(data.previous[prop].html);
						console.log(data.previous[prop]);
						ctr++;
					}
				}
			}

		});


	});

};

srt.doMarquee = function() {
	postData('/'+settings.router_id+'/getbulletin/','marquee='+moment().format('X'),function(data){

		//console.log(typeof(data));

		if(typeof(data)!='object') {
			return false;
		}

		//console.log(data);

		if(typeof data.bulletin != 'undefined') {
			jQuery('#marquee').html(data.bulletin);
			jQuery('#marquee').marquee({duration: 10000});
		}
	});
}

srt.doShowDateTime = function() {
	//var dt = moment().format('LLLL');

	postData('/'+settings.router_id+'/getdatetime/','test=1',function(data){
		console.log('doShowDateTime',data);

		jQuery('#currentdatetime').html(data.currentTimeString);
		srt.myForm.setItemValue('unixtime',data.currentTime);

	});
}

jQuery(document).ready(function($) {
	srt.etap();
	srt.doMarquee();
	srt.doShowDateTime();

	var width = jQuery(window).width();
	var height = jQuery(window).height();
	var contentleftWidth = jQuery("#contentleft").width();
	var contenttopHeight = 100;
	var contentbottomHeight = 60;
	var contentmiddleHeight = height - (contenttopHeight+contentbottomHeight);
	var studentphotobgHeight = parseInt(contentleftWidth * 0.75);
	var studentphotobgMarginTop = (contentmiddleHeight - studentphotobgHeight) / 2;
	var infoMarginTop = (studentphotobgMarginTop / 2) - 10;
	var studentcontentHeight = jQuery("#studentcontent").height(); // + 100;
	var contentpreviousHeight = jQuery("#contentprevious").height(); // + 20;
	var studentcontentMarginTop = (contentmiddleHeight - (studentcontentHeight+contentpreviousHeight)) / 3;
	var contentpreviousWidth = jQuery("#contentprevious").width();
	var studentprevWidth = 0;
	var studentprevCtr = 0;
	var studentprevMargin = 0;

	if(studentcontentMarginTop<0) {
		studentcontentMarginTop = 20;
	}

	//console.log('studentcontentMarginTop',studentcontentMarginTop);

	//console.log('contentleftWidth',contentleftWidth);

	jQuery(".studentprev").each(function(idx){
		studentprevCtr++;
		studentprevWidth = studentprevWidth + jQuery(this).width();
		//console.log('studentprevWidth',studentprevWidth);
		//console.log(studentprev,idx);
	});

	studentprevMargin = ((contentpreviousWidth - studentprevWidth - 10) / studentprevCtr) / 2;

	jQuery(".studentprev").each(function(idx){
		jQuery(this).css({marginLeft:studentprevMargin,marginRight:studentprevMargin});
	});

	//console.log('studentprev',jQuery("#studentprev").width());

	//console.log('studentprevWidth',studentprevWidth);

	/*jQuery("#info").html("width: "+width+", height: "+height);*/
	jQuery("#contentmiddle").css({height:contentmiddleHeight});
	jQuery("#studentphotobg").css({marginTop:studentphotobgMarginTop});
	jQuery("#info").css({marginTop:infoMarginTop});
	jQuery("#studentcontent").css({marginTop:studentcontentMarginTop});
	jQuery("#contentprevious").css({marginTop:studentcontentMarginTop});
	jQuery("#studentphotobg").css({width:studentphotobgHeight,height:studentphotobgHeight});
	jQuery("#studentphoto").css({width:studentphotobgHeight,height:studentphotobgHeight});
	jQuery("#studentphoto img").css({width:studentphotobgHeight,height:studentphotobgHeight});

	srt.myForm.setItemValue('imagesize',studentphotobgHeight);

	setInterval(function(){
		jQuery("#body").css({opacity:1});
	},1000);

	setInterval(function(){
		srt.doMarquee();
	},90000);

	setInterval(function(){
		srt.doShowDateTime();
	},60000);
});
