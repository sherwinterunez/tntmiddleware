/*
* 
* Author: Sherwin R. Terunez
* Contact: sherwinterunez@yahoo.com
*
* Description:
*
* Javascript Utilities
*
* Created: November 12, 2015
*
*/

var srt = {}; // namespace

srt.fn = {};

dhtmlXToolbarObject.prototype.tbLoad = function(turl) {
	var tb = this;
	getData(turl,"",function(data){
		tb.tbdata = data;
		if(data.length>0) {
			tb.tbRender(data);
		}
	});
}

dhtmlXToolbarObject.prototype.tbLoadPost = function(turl,param) {
	var tb = this;
	postData(turl,param,function(data){
		tb.tbdata = data;
		if(data.length>0) {
			tb.tbRender(data);
		}
	});
}

dhtmlXToolbarObject.prototype.tbRender = function(data) {
	this.clearAll();
	for(var i=0; i<data.length; i++) {
		if(data[i].type=="button") {
			this.addButton(data[i].id, i, data[i].text, data[i].img);
		} else
		if(data[i].type=="buttonSelect") {
			this.addButtonSelect(data[i].id, i, data[i].text, data[i].opts, data[i].img, null, 0);				
		} else
		if(data[i].type=="separator") {
			this.addSeparator(data[i].id,i);
		} else
		if(data[i].type=="text") {
			this.addText(data[i].id, i, data[i].text);
		} else
		if(data[i].type=="spacer") {
			this.addSpacer(data[i].id);
		}
	}
}

dhtmlXCellObject.prototype.attachHTMLStringFromGetURL = function(turl) {
	var tb = this;

	getData(turl,"",function(data){
		if(data.html&&data.html.length>0) {

		    tb._attachObject(null, null, data.html);

		    tb._executeScript(data);

		}
	});
};

dhtmlXCellObject.prototype.attachFORMFromPostURL = function(turl,param,tbobj,cb) {
	var tb = this;

	postData(turl,param,function(data){

		if(typeof data.layout == 'object') {

		    if(typeof(cb) == 'function') {
		    	cb(data,tbobj,(typeof(data.formval)=="undefined"?null:data.formval));
		    }

			tbobj.layout = tbobj.toolbar.curtab.attachLayout(data.layout.pattern);

			if(data.layout.cells.length>0) {
				for(var i=0; i<data.layout.cells.length;i++) {
					if(typeof(data.layout.cells[i].id)!='undefined') {
						if(typeof(data.layout.cells[i].text)!='undefined') tbobj.layout.cells(data.layout.cells[i].id).setText(data.layout.cells[i].text);
						if(typeof(data.layout.cells[i].width)!='undefined') tbobj.layout.cells(data.layout.cells[i].id).setWidth(data.layout.cells[i].width);
						if(typeof(data.layout.cells[i].html)!='undefined') tbobj.layout.cells(data.layout.cells[i].id).attachHTMLString(data.layout.cells[i].html);
					}
				}
			}
		}

		if(data.html&&data.html.length>0) {

			var html = data.html;

		    tb._attachObject(null, null, html);

		    if(typeof(cb) == 'function') {
		    	cb(data,tbobj,(typeof(data.formval)=="undefined"?null:data.formval));
		    }

		    tb._executeScript(html);

		    /*if(data.formval) {
			    try {
			    	eval('srt.obj_'+data.formval+'.init.apply(tbobj,[data,tbobj,data.formval])');
			    } catch(e) {
			    	alert(e);
			    }		    	
		    }*/
		}
	});
};

dhtmlXCellObject.prototype._executeScript = function(data) {

    var z = data.match(/<script[^>]*>[^\f]*?<\/script>/g) || [];
    for (var i = 0; i < z.length; i++) {
        var s = z[i].replace(/<([\/]{0,1})script[^>]*>/gi, "");
        if (s) {
            if (window.execScript) {
            	//alert(s);
                window.execScript(s)
            } else {
            	//alert(s);
                window.eval(s)
            }
        }
    }

};

function _executeScript(data) {

    var z = data.match(/<script[^>]*>[^\f]*?<\/script>/g) || [];
    for (var i = 0; i < z.length; i++) {
        var s = z[i].replace(/<([\/]{0,1})script[^>]*>/gi, "");
        if (s) {
            if (window.execScript) {
            	//alert(s);
                window.execScript(s)
            } else {
            	//alert(s);
                window.eval(s)
            }
        }
    }

};

