<?php


class Hugeit_Slider_Tracking
{
    /**
     * @var int
     */
    private $opted_id = 0;

    /**
     * Hugeit_Slider_Tracking constructor.
     */
    public function __construct()
    {
        if (isset($_GET['hugeit_slider_tracking_opt_in'])) {
            add_action('admin_init', array($this, 'maybe_opt_in'));
        }

        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('hugeit_slider_opt_in_cron',array($this,'track_data'));
    }

    /**
     *
     */
    public function maybe_opt_in()
    {
        if (!$this->can_opt_in()) {
            return;
        }

        $opt_in_action = htmlspecialchars($_GET['hugeit_slider_tracking_opt_in']);

        if(1==$opt_in_action){
            $this->opt_in();
        }elseif(0==$opt_in_action){
            $this->opt_out();
        }

        header( 'Location: ' . admin_url( 'admin.php?page=hugeit_slider' ) );

    }

    /**
     * Check if current user is capable for opting in/out to track user data
     *
     * @return bool
     */
    public function can_opt_in()
    {
        return current_user_can('manage_options');
    }

    /**
     * Print out the admin notice for opting in/out to track user data
     */
    public function admin_notice()
    {
        if (!$this->can_opt_in()) return;

        if ($this->is_opted_in() || $this->is_opted_out()) return;

        ?>
        <div class="hugeit-tracking-optin">
            <div class="hugeit-tracking-optin-left">
                <div class="hugeit-tracking-optin-icon"><img src="<?php echo HUGEIT_SLIDER_ADMIN_IMAGES_URL.'/tracking/plugin-icon.png'; ?>" alt="<?php echo Hugeit_Slider()->get_slug() ?>" /></div>
                <div class="hugeit-tracking-optin-info">
                    <div class="hugeit-tracking-optin-header"><?php _e('Let us know how you wish to better this plugin! ','hugeit-slider'); ?></div>
                    <div class="hugeit-tracking-optin-description"><?php _e('Allow us to email you and ask how you like our plugin and what issues we may fix or add in the future. We collect <a href="http://huge-it.com/privacy-policy/#collected_data_from_plugins" target="_blank">basic data</a>, in order to help the community to improve the quality of the plugin for you. Data will never be shared with any third party.','hugeit-slider'); ?></div>
                    <div>
                        <a href="<?php echo $this->get_opt_in_url(); ?>" class="hugeit-tracking-optin-button"><?php _e('Yes, sure','hugeit-slider'); ?></a><a href="<?php echo $this->get_opt_out_url(); ?>" class="hugeit-tracking-optout-button"><?php _e('No, thanks','hugeit-slider'); ?></a>
                    </div>
                </div>
            </div>
            <div class="hugeit-tracking-optin-right">
                <div class="hugeit-tracking-optin-logo">
                    <img src="<?php echo HUGEIT_SLIDER_ADMIN_IMAGES_URL.'/tracking/logo.png'; ?>" alt="Huge-IT" />
                </div>
                <div class="hugeit-tracking-optin-links">
                    <a href="http://huge-it.com/privacy-policy/#collected_data_from_plugins" target="_blank"><?php _e('What data We Collect','hugeit-slider'); ?></a>
                    <a href="https://huge-it.com/privacy-policy" target="_blank"><?php _e('Privacy Policy','hugeit-slider'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get url for opting out from tracking data
     *
     * @return string
     */
    public function get_opt_in_url()
    {
        return add_query_arg( 'hugeit_slider_tracking_opt_in', 1, admin_url( 'admin.php?page=hugeit_slider' ) );
    }

    /**
     * Get url for opting out from tracking data
     *
     * @return string
     */
    public function get_opt_out_url()
    {
        return add_query_arg( 'hugeit_slider_tracking_opt_in', 0, admin_url( 'admin.php?page=hugeit_slider' ) );
    }

    /**
     * Check if user has opted in to track data
     *
     * @return bool
     */
    public function is_opted_in()
    {
        return (bool)get_option('hugeit_slider_allow_tracking', false);
    }

    /**
     * Check if the user has opted out from tracking data
     *
     * @return bool
     */
    public function is_opted_out()
    {
        return (bool)get_option('hugeit_slider_disallow_tracking', false);
    }

    /**
     * Opt in to send data
     */
    public function opt_in()
    {
        update_option('hugeit_slider_allow_tracking',true);
    }

    /**
     * Opt out from sending data
     */
    public function opt_out()
    {
        update_option('hugeit_slider_disallow_tracking',true);
    }

    /**
     * If the user has opted id for data tracking
     * than send the data to http://huge-it.com
     *
     * @return bool
     */
    public function track_data()
    {
        if(!$this->is_opted_in() || $this->is_opted_out()){
            return false;
        }

        $all_plugins = array();
        $plugins = get_plugins();
        foreach ( $plugins as $plugin_slug => $plugin_info ) {
            $plugin = array(
                "Name" => $plugin_info["Name"],
                "PluginURI" => $plugin_info["PluginURI"],
                "Author" => $plugin_info["Author"],
                "AuthorURI" => $plugin_info["AuthorURI"]
            );
            $all_plugins[$plugin_slug] = $plugin;
        }

        $data = array();
        $data["site_url"] = home_url();
        $data["email"] = get_option('admin_email');

        $user = wp_get_current_user();

        $first_name = get_user_meta( $user->ID, "first_name", true );
        $last_name = get_user_meta( $user->ID, "last_name", true );

        $data["name"] = $first_name || $last_name ? $first_name . " " . $last_name : $user->data->user_login;

        $data["wp_version"] = get_bloginfo( 'version' );
        $data["project_id"] = Hugeit_Slider()->get_project_id();
        $data["project_plan"] = Hugeit_Slider()->get_project_plan();
        $data["project_version"] = Hugeit_Slider()->get_version();
        $data["all_plugins"] = $all_plugins;


        wp_remote_post( "https://huge-it.com/track-user-data/", array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'blocking' => true,
                'headers' => array(),
                'body' => $data,
            )
        );
    }

}