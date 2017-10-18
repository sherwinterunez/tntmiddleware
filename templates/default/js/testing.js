
var dba = {}; //namespace

function doOnFormInit() {
	// will called immediately after form init
	// myForm is an instance of new dhtmlXForm object in this sample
	//alert('doOnFormInit()');

	//dba.myform = myForm;
}

function formid_oninit() {
	alert('formid_oninit');
}

dba.start_text = "<br/><span class = 'info_message'>Select any entity in tree to view details</span>";
dba.i18n = {
	select_db: "You need to select DB first",
	select_connection: "Select any connection first",
	delete_connection: "Are you sure to delete connection:<br>"
};

dba.uid = function() {
	return (new Date()).valueOf();
}
dba.get_id_chain = function(tree,id) {
	var chain = [id];
	while (id = tree.getParentId(id))
		chain.push(id);
        
	return chain.reverse().join("|");
}
dba.pages = {};// store opened tab
dba.add_connection = function(server,user,pass) {
	top.setTimeout(function() {
		dhx4.ajax.get("./logic/php/connection.php?mode=add&server="+encodeURIComponent(server)+"&user="+encodeURIComponent(user)+"&pass="+encodeURIComponent(pass), function(xml){
			eval(xml.xmlDoc.responseText);
		});
		dba.popup_win.close();
	},1);
}
dba.delete_connection = function(server) {
	top.setTimeout(function() {
		dhx4.ajax.get("./logic/php/connection.php?mode=delete&server="+encodeURIComponent(server), function(xml){
			eval(xml.xmlDoc.responseText);
		});
	},1);
};
dba.create_tab = function(id,full_id,text,extra) {
	full_id = full_id||dba.get_id_chain(dba.tree,id);
	
	if (!dba.pages[full_id]) {
		var details = id.split("^");
		dba.tabbar.addTab(full_id,full_id,100);
		var win = dba.tabbar.cells(full_id);
		
		//using window instead of tab
		var toolbar = win.attachToolbar();
		toolbar.attachEvent("onClick",dba.tab_toolbar_click);
		toolbar.setIconsPath("./imgs/");
		toolbar.loadStruct("xml/toolbar_"+details[0]+".xml");
		
		dba.tabbar.cells(full_id).setActive();
		dba.tabbar.cells(full_id).setText(text||dba.tree.getItemText(id));
		switch(details[0]) {
		case "table":
			dba.set_data_table(win,full_id);
			break;
		case "query":
			dba.set_query_layout(win);
			break;
		}
		
		dba.pages[full_id] = win;
		win.extra = extra;
	}
	else dba.tabbar.cells(full_id).setActive();
	
};
dba.tab_toolbar_click = function(id) {
	switch(id) {
        case "close":
        	var id = dba.tabbar.getActiveTab();
        	delete dba.pages[id];
        	dba.tabbar.tabs(id).close(true);
        	break;
        case "run_query":
        	var win = dba.tabbar.cells(dba.tabbar.getActiveTab());
        	win.area.parentNode.removeChild(win.area);
        	win.grid.post("./logic/php/sql.php","id="+encodeURIComponent(win.extra.join("|"))+"&sql="+encodeURIComponent(win.area.value));
        	break;
        case "refresh_table":
        	var win = dba.tabbar.cells(dba.tabbar.getActiveTab());
        	win.grid.loadXML(win.grid._refresh);
        	break;
        	
        case "show_structure":
        	var win = dba.tabbar.cells(dba.tabbar.getActiveTab());
        	win.grid.load(win.grid._refresh+"&struct=true");
        	break;
        	
        default:
        	dhtmlx.alert({
			title:"Information!",
			type:"alert-error",
			text:"Not implemented"
        	});
        	break;
        }
};
dba.main_toolbar_click = function(id) {
	switch(id) {
		case "add_connection":
			var win = dba.layout.dhxWins.createWindow("creation",1,1,300,180);
			win.setText("Add connection");
			win.setModal(true);
			win.denyResize();
			win.center();
			win.attachURL("connection.html?etc = "+new Date().getTime());
			dba.popup_win = win;
			break;
		
		case "delete_connection":
			var data = dba.tree.getSelectedItemId();
			if (!data) {
				dhtmlx.alert({
					title: "Information!",
					type: "alert-error",
					text: dba.i18n.select_connection
				});
				return;
			}
			data = dba.get_id_chain(dba.tree,data).split("|")[0];
			dhtmlx.confirm({
				title: "Information!",
				type: "confirm-error",
				text: dba.i18n.delete_connection + dba.tree.getItemText(data),
				callback: function(r) {
					if (r == true) dba.delete_connection(data.split("^")[1]);
				}
			});
			break;
		
		case "sql_query":
			var data = dba.get_id_chain(dba.tree,dba.tree.getSelectedItemId()).split("|");
			if (data.length<2) {
				dhtmlx.alert({
					title: "Information!",
					type: "alert-error",
					text: dba.i18n.select_db
				});
				return;
			}
			dba.create_tab("query",dba.uid(),"SQL : "+dba.tree.getItemText(data[0])+" : "+data[1].split("^")[1],data);
			break;
        }
};
dba.set_query_layout = function(win) {
	var grid = win.grid = win.attachGrid();
	grid.enableSmartRendering(true);
	grid.setHeader("<textarea style = 'width: 100%; height: 80px; '>Type SQL query here</textarea>")
	grid.setInitWidths("*")
	grid.init();
	grid.attachEvent("onXLE",function() {
		this.hdr.rows[1].cells[0].firstChild.appendChild(win.area);
		this.hdr.rows[1].cells[0].className = "grid_hdr_editable";
		this.setSizes();
		win.area.focus();
	});
	
	var area = grid.hdr.rows[1].cells[0];
	area.className = "grid_hdr_editable";
	area.onselectstart = function(e) { return ((e||event).cancelBubble = true); }
	
	area = area.firstChild.firstChild;
	area.focus();
	area.select();
	dhtmlxEvent(area,"keypress",function(e) {
		e = e||event;
		code = e.charCode||e.keyCode;
		if (e.ctrlKey && code == 13) dba.tab_toolbar_click("run_query");
	});
	
	win.area = area;
}
dba.set_data_table = function(win,full_id) {
	var grid = win.grid = win.attachGrid();
	grid.enableSmartRendering(true);
	grid._refresh = "./logic/php/datagrid.php?id="+encodeURIComponent(full_id);
	grid.loadXML(grid._refresh);
};

