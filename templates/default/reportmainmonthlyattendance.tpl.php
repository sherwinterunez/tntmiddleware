<?php


//pre($vars);

?>
<style>
body, html {
  overflow: unset;
}
body, html, #printform {
  height: auto;
  width: auto;
}
@media all {
	.page-break	{ display: none; }
}

@media print {
	/*.page-break	{ display: block; page-break-before: always; }*/
  .page-break	{ display: block; page-break-before: auto; }
}
#printform .schoolName_%formval% {
  font-size: 25px;
}
#printform .period_%formval% {
  font-size: 14px;
}
#printform .monthlyattendancereport_%formval% {
  font-size: 18px;
}
#printform .dailyabsentreport_%formval% {
  font-size: 18px;
}
#printform .dailytardyreport_%formval% {
  font-size: 18px;
}
#printform .yearlevel_%formval% {
  font-size: 16px;
  font-weight: bold;
}
#printform .section_%formval% {
  font-size: 14px;
  font-weight: normal;
}
#printform .studentName_%formval% {
  font-size: 12px;
  font-weight: normal;
}
#printform .totalabsent_%formval% {
  font-size: 16px;
  font-weight: normal;
}
#printform .totalabsentitem_%formval% {
  font-size: 12px;
  font-weight: normal;
}
#printform .totalabsenttotal_%formval% {
  font-size: 14px;
  font-weight: bold;
}
#printform .totaltardy_%formval% {
  font-size: 16px;
  font-weight: normal;
}
#printform .totaltardyitem_%formval% {
  font-size: 12px;
  font-weight: normal;
}
#printform .totaltardytotal_%formval% {
  font-size: 14px;
  font-weight: bold;
}
#printform .individualschoolname_%formval% {
  font-size: 25px;
  text-align: center;
}
#printform .individualperiod_%formval% {
  font-size: 14px;
  text-align: center;
}
#printform .individualattendancereport_%formval% {
  font-size: 18px;
  text-align: center;
}
#printform .individualyearlevel_%formval% {
  font-size: 16px;
  text-align: center;
}
#printform .individualstudentname_%formval% {
  font-size: 25px;
  font-weight: bold;
  text-align: center;
}
#printform .blockdtrblock_%formval% {
  display: block;
  /*border: 1px solid #f00;*/
  text-align: center;
  width: 1000px;
}
#printform .blockdtrblock_%formval% .dhxform_block {
  display: block;
  /*border: 1px solid #0f0;*/
  float: none;
  margin: 0 auto;
}
#printform .studentName_%formval% div.dhxform_txt_label2 {
  overflow: hidden;
  display: block;
  height: 20px;
}
#printform .block_%formval% {
  /*display: block;
  border: 1px solid #00f;*/
}
#printform .absent_%formval% {
  display: block;
  width: 32px;
  height: 1px;
  border-bottom: 20px solid #f00;
  margin-top: -3px;
}
#printform .present_%formval% {
  display: block;
  width: 32px;
  height: 1px;
  border-bottom: 20px solid #ffff0b;
  margin-top: -3px;
}
#printform .block_%formval% div.dhxform_txt_label2 {
  margin: 0;
  padding: 0;
  /*text-align: center;*/
}
#printform .block_%formval% .ddmm_%formval% div.dhxform_txt_label2 {
  margin: 0;
  text-align: center;
}
</style>
<div id="printform"></div>
<script type="text/javascript">
  var myForm = <?php echo $vars['json']; ?>

  var formData = [
    {type: "settings", position: "label-left", labelWidth: 130, inputWidth: 200},
    {type: "block", name: "tbReports", hidden:false, width: 500, blockOffset: 0, offsetTop:0, list:myForm.tbReports},
    {type: "label", label: ""}
  ];

  var pForm = new dhtmlXForm("printform",formData);

  //document.body.style.overflow = unset;
  //document.html.style.overflow = unset;

  window.print();

</script>
