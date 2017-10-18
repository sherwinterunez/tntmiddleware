<?php

$messages = '';

if(!empty($params['messages'])) {
	$messages = $params['messages'];
}

?>
<style>

#formdiv_%formval% #messagingmisccontacts {
	padding: 5px;
	overflow-y: scroll;
	/*border: 1px solid #f00;*/
}

#formdiv_%formval% #messagingmisccontacts blockquote {
	border-left: none;
}

#formdiv_%formval% #messagingmisccontacts blockquote p {
  /*overflow: hidden;*/
  word-wrap: break-word;
}

#formdiv_%formval% #messagingmisccontacts .example-right {
  position:relative;
  padding:15px 30px;
  margin:0;
  color:#fff;
  background:#5a8f00; /* default background for browsers without gradient support */
  /* css3 */
  background:-webkit-gradient(linear, 0 0, 0 100%, from(#b8db29), to(#5a8f00));
  background:-moz-linear-gradient(#b8db29, #5a8f00);
  background:-o-linear-gradient(#b8db29, #5a8f00);
  background:linear-gradient(#b8db29, #5a8f00);
  -webkit-border-radius:10px;
  -moz-border-radius:10px;
  border-radius:10px;
}

/* display of quote author (alternatively use a class on the element following the blockquote) */
#formdiv_%formval% #messagingmisccontacts .example-right + p {margin:15px 0 2em 85px; font-style:italic;}

/* creates the triangle */
#formdiv_%formval% #messagingmisccontacts .example-right:after {
  content:"";
  position:absolute;
  bottom:-50px;
  left:50px;
  border-width:0 20px 50px 0px;
  border-style:solid;
  border-color:transparent #5a8f00;
  /* reduce the damage in FF3.0 */
  display:block;
  width:0;
}

#formdiv_%formval% #messagingmisccontacts .example-obtuse {
  position:relative;
  padding:15px 30px;
  margin:0;
  color:#000;
  background:#f3961c; /* default background for browsers without gradient support */
  /* css3 */
  background:-webkit-gradient(linear, 0 0, 0 100%, from(#f9d835), to(#f3961c));
  background:-moz-linear-gradient(#f9d835, #f3961c);
  background:-o-linear-gradient(#f9d835, #f3961c);
  background:linear-gradient(#f9d835, #f3961c);
  /* Using longhand to avoid inconsistencies between Safari 4 and Chrome 4 */
  -webkit-border-top-left-radius:25px 50px;
  -webkit-border-top-right-radius:25px 50px;
  -webkit-border-bottom-right-radius:25px 50px;
  -webkit-border-bottom-left-radius:25px 50px;
  -moz-border-radius:25px / 50px;
  border-radius:25px / 50px;
}

/* display of quote author (alternatively use a class on the element following the blockquote) */
#formdiv_%formval% #messagingmisccontacts .example-obtuse + p {margin:10px 150px 2em 0; text-align:right; font-style:italic;}

/* creates the larger triangle */
#formdiv_%formval% #messagingmisccontacts .example-obtuse:before {
  content:"";
  position:absolute;
  bottom:-30px;
  right:80px;
  border-width:0 0 30px 50px;
  border-style:solid;
  border-color:transparent #f3961c;
  /* reduce the damage in FF3.0 */
  display:block;
  width:0;
}

/* creates the smaller triangle */
#formdiv_%formval% #messagingmisccontacts .example-obtuse:after {
  content:"";
  position:absolute;
  bottom:-30px;
  right:110px;
  border-width:0 0 30px 20px;
  border-style:solid;
  border-color:transparent #fff;
  /* reduce the damage in FF3.0 */
  display:block;
  width:0;
}

</style>
<div id="messagingmisc">
	<div id="messagingmisccontacts">
		<?php echo $messages; ?>
		<br id="scrollend" />
	</div>
	<?php //pre(array('$vars'=>$vars)); ?>
</div>
<script>

	$ = jQuery;

	$("#formdiv_%formval% #messagingmisc").parent().css({'overflow':'hidden'});

	$("#formdiv_%formval% #messagingmisccontacts").scrollTo("1000000px",1);

</script>