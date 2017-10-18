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
* Date: November 13, 2015
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

echo "\n\n/*\n\n";
pre($vars);
echo "\n\n*/\n\n";

/*
			global $apptemplate;

			//$apptemplate->add_script($apptemplate->templates_urlpath().'dhtmlx/codebase/dhtmlx.2.js');

*/

?>

function SRTTabControl(o) {

	if(typeof(o) == "object" && typeof(o.tabbar) == "object") {

		this.width = typeof(o.width) == "number" ? o.width : 80;

		this.tabbar = o.tabbar;
		this.tabbar.setArrowsMode("auto");

		//if(typeof(o.init) == "function") {
		//	o.init(this.tabbar,this);
		//}
	}

	this.addTab = function(a,b,c,d,e,f,g) {
		this.tabbar.addTab(a,b,c,d,e,f);

		var r = this.tabbar.cells(a);
		r.parentobj = this;

		if(typeof(g) == "function") {
			g(r,this.tabbar);
		}

		return r;
	}

	this.attachEvent = function(a,b) {
		this.tabbar.attachEvent(a,b);
		return this.tabbar;
	}

	this.getAllTabs = function() {
		return this.tabbar.getAllTabs();
	}
	
}

function toolbar_func(obj,id) {
	alert("toolbar_func: "+id);
}

function toolbar_open(p1,p2) {
	//alert("Open! "+p1);
	srt.toolbar_func(p1,this);
}

function toolbar_close(p1,p2) {
	srt.toolbar_func(p1,this);
}

srt.toolbarLoad = function(turl,tb) {
	getData(turl,"",function(data){
		tb.tbdata = data;
		for(var i=0; i<data.length; i++) {
			//alert(data[i].id);
			tb.addButton(data[i].id, i, data[i].text, data[i].img);
		}
	});
};