function init() {
	
	//dba.layout = new dhtmlXLayoutObject(document.body, "3W");

	var db = document;


	dba.layout = new dhtmlXLayoutObject({
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

	dba.layout.cells("a").setText("Main Menu");
	dba.layout.cells("a").setWidth(150);
	dba.layout.cells("b").setText("Details");
	dba.layout.cells("c").setWidth(150);
	dba.layout.cells("c").setText("Info");

	dba.layout.setAutoSize("b","a;b;c");
	
	/*dba.tree = dba.layout.cells("a").attachTree(0);
	dba.tree.setIconSize(18,18);
	dba.tree.setImagePath("/imgs/tree/");
	dba.tree.setXMLAutoLoadingBehaviour("function");
	//dba.tree.setXMLAutoLoading(function(id) {
	//	dba.tree.loadXML("./logic/php/tree.php?id="+id+"&full_id="+encodeURIComponent(dba.get_id_chain(dba.tree,unescape(id))));
	//});
	
	//dba.tree.loadXML("./logic/php/tree.php");
	//dba.tree.attachEvent("onClick",function(id) {
	//	if (id.split("^")[0] == "table") dba.create_tab(id);
	//	return true;
	//});*/

	/*dba.accordion = dba.layout.cells("a").attachAccordion({
		icons_path: "/imgs/icons/",
		items: [
			{ id: "a1", text: "Main Page", icon: "flag_red.png" },
			{ id: "a2", text: "Navigation", icon: "flag_green.png" },
			{ id: "a3", text: "Feedback", icon: "flag_blue.png" }
		]
	});*/

	dba.sidebar = dba.layout.cells("a").attachSidebar({
		width: dba.layout.cells("a").getWidth(),
		icons_path: "/common/win_16x16/",
		json: "/common/sidebar.json"
	});

	//dba.sidebar.setSizes();
	
	dba.toolbar = dba.layout.attachToolbar();
	dba.toolbar.setIconsPath("/imgs/");
	//dba.toolbar.attachEvent("onClick",dba.main_toolbar_click);
	dba.toolbar.loadStruct("/xml/buttons.xml", function(){
		dba.toolbar.addSpacer("sep1");		
	});

	dba.status = dba.layout.attachStatusBar({text:"Status bar text here"});
	
	dba.tabbar = dba.layout.cells("b").attachTabbar();
	dba.tabbar.setArrowsMode("auto");
	dba.tabbar.addTab("start","Welcome","100");
	dba.tabbar.cells("start").attachHTMLString('<h1>'+dba.start_text+'</h1>'); //dba.tabbar.setContentHTML("start",dba.start_text)
	dba.tabbar.cells("start").setActive(); //dba.tabbar.setTabActive("start");
	dba.tabbar.enableTabCloseButton(true);

	dba.tabbar.addTab("editor","Editor","100");
	dba.editor = dba.tabbar.cells("editor").attachEditor();
	dba.editor.setContent("Integration with dhtmlxEditor");

	dba.tabbar.addTab("myform","Form","100");
	dba.myform = dba.tabbar.cells("myform").attachObject("myForm");

	dba.tabbar.addTab("search","Search","100");

	dba.search = dba.tabbar.cells("search").attachToolbar({
		icons_path: "/common/imgs/",
		xml: "/common/dhxtoolbar_button.xml"
	});

	dba.stabbar = dba.tabbar.cells("search").attachTabbar();
	dba.stabbar.setArrowsMode("auto");
	dba.stabbar.addTab("newtab","New","100");
	dba.stabbar.cells("newtab").setActive();

	// enable scroll
	//if($("#myForm").parent().attr('class') == 'dhx_cell_cont_tabbar') {
	//	$("#myForm").parent().css('overflow','auto');
	//}


	dba.layout.attachEvent("onPanelResizeFinish", function(names){
		var w = dba.layout.cells("a").getWidth();
		dba.sidebar.setSideWidth(w);
		//alert('onPanelResizeFinish');
	    // your code here
	});
	
	dba.tabbar.attachEvent("onTabClose",function(id) {
		//alert("Closing "+id);
		//return false;
		delete dba.pages[id];
		return true;
	});

	myForm.attachEvent("onButtonClick",function(id) {
		//alert("Button "+id);

		dhtmlx.message({
				type: "error",
				text: "Button "+id+"!",
				expire: 1000
		})

		if(id=='save') {
			/*myForm.send("/login/ajax/","post",function(loader, response){
			  alert("Saved! loader: "+loader+", response: "+response);
			});*/

			//var fArr = formToArray($("#myForm")[0]);

			// 
			$("#myForm").attr('method','POST');

			$("#myForm").ajaxSubmit({
				url: '/login/api/',
				dataType: 'json',
				semantic: true,
				success: function(data, statusText, xhr, $form){
					if(data.error_code) {
						dhtmlx.alert({
							/*title: "Information!",*/
							type: "alert-error",
							text: data.error_message
						});
						//alert();
					} else {
						alert(data);						
					}
				}
			});


			//alert(fArr);
		}

		return false;
	});

	dba.pop = new dhtmlXPopup({ form: myForm, id: ["login","password"]});
	
	myForm.attachEvent("onFocus", function(id,value){
		if (typeof(value) != "undefined") id=[id,value]; // for radiobutton
		dba.pop.show(id);

		if(id=="login") {
			dba.pop.attachHTML("Please enter your login name.");
		} else
		if(id=="password") {
			dba.pop.attachHTML("Please enter your password.");
		}

		dhtmlx.message({
				text: "Form "+id+"!",
				expire: 1000
		})

	});

	myForm.attachEvent("onBlur", function(id,value){
		dba.pop.hide();
	});

}