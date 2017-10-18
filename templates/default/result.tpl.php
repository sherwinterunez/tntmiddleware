<div class="container-fluid bg-noise3">
    <div class="container" id="index">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="row">
                    <div class="col-lg-12" id="cont-body">
                        <div class="search">
                            <form class="form" method="get" action="/">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control col-lg-8" name="q" placeholder="Search">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="cont-body">
                        <div class="page-header">
                            <h1><?php echo htmlentities(ucwords(strtolower($vars['vars']['q']))); ?></h1>
                        </div>

                        <?php foreach($vars['results']['images'] as $k=>$v) { ?>

                        <?php /*<div class="result-<?php echo $v['id']; ?>">

                            <div class="page-header">
                                <h3><?php echo !empty($v['imgkeyword1']) ? htmlentities($v['imgkeyword1']) : '&nbsp;'; ?></h3>
                            </div>

                            <div class="img-result img-thumbnail">
                                <a id="a-<?php echo $v['id']; ?>" href="<?php echo $v['imgurl']; ?>" target="_blank" title="View image <?php echo !empty($v['imgkeyword1']) ? htmlentities($v['imgkeyword1']) : '&nbsp;';  ?>"><img src="<?php echo $v['imgurl']; ?>" class="img-responsive" onerror="<?php echo !is_bot() ? 'imgerror(this,\''.$v['id'].'\');' : ''; ?>" alt="<?php echo !empty($v['imgkeyword1']) ? htmlentities($v['imgkeyword1']).' ' : '';  ?>Image and Photo" /></a>
                            </div>

                        </div> <!-- .result-<?php echo $v['id']; ?> --> */ ?>

                        <?php require('imageresult.tpl.php'); ?>

                        <?php } ?>

                        <?php //pre($vars); ?>
                        
                    </div>
                </div>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-3" id="cont-sidebar">

                <?php require_once('signin.tpl.php'); ?>

                <?php require_once('related.tpl.php'); ?>

                <?php require_once('recent.tpl.php'); ?>

            </div>        
        </div>
    </div>
</div>