srt.toolbar_func = function(tb,id) {
	for(var i=0; i<tb.tbdata.length; i++) {
		if(tb.tbdata[i].type=="button"&&tb.tbdata[i].id==id) {

			var ids = srt.tabcontrol.getAllTabs();

			if(!tb.tbdata[i].multiple) {
				var bypass = false;

				if(ids.length>0) {
					for (var q=0; q<ids.length; q++) {
					    if(ids[q]==id) {
							bypass = true;
							break;		    	
					    }
					}
				}

				if(bypass) {
					srt.tabcontrol.tabbar.cells(id).setActive();
					break;
				}
			} else {

				if(!Array.isArray(tb.tbdata[i].multiple_tabs)) {
					tb.tbdata[i].multiple_tabs = [];
					id = id+'_1';
					tb.tbdata[i].multiple_tabs.push(id);
					//alert(tb.tbdata[i].multiple_tabs+", "+tb.tbdata[i].multiple_tabs.length);
				} else {
					id = id + '_' + (tb.tbdata[i].multiple_tabs.length + 1);
					tb.tbdata[i].multiple_tabs.push(id);
					//alert(tb.tbdata[i].multiple_tabs+", "+tb.tbdata[i].multiple_tabs.length);
				}

			}

			var tabwidth = srt.tabcontrol.width;

			if(tb.tbdata[i].tab_width) tabwidth = tb.tbdata[i].tab_width;

			tb.tbdata[i].tab = srt.tabcontrol.addTab(id,tb.tbdata[i].text,tabwidth,null,true,true,function(o,p){

				var pparam = "routerid="+settings.router_id+"&action=form&formid="+tb.tbdata[i].id;

				if(tb.tbdata[i].multiple) {
					pparam = pparam+"&tabid="+id;
					tb.tbdata[i].tbid = id;
				}

				o.attachFORMFromPostURL("/"+settings.router_id+"/json/",pparam,tb.tbdata[i],function(data,t,formval){
					if(data.toolbar) {
						t.toolbar = o.attachToolbar({
							icons_path: settings.template_assets+"toolbar/",
						});	
						t.toolbar.tbdata = data.toolbar;
						t.toolbar.tabid = t.id;
						
						t.toolbar.tabbar = srt.tabcontrol.tabbar;

						if(t.tbid) {
							t.toolbar.tbid = t.tbid;
							t.toolbar.curtab = t.toolbar.tabbar.tabs(t.toolbar.tbid);
						} else {
							t.toolbar.curtab = t.toolbar.tabbar.tabs(t.toolbar.tabid);							
						}

						if(formval) t.toolbar.formval = formval;
						
						t.toolbar.tbRender(data.toolbar);	

						t.toolbar.attachEvent("onClick", function(id){
							alert('onClick: '+id);
							if(this.tbdata&&this.tbdata.length>0) {
								for(var i=0; i<this.tbdata.length;i++) {
									if(this.tbdata[i].id==id) {
										if(this.formval) {
											if(typeof this.tbdata[i].confirm == 'object') {
												var tt = this;
												var formval = this.formval;
												var ti = i;

												this.tbdata[i].confirm.callback = function(response){
													if(response){
														srt.tbOnClick.apply(tt,[tt,id,formval,tt.tbdata[ti]])
													}
												};

												dhtmlx.confirm(this.tbdata[i].confirm);
											} else {
												srt.tbOnClick.apply(this,[this,id,this.formval,this.tbdata[i]]);
											}
										} else {
											if(typeof(this.tbdata[i].func) != "undefined") {
												try {
													eval(this.tbdata[i].func);											
												} catch(e) {
													alert(e);
												}
											}
										}
										break;
									}
								}
							}
						});
					}
				});
			});

			break;

		} else
		if(tb.tbdata[i].type=="buttonSelect"&&tb.tbdata[i].opts&&tb.tbdata[i].opts.length>0) {
			for(var j=0;j<tb.tbdata[i].opts.length;j++) {
				if(tb.tbdata[i].opts[j].id==id) {

					var ids = srt.tabcontrol.getAllTabs();

					var bypass = false;

					if(ids.length>0) {
						for (var q=0; q<ids.length; q++) {
						    if(ids[q]==id) {
								bypass = true;
								break;		    	
						    }
						}
					}

					if(bypass) {
						srt.tabcontrol.tabbar.cells(id).setActive();
						break;
					}

					var tabwidth = srt.tabcontrol.width;

					if(tb.tbdata[i].opts[j].tab_width) tabwidth = tb.tbdata[i].opts[j].tab_width;

					tb.tbdata[i].opts[j].tab =srt.tabcontrol.addTab(id,tb.tbdata[i].opts[j].text,tabwidth,null,true,true,function(o,p){

						var pparam = "routerid="+settings.router_id+"&action=form&formid="+tb.tbdata[i].opts[j].id;

						if(tb.tbdata[i].opts[j].multiple) {
							pparam = pparam + "&tabid="+id;
							tb.tbdata[i].opts[j].tbid = id;
						}

						o.attachFORMFromPostURL("/"+settings.router_id+"/json/",pparam,tb.tbdata[i].opts[j],function(data,t){
							if(data.toolbar) {
								t.toolbar = o.attachToolbar({
									icons_path: settings.template_assets+"toolbar/",
								});	
								t.toolbar.tbdata = data.toolbar;
								t.toolbar.tabid = t.id;

								if(t.tbid) t.toolbar.tbid = t.tbid;

								t.toolbar.tabbar = srt.tabcontrol.tabbar;
								t.toolbar.tbRender(data.toolbar);
								t.toolbar.attachEvent("onClick", function(id){
									if(this.tbdata) {
										alert(id);
									}
								});
							}
						});
					});

					break;
				}
			}
		}
	}
};

srt.dummy = function() {

	//showMessage(arguments+', length: '+arguments.length+', callee: '+arguments.callee+', caller: '+arguments.caller);

	showMessage(arguments+', length: '+arguments.length);

	for(var i=0;i<arguments.length;i++) {
		showMessage('arguments['+i+'] => '+arguments[i]);
	}
}

srt.doConfirm = function() {
	var args = arguments;
	var tt = this;
	showConfirmWarning('Do you want to do this?','Confirm',function(result){
		//showAlert('Result: '+result);
		srt.dummy.apply(tt, args);
	});
}

srt.tbOnClickError = function(data, statusText, xhr, $form, obj, id, formval, tbdata) {
	showConfirmWarning(data.error_message,'Confirm',function(result){
		showAlert('Result: '+result);
	});
}

srt.tbOnClickSuccess = function(data, statusText, xhr, $form, obj, id, formval, tbdata) {
	var $ = jQuery;

	$("#formdiv_"+formval).parent().html(data.html);

	if(data.toolbar) {

		obj.tbdata = data.toolbar;

		if(data.formval) obj.formval = data.formval;

		obj.tbRender(data.toolbar);
	}
}

