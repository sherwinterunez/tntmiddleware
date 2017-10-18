/* scripts */

function imgload(o) {
	//alert(o);
}

function imgerror(o,i) {
	$(o).unbind('error');
	$(o).unbind('load');

	var isrc = $(o).attr('src');
	var u = isrc;

	u = u.replace('http://','');
	u = u.replace('https://','');

	isrc = 'http://i.imagesfu.com/images/' + u;
	$(o).attr('src',isrc);

	$('#a-'+i).attr('href',isrc);
}