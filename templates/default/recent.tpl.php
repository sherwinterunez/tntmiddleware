                <div class="panel panel-default panel-border-radius">
                    <div class="panel-heading">
                        <h3 class="panel-title">Recent</h3>
                    </div>
                    <div class="panel-body">
                        <?php if(!empty($vars['recents'])&&is_array($vars['recents'])) {
                            echo "\n".'<div class="list-group">'."\n";
                            //pre($vars['results']['relatedkeys']);
                            foreach($vars['recents'] as $k=>$v) {

                                $murl = makeurl($v);

                                echo '<a class="list-group-item" href="'.$murl['url'].'" target="_blank">';
                                echo htmlentities(ucwords(strtolower($v)));
                                echo '</a>'."\n";
                            }
                            echo '</div>';
                        } ?>
                        <?php //pre($vars['recents']); ?>
                    </div>
                </div> <!-- recent.panel -->
