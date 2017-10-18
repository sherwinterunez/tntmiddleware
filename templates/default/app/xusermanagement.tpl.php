<script>

	var myForm_%formval%, formData_%formval%;

	srt.obj_%formval% = {};

	srt.obj_%formval%.init = function(data,t,formval) {

		//srt.dummy.apply(this,arguments);

		t.layout = t.toolbar.curtab.attachLayout("2U");

		t.layout.cells("a").setText("Control");
		t.layout.cells("a").setWidth(300);
		t.layout.cells("b").setText("Manage");

		t.layout.cells("b").attachHTMLString("<div>some content</div>");

		srt.dummy.apply(this,arguments);
	}

</script>