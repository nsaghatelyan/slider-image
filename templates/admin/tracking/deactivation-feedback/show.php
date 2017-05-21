<?php
/**
 * @var $slug string The plugin slug
 */
?>
<div id="<?php echo $slug ?>-deactivation-feedback" class="-hugeit-modal">
    <div class="-hugeit-modal-content">
        <div class="-hugeit-modal-content-header">
            <?php _e('Dear User please help us to improve our users\' experience by letting us know why you decided to deactivate the Huge-it Slider','hugeit-slider'); ?>
        </div>
        <div class="-hugeit-modal-content-body">
            <?php wp_nonce_field('hugeit-slider-deactivation-feedback','hugeit-slider-deactivation-nonce'); ?>
            <div>
                <label>
                    <input type="radio" value="hard_to_use" name="<?php echo $slug ?>-deactivation-reason" /><span><?php _e('Hard to use','hugeit-slider'); ?></span>
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" value="free_version_limited" name="<?php echo $slug ?>-deactivation-reason" /><span><?php _e('Free version limited','hugeit-slider'); ?></span>
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" value="premium_is_expensive" name="<?php echo $slug ?>-deactivation-reason" /><span><?php _e('Premium is expensive','hugeit-slider'); ?></span>
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" value="upgraiding_to_paid_version" name="<?php echo $slug ?>-deactivation-reason" /><span><?php _e('Upgrading to paid version','hugeit-slider'); ?></span>
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" value="temporary_deactivation" name="<?php echo $slug ?>-deactivation-reason" /><span><?php _e('Temporary Deactivation','hugeit-slider'); ?></span>
                </label>
            </div>
        </div>
        <div class="-hugeit-modal-content-footer">
            <a href="#" class="hugeit-deactivate-plugin button"><?php _e('Deactivate','hugeit-slider') ?></a>
            <a href="#" class="hugeit-cancel-deactivation button-primary"><?php _e('Cancel','hugeit-slider') ?></a>
        </div>
    </div>
</div>