function getData(purl,pdata,psuccess) {
	//var $ = settings.$;
	
	$.ajax({
		type: "GET",
		url: purl,
		data: pdata,
		processData: true,
		success: function(data){
			if(data.error_code&&data.error_code==255) {
				alert('ERROR ('+data.error_code+'): '+data.error_message);
				//setTimeout(function(){
				//	window.location = settings.site+'/login/';
				//},2000);
			} else
			if(data.error_code) {
				alert('ERROR ('+data.error_code+'): '+data.error_message);
			} else {
				if(psuccess != null) {
					psuccess(data); 					
				}
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if(textStatus&&errorThrown) {
				alert('ERROR: '+textStatus+' / '+errorThrown);
			} else
			if(textStatus) {
				alert('ERROR: '+textStatus);
			} else
			if(errorThrown) {
				alert('ERROR: '+errorThrown);
			} else {
				alert('An unknown error has occured while processing/waiting/receiving ajax response');
			}
		}
	});
}

function postData(purl,pdata,psuccess) {
	//var $ = settings.$;
	
	$.ajax({
		type: "POST",
		url: purl,
		data: pdata,
		processData: true,
		success: function(data){
			if(data.error_code&&data.error_code==255) {
				alert('ERROR ('+data.error_code+'): '+data.error_message);
				setTimeout(function(){
					window.location = settings.site+'/login/';
				},2000);
			} else
			if(data.error_code) {
				alert('ERROR ('+data.error_code+'): '+data.error_message);
			} else {
				psuccess(data); 
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			if(textStatus&&errorThrown) {
				alert('ERROR: '+textStatus+' / '+errorThrown);
			} else
			if(textStatus) {
				alert('ERROR: '+textStatus);
			} else
			if(errorThrown) {
				alert('ERROR: '+errorThrown);
			} else {
				alert('An unknown error has occured while processing/waiting/receiving ajax response');
			}
		}
	});
}

function formToArray(form) {
	var a = [];
	var semantic = true;

	var els = semantic ? form.getElementsByTagName('*') : form.elements;
	if (!els) {
		return a;
	}
	
	var i,j,n,v,el,max,jmax;
	for(i=0, max=els.length; i < max; i++) {
		el = els[i];
		n = el.name;
		id = el.id;
		cls = el.className;

		if (!n) {
			continue;
		}

		v = $.fieldValue(el, true);
		if (v && v.constructor == Array) {
			for(j=0, jmax=v.length; j < jmax; j++) {
				//jlog('FA: '+n+' => '+v[j]);
				a.push({name: n, value: v[j], id:id, class:cls, el:el});
			}
		}
		else if (v !== null && typeof v != 'undefined') {
			//jlog('FB: '+n+' => '+v);
			a.push({name: n, value: v, id:id, class:cls, el:el});
		}
	}
	
	return a;
}

function showErrorMessage(tx,ex) {

	if(typeof(tx)=="undefined") tx = "An error has occured!";
	if(typeof(ex)=="undefined") ex = -1;

	dhtmlx.message({
			type: "error",
			text: tx,
			expire: ex
	})
}

function showMessage(tx,ex) {

	if(typeof(tx)=="undefined") tx = "An error has occured!";
	if(typeof(ex)=="undefined") ex = -1;

	dhtmlx.message({
			text: tx,
			expire: ex
	})
}

function _showAlert(type,text,title,ok) {
	dhtmlx.alert({
		type: type,
		text: text,
		title: title,
		ok: ok
	});	
}


//function _showConfirm(type-a,text-b,title-c,ok-d,cancel-e,cb-f) {
function _showConfirm(type,text,c,d,e,f) {

	var ok = (typeof(d)=='string') ? d : 'Yes';
	var cancel = (typeof(d)=='string') ? d : 'No';
	var title = (typeof(c)=='string') ? c : null;
	var cb = null;

	if(typeof(c)=='function') cb = c;
	else if(typeof(d)=='function') cb = d;
	else if(typeof(e)=='function') cb = e;
	else if(typeof(f)=='function') cb = f;

	dhtmlx.confirm({
		type: type,
		text: text,
		title: title,
		ok: ok,
		cancel: cancel,
		callback: cb,
	});	
}

function showAlert(text,title,ok) {
	_showAlert("alert",text,title,ok);
}

function showAlertError(text,title,ok) {
	_showAlert("alert-error",text,title,ok);
}

function showAlertWarning(text,title,ok) {
	_showAlert("alert-warning",text,title,ok);
}

function showConfirm(b,c,d,e,f) {
	_showConfirm('confirm',b,c,d,e,f);
}

function showConfirmWarning(b,c,d,e,f) {
	_showConfirm('confirm-warning',b,c,d,e,f);
}

function showConfirmWarning(b,c,d,e,f) {
	_showConfirm('confirm-error',b,c,d,e,f);
}