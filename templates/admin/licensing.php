<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$license = array(
    array(
        "title" => __('Unlimited Amount Of Images', 'hugeit-slider'),
        "text" => __('Create a slider with countless numbers of images in countless numbers of sliders. This is all possible with the Huge-IT Slider plugin.', 'hugeit-slider'),
        "icon" => "-12px -545px"
    ),
    array(
        "title" => __('Custom Options For Each Slider', 'hugeit-slider'),
        "text" => __('Each slider can be customized using Current Options, including size, transition speed, effects and many other useful options. Sliders can have their own customization settings separate from one another.', 'hugeit-slider'),
        "icon" => "-103px -545px"
    ),
    array(
        "title" => __('Fully Responsive', 'hugeit-slider'),
        "text" => __('The slider plugin can be used on different mobile devices as it’s flexible for any screen size. A very important feature is that responsiveness works on the title and description of the slider image as well.', 'hugeit-slider'),
        "icon" => "-188px -545px"
    ),
    array(
        "title" => __('Title And Description', 'hugeit-slider'),
        "text" => __('A Title and Description can be added to all the images within a slider. It overlays beautifully on the slider image with a slightly transparent background.', 'hugeit-slider'),
        "icon" => "-263px -545px"
    ),
    array(
        "title" => __('Custom URL For Each Slide', 'hugeit-slider'),
        "text" => __('When adding an image to a Slider, you can make add a clickable link from it to a specific page or URL.', 'hugeit-slider'),
        "icon" => "-325px -545px"
    ),
    array(
        "title" => __('A Dosen Style Options', 'hugeit-slider'),
        "text" => __('Under General Options in Huge-IT Slider you can find a number of various settings to enhance your slider’s appearance and actions.', 'hugeit-slider'),
        "icon" => "-407px -545px"
    ),
    array(
        "title" => __('Post Slides', 'hugeit-slider'),
        "text" => __('Using Post Slide you can create great looking slides of your posts using posts by category or just recent posts from all categories.', 'hugeit-slider'),
        "icon" => "-12px -632px"
    ),
    array(
        "title" => __('Youtube Slides', 'hugeit-slider'),
        "text" => __('YouTube videos can also be added to slides using our fancy slider plugin. You can add the links to the YouTube videos very easily, make some adjustments and add a number of beautiful effects.', 'hugeit-slider'),
        "icon" => "-99px -632px"
    ),
    array(
        "title" => __('Vimeo Slides', 'hugeit-slider'),
        "text" => __('Vimeo videos can also be added to a slider very easily. As with YouTube items, insert the video links into the slides and choose from the many different enhancement effects and modern features.', 'hugeit-slider'),
        "icon" => "-196px -632px"
    ),
    array(
        "title" => __('16 Navigation Buttons Style', 'hugeit-slider'),
        "text" => __('We have collected a large selection of different navigation arrow types to fit any design of your website.', 'hugeit-slider'),
        "icon" => "-291px -632px"
    ),
    array(
        "title" => __('Thumbnails Navigation', 'hugeit-slider'),
        "text" => __('Navigate images and videos in a slider using thumbnail images. This super feature allows you to preview previous and next slides.', 'hugeit-slider'),
        "icon" => "-379px -632px"
    )
);
?>


<div class="responsive grid">
    <?php foreach ($license as $key => $val) { ?>
        <div class="col column_1_of_3">
            <div class="header">
                <div class="col-icon" style="background-position: <?php echo $val["icon"]; ?>; ">
                </div>
                <?php echo $val["title"]; ?>
            </div>
            <p><?php echo $val["text"]; ?></p>
            <div class="col-footer">
                <a href="https://huge-it.com/slider/" target="_blank" class="a-upgrate">Upgrade</a>
            </div>
        </div>
    <?php } ?>
</div>


<div class="license-footer">
    <p class="footer-text">
        You are using the Lite version of the Image Slider Plugin for WordPress. If you want to get more awesome
        options,
        advanced features, settings to customize every area of the plugin, then check out the Full License plugin.
        The full version of the plugin is available in 3 different packages of one-time payment.
    </p>
    <p class="this-steps max-width">
        After the purchasing the commercial version follow this steps
    </p>
    <ul class="steps">
        <li>Deactivate Huge IT Image Slider Plugin</li>
        <li>Delete Huge IT Image Slider</li>
        <li>Install the downloaded commercial version of the plugin</li>
    </ul>
    <a href="https://huge-it.com/slider/" target="_blank">Purchase a License</a>
</div>