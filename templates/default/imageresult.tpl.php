                        <div class="result-<?php echo $v['id']; ?>" style="margin:0 0 100px 0">

                            <div class="page-header">
                                <h3><?php echo !empty($v['imgkeyword1']) ? htmlentities($v['imgkeyword1']) : '&nbsp;'; ?></h3>
                            </div>

                            <div class="img-result img-thumbnail">
                                <a id="a-<?php echo $v['id']; ?>" href="<?php echo $v['imgurl']; ?>" target="_blank" title="View image <?php echo !empty($v['imgkeyword1']) ? htmlentities($v['imgkeyword1']) : '&nbsp;';  ?>"><img src="<?php echo $v['imgurl']; ?>" class="img-responsive" onerror="<?php echo !is_bot() ? 'imgerror(this,\''.$v['id'].'\');' : ''; ?>" alt="<?php echo !empty($v['imgkeyword1']) ? htmlentities($v['imgkeyword1']).' ' : '';  ?>Image and Photo" /></a>
                            </div>

<p style="margin:10px 0;">
<span>Share this page to friends</span>
<br style="clear:both;" />
<textarea id="codeshare" style="width:100%;clear:both;height:auto;"><?php 
	echo 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
?></textarea>
</p>
<p style="margin:10px 0;">
<span>Code for forums</span>
<br style="clear:both;" />
<textarea id="codeforums" style="width:100%;clear:both;height:auto;">
[URL=<?php echo 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>][IMG]<?php echo $v['imgurl']; ?>[/IMG][/URL]
</textarea>
</p>
<p style="margin:10px 0;">
<span>HTML code for embedding to website</span>
<br style="clear:both;" />
<textarea id="codewebsite" style="width:100%;clear:both;height:auto;"><?php
	$x = '<a href="'.'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'].'"><img src="'.$v['imgurl'].'" border=0></a>';
	echo htmlentities($x);
?></textarea>
</p>
                        </div> <!-- .result-<?php echo $v['id']; ?> -->