srt.tbOnClick = function(obj,id,formval,tbdata) {
	var $ = jQuery;

	if(tbdata.func_beforeTbOnClick) {
		eval(tbdata.func_beforeTbOnClick);
	}

	$("#formval_"+formval+" input[name='formid']").val(obj.tabid);
	$("#formval_"+formval+" input[name='buttonid']").val(id);

	$("#formval_"+formval).ajaxSubmit({
		url: "/"+settings.router_id+"/json/",
		dataType: 'json',
		semantic: true,
		formval: formval,
		obj: obj,
		id: id,
		tbdata: tbdata,
		success: function(data, statusText, xhr, $form, obj, id, formval, tbdata){
			var $ = jQuery;

			if(data.error_code) {
				if(tbdata.func_afterTbOnClick) {
					eval(tbdata.func_afterTbOnClick);
				} else {
					srt.tbOnClickError.apply(this,arguments);
				}
			} else 
			if(data.html) {
				if(tbdata.func_afterTbOnClick) {
					eval(tbdata.func_afterTbOnClick);
				} else {
					srt.tbOnClickSuccess.apply(this,arguments);
				}
			}
		}
	});
};

srt.tabbar_close_func = function(obj,id) {
	alert("tabbar_close_func! "+obj+", "+id);

	var tbdata = srt.toolbar.tbdata;

	srt.tabbar_close(obj,id);

	/*for(var i=0; i<tbdata.length; i++) {
		//if(tbdata[i].tab&&tbdata[i].tab._idd&&tbdata[i].tab._idd==id) {
		if(tbdata[i].type=="button"&&tbdata[i].id==id) {
			delete tbdata[i].tab;
			delete tbdata[i].toolbar;
		} else
		if(tbdata[i].type=="buttonSelect"&&tbdata[i].opts&&tbdata[i].opts.length>0) {
			for(var j=0;j<tbdata[i].opts.length;j++) {
				if(tbdata[i].opts[j].id==id) {
					delete tbdata[i].tab;
					delete tbdata[i].toolbar;
				}
			}
		}
	}*/
};

srt.tabbar_close = function(obj,id) {
	var ids = obj.getAllTabs();

	if(ids.length>1) {
		for (var q=0; q<ids.length; q++) {
		    if(ids[q]==id) {
				obj.cells(id).close(ids[q-1]);		    	
		    }
		}
	}
};

