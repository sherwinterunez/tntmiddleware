/* scripts */

function imgerror(o,i) {
	$(o).unbind('error');
	$(o).unbind('load');
	$('div.result-'+i).hide();
}