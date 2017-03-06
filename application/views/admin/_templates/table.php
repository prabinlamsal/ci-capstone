<?php defined('BASEPATH') OR exit('No direct script allowed'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                    <h5><?php echo lang($caption_lang); ?></h5>
                </div>
                <div class="widget-content nopadding">
                    <?php echo $table_data; ?>
                </div>
            </div>
        </div>
        <?php
        if (isset($pagination))
        {
                echo '<div class="pagination alternate pull-right">';
                echo $pagination;
                echo '</div>';
        }
        ?>
    </div>
</div>