srt.app_mod_init = function(){
	srt.layout = new dhtmlXLayoutObject({
	    parent:     document.body,    // id/object, parent container where layout will be located
	    pattern:    "3W",           // string, layout's pattern
	    skin:       "dhx_skyblue",  // string, optional,"dhx_skyblue","dhx_web","dhx_terrace"
	 
	    offsets: {          // optional, offsets for fullscreen init
	        top:    0,     // you can specify all four sides
	        right:  0,     // or only the side where you want to have an offset
	        bottom: 0,
	        left:   0
	    },
    });

	srt.layout.cells("a").setText("Main Menu");
	srt.layout.cells("a").setWidth(150);
	srt.layout.cells("b").setText("Details");
	srt.layout.cells("c").setWidth(150);
	srt.layout.cells("c").setText("Info");

	srt.layout.setAutoSize("b","a;b;c");

	// toolbar

	srt.toolbar = srt.layout.attachToolbar();
	srt.toolbar.setIconsPath(settings.template_assets+"toolbar/");

	//srt.toolbar.tbLoad("/json/toolbar/app/main/");	

	srt.toolbar.tbLoadPost("/"+settings.router_id+"/json/","routerid="+settings.router_id+"&action=toolbar&toolbarid=main");	

	srt.toolbar.attachEvent("onClick", function(id){
		//alert("onClick: "+id);
		if(this.tbdata) {
			for(var i=0;i<this.tbdata.length;i++) {
				if(this.tbdata[i].type=='button') {
					if(this.tbdata[i].id==id&&this.tbdata[i].func) {
						eval(this.tbdata[i].func);
					} else
					if(this.tbdata[i].id==id&&!this.tbdata[i].func) {
						alert("onClick: No event associated with \""+id+"\" toolbar")
					}
				} else
				if(this.tbdata[i].type=='buttonSelect'&&this.tbdata[i].opts&&this.tbdata[i].opts.length>0) {
					for(var j=0;j<this.tbdata[i].opts.length;j++) {
						//alert(this.tbdata[i].opts[j].id);
						if(this.tbdata[i].opts[j].id==id&&this.tbdata[i].opts[j].func) {
							//alert("buttonSelect: "+this.tbdata[i].opts[j].id+", "+this.tbdata[i].opts[j].func);
							eval(this.tbdata[i].opts[j].func);
						} else
						if(this.tbdata[i].opts[j].id==id&&!this.tbdata[i].opts[j].func) {
							alert("onClick: No event associated with \""+id+"\" toolbar")
						}
					}
				}
			}
		}
	});

	// status bar

	srt.status = srt.layout.attachStatusBar({text:"Loading..."});

	// tab bar

	srt.tabcontrol = new SRTTabControl({
		tabbar: srt.layout.cells("b").attachTabbar(),
		width: 80,
	});

	srt.patients = srt.tabcontrol.addTab("patients","Patients",srt.tabcontrol.width,null,true,null,function(o,p){
		//o.attachHTMLString('<h1>Welcome</h1>');
		//o.attachObject("appForm");

		var pparam = "routerid="+settings.router_id+"&action=form&formid=patients";

		o.attachFORMFromPostURL("/"+settings.router_id+"/json/",pparam,"patients",function(data){
			if(data.toolbar) {
				srt.schedules.toolbar = o.attachToolbar({
					icons_path: settings.template_assets+"toolbar/",
				});	
				srt.schedules.toolbar.tbdata = data.toolbar;
				srt.schedules.toolbar.tbRender(data.toolbar);	
				srt.schedules.toolbar.attachEvent("onClick", function(id){
					//if(this.tbdata) {
						alert(id);
					//}
				});
			}
		});
	});

	srt.schedules = srt.tabcontrol.addTab("schedules","Schedules",srt.tabcontrol.width,null,true,null,function(o,p){
		//o.attachHTMLString('<h1>Welcome</h1>');
		//o.attachObject("appForm");

		var pparam = "routerid="+settings.router_id+"&action=form&formid=schedules";

		o.attachFORMFromPostURL("/"+settings.router_id+"/json/",pparam,"schedules",function(data){
			if(data.toolbar) {
				srt.schedules.toolbar = o.attachToolbar({
					icons_path: settings.template_assets+"toolbar/",
				});	
				srt.schedules.toolbar.tbdata = data.toolbar;
				srt.schedules.toolbar.tbRender(data.toolbar);	
				srt.schedules.toolbar.attachEvent("onClick", function(id){
					//if(this.tbdata) {
						alert(id);
					//}
				});
			}
		});

		p.attachEvent("onTabClose", function(id){
			alert("onTabClose: "+id);
			if(srt.toolbar.tbdata) {
				for(var i=0;i<srt.toolbar.tbdata.length;i++) {
					var regex = new RegExp( srt.toolbar.tbdata[i].id, 'g' );
					if(srt.toolbar.tbdata[i].type=='button') {
						if(srt.toolbar.tbdata[i].id==id&&srt.toolbar.tbdata[i].func_tab_close) {
							eval(srt.toolbar.tbdata[i].func_tab_close);
						} else
						if(srt.toolbar.tbdata[i].id==id&&!srt.toolbar.tbdata[i].func_tab_close) {
							alert("onTabClose: No event associated with \""+id+"\" tabbar");
						} else
						if(id.match(regex)&&srt.toolbar.tbdata[i].func_tab_close) {
							eval(srt.toolbar.tbdata[i].func_tab_close);
						} else
						if(id.match(regex)&&!srt.toolbar.tbdata[i].func_tab_close) {
							alert("onTabClose: No event associated with \""+id+"\" tabbar");
						}
					} else
					if(srt.toolbar.tbdata[i].type=='buttonSelect'&&srt.toolbar.tbdata[i].opts&&srt.toolbar.tbdata[i].opts.length>0) {
						//alert("buttonSelect(onTabClose): "+id);
						for(var j=0;j<srt.toolbar.tbdata[i].opts.length;j++) {
							if(srt.toolbar.tbdata[i].opts[j].id==id&&srt.toolbar.tbdata[i].opts[j].func_tab_close) {
								eval(srt.toolbar.tbdata[i].opts[j].func_tab_close);
							} else
							if(srt.toolbar.tbdata[i].opts[j].id==id&&!srt.toolbar.tbdata[i].opts[j].func_tab_close) {
								alert("onTabClose: No event associated with \""+id+"\" tabbar")
							}
						}
					}
				}
			}
		});
	});

};

jQuery(document).ready(function($) {
	srt.app_mod_init();
});

