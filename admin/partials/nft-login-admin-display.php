<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://davehagler.github.io/nftlogin/
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form action="options.php" method="post">
        <?php
        settings_errors();
        settings_fields( $this->plugin_name );
        do_settings_sections( $this->plugin_name );
        submit_button(); ?>
    </form>
</div>