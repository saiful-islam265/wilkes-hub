<div class="col-xl-12 col-lg-12">
                            <div class="dashboard-page__order" id="comments_history">
                                <div class="dashboard-page__order-header">
                                    <div class="navbar-header">
                                        <h6 class="title">Order Communication</h6>
                                    </div>

                                    <div class="navbar-collapse">
                                        <ul class="navbar-nav ml-auto">
                                            <li><a href="#" class="icon-setting-sliders"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="dashboard-page__order-body">

                                <?php
                                    $order_id = intval( $_GET['order_id'] );
                                    $post_id = intval( $_GET['post_id'] );
                                    echo do_shortcode('[order-comment-field order_id="'.$order_id.'" post_id="'.$post_id.'"]');
                                ?>
                                </div>
                            </div>
                        </div>