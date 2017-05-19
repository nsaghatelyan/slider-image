<?php

class Hugeit_Slider_Deactivation_Feedback
{

    public function __construct()
    {

        add_action('current_screen', array($this, 'init'));
        add_action('wp_ajax_deactivation_feedback', array($this, 'init'));
    }

    public function init()
    {
        $screen = get_current_screen();

        if('plugins' === $screen->id){
            add_action('admin_footer',array($this,'render_footer'));
        }
    }

    public function render_footer()
    {
        $slug = Hugeit_Slider()->get_slug();
        echo Hugeit_Slider_Template_Loader::render(HUGEIT_SLIDER_ADMIN_TEMPLATES_PATH.'/tracking/deactivation-feedback/show.php', compact('slug'));
    }

}