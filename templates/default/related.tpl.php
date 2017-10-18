                <div class="panel panel-default panel-border-radius">
                    <div class="panel-heading">
                        <h3 class="panel-title">Related</h3>
                    </div>
                    <div class="panel-body">
                        <?php if(!empty($vars['results']['relatedkeys'])&&is_array($vars['results']['relatedkeys'])) {
                            echo "\n".'<div class="list-group">'."\n";
                            //pre($vars['results']['relatedkeys']);
                            foreach($vars['results']['relatedkeys'] as $k=>$v) {

                                $murl = makeurl($v);

                                echo '<a class="list-group-item" href="'.$murl['url'].'" target="_blank">';
                                echo htmlentities(ucwords(strtolower($v)));
                                echo '</a>'."\n";
                            }
                            echo '</div>';
                        } ?>
                    </div>
                </div> <!--  related.panel -->
