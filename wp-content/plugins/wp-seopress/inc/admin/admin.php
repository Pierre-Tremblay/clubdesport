<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

class seopress_options
{
    /**
     * Holds the values to be used in the fields callbacks.
     */
    private $options;

    /**
     * Start up.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_plugin_page'], 10);
        add_action('admin_init', [$this, 'page_init']);

        require_once dirname(__FILE__) . '/admin-dyn-variables-helper.php'; //Dynamic variables
    }

    /**
     * Add options page.
     */
    public function add_plugin_page() {
        if (has_filter('seopress_seo_admin_menu')) {
            $sp_seo_admin_menu['icon'] = '';
            $sp_seo_admin_menu['icon'] = apply_filters('seopress_seo_admin_menu', $sp_seo_admin_menu['icon']);
        } else {
            $sp_seo_admin_menu['icon'] = 'dashicons-admin-seopress';
        }

        $sp_seo_admin_menu['title'] = __('SEO', 'wp-seopress');
        if (has_filter('seopress_seo_admin_menu_title')) {
            $sp_seo_admin_menu['title'] = apply_filters('seopress_seo_admin_menu_title', $sp_seo_admin_menu['title']);
        }

        add_menu_page(__('SEOPress Option Page', 'wp-seopress-pro'), $sp_seo_admin_menu['title'], seopress_capability('manage_options', 'menu'), 'seopress-option', [$this, 'create_admin_page'], $sp_seo_admin_menu['icon'], 90);
        add_submenu_page('seopress-option', __('Dashboard', 'wp-seopress'), __('Dashboard', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-option', [$this, 'create_admin_page']);
        $seopress_titles_help_tab           = add_submenu_page('seopress-option', __('Titles & Metas', 'wp-seopress'), __('Titles & Metas', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-titles', [$this, 'seopress_titles_page']);
        $seopress_xml_sitemaps_help_tab     = add_submenu_page('seopress-option', __('XML / Image / Video / HTML Sitemap', 'wp-seopress'), __('XML / HTML Sitemap', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-xml-sitemap', [$this, 'seopress_xml_sitemap_page']);
        $seopress_social_networks_help_tab  = add_submenu_page('seopress-option', __('Social Networks', 'wp-seopress'), __('Social Networks', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-social', [$this, 'seopress_social_page']);
        $seopress_google_analytics_help_tab = add_submenu_page('seopress-option', __('Analytics', 'wp-seopress'), __('Analytics', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-google-analytics', [$this, 'seopress_google_analytics_page']);
        add_submenu_page('seopress-option', __('Advanced', 'wp-seopress'), __('Advanced', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-advanced', [$this, 'seopress_advanced_page']);
        add_submenu_page('seopress-option', __('Tools', 'wp-seopress'), __('Tools', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-import-export', [$this, 'seopress_import_export_page']);

        if (function_exists('seopress_get_toggle_white_label_option')) {
            $white_label_toggle = seopress_get_toggle_white_label_option();
            if ('1' === $white_label_toggle) {
                if (function_exists('seopress_white_label_help_links_option') && '1' === seopress_white_label_help_links_option()) {
                    return;
                }
            }
        }

        function seopress_titles_help_tab() {
            $screen = get_current_screen();

            require_once dirname(__FILE__) . '/admin-dyn-variables-helper.php'; //Dynamic variables

            $dyn_var = seopress_get_dyn_variables();

            $seopress_titles_help_tab_content = '<ul>';
            foreach ($dyn_var as $key => $value) {
                $seopress_titles_help_tab_content .= '<li>';
                $seopress_titles_help_tab_content .= '<strong>' . $key . '</strong> - <em>' . $value . '</em>';
                $seopress_titles_help_tab_content .= '</li>';
            }

            $seopress_titles_help_tab_content .= '<ul>';

            $seopress_titles_help_tab_content .= wp_oembed_get('https://www.youtube.com/watch?v=Jretu4Gpgo8', ['width'=>530]);

            $seopress_titles_help_robots_tab_content = wp_oembed_get('https://www.youtube.com/watch?v=Jretu4Gpgo8', ['width'=>530]);

            $screen->add_help_tab([
                'id'        => 'seopress_titles_help_tab',
                'title'     => __('Templates variables', 'wp-seopress'),
                'content'   => $seopress_titles_help_tab_content,
            ]);

            $screen->add_help_tab([
                'id'        => 'seopress_titles_help_robots_tab',
                'title'     => __('Edit your meta robots', 'wp-seopress'),
                'content'   => $seopress_titles_help_robots_tab_content,
            ]);

            if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/fr/support/guides/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Browse our guides', 'wp-seopress') . '</a></li>
						<li><a href="https://www.seopress.org/fr/support/faq/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our FAQ', 'wp-seopress') . '</a></li>
						<li><a href="https://www.seopress.org/fr/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Check our website', 'wp-seopress') . '</a></li>
					</ul>'
                );
            } else {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/support/guides/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Browse our guides', 'wp-seopress') . '</a></li>
						<li><a href="https://www.seopress.org/support/faq/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our FAQ', 'wp-seopress') . '</a></li>
						<li><a href="https://www.seopress.org/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Check our website', 'wp-seopress') . '</a></li>
					</ul>'
                );
            }
        }
        add_action('load-' . $seopress_titles_help_tab, 'seopress_titles_help_tab');

        function seopress_xml_sitemaps_help_tab() {
            $screen = get_current_screen();

            $seopress_xml_sitemaps_help_tab_content = '
				<p>' . __('Watch our video to learn how to enable XML sitemaps to improve crawling and them to Google Search Console.', 'wp-seopress') . '</p>
			' . wp_oembed_get('https://www.youtube.com/watch?v=Bjfspe1nusY', ['width'=>530]);

            $screen->add_help_tab([
                'id'        => 'seopress_google_analytics_help_tab',
                'title'     => __('How-to', 'wp-seopress'),
                'content'   => $seopress_xml_sitemaps_help_tab_content,
            ]);

            if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/fr/support/guides/activer-sitemap-xml/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
					</ul>'
                );
            } else {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/support/guides/enable-xml-sitemaps/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
					</ul>'
                );
            }
        }
        add_action('load-' . $seopress_xml_sitemaps_help_tab, 'seopress_xml_sitemaps_help_tab');

        function seopress_social_networks_help_tab() {
            $screen = get_current_screen();

            $seopress_social_networks_help_tab_content = '
				<p>' . __('Watch our video to learn how to edit your Open Graph and Twitters Cards tags to improve social sharing.', 'wp-seopress') . '</p>
			' . wp_oembed_get('https://www.youtube.com/watch?v=TX3AUsI6vKk', ['width'=>530]);

            $screen->add_help_tab([
                'id'        => 'seopress_social_networks_help_tab',
                'title'     => __('How-to', 'wp-seopress'),
                'content'   => $seopress_social_networks_help_tab_content,
            ]);

            if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
					</ul>'
                );
            } else {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/support/guides/manage-facebook-open-graph-and-twitter-cards-metas/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
					</ul>'
                );
            }
        }
        add_action('load-' . $seopress_social_networks_help_tab, 'seopress_social_networks_help_tab');

        function seopress_google_analytics_help_tab() {
            $screen = get_current_screen();

            $seopress_google_analytics_help_tab_content = '
				<p>' . __('Watch our video to learn how to connect your WordPress site with Google Analytics and get statistics right in your dashboard (PRO only).', 'wp-seopress') . '</p>
			' . wp_oembed_get('https://www.youtube.com/watch?v=2EWdogYuFgs', ['width'=>530]);

            $screen->add_help_tab([
                'id'        => 'seopress_google_analytics_help_tab',
                'title'     => __('How-to', 'wp-seopress'),
                'content'   => $seopress_google_analytics_help_tab_content,
            ]);

            if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/fr/support/guides/connectez-site-wordpress-a-google-analytics/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
					</ul>'
                );
            } else {
                $screen->set_help_sidebar(
                    '<ul>
						<li><a href="https://www.seopress.org/support/guides/connect-wordpress-site-google-analytics/?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
					</ul>'
                );
            }
        }
        add_action('load-' . $seopress_google_analytics_help_tab, 'seopress_google_analytics_help_tab');
    }

    public function seopress_titles_page() {
        $this->options = get_option('seopress_titles_option_name');
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
		<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
		<?php
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            echo '<div class="sp-components-snackbar-list"><div class="sp-components-snackbar"><div class="sp-components-snackbar__content"><span class="dashicons dashicons-yes"></span>' . __('Your settings have been saved.', 'wp-seopress') . '</div></div></div>';
        }

        global $wp_version, $title;
        $current_tab = '';
        $tag         = version_compare($wp_version, '4.4') >= 0 ? 'h1' : 'h2';
        echo '<' . $tag . '><span class="dashicons dashicons-editor-table"></span>' . $title;

        if ('1' == seopress_get_toggle_option('titles')) {
            $seopress_get_toggle_titles_option = '"1"';
        } else {
            $seopress_get_toggle_titles_option = '"0"';
        } ?>

		<input type="checkbox" name="toggle-titles" id="toggle-titles" class="toggle" data-toggle=<?php echo $seopress_get_toggle_titles_option; ?>>
		<label for="toggle-titles"></label>

		<?php
        if ('1' == seopress_get_toggle_option('titles')) {
            echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
            echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
        } else {
            echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
            echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</' . $tag . '>';

        settings_fields('seopress_titles_option_group'); ?>

		<div id="seopress-tabs" class="wrap">
		<?php

            $plugin_settings_tabs = [
                'tab_seopress_titles_home'     => __('Home', 'wp-seopress'),
                'tab_seopress_titles_single'   => __('Single Post Types', 'wp-seopress'),
                'tab_seopress_titles_archives' => __('Archives', 'wp-seopress'),
                'tab_seopress_titles_tax'      => __('Taxonomies', 'wp-seopress'),
                'tab_seopress_titles_advanced' => __('Advanced', 'wp-seopress'),
            ];

        echo '<div class="nav-tab-wrapper">';
        foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
            echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-titles#tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</div>'; ?>
			<div class="seopress-tab <?php if ('tab_seopress_titles_home' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_titles_home"><?php do_settings_sections('seopress-settings-admin-titles-home'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_titles_single' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_titles_single"><?php do_settings_sections('seopress-settings-admin-titles-single'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_titles_archives' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_titles_archives"><?php do_settings_sections('seopress-settings-admin-titles-archives'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_titles_tax' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_titles_tax"><?php do_settings_sections('seopress-settings-admin-titles-tax'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_titles_advanced' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_titles_advanced"><?php do_settings_sections('seopress-settings-admin-titles-advanced'); ?></div>
		</div>

		<?php submit_button(); ?>
		</form>
		<?php
    }

    public function seopress_xml_sitemap_page() {
        $this->options = get_option('seopress_xml_sitemap_option_name');
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
		<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option" name="seopress-flush">
		<?php
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            echo '<div class="sp-components-snackbar-list"><div class="sp-components-snackbar"><div class="sp-components-snackbar__content"><span class="dashicons dashicons-yes"></span>' . __('Your settings have been saved.', 'wp-seopress') . '</div></div></div>';
        }
        global $wp_version, $title;
        $current_tab = '';
        $tag         = version_compare($wp_version, '4.4') >= 0 ? 'h1' : 'h2';
        echo '<' . $tag . '><span class="dashicons dashicons-media-spreadsheet"></span>' . $title;

        if ('1' == seopress_get_toggle_option('xml-sitemap')) {
            $seopress_get_toggle_xml_sitemap_option = '"1"';
        } else {
            $seopress_get_toggle_xml_sitemap_option = '"0"';
        } ?>

		<input type="checkbox" name="toggle-xml-sitemap" id="toggle-xml-sitemap" class="toggle" data-toggle=<?php echo $seopress_get_toggle_xml_sitemap_option; ?>>

		<label for="toggle-xml-sitemap"></label>

		<?php if ('1' == seopress_get_toggle_option('xml-sitemap')) {
            echo '<span id="xml-sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
            echo '<span id="xml-sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
        } else {
            echo '<span id="xml-sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
            echo '<span id="xml-sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</' . $tag . '>';

        settings_fields('seopress_xml_sitemap_option_group'); ?>

		<div id="seopress-tabs" class="wrap">
		 <?php

            $plugin_settings_tabs = [
                'tab_seopress_xml_sitemap_general'    => __('General', 'wp-seopress'),
                'tab_seopress_xml_sitemap_post_types' => __('Post Types', 'wp-seopress'),
                'tab_seopress_xml_sitemap_taxonomies' => __('Taxonomies', 'wp-seopress'),
                'tab_seopress_html_sitemap'           => __('HTML Sitemap', 'wp-seopress'),
            ];

        echo '<div class="nav-tab-wrapper">';
        foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
            echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-xml-sitemap#tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</div>'; ?>
			<div class="seopress-tab <?php if ('tab_seopress_xml_sitemap_general' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_xml_sitemap_general"><?php do_settings_sections('seopress-settings-admin-xml-sitemap-general'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_xml_sitemap_post_types' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_xml_sitemap_post_types"><?php do_settings_sections('seopress-settings-admin-xml-sitemap-post-types'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_xml_sitemap_taxonomies' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_xml_sitemap_taxonomies"><?php do_settings_sections('seopress-settings-admin-xml-sitemap-taxonomies'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_html_sitemap' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_html_sitemap"><?php do_settings_sections('seopress-settings-admin-html-sitemap'); ?></div>
		</div>
		<?php submit_button(); ?>
		</form>
		<?php
    }

    public function seopress_social_page() {
        $this->options = get_option('seopress_social_option_name');
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
		<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
		<?php
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            echo '<div class="sp-components-snackbar-list"><div class="sp-components-snackbar"><div class="sp-components-snackbar__content"><span class="dashicons dashicons-yes"></span>' . __('Your settings have been saved.', 'wp-seopress') . '</div></div></div>';
        }

        global $wp_version, $title;
        $current_tab = '';
        $tag         = version_compare($wp_version, '4.4') >= 0 ? 'h1' : 'h2';
        echo '<' . $tag . '><span class="dashicons dashicons-share"></span>' . $title;

        if ('1' == seopress_get_toggle_option('social')) {
            $seopress_get_toggle_social_option = '"1"';
        } else {
            $seopress_get_toggle_social_option = '"0"';
        } ?>

		<input type="checkbox" name="toggle-social" id="toggle-social" class="toggle" data-toggle=<?php echo $seopress_get_toggle_social_option; ?>>
		<label for="toggle-social"></label>

		<?php
        if ('1' == seopress_get_toggle_option('social')) {
            echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
            echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
        } else {
            echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
            echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</' . $tag . '>';

        settings_fields('seopress_social_option_group'); ?>

		 <div id="seopress-tabs" class="wrap">
		 <?php

            $plugin_settings_tabs = [
                'tab_seopress_social_knowledge' => __('Knowledge Graph', 'wp-seopress'),
                'tab_seopress_social_accounts'  => __('Your social accounts', 'wp-seopress'),
                'tab_seopress_social_facebook'  => __('Facebook (Open Graph)', 'wp-seopress'),
                'tab_seopress_social_twitter'   => __('Twitter (Twitter card)', 'wp-seopress'),
            ];

        echo '<div class="nav-tab-wrapper">';
        foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
            echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-social#tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</div>'; ?>
			<div class="seopress-tab <?php if ('tab_seopress_social_knowledge' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_social_knowledge"><?php do_settings_sections('seopress-settings-admin-social-knowledge'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_social_accounts' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_social_accounts"><?php do_settings_sections('seopress-settings-admin-social-accounts'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_social_facebook' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_social_facebook"><?php do_settings_sections('seopress-settings-admin-social-facebook'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_social_twitter' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_social_twitter"><?php do_settings_sections('seopress-settings-admin-social-twitter'); ?></div>
		</div>

		<?php submit_button(); ?>
		</form>
		<?php
    }

    public function seopress_google_analytics_page() {
        $this->options = get_option('seopress_google_analytics_option_name');
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
		<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
		<?php
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            echo '<div class="sp-components-snackbar-list"><div class="sp-components-snackbar"><div class="sp-components-snackbar__content"><span class="dashicons dashicons-yes"></span>' . __('Your setting have been saved.', 'wp-seopress') . '</div></div></div>';
        }

        global $wp_version, $title;
        $current_tab = '';
        $tag         = version_compare($wp_version, '4.4') >= 0 ? 'h1' : 'h2';
        echo '<' . $tag . '><span class="dashicons dashicons-chart-area"></span>' . $title;

        if ('1' == seopress_get_toggle_option('google-analytics')) {
            $seopress_get_toggle_google_analytics_option = '"1"';
        } else {
            $seopress_get_toggle_google_analytics_option = '"0"';
        } ?>

		<input type="checkbox" name="toggle-google-analytics" id="toggle-google-analytics" class="toggle" data-toggle=<?php echo $seopress_get_toggle_google_analytics_option; ?>>

		<label for="toggle-google-analytics"></label>

		<?php
        if ('1' == seopress_get_toggle_option('google-analytics')) {
            echo '<span id="google-analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
            echo '<span id="google-analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
        } else {
            echo '<span id="google-analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
            echo '<span id="google-analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</' . $tag . '>';

        settings_fields('seopress_google_analytics_option_group'); ?>

		 <div id="seopress-tabs" class="wrap">
		 <?php

            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                $plugin_settings_tabs = [
                    'tab_seopress_google_analytics_enable'              => __('General', 'wp-seopress'),
                    'tab_seopress_google_analytics_features'            => __('Tracking', 'wp-seopress'),
                    'tab_seopress_google_analytics_ecommerce'           => __('Ecommerce', 'wp-seopress'),
                    'tab_seopress_google_analytics_events'              => __('Events', 'wp-seopress'),
                    'tab_seopress_google_analytics_custom_dimensions'   => __('Custom Dimensions', 'wp-seopress'),
                    'tab_seopress_google_analytics_dashboard'           => __('Stats in Dashboard', 'wp-seopress'),
                    'tab_seopress_google_analytics_gdpr'                => __('Cookie bar / GDPR', 'wp-seopress'),
                    'tab_seopress_google_analytics_matomo'              => __('Matomo', 'wp-seopress'),
                ];
            } else {
                $plugin_settings_tabs = [
                    'tab_seopress_google_analytics_enable'              => __('General', 'wp-seopress'),
                    'tab_seopress_google_analytics_features'            => __('Tracking', 'wp-seopress'),
                    'tab_seopress_google_analytics_events'              => __('Events', 'wp-seopress'),
                    'tab_seopress_google_analytics_custom_dimensions'   => __('Custom Dimensions', 'wp-seopress'),
                    'tab_seopress_google_analytics_gdpr'                => __('Cookie bar / GDPR', 'wp-seopress'),
                    'tab_seopress_google_analytics_matomo'              => __('Matomo', 'wp-seopress'),
                ];
            }

        echo '<div class="nav-tab-wrapper">';
        foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
            echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-google-analytics#tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</div>'; ?>
			<div class="seopress-tab <?php if ('tab_seopress_google_analytics_enable' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_enable"><?php do_settings_sections('seopress-settings-admin-google-analytics-enable'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_google_analytics_features' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_features"><?php do_settings_sections('seopress-settings-admin-google-analytics-features'); ?></div><div class="seopress-tab <?php if ('tab_seopress_google_analytics_events' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_events"><?php do_settings_sections('seopress-settings-admin-google-analytics-events'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_google_analytics_custom_dimensions' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_custom_dimensions"><?php do_settings_sections('seopress-settings-admin-google-analytics-custom-dimensions'); ?></div>
			<?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
				<div class="seopress-tab <?php if ('tab_seopress_google_analytics_dashboard' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_dashboard"><?php do_settings_sections('seopress-settings-admin-google-analytics-dashboard'); ?></div>
				<div class="seopress-tab <?php if ('tab_seopress_google_analytics_ecommerce' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_ecommerce"><?php do_settings_sections('seopress-settings-admin-google-analytics-ecommerce'); ?></div>
			<?php } ?>
			<div class="seopress-tab <?php if ('tab_seopress_google_analytics_gdpr' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_gdpr"><?php do_settings_sections('seopress-settings-admin-google-analytics-gdpr'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_google_analytics_matomo' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_google_analytics_matomo"><?php do_settings_sections('seopress-settings-admin-google-analytics-matomo'); ?></div>
		</div>

		<?php submit_button(); ?>
		</form>
		<?php
    }

    public function seopress_advanced_page() {
        $this->options = get_option('seopress_advanced_option_name');
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
		<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
		<?php
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            echo '<div class="sp-components-snackbar-list"><div class="sp-components-snackbar"><div class="sp-components-snackbar__content"><span class="dashicons dashicons-yes"></span>' . __('Your settings have been saved.', 'wp-seopress') . '</div></div></div>';
        }

        global $wp_version, $title;
        $current_tab = '';
        $tag         = version_compare($wp_version, '4.4') >= 0 ? 'h1' : 'h2';
        echo '<' . $tag . '><span class="dashicons dashicons-admin-tools"></span>' . $title;

        if ('1' == seopress_get_toggle_option('advanced')) {
            $seopress_get_toggle_advanced_option = '"1"';
        } else {
            $seopress_get_toggle_advanced_option = '"0"';
        } ?>

		<input type="checkbox" name="toggle-advanced" id="toggle-advanced" class="toggle" data-toggle=<?php echo $seopress_get_toggle_advanced_option; ?>>
		<label for="toggle-advanced"></label>

		<?php
        if ('1' == seopress_get_toggle_option('advanced')) {
            echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
            echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
        } else {
            echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
            echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
        }

        echo '<div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>';

        echo '</' . $tag . '>';

        settings_fields('seopress_advanced_option_group'); ?>

		 <div id="seopress-tabs" class="wrap">
		 <?php

            $plugin_settings_tabs = [
                'tab_seopress_advanced_image'       => __('Image SEO', 'wp-seopress'),
                'tab_seopress_advanced_advanced'    => __('Advanced', 'wp-seopress'),
                'tab_seopress_advanced_appearance'  => __('Appearance', 'wp-seopress'),
                'tab_seopress_advanced_security'    => __('Security', 'wp-seopress'),
            ];

        echo '<div class="nav-tab-wrapper">';
        foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
            echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-advanced#tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</div>'; ?>
		<div class="seopress-tab <?php if ('tab_seopress_advanced_image' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_advanced_image"><?php do_settings_sections('seopress-settings-admin-advanced-image'); ?>
		</div>
		<div class="seopress-tab <?php if ('tab_seopress_advanced_advanced' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_advanced_advanced"><?php do_settings_sections('seopress-settings-admin-advanced-advanced'); ?>
		</div>
			<div class="seopress-tab <?php if ('tab_seopress_advanced_appearance' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_advanced_appearance"><?php do_settings_sections('seopress-settings-admin-advanced-appearance'); ?></div>
			<div class="seopress-tab <?php if ('tab_seopress_advanced_security' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_advanced_security"><?php do_settings_sections('seopress-settings-admin-advanced-security'); ?></div>
		</div>

		<?php submit_button(); ?>
		</form>
		<?php
    }

    public function seopress_import_export_page() {
        $this->options = get_option('seopress_import_export_option_name');
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
		<div class="seopress-option">
			<?php global $wp_version, $title;
        $current_tab = '';
        $tag         = version_compare($wp_version, '4.4') >= 0 ? 'h1' : 'h2';
        echo '<' . $tag . '><span class="dashicons dashicons-admin-settings"></span>' . $title . '</' . $tag . '>'; ?>
			<div id="seopress-tabs" class="wrap">
				<?php
                    $plugin_settings_tabs = [
                        'tab_seopress_tool_compatibility'  => __('Compatibility Center', 'wp-seopress'),
                        'tab_seopress_tool_data'           => __('Data', 'wp-seopress'),
                        'tab_seopress_tool_settings'       => __('Settings', 'wp-seopress'),
                        'tab_seopress_tool_plugins'        => __('Plugins', 'wp-seopress'),
                        'tab_seopress_tool_redirects'      => __('Redirections', 'wp-seopress'),
                        'tab_seopress_tool_reset'          => __('Reset', 'wp-seopress'),
                    ];

        if ( ! is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            unset($plugin_settings_tabs['tab_seopress_tool_data']);
            unset($plugin_settings_tabs['tab_seopress_tool_redirects']);
        }

        echo '<div class="nav-tab-wrapper">';
        foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
            echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-import-export#tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</div>'; ?>
				<div class="seopress-tab <?php if ('tab_seopress_tool_compatibility' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_tool_compatibility">
			<form method="post" action="<?php echo admin_url('options.php'); ?>">
				<?php
                    settings_fields('seopress_tools_option_group');
        do_settings_sections('seopress-settings-admin-tools-compatibility');
        submit_button(); ?>
			</form>
				</div>
				<?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
					<div class="seopress-tab <?php if ('tab_seopress_tool_data' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_tool_data">
						<div class="postbox section-tool">
							<div class="inside">
								<h3><span><?php _e('Import data from a CSV', 'wp-seopress'); ?></span></h3>
								<p><?php _e('Upload a CSV file to quickly import post (post, page, single post type) and term metadata:', 'wp-seopress'); ?></p>
								<ul>
									<li>
										<?php _e('Meta title', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Meta description', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Meta robots (noindex, nofollow...)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Facebook Open Graph tags (title, description, image)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Twitter cards tags (title, description, image)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Redirection (enable, type, URL)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Primary category', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Canonical URL', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Target keywords', 'wp-seopress'); ?>
									</li>
								</ul>
								<a class="button" href="<?php echo admin_url('admin.php?page=seopress_csv_importer'); ?>"><?php _e('Run the importer', 'wp-seopress'); ?></a>
							</div><!-- .inside -->
						</div><!-- .postbox -->
						<div id="metadata-migration-tool" class="postbox section-tool">
							<div class="inside">
								<h3><span><?php _e('Export metadata to a CSV', 'wp-seopress'); ?></span></h3>
								<p><?php _e('Export your post (post, page, single post type) and term metadata for this site as a .csv file.', 'wp-seopress'); ?></p>
								<ul>
									<li>
										<?php _e('Meta title', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Meta description', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Meta robots (noindex, nofollow...)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Facebook Open Graph tags (title, description, image)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Twitter cards tags (title, description, image)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Redirection (enable, type, URL)', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Primary category', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Canonical URL', 'wp-seopress'); ?>
									</li>
									<li>
										<?php _e('Target keywords', 'wp-seopress'); ?>
									</li>
								</ul>
								<form method="post">
									<p><input type="hidden" name="seopress_action" value="export_csv_metadata" /></p>
									<p>
										<?php wp_nonce_field('seopress_export_csv_metadata_nonce', 'seopress_export_csv_metadata_nonce'); ?>
										<button id="seopress-metadata-migrate" type="button" class="button"><?php _e('Export', 'wp-seopress'); ?></button>
										<span class="spinner"></span>
										<div class="log"></div>
									</p>
								</form>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div>
				<?php } ?>
				<div class="seopress-tab <?php if ('tab_seopress_tool_settings' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_tool_settings">
					<div class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Export plugin settings', 'wp-seopress'); ?></span></h3>
							<p><?php _e('Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-seopress'); ?></p>
							<form method="post">
								<p><input type="hidden" name="seopress_action" value="export_settings" /></p>
								<p>
									<?php wp_nonce_field('seopress_export_nonce', 'seopress_export_nonce'); ?>
									<?php submit_button(__('Export', 'wp-seopress'), 'secondary', 'submit', false); ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<div class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import plugin settings', 'wp-seopress'); ?></span></h3>
							<p><?php _e('Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-seopress'); ?></p>
							<form method="post" enctype="multipart/form-data">
								<p>
									<input type="file" name="import_file"/>
								</p>
								<p>
									<input type="hidden" name="seopress_action" value="import_settings" />
									<?php wp_nonce_field('seopress_import_nonce', 'seopress_import_nonce'); ?>
									<?php submit_button(__('Import', 'wp-seopress'), 'secondary', 'submit', false); ?>
									<?php if ( ! empty($_GET['success']) && 'true' == htmlspecialchars($_GET['success'])) {
            echo '<div class="log">' . __('Import completed!', 'wp-seopress') . '</div>';
        } ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->
				</div>
				<div class="seopress-tab <?php if ('tab_seopress_tool_plugins' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_tool_plugins">
					<div class="postbox section-tool">
						<h3><span><?php _e('Import posts and terms metadata from', 'wp-seopress'); ?></span></h3>
						<select id="select-wizard-import" name="select-wizard-import">
							<option value="none"><?php _e('Select an option', 'wp-seopress'); ?></option>
							<option value="yoast-migration-tool"><?php _e('Yoast SEO', 'wp-seopress'); ?></option>
							<option value="aio-migration-tool"><?php _e('All In One SEO', 'wp-seopress'); ?></option>
							<option value="seo-framework-migration-tool"><?php _e('The SEO Framework', 'wp-seopress'); ?></option>
							<option value="rk-migration-tool"><?php _e('Rank Math', 'wp-seopress'); ?></option>
							<option value="squirrly-migration-tool"><?php _e('Squirrly SEO', 'wp-seopress'); ?></option>
							<option value="seo-ultimate-migration-tool"><?php _e('SEO Ultimate', 'wp-seopress'); ?></option>
							<option value="wp-meta-seo-migration-tool"><?php _e('WP Meta SEO', 'wp-seopress'); ?></option>
							<option value="premium-seo-pack-migration-tool"><?php _e('Premium SEO Pack', 'wp-seopress'); ?></option>
							<option value="wpseo-migration-tool"><?php _e('wpSEO', 'wp-seopress'); ?></option>
							<option value="platinum-seo-migration-tool"><?php _e('Platinum SEO Pack', 'wp-seopress'); ?></option>
							<option value="smart-crawl-migration-tool"><?php _e('SmartCrawl', 'wp-seopress'); ?></option>
							<option value="seopressor-migration-tool"><?php _e('SEOPressor', 'wp-seopress'); ?></option>
						</select>
						<br><br>
                        <p class="description"><?php _e('You don\'t have to enable the selected SEO plugin to run the import.', 'wp-seopress'); ?></p>
					</div>
					<!-- Yoast import tool -->
					<div id="yoast-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts and terms metadata from Yoast', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex, nofollow...)', 'wp-seopress'); ?></li>
								<li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
								<li><?php _e('Focus keywords', 'wp-seopress'); ?></li>
								<li><?php _e('Primary category', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will delete / update all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any Yoast data.', 'wp-seopress'); ?></p>
							<button id="seopress-yoast-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- All In One import tool -->
					<div id="aio-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts metadata from All In One SEO', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
                                <li><?php _e('Title tags', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta description', 'wp-seopress'); ?></li>
                                <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta Robots (noindex, nofollow...)', 'wp-seopress'); ?></li>
                                <li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Focus keyword', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update/delete all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any AIO data.', 'wp-seopress'); ?></p>
							<button id="seopress-aio-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- SEO Framework import tool -->
					<div id="seo-framework-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts and terms metadata from The SEO Framework', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex, nofollow, noarchive)', 'wp-seopress'); ?></li>
								<li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
								<li><?php _e('Redirect URL', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any SEO Framework data.', 'wp-seopress'); ?></p>
							<button id="seopress-seo-framework-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- RK import tool -->
					<div id="rk-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts and terms metadata from Rank Math', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex, nofollow, noarchive, noimageindex)', 'wp-seopress'); ?></li>
								<li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
								<li><?php _e('Focus keywords', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any Rank Math data.', 'wp-seopress'); ?></p>
							<button id="seopress-rk-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- Squirrly import tool -->
					<div id="squirrly-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts metadata from Squirrly SEO', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex or nofollow)', 'wp-seopress'); ?></li>
								<li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update/delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any Squirrly SEO data.', 'wp-seopress'); ?></p>
							<button id="seopress-squirrly-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- SEO Ultimate import tool -->
					<div id="seo-ultimate-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts metadata from SEO Ultimate', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex or nofollow)', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any SEO Ultimate data.', 'wp-seopress'); ?></p>
							<button id="seopress-seo-ultimate-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- WP Meta SEO import tool -->
					<div id="wp-meta-seo-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts and terms metadata from WP Meta SEO', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any WP Meta SEO data.', 'wp-seopress'); ?></p>
							<button id="seopress-wp-meta-seo-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- Premium SEO Pack import tool -->
					<div id="premium-seo-pack-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts and terms metadata from Premium SEO Pack', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex, nofollow)', 'wp-seopress'); ?></li>
								<li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
								<li><?php _e('Focus keywords', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any Premium SEO Pack data.', 'wp-seopress'); ?></p>
							<button id="seopress-premium-seo-pack-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<!-- wpSEO import tool -->
					<div id="wpseo-migration-tool" class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Import posts and terms metadata from wpSEO', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
							<ul>
								<li><?php _e('Title tags', 'wp-seopress'); ?></li>
								<li><?php _e('Meta description', 'wp-seopress'); ?></li>
								<li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
								<li><?php _e('Meta Robots (noindex, nofollow)', 'wp-seopress'); ?></li>
								<li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
								<li><?php _e('Redirect URL', 'wp-seopress'); ?></li>
								<li><?php _e('Main keyword', 'wp-seopress'); ?></li>
							</ul>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any wpSEO data.', 'wp-seopress'); ?></p>
							<button id="seopress-wpseo-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
							<span class="spinner"></span>
							<div class="log"></div>
						</div><!-- .inside -->
					</div><!-- .postbox -->

                    <!-- Platinum SEO import tool -->
                    <div id="platinum-seo-migration-tool" class="postbox section-tool">
                        <div class="inside">
                            <h3><span><?php _e('Import posts and terms metadata from Platinum SEO Pack', 'wp-seopress'); ?></span></h3>
                            <p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
                            <ul>
                                <li><?php _e('Title tags', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta description', 'wp-seopress'); ?></li>
                                <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta Robots (noindex, nofollow, noarchive, nosnippet, noimageindex)', 'wp-seopress'); ?></li>
                                <li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Redirect URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Primary category', 'wp-seopress'); ?></li>
                                <li><?php _e('Keywords', 'wp-seopress'); ?></li>
                            </ul>
                            <p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any Platinum SEO data.', 'wp-seopress'); ?></p>
                            <button id="seopress-platinum-seo-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
                            <span class="spinner"></span>
                            <div class="log"></div>
                        </div><!-- .inside -->
                    </div><!-- .postbox -->

                    <!-- Smart Crawl import tool -->
                    <div id="smart-crawl-migration-tool" class="postbox section-tool">
                        <div class="inside">
                            <h3><span><?php _e('Import posts and terms metadata from SmartCrawl', 'wp-seopress'); ?></span></h3>
                            <p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
                            <ul>
                                <li><?php _e('Title tags', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta description', 'wp-seopress'); ?></li>
                                <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta Robots (noindex, nofollow, noarchive, nosnippet)', 'wp-seopress'); ?></li>
                                <li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Redirect URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Focus keywords', 'wp-seopress'); ?></li>
                            </ul>
                            <p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any SmartCrawl data.', 'wp-seopress'); ?></p>
                            <button id="seopress-smart-crawl-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
                            <span class="spinner"></span>
                            <div class="log"></div>
                        </div><!-- .inside -->
                    </div><!-- .postbox -->

                    <!-- SEOPressor import tool -->
                    <div id="seopressor-migration-tool" class="postbox section-tool">
                        <div class="inside">
                            <h3><span><?php _e('Import posts metadata from SEOPressor', 'wp-seopress'); ?></span></h3>
                            <p><?php _e('By clicking Migrate, we\'ll import:', 'wp-seopress'); ?></p>
                            <ul>
                                <li><?php _e('Title tags', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta description', 'wp-seopress'); ?></li>
                                <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Twitter tags (title, description and image thumbnail)', 'wp-seopress'); ?></li>
                                <li><?php _e('Meta Robots (noindex, nofollow, noarchive, nosnippet, noodp, noimageindex)', 'wp-seopress'); ?></li>
                                <li><?php _e('Canonical URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Redirect URL', 'wp-seopress'); ?></li>
                                <li><?php _e('Keywords', 'wp-seopress'); ?></li>
                            </ul>
                            <p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Migration will update / delete all SEOPress posts metadata. Some dynamic variables will not be interpreted. We do NOT delete any SEOPressor data.', 'wp-seopress'); ?></p>
                            <button id="seopress-seopressor-migrate" type="button" class="button"><?php _e('Migrate now', 'wp-seopress'); ?></button>
                            <span class="spinner"></span>
                            <div class="log"></div>
                        </div><!-- .inside -->
                    </div><!-- .postbox -->
				</div>
				<div class="seopress-tab <?php if ('tab_seopress_tool_redirects' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_tool_redirects">
					<?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
						<?php if ('1' == seopress_get_toggle_option('404') && function_exists('seopress_get_redirection_pro_html')) {
            seopress_get_redirection_pro_html();
        } else { ?>
							<p><?php _e('Redirections feature is disabled. Please activate it from the PRO page.', 'wp-seopress'); ?></p>
							<a href="<?php echo admin_url('admin.php?page=seopress-pro-page'); ?>"><?php _e('Activate Redirections', 'wp-seopress'); ?></a>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="seopress-tab <?php if ('tab_seopress_tool_reset' == $current_tab) {
            echo 'active';
        } ?>" id="tab_seopress_tool_reset">
					<div class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Reset All Notices From Notifications Center', 'wp-seopress'); ?></span></h3>
							<p><?php _e('By clicking Reset Notices, all notices in the notifications center will be set to their initial status.', 'wp-seopress'); ?></p>
							 <form method="post" enctype="multipart/form-data">
								<p>
									<input type="hidden" name="seopress_action" value="reset_notices_settings" />
									<?php wp_nonce_field('seopress_reset_notices_nonce', 'seopress_reset_notices_nonce'); ?>
									<?php submit_button(__('Reset notices', 'wp-seopress'), 'secondary', 'submit', false); ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<div class="postbox section-tool">
						<div class="inside">
							<h3><span><?php _e('Reset All Settings', 'wp-seopress'); ?></span></h3>
							<p style="color:red"><span class="dashicons dashicons-info"></span> <?php _e('<strong>WARNING:</strong> Delete all options related to this plugin in your database AND set settings to their default values.', 'wp-seopress'); ?></p>
							 <form method="post" enctype="multipart/form-data">
								<p>
									<input type="hidden" name="seopress_action" value="reset_settings" />
									<?php wp_nonce_field('seopress_reset_nonce', 'seopress_reset_nonce'); ?>
									<?php submit_button(__('Reset settings', 'wp-seopress'), 'secondary', 'submit', false); ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->
				</div>
			</div>
		</div>
	<?php
    }

    /**
     * Options page callback.
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('seopress_option_name');
        $current_tab   ='';
        if (function_exists('seopress_admin_header')) {
            echo seopress_admin_header();
        } ?>
			<div id="seopress-content">
				<!--Get started-->
				<?php
                    function seopress_get_hidden_notices_get_started_option() {
                        $seopress_get_hidden_notices_get_started_option = get_option('seopress_notices');
                        if ( ! empty($seopress_get_hidden_notices_get_started_option)) {
                            foreach ($seopress_get_hidden_notices_get_started_option as $key => $seopress_get_hidden_notices_get_started_value) {
                                $options[$key] = $seopress_get_hidden_notices_get_started_value;
                            }
                            if (isset($seopress_get_hidden_notices_get_started_option['notice-get-started'])) {
                                return $seopress_get_hidden_notices_get_started_option['notice-get-started'];
                            }
                        }
                    }
        if ('1' != seopress_get_hidden_notices_get_started_option()) {
            if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
                //do nothing
            } else {
                include_once dirname(__FILE__) . '/admin-get-started.php';
            }
        } ?>

				<!--Notifications Center-->
				<?php include_once dirname(__FILE__) . '/admin-notifications-center.php'; ?>

				<!--Features list-->
				<?php include_once dirname(__FILE__) . '/admin-features-list.php'; ?>
			</div>
		<?php
    }

    /**
     * Register and add settings.
     */
    public function page_init() {
        register_setting(
            'seopress_option_group', // Option group
            'seopress_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_titles_option_group', // Option group
            'seopress_titles_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_xml_sitemap_option_group', // Option group
            'seopress_xml_sitemap_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_social_option_group', // Option group
            'seopress_social_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_google_analytics_option_group', // Option group
            'seopress_google_analytics_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_advanced_option_group', // Option group
            'seopress_advanced_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_tools_option_group', // Option group
            'seopress_tools_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_import_export_option_group', // Option group
            'seopress_import_export_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        //Titles & metas SECTION===================================================================
        add_settings_section(
            'seopress_setting_section_titles_home', // ID
            '',
            //__("Home","wp-seopress"), // Title
            [$this, 'print_section_info_titles'], // Callback
            'seopress-settings-admin-titles-home' // Page
        );

        add_settings_field(
            'seopress_titles_sep', // ID
            __('Separator', 'wp-seopress'), // Title
            [$this, 'seopress_titles_sep_callback'], // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section
        );

        add_settings_field(
            'seopress_titles_home_site_title', // ID
            __('Site title', 'wp-seopress'), // Title
            [$this, 'seopress_titles_home_site_title_callback'], // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section
        );

        add_settings_field(
            'seopress_titles_home_site_desc', // ID
            __('Meta description', 'wp-seopress'), // Title
            [$this, 'seopress_titles_home_site_desc_callback'], // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section
        );

        //Single Post Types SECTION================================================================
        add_settings_section(
            'seopress_setting_section_titles_single', // ID
            '',
            //__("Single Post Types","wp-seopress"), // Title
            [$this, 'print_section_info_single'], // Callback
            'seopress-settings-admin-titles-single' // Page
        );

        add_settings_field(
            'seopress_titles_single_titles', // ID
            '',
            [$this, 'seopress_titles_single_titles_callback'], // Callback
            'seopress-settings-admin-titles-single', // Page
            'seopress_setting_section_titles_single' // Section
        );

        if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
            add_settings_field(
                'seopress_titles_bp_groups_title', // ID
                '',
                [$this, 'seopress_titles_bp_groups_title_callback'], // Callback
                'seopress-settings-admin-titles-single', // Page
                'seopress_setting_section_titles_single' // Section
            );

            add_settings_field(
                'seopress_titles_bp_groups_desc', // ID
                '',
                [$this, 'seopress_titles_bp_groups_desc_callback'], // Callback
                'seopress-settings-admin-titles-single', // Page
                'seopress_setting_section_titles_single' // Section
            );

            add_settings_field(
                'seopress_titles_bp_groups_noindex', // ID
                '',
                [$this, 'seopress_titles_bp_groups_noindex_callback'], // Callback
                'seopress-settings-admin-titles-single', // Page
                'seopress_setting_section_titles_single' // Section
            );
        }

        //Archives SECTION=========================================================================
        add_settings_section(
            'seopress_setting_section_titles_archives', // ID
            '',
            //__("Archives","wp-seopress"), // Title
            [$this, 'print_section_info_archives'], // Callback
            'seopress-settings-admin-titles-archives' // Page
        );

        add_settings_field(
            'seopress_titles_archives_titles', // ID
            '',
            [$this, 'seopress_titles_archives_titles_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_author_title', // ID
            '',
            //__('Title template','wp-seopress'),
            [$this, 'seopress_titles_archives_author_title_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_author_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            [$this, 'seopress_titles_archives_author_desc_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_author_noindex', // ID
            '',
            //__("noindex","wp-seopress"), // Title
            [$this, 'seopress_titles_archives_author_noindex_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_author_disable', // ID
            '',
            //__("disable","wp-seopress"), // Title
            [$this, 'seopress_titles_archives_author_disable_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_date_title', // ID
            '',
            //__('Title template','wp-seopress'),
            [$this, 'seopress_titles_archives_date_title_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_date_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            [$this, 'seopress_titles_archives_date_desc_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_date_noindex', // ID
            '',
            //__("noindex","wp-seopress"), // Title
            [$this, 'seopress_titles_archives_date_noindex_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_date_disable', // ID
            '',
            //__("disable","wp-seopress"), // Title
            [$this, 'seopress_titles_archives_date_disable_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_search_title', // ID
            '',
            //__('Title template','wp-seopress'),
            [$this, 'seopress_titles_archives_search_title_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_search_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            [$this, 'seopress_titles_archives_search_desc_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_search_title_noindex', // ID
            '',
            //__('noindex','wp-seopress'),
            [$this, 'seopress_titles_archives_search_title_noindex_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_404_title', // ID
            '',
            //__('Title template','wp-seopress'),
            [$this, 'seopress_titles_archives_404_title_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        add_settings_field(
            'seopress_titles_archives_404_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            [$this, 'seopress_titles_archives_404_desc_callback'], // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section
        );

        //Taxonomies SECTION=======================================================================
        add_settings_section(
            'seopress_setting_section_titles_tax', // ID
            '',
            //__("Taxonomies","wp-seopress"), // Title
            [$this, 'print_section_info_tax'], // Callback
            'seopress-settings-admin-titles-tax' // Page
        );

        add_settings_field(
            'seopress_titles_tax_titles', // ID
            '',
            [$this, 'seopress_titles_tax_titles_callback'], // Callback
            'seopress-settings-admin-titles-tax', // Page
            'seopress_setting_section_titles_tax' // Section
        );

        //Advanced SECTION=========================================================================
        add_settings_section(
            'seopress_setting_section_titles_advanced', // ID
            '',
            //__("Advanced","wp-seopress"), // Title
            [$this, 'print_section_info_advanced'], // Callback
            'seopress-settings-admin-titles-advanced' // Page
        );

        add_settings_field(
            'seopress_titles_noindex', // ID
            __('noindex', 'wp-seopress'), // Title
            [$this, 'seopress_titles_noindex_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_nofollow', // ID
            __('nofollow', 'wp-seopress'), // Title
            [$this, 'seopress_titles_nofollow_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_noodp', // ID
            __('noodp', 'wp-seopress'), // Title
            [$this, 'seopress_titles_noodp_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_noimageindex', // ID
            __('noimageindex', 'wp-seopress'), // Title
            [$this, 'seopress_titles_noimageindex_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_noarchive', // ID
            __('noarchive', 'wp-seopress'), // Title
            [$this, 'seopress_titles_noarchive_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_nosnippet', // ID
            __('nosnippet', 'wp-seopress'), // Title
            [$this, 'seopress_titles_nosnippet_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_nositelinkssearchbox', // ID
            __('nositelinkssearchbox', 'wp-seopress'), // Title
            [$this, 'seopress_titles_nositelinkssearchbox_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_paged_rel', // ID
            __('Indicate paginated content to Google', 'wp-seopress'), // Title
            [$this, 'seopress_titles_paged_rel_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        add_settings_field(
            'seopress_titles_paged_noindex', // ID
            __('noindex on paged archives', 'wp-seopress'), // Title
            [$this, 'seopress_titles_paged_noindex_callback'], // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section
        );

        //XML Sitemap SECTION======================================================================
        add_settings_section(
            'seopress_setting_section_xml_sitemap_general', // ID
            '',
            //__("General","wp-seopress"), // Title
            [$this, 'print_section_info_xml_sitemap_general'], // Callback
            'seopress-settings-admin-xml-sitemap-general' // Page
        );

        add_settings_field(
            'seopress_xml_sitemap_general_enable', // ID
            __('Enable XML Sitemap', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_general_enable_callback'], // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_img_enable', // ID
            __('Enable XML Image Sitemaps', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_img_enable_callback'], // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section
        );

        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            add_settings_field(
                'seopress_xml_sitemap_video_enable_callback', // ID
                __('Enable XML Video Sitemaps', 'wp-seopress'), // Title
                [$this, 'seopress_xml_sitemap_video_enable_callback'], // Callback
                'seopress-settings-admin-xml-sitemap-general', // Page
                'seopress_setting_section_xml_sitemap_general' // Section
            );
        }

        add_settings_field(
            'seopress_xml_sitemap_author_enable', // ID
            __('Enable Author Sitemap', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_author_enable_callback'], // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_html_enable', // ID
            __('Enable HTML Sitemap', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_enable_callback'], // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section
        );

        add_settings_section(
            'seopress_setting_section_xml_sitemap_post_types', // ID
            '',
            //__("Post Types","wp-seopress"), // Title
            [$this, 'print_section_info_xml_sitemap_post_types'], // Callback
            'seopress-settings-admin-xml-sitemap-post-types' // Page
        );

        add_settings_field(
            'seopress_xml_sitemap_post_types_list', // ID
            __('Check to INCLUDE Post Types', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_post_types_list_callback'], // Callback
            'seopress-settings-admin-xml-sitemap-post-types', // Page
            'seopress_setting_section_xml_sitemap_post_types' // Section
        );

        add_settings_section(
            'seopress_setting_section_xml_sitemap_taxonomies', // ID
            '',
            //__("Taxonomies","wp-seopress"), // Title
            [$this, 'print_section_info_xml_sitemap_taxonomies'], // Callback
            'seopress-settings-admin-xml-sitemap-taxonomies' // Page
        );

        add_settings_field(
            'seopress_xml_sitemap_taxonomies_list', // ID
            __('Check to INCLUDE Taxonomies', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_taxonomies_list_callback'], // Callback
            'seopress-settings-admin-xml-sitemap-taxonomies', // Page
            'seopress_setting_section_xml_sitemap_taxonomies' // Section
        );

        add_settings_section(
            'seopress_setting_section_html_sitemap', // ID
            '',
            //__("HTML Sitemap","wp-seopress"), // Title
            [$this, 'print_section_info_html_sitemap'], // Callback
            'seopress-settings-admin-html-sitemap' // Page
        );

        add_settings_field(
            'seopress_xml_sitemap_html_mapping', // ID
            __('Enter a post, page or custom post type ID(s) to display the sitemap', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_mapping_callback'], // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_html_exclude', // ID
            __('Exclude some Posts, Pages, Custom Post Types or Terms IDs', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_exclude_callback'], // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_html_order', // ID
            __('Sort order', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_order_callback'], // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_html_orderby', // ID
            __('Order posts by', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_orderby_callback'], // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_html_date', // ID
            __('Disable the display of the publication date', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_date_callback'], // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section
        );

        add_settings_field(
            'seopress_xml_sitemap_html_archive_links', // ID
            __('Remove links from archive pages', 'wp-seopress'), // Title
            [$this, 'seopress_xml_sitemap_html_archive_links_callback'], // Callback
            'seopress-settings-admin-html-sitemap', // Page
            'seopress_setting_section_html_sitemap' // Section
        );

        //Knowledge graph SECTION======================================================================
        add_settings_section(
            'seopress_setting_section_social_knowledge', // ID
            '',
            //__("Knowledge graph","wp-seopress"), // Title
            [$this, 'print_section_info_social_knowledge'], // Callback
            'seopress-settings-admin-social-knowledge' // Page
        );

        add_settings_field(
            'seopress_social_knowledge_type', // ID
            __('Person or organization', 'wp-seopress'), // Title
            [$this, 'seopress_social_knowledge_type_callback'], // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section
        );

        add_settings_field(
            'seopress_social_knowledge_name', // ID
            __('Your name/organization', 'wp-seopress'), // Title
            [$this, 'seopress_social_knowledge_name_callback'], // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section
        );

        add_settings_field(
            'seopress_social_knowledge_img', // ID
            __('Your photo/organization logo', 'wp-seopress'), // Title
            [$this, 'seopress_social_knowledge_img_callback'], // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section
        );

        add_settings_field(
            'seopress_social_knowledge_phone', // ID
            __("Organization's phone number (only for Organizations)", 'wp-seopress'), // Title
            [$this, 'seopress_social_knowledge_phone_callback'], // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section
        );

        add_settings_field(
            'seopress_social_knowledge_contact_type', // ID
            __('Contact type (only for Organizations)', 'wp-seopress'), // Title
            [$this, 'seopress_social_knowledge_contact_type_callback'], // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section
        );

        add_settings_field(
            'seopress_social_knowledge_contact_option', // ID
            __('Contact option (only for Organizations)', 'wp-seopress'), // Title
            [$this, 'seopress_social_knowledge_contact_option_callback'], // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section
        );

        //Social SECTION=====================================================================================
        add_settings_section(
            'seopress_setting_section_social_accounts', // ID
            '',
            //__("Social","wp-seopress"), // Title
            [$this, 'print_section_info_social_accounts'], // Callback
            'seopress-settings-admin-social-accounts' // Page
        );

        add_settings_field(
            'seopress_social_accounts_facebook', // ID
            __('Facebook Page URL', 'wp-seopress'), // Title
            [$this, 'seopress_social_accounts_facebook_callback'], // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section
        );

        add_settings_field(
            'seopress_social_accounts_twitter', // ID
            __('Twitter Username', 'wp-seopress'), // Title
            [$this, 'seopress_social_accounts_twitter_callback'], // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section
        );

        add_settings_field(
            'seopress_social_accounts_pinterest', // ID
            __('Pinterest URL', 'wp-seopress'), // Title
            [$this, 'seopress_social_accounts_pinterest_callback'], // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section
        );

        add_settings_field(
            'seopress_social_accounts_instagram', // ID
            __('Instagram URL', 'wp-seopress'), // Title
            [$this, 'seopress_social_accounts_instagram_callback'], // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section
        );

        add_settings_field(
            'seopress_social_accounts_youtube', // ID
            __('YouTube URL', 'wp-seopress'), // Title
            [$this, 'seopress_social_accounts_youtube_callback'], // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section
        );

        add_settings_field(
            'seopress_social_accounts_linkedin', // ID
            __('LinkedIn URL', 'wp-seopress'), // Title
            [$this, 'seopress_social_accounts_linkedin_callback'], // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section
        );

        //Facebook SECTION=========================================================================
        add_settings_section(
            'seopress_setting_section_social_facebook', // ID
            '',
            //__("Facebook","wp-seopress"), // Title
            [$this, 'print_section_info_social_facebook'], // Callback
            'seopress-settings-admin-social-facebook' // Page
        );

        add_settings_field(
            'seopress_social_facebook_og', // ID
            __('Enable Open Graph Data', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_og_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        add_settings_field(
            'seopress_social_facebook_img', // ID
            __('Select a default image', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_img_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        add_settings_field(
            'seopress_social_facebook_img_default', // ID
            __('Apply this image to all your og:image tag', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_img_default_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        add_settings_field(
            'seopress_social_facebook_img_cpt', // ID
            __('Define custom og:image tag for post type archive pages', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_img_cpt_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        add_settings_field(
            'seopress_social_facebook_link_ownership_id', // ID
            __('Facebook Link Ownership ID', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_link_ownership_id_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        add_settings_field(
            'seopress_social_facebook_admin_id', // ID
            __('Facebook Admin ID', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_admin_id_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        add_settings_field(
            'seopress_social_facebook_app_id', // ID
            __('Facebook App ID', 'wp-seopress'), // Title
            [$this, 'seopress_social_facebook_app_id_callback'], // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section
        );

        //Twitter SECTION==========================================================================
        add_settings_section(
            'seopress_setting_section_social_twitter', // ID
            '',
            //__("Twitter","wp-seopress"), // Title
            [$this, 'print_section_info_social_twitter'], // Callback
            'seopress-settings-admin-social-twitter' // Page
        );

        add_settings_field(
            'seopress_social_twitter_card', // ID
            __('Enable Twitter Card', 'wp-seopress'), // Title
            [$this, 'seopress_social_twitter_card_callback'], // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section
        );

        add_settings_field(
            'seopress_social_twitter_card_og', // ID
            __('Use Open Graph if no Twitter Card is filled', 'wp-seopress'), // Title
            [$this, 'seopress_social_twitter_card_og_callback'], // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section
        );

        add_settings_field(
            'seopress_social_twitter_card_img', // ID
            __('Default Twitter Image', 'wp-seopress'), // Title
            [$this, 'seopress_social_twitter_card_img_callback'], // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section
        );

        add_settings_field(
            'seopress_social_twitter_card_img_size', // ID
            __('Image size for Twitter Summary card', 'wp-seopress'), // Title
            [$this, 'seopress_social_twitter_card_img_size_callback'], // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section
        );

        //Google Analytics Enable SECTION==========================================================
        add_settings_section(
            'seopress_setting_section_google_analytics_enable', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            [$this, 'print_section_info_google_analytics_enable'], // Callback
            'seopress-settings-admin-google-analytics-enable' // Page
        );

        add_settings_field(
            'seopress_google_analytics_enable', // ID
            __('Enable Google Analytics tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_enable_callback'], // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section
        );

        add_settings_field(
            'seopress_google_analytics_ua', // ID
            __('Enter your tracking ID', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_ua_callback'], // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section
        );

        add_settings_field(
            'seopress_google_analytics_ga4', // ID
            __('Enter your measurement ID (GA4)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_ga4_callback'], // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section
        );

        add_settings_field(
            'seopress_google_analytics_roles', // ID
            __('Exclude user roles from tracking (Google Analytics and Matomo)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_roles_callback'], // Callback
            'seopress-settings-admin-google-analytics-enable', // Page
            'seopress_setting_section_google_analytics_enable' // Section
        );

        //Cookie bar / GDPR SECTION================================================================
        add_settings_section(
            'seopress_setting_section_google_analytics_gdpr', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            [$this, 'print_section_info_google_analytics_gdpr'], // Callback
            'seopress-settings-admin-google-analytics-gdpr' // Page
        );

        add_settings_field(
            'seopress_google_analytics_hook', // ID
            __('Where to display the cookie bar?', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_hook_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_disable', // ID
            __('Analytics tracking opt-in', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_disable_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_half_disable', // ID
            '', // Title
            [$this, 'seopress_google_analytics_half_disable_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_opt_out_edit_choice', // ID
            __('Allow user to change its choice', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_opt_out_edit_choice_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_opt_out_msg', // ID
            __('Consent message for user tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_opt_out_msg_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_opt_out_msg_ok', // ID
            __('Accept button for user tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_opt_out_msg_ok_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_opt_out_msg_close', // ID
            __('Close button', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_opt_out_msg_close_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_opt_out_msg_edit', // ID
            __('Edit cookies button', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_opt_out_msg_edit_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_exp_date', // ID
            __('User consent cookie expiration date', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cb_exp_date_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_pos', // ID
            __('Cookie bar position', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cb_pos_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_txt_align', // ID
            __('Text alignment', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cb_txt_align_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_width', // ID
            __('Cookie bar width', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cb_width_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_backdrop', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_backdrop_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_backdrop_bg', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_backdrop_bg_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_bg', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_bg_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_txt_col', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_txt_col_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_lk_col', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_lk_col_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_bg', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_bg_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_bg_hov', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_bg_hov_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_col', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_col_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_col_hov', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_col_hov_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_sec_bg', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_sec_bg_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_sec_col', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_sec_col_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_sec_bg_hov', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_sec_bg_hov_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cb_btn_sec_col_hov', // ID
            '', // Title
            [$this, 'seopress_google_analytics_cb_btn_sec_col_hov_callback'], // Callback
            'seopress-settings-admin-google-analytics-gdpr', // Page
            'seopress_setting_section_google_analytics_gdpr' // Section
        );

        //Google Analytics Tracking SECTION========================================================

        add_settings_section(
            'seopress_setting_section_google_analytics_features', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            [$this, 'print_section_info_google_analytics_features'], // Callback
            'seopress-settings-admin-google-analytics-features' // Page
        );

        add_settings_field(
            'seopress_google_analytics_optimize', // ID
            __('Enable Google Optimize', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_optimize_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_ads', // ID
            __('Enable Google Ads', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_ads_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_other_tracking', // ID
            __('[HEAD] Add an additional tracking code (like Facebook Pixel, Hotjar...)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_other_tracking_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_other_tracking_body', // ID
            __('[BODY] Add an additional tracking code (like Google Tag Manager...)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_other_tracking_body_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_other_tracking_footer', // ID
            __('[BODY (FOOTER)] Add an additional tracking code (like Google Tag Manager...)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_other_tracking_footer_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_remarketing', // ID
            __('Enable remarketing, demographics, and interests reporting', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_remarketing_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_ip_anonymization', // ID
            __('Enable IP Anonymization', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_ip_anonymization_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_link_attribution', // ID
            __('Enhanced Link Attribution', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_link_attribution_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cross_domain_enable', // ID
            __('Enable cross-domain tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cross_enable_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cross_domain', // ID
            __('Cross domains', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cross_domain_callback'], // Callback
            'seopress-settings-admin-google-analytics-features', // Page
            'seopress_setting_section_google_analytics_features' // Section
        );

        //Google Analytics Events SECTION==========================================================

        add_settings_section(
            'seopress_setting_section_google_analytics_events', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            [$this, 'print_section_info_google_analytics_events'], // Callback
            'seopress-settings-admin-google-analytics-events' // Page
        );

        add_settings_field(
            'seopress_google_analytics_link_tracking_enable', // ID
            __('Enable external links tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_link_tracking_enable_callback'], // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section
        );

        add_settings_field(
            'seopress_google_analytics_download_tracking_enable', // ID
            __('Enable downloads tracking (eg: PDF, XLSX, DOCX...)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_download_tracking_enable_callback'], // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section
        );

        add_settings_field(
            'seopress_google_analytics_download_tracking', // ID
            __("Track downloads' clicks", 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_download_tracking_callback'], // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section
        );

        add_settings_field(
            'seopress_google_analytics_affiliate_tracking_enable', // ID
            __('Enable affiliate/outbound links tracking (eg: aff, go, out, recommends)', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_affiliate_tracking_enable_callback'], // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section
        );

        add_settings_field(
            'seopress_google_analytics_affiliate_tracking', // ID
            __('Track affiliate/outbound links', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_affiliate_tracking_callback'], // Callback
            'seopress-settings-admin-google-analytics-events', // Page
            'seopress_setting_section_google_analytics_events' // Section
        );

        //Google Analytics Custom Dimensions SECTION===============================================

        add_settings_section(
            'seopress_setting_section_google_analytics_custom_dimensions', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            [$this, 'print_section_info_google_analytics_custom_dimensions'], // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions' // Page
        );

        add_settings_field(
            'seopress_google_analytics_cd_author', // ID
            __('Track Authors', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cd_author_callback'], // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cd_category', // ID
            __('Track Categories', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cd_category_callback'], // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cd_tag', // ID
            __('Track Tags', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cd_tag_callback'], // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cd_post_type', // ID
            __('Track Post Types', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cd_post_type_callback'], // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section
        );

        add_settings_field(
            'seopress_google_analytics_cd_logged_in_user', // ID
            __('Track Logged In Users', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_cd_logged_in_user_callback'], // Callback
            'seopress-settings-admin-google-analytics-custom-dimensions', // Page
            'seopress_setting_section_google_analytics_custom_dimensions' // Section
        );

        //Matomo SECTION===========================================================================
        add_settings_section(
            'seopress_setting_section_google_analytics_matomo', // ID
            '',
            //__("Google Analytics","wp-seopress"), // Title
            [$this, 'print_section_info_google_analytics_matomo'], // Callback
            'seopress-settings-admin-google-analytics-matomo' // Page
        );

        add_settings_field(
            'seopress_google_analytics_matomo_enable', // ID
            __('Enable Matomo tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_enable_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_id', // ID
            __('Enter your tracking ID', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_id_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_site_id', // ID
            __('Enter your site ID', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_site_id_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_subdomains', // ID
            __('Track visitors across all subdomains', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_subdomains_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_site_domain', // ID
            __('Prepend the site domain', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_site_domain_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_no_js', // ID
            __('Track users with JavaScript disabled', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_no_js_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_cross_domain', // ID
            __('Enables cross domain linking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_cross_domain_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_cross_domain_sites', // ID
            __('Cross domain', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_cross_domain_sites_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );
        add_settings_field(
            'seopress_google_analytics_matomo_dnt', // ID
            __('Enable DoNotTrack detection', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_dnt_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_no_cookies', // ID
            __('Disable all tracking cookies', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_no_cookies_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_link_tracking', // ID
            __('Download & Outlink tracking', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_link_tracking_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        add_settings_field(
            'seopress_google_analytics_matomo_no_heatmaps', // ID
            __('Disable all heatmaps and session recordings', 'wp-seopress'), // Title
            [$this, 'seopress_google_analytics_matomo_no_heatmaps_callback'], // Callback
            'seopress-settings-admin-google-analytics-matomo', // Page
            'seopress_setting_section_google_analytics_matomo' // Section
        );

        //Image SECTION============================================================================
        add_settings_section(
            'seopress_setting_section_advanced_image', // ID
            '',
            //__("Image SEO","wp-seopress"), // Title
            [$this, 'print_section_info_advanced_image'], // Callback
            'seopress-settings-admin-advanced-image' // Page
        );

        add_settings_field(
            'seopress_advanced_advanced_attachments', // ID
            __('Redirect attachment pages to post parent', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_attachments_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_attachments_file', // ID
            __('Redirect attachment pages to their file URL', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_attachments_file_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_replytocom', // ID
            __('Remove ?replytocom link to avoid duplicate content', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_replytocom_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_image_auto_title_editor', // ID
            __('Automatically set the image Title', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_image_auto_title_editor_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_image_auto_alt_editor', // ID
            __('Automatically set the image Alt text', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_image_auto_alt_editor_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_image_auto_alt_target_kw', // ID
            __('Automatically set the image Alt text from target keywords', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_image_auto_alt_target_kw_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_image_auto_caption_editor', // ID
            __('Automatically set the image Caption', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_image_auto_caption_editor_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_image_auto_desc_editor', // ID
            __('Automatically set the image Description', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_image_auto_desc_editor_callback'], // Callback
            'seopress-settings-admin-advanced-image', // Page
            'seopress_setting_section_advanced_image' // Section
        );

        //Advanced SECTION=========================================================================
        add_settings_section(
            'seopress_setting_section_advanced_advanced', // ID
            '',
            //__("Advanced","wp-seopress"), // Title
            [$this, 'print_section_info_advanced_advanced'], // Callback
            'seopress-settings-admin-advanced-advanced' // Page
        );

        add_settings_field(
            'seopress_advanced_advanced_tax_desc_editor', // ID
            __('Add WP Editor to taxonomy description textarea', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_tax_desc_editor_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_category_url', // ID
            __('Remove /category/ in URL', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_category_url_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_trailingslash', // ID
            __('Disable trailing slash for metas', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_trailingslash_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_generator', // ID
            __('Remove WordPress generator meta tag', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_wp_generator_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_hentry', // ID
            __('Remove hentry post class', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_hentry_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_comments_author_url', // ID
            __('Remove author URL', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_comments_author_url_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_comments_website', // ID
            __('Remove website field in comment form', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_comments_website_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_shortlink', // ID
            __('Remove WordPress shortlink meta tag', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_wp_shortlink_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_wlw', // ID
            __('Remove Windows Live Writer meta tag', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_wp_wlw_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_wp_rsd', // ID
            __('Remove RSD meta tag', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_wp_rsd_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_google', // ID
            __('Google site verification', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_google_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_bing', // ID
            __('Bing site verification', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_bing_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_pinterest', // ID
            __('Pinterest site verification', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_pinterest_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        add_settings_field(
            'seopress_advanced_advanced_yandex', // ID
            __('Yandex site verification', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_advanced_yandex_callback'], // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section
        );

        //Appearance SECTION=======================================================================
        add_settings_section(
            'seopress_setting_section_advanced_appearance', // ID
            '',
            //__("Appearance","wp-seopress"), // Title
            [$this, 'print_section_info_advanced_appearance'], // Callback
            'seopress-settings-admin-advanced-appearance' // Page
        );

        add_settings_field(
            'seopress_advanced_appearance_adminbar', // ID
            __('SEO in admin bar', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_adminbar_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_adminbar_noindex', // ID
            __('Noindex in admin bar', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_adminbar_noindex_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_metabox_position', // ID
            __("Move SEO metabox's position", 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_metaboxe_position_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            add_settings_field(
                'seopress_advanced_appearance_schema_default_tab', // ID
                __('Set default tab for Structured data metabox', 'wp-seopress'), // Title
                [$this, 'seopress_advanced_appearance_schema_default_tab_callback'], // Callback
                'seopress-settings-admin-advanced-appearance', // Page
                'seopress_setting_section_advanced_appearance' // Section
            );
        }

        add_settings_field(
            'seopress_advanced_appearance_notifications', // ID
            __('Hide Notifications Center', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_notifications_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_seo_tools', // ID
            __('Hide SEO tools', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_seo_tools_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_useful_links', // ID
            __('Hide Useful Links', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_useful_links_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_title_col', // ID
            __('Show Title tag column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_title_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_meta_desc_col', // ID
            __('Show Meta description column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_meta_desc_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_redirect_enable_col', // ID
            __('Show Redirection Enable column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_redirect_enable_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_redirect_url_col', // ID
            __('Show Redirect URL column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_redirect_url_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_canonical', // ID
            __('Show canonical URL column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_canonical_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_target_kw_col', // ID
            __('Show Target Keyword column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_target_kw_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_noindex_col', // ID
            __('Show noindex column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_noindex_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_nofollow_col', // ID
            __('Show nofollow column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_nofollow_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_words_col', // ID
            __('Show total number of words column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_words_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_w3c_col', // ID
            __('Show W3C validator column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_w3c_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            add_settings_field(
                'seopress_advanced_appearance_ps_col', // ID
                __('Show Google Page Speed column in post types', 'wp-seopress'), // Title
                [$this, 'seopress_advanced_appearance_ps_col_callback'], // Callback
                'seopress-settings-admin-advanced-appearance', // Page
                'seopress_setting_section_advanced_appearance' // Section
            );
        }

        if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
            add_settings_field(
                'seopress_advanced_appearance_insights_col', // ID
                __('Show Insights column in post types', 'wp-seopress'), // Title
                [$this, 'seopress_advanced_appearance_insights_col_callback'], // Callback
                'seopress-settings-admin-advanced-appearance', // Page
                'seopress_setting_section_advanced_appearance' // Section
            );
        }

        add_settings_field(
            'seopress_advanced_appearance_score_col', // ID
            __('Show content analysis score column in post types', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_score_col_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_genesis_seo_metaboxe', // ID
            __('Hide Genesis SEO Metabox', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_genesis_seo_metaboxe_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_genesis_seo_menu', // ID
            __('Hide Genesis SEO Settings link', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_genesis_seo_menu_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        add_settings_field(
            'seopress_advanced_appearance_advice_schema', // ID
            __('Hide advice in Structured Data Types metabox', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_appearance_advice_schema_callback'], // Callback
            'seopress-settings-admin-advanced-appearance', // Page
            'seopress_setting_section_advanced_appearance' // Section
        );

        //Security SECTION=======================================================================
        add_settings_section(
            'seopress_setting_section_advanced_security', // ID
            '',
            //__("Security","wp-seopress"), // Title
            [$this, 'print_section_info_advanced_security'], // Callback
            'seopress-settings-admin-advanced-security' // Page
        );

        add_settings_field(
            'seopress_advanced_security_metaboxe_role', // ID
            __('Block SEO metabox to user roles', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_security_metaboxe_role_callback'], // Callback
            'seopress-settings-admin-advanced-security', // Page
            'seopress_setting_section_advanced_security' // Section
        );

        add_settings_field(
            'seopress_advanced_security_metaboxe_ca_role', // ID
            __('Block Content analysis metabox to user roles', 'wp-seopress'), // Title
            [$this, 'seopress_advanced_security_metaboxe_ca_role_callback'], // Callback
            'seopress-settings-admin-advanced-security', // Page
            'seopress_setting_section_advanced_security' // Section
        );

        //Tools SECTION=======================================================================
        add_settings_section(
            'seopress_setting_section_tools_compatibility', // ID
            '',
            //__("Compatibility Center","wp-seopress"), // Title
            [$this, 'print_section_info_tools_compatibility'], // Callback
            'seopress-settings-admin-tools-compatibility' // Page
        );

        add_settings_field(
            'seopress_setting_section_tools_compatibility_oxygen', // ID
            __('Oxygen Builder compatibility', 'wp-seopress'), // Title
            [$this, 'seopress_setting_section_tools_compatibility_oxygen_callback'], // Callback
            'seopress-settings-admin-tools-compatibility', // Page
            'seopress_setting_section_tools_compatibility' // Section
        );

        add_settings_field(
            'seopress_setting_section_tools_compatibility_divi', // ID
            __('Divi Builder compatibility', 'wp-seopress'), // Title
            [$this, 'seopress_setting_section_tools_compatibility_divi_callback'], // Callback
            'seopress-settings-admin-tools-compatibility', // Page
            'seopress_setting_section_tools_compatibility' // Section
        );

        add_settings_field(
            'seopress_setting_section_tools_compatibility_bakery', // ID
            __('WP Bakery Builder compatibility', 'wp-seopress'), // Title
            [$this, 'seopress_setting_section_tools_compatibility_bakery_callback'], // Callback
            'seopress-settings-admin-tools-compatibility', // Page
            'seopress_setting_section_tools_compatibility' // Section
        );

        add_settings_field(
            'seopress_setting_section_tools_compatibility_avia', // ID
            __('Avia Layout Builder compatibility', 'wp-seopress'), // Title
            [$this, 'seopress_setting_section_tools_compatibility_avia_callback'], // Callback
            'seopress-settings-admin-tools-compatibility', // Page
            'seopress_setting_section_tools_compatibility' // Section
        );

        add_settings_field(
            'seopress_setting_section_tools_compatibility_fusion', // ID
            __('Fusion Builder compatibility', 'wp-seopress'), // Title
            [$this, 'seopress_setting_section_tools_compatibility_fusion_callback'], // Callback
            'seopress-settings-admin-tools-compatibility', // Page
            'seopress_setting_section_tools_compatibility' // Section
        );
    }

    /**
     * Sanitize each setting field as needed.
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input) {
        $seopress_sanitize_fields = [
            'seopress_titles_home_site_title',
            'seopress_titles_home_site_desc',
            'seopress_titles_archives_author_title',
            'seopress_titles_archives_author_desc',
            'seopress_titles_archives_date_title',
            'seopress_titles_archives_date_desc',
            'seopress_titles_archives_search_title',
            'seopress_titles_archives_search_desc',
            'seopress_titles_archives_404_title',
            'seopress_titles_archives_404_desc',
            'seopress_xml_sitemap_html_exclude',
            'seopress_social_knowledge_name',
            'seopress_social_knowledge_img',
            'seopress_social_knowledge_phone',
            'seopress_social_accounts_facebook',
            'seopress_social_accounts_twitter',
            'seopress_social_accounts_pinterest',
            'seopress_social_accounts_instagram',
            'seopress_social_accounts_youtube',
            'seopress_social_accounts_linkedin',
            'seopress_social_facebook_link_ownership_id',
            'seopress_social_facebook_admin_id',
            'seopress_social_facebook_app_id',
            'seopress_google_analytics_ua',
            'seopress_google_analytics_ga4',
            'seopress_google_analytics_download_tracking',
            'seopress_google_analytics_opt_out_msg',
            'seopress_google_analytics_opt_out_msg_ok',
            'seopress_google_analytics_opt_out_msg_close',
            'seopress_google_analytics_opt_out_msg_edit',
            'seopress_google_analytics_other_tracking',
            'seopress_google_analytics_other_tracking_body',
            'seopress_google_analytics_optimize',
            'seopress_google_analytics_ads',
            'seopress_google_analytics_cross_domain',
            'seopress_google_analytics_matomo_id',
            'seopress_google_analytics_matomo_site_id',
            'seopress_google_analytics_matomo_cross_domain_sites',
            'seopress_google_analytics_cb_backdrop_bg',
            'seopress_google_analytics_cb_exp_date',
            'seopress_google_analytics_cb_bg',
            'seopress_google_analytics_cb_txt_col',
            'seopress_google_analytics_cb_lk_col',
            'seopress_google_analytics_cb_btn_bg',
            'seopress_google_analytics_cb_btn_col',
            'seopress_google_analytics_cb_btn_bg_hov',
            'seopress_google_analytics_cb_btn_col_hov',
            'seopress_google_analytics_cb_btn_sec_bg',
            'seopress_google_analytics_cb_btn_sec_col',
            'seopress_google_analytics_cb_btn_sec_bg_hov',
            'seopress_google_analytics_cb_btn_sec_col_hov',
            'seopress_google_analytics_cb_width',
        ];

        $seopress_esc_attr = [
            'seopress_titles_sep',
        ];

        $seopress_sanitize_site_verification = [
            'seopress_advanced_advanced_google',
            'seopress_advanced_advanced_bing',
            'seopress_advanced_advanced_pinterest',
            'seopress_advanced_advanced_yandex',
        ];

        foreach ($seopress_sanitize_fields as $value) {
            if ( ! empty($input['seopress_google_analytics_opt_out_msg']) && 'seopress_google_analytics_opt_out_msg' == $value) {
                $args = [
                        'strong' => [],
                        'em'     => [],
                        'br'     => [],
                        'a'      => [
                            'href'   => [],
                            'target' => [],
                        ],
                ];
                $input[$value] = wp_kses($input[$value], $args);
            } elseif (( ! empty($input['seopress_google_analytics_other_tracking']) && 'seopress_google_analytics_other_tracking' == $value) || ( ! empty($input['seopress_google_analytics_other_tracking_body']) && 'seopress_google_analytics_other_tracking_body' == $value) || ( ! empty($input['seopress_google_analytics_other_tracking_footer']) && 'seopress_google_analytics_other_tracking_footer' == $value)) {
                $input[$value] = $input[$value]; //No sanitization for this field
            } elseif ( ! empty($input[$value])) {
                $input[$value] = sanitize_text_field($input[$value]);
            }
        }

        foreach ($seopress_esc_attr as $value) {
            if ( ! empty($input[$value])) {
                $input[$value] = esc_attr($input[$value]);
            }
        }

        foreach ($seopress_sanitize_site_verification as $value) {
            if ( ! empty($input[$value])) {
                if (preg_match('#content=\'([^"]+)\'#', $input[$value], $m)) {
                    $input[$value] = esc_attr($m[1]);
                } elseif (preg_match('#content="([^"]+)"#', $input[$value], $m)) {
                    $input[$value] = esc_attr($m[1]);
                } else {
                    $input[$value] = esc_attr($input[$value]);
                }
            }
        }

        return $input;
    }

    /**
     * Print the Section text.
     */
    public function print_section_info_titles() {
        echo __('<p>Customize your title & meta description for homepage</p>', 'wp-seopress');

        echo "<script>function sp_get_field_length(e) {
			if (e.val().length > 0) {
				meta = e.val() + ' ';
			} else {
				meta = e.val();
			}
			return meta;
		}</script>";
    }

    public function print_section_info_single() {
        echo __('<p>Customize your titles & metas for Single Custom Post Types</p>', 'wp-seopress');
    }

    public function print_section_info_advanced() {
        echo __('<p>Customize your metas for all pages</p>', 'wp-seopress');
    }

    public function print_section_info_tax() {
        echo __('<p>Customize your metas for all taxonomies archives</p>', 'wp-seopress');
    }

    public function print_section_info_archives() {
        echo __('<p>Customize your metas for all archives</p>', 'wp-seopress');
    }

    public function print_section_info_xml_sitemap_general() {
        if ('' == get_option('permalink_structure')) {
            echo '<div class="error notice is-dismissable">';
            echo '<p>' . __('Your permalinks are not SEO Friendly! Enable pretty permalinks to fix this.', 'wp-seopress');
            echo ' <a href="' . admin_url('options-permalink.php') . '">' . __('Change this settings', 'wp-seopress') . '</a></p>';
            echo '</div>';
        }
        echo '<p>' . __('To view your sitemap, enable permalinks (not default one), and save settings to flush them.', 'wp-seopress') . '</p>';

        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $server_software = explode('/', $_SERVER['SERVER_SOFTWARE']);
            reset($server_software);
            if ('nginx' == current($server_software)) { //IF NGINX
                echo '<p>' . __('Your server uses NGINX. If XML Sitemaps doesn\'t work properly, you need to add this rule to your configuration:', 'wp-seopress') . '</p><br>';
                echo '<pre style="margin:0;padding:10px;font-weight: bold;background:#F3F3F3;display:inline-block;width: 100%">
					location ~ ([^/]*)sitemap(.*)\.x(m|s)l$ {
						## SEOPress
						rewrite ^.*/sitemaps\.xml$ /index.php?seopress_sitemap=1 last;
						rewrite ^.*/sitemaps/news.xml$ /index.php?seopress_news=$1 last;
						rewrite ^.*/sitemaps/video.xml$ /index.php?seopress_video=$1 last;
						rewrite ^.*/sitemaps/author.xml$ /index.php?seopress_author=$1 last;
						rewrite ^.*/sitemaps_xsl\.xsl$ /index.php?seopress_sitemap_xsl=1 last;
						rewrite ^.*/sitemaps/([^/]+?)-sitemap([0-9]+)?.xml$ /index.php?seopress_cpt=$1&seopress_paged=$2 last;
					}
				</pre>';
            }
        }
        echo '<p>' . __('Noindex content will not be displayed in Sitemaps.', 'wp-seopress') . '</p>';
        echo '<p>' . __('If you disable globally this feature (using the blue toggle from above), the native WordPress XML sitemaps will be re-activated.', 'wp-seopress') . '</p>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['sitemaps']['error']['blank'] = 'https://www.seopress.org/fr/support/guides/xml-sitemap-page-blanche/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            $seopress_docs_link['sitemaps']['error']['404']   = 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-retourne-erreur-404/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            $seopress_docs_link['sitemaps']['error']['html']  = 'https://www.seopress.org/fr/support/guides/exclure-fichiers-xml-xsl-extensions-cache/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['sitemaps']['error']['blank'] = 'https://www.seopress.org/support/guides/xml-sitemap-blank-page/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            $seopress_docs_link['sitemaps']['error']['404']   = 'https://www.seopress.org/support/guides/xml-sitemap-returns-404-error/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            $seopress_docs_link['sitemaps']['error']['html']  = 'https://www.seopress.org/support/guides/how-to-exclude-xml-and-xsl-files-from-caching-plugins/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<p class="seopress-help"><span class="dashicons dashicons-external"></span><a href="' . $seopress_docs_link['sitemaps']['error']['blank'] . '" target="_blank">' . __('Blank sitemap?', 'wp-seopress') . '</a> - ';
        echo '<span class="dashicons dashicons-external"></span><a href="' . $seopress_docs_link['sitemaps']['error']['404'] . '" target="_blank">' . __('404 error?', 'wp-seopress') . '</a> - ';
        echo '<span class="dashicons dashicons-external"></span><a href="' . $seopress_docs_link['sitemaps']['error']['html'] . '" target="_blank">' . __('HTML error? Exclude XML and XSL from caching plugins!', 'wp-seopress') . '</a></p><br>';

        echo '<a href="' . get_option('home') . '/sitemaps.xml" target="_blank" class="button"><span class="dashicons dashicons-visibility"></span>' . __('View your sitemap', 'wp-seopress') . '</a>';
        echo '&nbsp;';
        echo '<a href="https://www.google.com/ping?sitemap=' . get_option('home') . '/sitemaps.xml/" target="_blank" class="button"><span class="dashicons dashicons-share-alt2"></span>' . __('Ping Google manually', 'wp-seopress') . '</a>';
        echo '&nbsp;';
        echo '<button type="button" id="seopress-flush-permalinks" class="button"><span class="dashicons dashicons-admin-links"></span>' . __('Flush permalinks', 'wp-seopress') . '</button>';
        echo '<span class="spinner"></span>';
    }

    public function print_section_info_html_sitemap() {
        echo __('<p>Create an HTML Sitemap for your visitors and boost your SEO.</p>', 'wp-seopress');
        echo __('<p>Limited to 1,000 posts per post type. You can change the order and sorting criteria below.</p>', 'wp-seopress');

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['sitemaps']['html'] = 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-html/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['sitemaps']['html'] = 'https://www.seopress.org/support/guides/enable-html-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a class="seopress-doc" href="' . $seopress_docs_link['sitemaps']['html'] . '" target="_blank"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Guide to enable a HTML Sitemap - new window', 'wp-seopress') . '</span></a></p>';
    }

    public function print_section_info_xml_sitemap_post_types() {
        echo __('<p>Include/Exclude Post Types.</p>', 'wp-seopress');
    }

    public function print_section_info_xml_sitemap_taxonomies() {
        echo __('<p>Include/Exclude Taxonomies.</p>', 'wp-seopress');
    }

    public function print_section_info_social_knowledge() {
        echo __('<p>Configure Google Knowledge Graph.</p>', 'wp-seopress');
        echo '<p class="seopress-help"><span class="dashicons dashicons-external"></span><a href="https://developers.google.com/search/docs/guides/enhance-site" target="_blank">' . __('Learn more on Google official website.', 'wp-seopress') . '</a></p>';
    }

    public function print_section_info_social_accounts() {
        echo __('<p>Link your site with your social accounts. Use markup on your website to add your social profile information to a Google Knowledge panel. Knowledge panels prominently display your social profile information in some Google Search results. Filling in these fields does not guarantee the display of this data in search results. It may take a long time to see these social-network links.</p>', 'wp-seopress');
    }

    public function print_section_info_social_facebook() {
        echo __('<p>Manage Open Graph data.</p>', 'wp-seopress');

        echo __('<p>We generate the <strong>og:image</strong> meta in this order:</p>', 'wp-seopress');

        echo '
		<ol>
			<li>' . __('Custom OG Image from SEO metabox', 'wp-seopress') . '</li>
			<li>' . __('Post thumbnail / Product category thumbnail', 'wp-seopress') . '</li>
			<li>' . __('First image of your post content', 'wp-seopress') . '</li>
			<li>' . __('Global OG Image set in SEO > Social > Open Graph', 'wp-seopress') . '</li>
		</ol>';
    }

    public function print_section_info_social_twitter() {
        echo __('<p>Manage your Twitter card.</p>', 'wp-seopress');

        echo __('<p>We generate the <strong>twitter:image</strong> meta in this order:</p>', 'wp-seopress');

        echo '
		<ol>
			<li>' . __('Custom Twitter image from SEO metabox', 'wp-seopress') . '</li>
			<li>' . __('Post thumbnail / Product category thumbnail', 'wp-seopress') . '</li>
			<li>' . __('First image of your post content', 'wp-seopress') . '</li>
			<li>' . __('Global Twitter:image set in SEO > Social > Twitter Card', 'wp-seopress') . '</li>
		</ol>';
    }

    public function print_section_info_google_analytics_enable() {
        echo __('<p>Link your Google Analytics to your website. The tracking code will be automatically added to your site.</p>', 'wp-seopress');
    }

    public function print_section_info_google_analytics_gdpr() {
        echo __('<p>Manage user consent for GDPR and customize your cookie bar easily.</p>', 'wp-seopress');
        echo '<p>' . __('Works with <strong>Google Analytics</strong> and <strong>Matomo</strong>.', 'wp-seopress') . '</p>';
    }

    public function print_section_info_google_analytics_features() {
        echo __('<p>Configure your Google Analytics tracking code.</p>', 'wp-seopress');
    }

    public function print_section_info_google_analytics_events() {
        echo __('<p>Track events in Google Analytics.</p>', 'wp-seopress');
    }

    public function print_section_info_google_analytics_custom_dimensions() {
        echo __('<p>Configure your Google Analytics custom dimensions. <br>Custom dimensions and custom metrics are like the default dimensions and metrics in your Analytics account, except you create them yourself.<br> Use them to collect and analyze data that Analytics doesn\'t automatically track.<br> Please note that you also have to setup your custom dimensions in your Google Analytics account. More info by clicking on the help icon.', 'wp-seopress');

        echo '<p>' . __('Custom dimensions also work with <strong>Matomo</strong> tracking code.', 'wp-seopress') . '</p>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['analytics']['custom_dimensions'] = 'https://www.seopress.org/fr/support/guides/creer-dimensions-personnalisees-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['analytics']['custom_dimensions'] = 'https://www.seopress.org/support/guides/create-custom-dimension-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a class="seopress-doc" href="' . $seopress_docs_link['support']['analytics']['custom_dimensions'] . '" target="_blank"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Guide to create custom dimensions in Google Analytics - new window', 'wp-seopress') . '</span></a></p>';
    }

    public function print_section_info_google_analytics_matomo() {
        echo '<p>' . __('Use Matomo to track your users with privacy in mind.', 'wp-seopress') . '</p>';

        echo '<p>' . __('Your <strong>Custom Dimensions</strong> will also work with Matomo tracking code.', 'wp-seopress') . '</p>';
    }

    public function print_section_info_advanced_image() {
        echo '<p>' . __('Image SEO options.', 'wp-seopress') . '</p>';
    }

    public function print_section_info_advanced_advanced() {
        echo '<p>' . __('Advanced SEO options.', 'wp-seopress') . '</p>';
    }

    public function print_section_info_advanced_appearance() {
        echo '<p>' . __('Customize the plugin to fit your needs.', 'wp-seopress') . '</p>';
    }

    public function print_section_info_advanced_security() {
        echo '<p>' . __('Manage security.', 'wp-seopress') . '</p>';
    }

    public function print_section_info_tools_compatibility() {
        echo '<p>' . __('Our <strong>Compatibility Center</strong> makes it easy to integrate SEOPress with your favorite tools.', 'wp-seopress') . '</p>';
        echo '<p>' . __('Even though a lot of things are completely transparent to you and automated, sometimes it is necessary to leave the final choice to you.', 'wp-seopress') . '</p>';
        echo '<p style="color:red">' . __('<span class="dashicons dashicons-info"></span> <strong>Warning</strong>: always test your site after activating one of these options. Running shortcodes to automatically generate meta title / description can have side effects. Clear your cache if necessary.', 'wp-seopress') . '</p>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['compatibility']['automatic'] = 'https://www.seopress.org/fr/support/guides/generez-automatiquement-meta-descriptions-divi-oxygen-builder/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['compatibility']['automatic'] = 'https://www.seopress.org/support/guides/generate-automatic-meta-description-from-page-builders/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a class="seopress-doc" href="' . $seopress_docs_link['support']['compatibility']['automatic'] . '" target="_blank"><span class="dashicons dashicons-info" title="' . __('Learn more about automatic meta descriptions', 'wp-seopress-pro') . '"></span></a></p>';
    }

    /**
     * Get the settings option array and print one of its values.
     */

    //Titles & metas
    public function seopress_titles_sep_callback() {
        $check = isset($this->options['seopress_titles_sep']) ? $this->options['seopress_titles_sep'] : null;

        printf(
            '<input type="text" id="seopress_titles_sep" name="seopress_titles_option_name[seopress_titles_sep]" placeholder="' . esc_html__('Enter your separator, eg: "-"', 'wp-seopress') . '" aria-label="' . __('Separator', 'wp-seopress') . '" value="%s"/>',
            esc_html($check)
        );

        echo '<p class="description">' . __('Use this separator with %%sep%% in your title and meta description.', 'wp-seopress') . '</p>';
    }

    public function seopress_titles_home_site_title_callback() {
        printf(
            '<input type="text" id="seopress_titles_home_site_title" name="seopress_titles_option_name[seopress_titles_home_site_title]" placeholder="' . esc_html__('My awesome website', 'wp-seopress') . '" aria-label="' . __('Site title', 'wp-seopress') . '" value="%s"/>',
            esc_html($this->options['seopress_titles_home_site_title'])
        );
        echo '<div class="wrap-tags"><span id="seopress-tag-site-title" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-site-sep" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-site-desc" data-tag="%%tagline%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Tagline', 'wp-seopress') . '</span>';

        echo seopress_render_dyn_variables('tag-title');
    }

    public function seopress_titles_home_site_desc_callback() {
        printf(
        '<textarea id="seopress_titles_home_site_desc" name="seopress_titles_option_name[seopress_titles_home_site_desc]" placeholder="' . esc_html__('This is a cool website about Wookiees', 'wp-seopress') . '" aria-label="' . __('Meta description', 'wp-seopress') . '">%s</textarea>',
        esc_html($this->options['seopress_titles_home_site_desc'])
        );
        echo '<div class="wrap-tags"><span id="seopress-tag-meta-desc" data-tag="%%tagline%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Tagline', 'wp-seopress') . '</span>';

        echo seopress_render_dyn_variables('tag-description');

        if (get_option('page_for_posts')) {
            echo '<p><a href="' . admin_url('post.php?post=' . get_option('page_for_posts') . '&action=edit') . '">' . __('Looking to edit your blog page?', 'wp-seopress') . '</a></p>';
        }
    }

    //Single CPT
    public function seopress_titles_single_titles_callback() {
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            echo '<h2>' . $seopress_cpt_value->labels->name . ' <em><small>[' . $seopress_cpt_value->name . ']</small></em></h2>';

            //Single on/off CPT
            echo '<div class="seopress_wrap_single_cpt">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['enable']) ? $options['seopress_titles_single_titles'][$seopress_cpt_key]['enable'] : null;

            echo '<input id="seopress_titles_single_cpt_enable[' . $seopress_cpt_key . ']" data-id=' . $seopress_cpt_key . ' name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][enable]" class="toggle" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes" data-toggle="0"';
            } else {
                echo 'data-toggle="1"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_single_cpt_enable[' . $seopress_cpt_key . ']">' . __('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress') . '</label>';

            if ('1' == $check) {
                echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes / columns for this post type', 'wp-seopress') . '</span>';
                echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress') . '</span>';
            } else {
                echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress') . '</span>';
                echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes / columns for this post type', 'wp-seopress') . '</span>';
            }

            $toggle_txt_on  = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes / columns for this post type', 'wp-seopress');
            $toggle_txt_off = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes / columns for this post type', 'wp-seopress');

            echo "<script>
				jQuery(document).ready(function($) {
					$('input[data-id=" . $seopress_cpt_key . "]').on('click', function() {
						$(this).attr('data-toggle', $(this).attr('data-toggle') == '1' ? '0' : '1');
						if ($(this).attr('data-toggle') == '1') {
							$(this).next().next('.feature-state').html('" . $toggle_txt_off . "');
						} else {
							$(this).next().next('.feature-state').html('" . $toggle_txt_on . "');
						}
					});
				});
				</script>";

            if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['enable'])) {
                esc_attr($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['enable']);
            }

            echo '</div>';

            //Single Title CPT
            echo '<div class="seopress_wrap_single_cpt">';

            _e('Title template', 'wp-seopress');

            $check = isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['title']) ? $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['title'] : null;

            echo '<br/>';

            echo "<script>
					jQuery(document).ready(function($) {
						$('#seopress-tag-single-title-" . $seopress_cpt_key . "').click(function() {
							$('#seopress_titles_single_titles_" . $seopress_cpt_key . "').val(sp_get_field_length($('#seopress_titles_single_titles_" . $seopress_cpt_key . "')) + $('#seopress-tag-single-title-" . $seopress_cpt_key . "').attr('data-tag'));
						});
						$('#seopress-tag-sep-" . $seopress_cpt_key . "').click(function() {
							$('#seopress_titles_single_titles_" . $seopress_cpt_key . "').val(sp_get_field_length($('#seopress_titles_single_titles_" . $seopress_cpt_key . "')) + $('#seopress-tag-sep-" . $seopress_cpt_key . "').attr('data-tag'));
						});
						$('#seopress-tag-single-sitetitle-" . $seopress_cpt_key . "').click(function() {
							$('#seopress_titles_single_titles_" . $seopress_cpt_key . "').val(sp_get_field_length($('#seopress_titles_single_titles_" . $seopress_cpt_key . "')) + $('#seopress-tag-single-sitetitle-" . $seopress_cpt_key . "').attr('data-tag'));
						});
					});
				</script>";

            printf(
                '<input type="text" id="seopress_titles_single_titles_' . $seopress_cpt_key . '" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][title]" value="%s"/>',
                esc_html($check)
                );

            echo '<div class="wrap-tags"><span id="seopress-tag-single-title-' . $seopress_cpt_key . '" data-tag="%%post_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Post Title', 'wp-seopress') . '</span>';

            echo '<span id="seopress-tag-sep-' . $seopress_cpt_key . '" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';

            echo '<span id="seopress-tag-single-sitetitle-' . $seopress_cpt_key . '" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';

            echo seopress_render_dyn_variables('tag-title');

            echo '</div>';

            //Single Meta Description CPT
            echo '<div class="seopress_wrap_single_cpt">';

            _e('Meta description template', 'wp-seopress');
            echo '<br/>';

            $check = isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['description']) ? $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['description'] : null;

            printf(
                '<textarea name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][description]">%s</textarea>',
                esc_html($check)
                );

            echo '</div>';

            //Single No-Index CPT
            echo '<div class="seopress_wrap_single_cpt">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);

            echo '<input id="seopress_titles_single_cpt_noindex[' . $seopress_cpt_key . ']" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][noindex]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_single_cpt_noindex[' . $seopress_cpt_key . ']">' . __('Do not display this single post type in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

            $cpt_in_sitemap = seopress_get_service('SitemapOption')->getPostTypesList();

            if ('1' == $check && isset($cpt_in_sitemap[$seopress_cpt_key]) && '1' === $cpt_in_sitemap[$seopress_cpt_key]['include']) {
                echo '<p class="seopress-notice error notice">' . __('This custom post type is <strong>NOT</strong> excluded from your XML sitemaps despite the fact that it is set to <strong>NOINDEX</strong>. We recommend that you check this out.', 'wp-seopress') . '</p><br>';
            }

            if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
                esc_attr($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);
            }

            echo '</div>';

            //Single No-Follow CPT
            echo '<div class="seopress_wrap_single_cpt">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);

            echo '<input id="seopress_titles_single_cpt_nofollow[' . $seopress_cpt_key . ']" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][nofollow]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_single_cpt_nofollow[' . $seopress_cpt_key . ']">' . __('Do not follow links for this single post type <strong>(nofollow)</strong>', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow'])) {
                esc_attr($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);
            }

            echo '</div>';

            //Single Published / modified date CPT
            echo '<div class="seopress_wrap_single_cpt">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);

            echo '<input id="seopress_titles_single_cpt_date[' . $seopress_cpt_key . ']" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][date]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_single_cpt_date[' . $seopress_cpt_key . ']">' . __('Display date in Google search results by adding <code>article:published_time</code> and <code>article:modified_time</code> meta?', 'wp-seopress') . '</label>';

            echo '<p class="description">' . __('Unchecking this doesn\'t prevent Google to display post date in search results.', 'wp-seopress') . '</p><br>';

            if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['date'])) {
                esc_attr($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);
            }

            echo '</div>';

            //Single meta thumbnail CPT
            echo '<div class="seopress_wrap_single_cpt">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['thumb_gcs']);

            echo '<input id="seopress_titles_single_cpt_thumb_gcs[' . $seopress_cpt_key . ']" name="seopress_titles_option_name[seopress_titles_single_titles][' . $seopress_cpt_key . '][thumb_gcs]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_single_cpt_thumb_gcs[' . $seopress_cpt_key . ']">' . __('Display post thumbnail in Google Custom Search results?', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['thumb_gcs'])) {
                esc_attr($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['thumb_gcs']);
            }

            echo '</div>';
        }
    }

    //BuddyPress Groups
    public function seopress_titles_bp_groups_title_callback() {
        if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
            echo '<h2>' . __('BuddyPress groups', 'wp-seopress') . '</h2>';

            _e('Title template', 'wp-seopress');
            echo '<br/>';

            $check = isset($this->options['seopress_titles_bp_groups_title']) ? $this->options['seopress_titles_bp_groups_title'] : null;

            printf(
            '<input id="seopress_titles_bp_groups_title" type="text" name="seopress_titles_option_name[seopress_titles_bp_groups_title]" value="%s"/>',
            esc_html($check)
            );

            echo '<div class="wrap-tags"><span id="seopress-tag-post-title-bd-groups" data-tag="%%post_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Post Title', 'wp-seopress') . '</span>';
            echo '<span id="seopress-tag-sep-bd-groups" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';
            echo '<span id="seopress-tag-site-title-bd-groups" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';
            echo seopress_render_dyn_variables('tag-title');
        }
    }

    public function seopress_titles_bp_groups_desc_callback() {
        if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
            _e('Meta description template', 'wp-seopress');
            echo '<br/>';

            $check = isset($this->options['seopress_titles_bp_groups_desc']) ? $this->options['seopress_titles_bp_groups_desc'] : null;

            printf(
            '<textarea name="seopress_titles_option_name[seopress_titles_bp_groups_desc]">%s</textarea>',
            esc_html($check)
            );
        }
    }

    public function seopress_titles_bp_groups_noindex_callback() {
        if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_bp_groups_noindex']);

            echo '<input id="seopress_titles_bp_groups_noindex" name="seopress_titles_option_name[seopress_titles_bp_groups_noindex]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_bp_groups_noindex">' . __('Do not display BuddyPress groups in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_titles_bp_groups_noindex'])) {
                esc_attr($this->options['seopress_titles_bp_groups_noindex']);
            }
        }
    }

    //Taxonomies
    public function seopress_titles_tax_titles_callback() {
        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
            echo '<h2>' . $seopress_tax_value->labels->name . ' <em><small>[' . $seopress_tax_value->name . ']</small></em></h2>';

            //Single on/off Tax
            echo '<div class="seopress_wrap_tax">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['enable']) ? $options['seopress_titles_tax_titles'][$seopress_tax_key]['enable'] : null;

            echo '<input id="seopress_titles_tax_titles_enable[' . $seopress_tax_key . ']" data-id=' . $seopress_tax_key . ' name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][enable]" class="toggle" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes" data-toggle="0"';
            } else {
                echo 'data-toggle="1"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_tax_titles_enable[' . $seopress_tax_key . ']">' . __('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress') . '</label>';

            if ('1' == $check) {
                echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes for this taxonomy', 'wp-seopress') . '</span>';
                echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress') . '</span>';
            } else {
                echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress') . '</span>';
                echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes for this taxonomy', 'wp-seopress') . '</span>';
            }

            $toggle_txt_on  = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to display any SEO metaboxes for this taxonomy', 'wp-seopress');
            $toggle_txt_off = '<span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to hide any SEO metaboxes for this taxonomy', 'wp-seopress');

            echo "<script>
				jQuery(document).ready(function($) {
					$('input[data-id=" . $seopress_tax_key . "]').on('click', function() {
						$(this).attr('data-toggle', $(this).attr('data-toggle') == '1' ? '0' : '1');
						if ($(this).attr('data-toggle') == '1') {
							$(this).next().next('.feature-state').html('" . $toggle_txt_off . "');
						} else {
							$(this).next().next('.feature-state').html('" . $toggle_txt_on . "');
						}
					});
				});
				</script>";

            if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['enable'])) {
                esc_attr($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['enable']);
            }

            echo '</div>';

            //Tax Title
            $check = isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['title']) ? $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['title'] : null;

            echo '<div class="seopress_wrap_tax">';

            _e('Title template', 'wp-seopress');
            echo '<br/>';

            echo "<script>
					jQuery(document).ready(function($) {
						$('#seopress-tag-tax-title-" . $seopress_tax_key . "').click(function() {
							$('#seopress_titles_tax_titles_" . $seopress_tax_key . "').val(sp_get_field_length($('#seopress_titles_tax_titles_" . $seopress_tax_key . "')) + $('#seopress-tag-tax-title-" . $seopress_tax_key . "').attr('data-tag'));
						});
						$('#seopress-tag-sep-" . $seopress_tax_key . "').click(function() {
							$('#seopress_titles_tax_titles_" . $seopress_tax_key . "').val(sp_get_field_length($('#seopress_titles_tax_titles_" . $seopress_tax_key . "')) + $('#seopress-tag-sep-" . $seopress_tax_key . "').attr('data-tag'));
						});
						$('#seopress-tag-tax-sitetitle-" . $seopress_tax_key . "').click(function() {
							$('#seopress_titles_tax_titles_" . $seopress_tax_key . "').val(sp_get_field_length($('#seopress_titles_tax_titles_" . $seopress_tax_key . "')) + $('#seopress-tag-tax-sitetitle-" . $seopress_tax_key . "').attr('data-tag'));
						});
					});
				</script>";

            printf(
                '<input type="text" id="seopress_titles_tax_titles_' . $seopress_tax_key . '" name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][title]" value="%s"/>',
                esc_html($check)
                );

            if ('category' == $seopress_tax_key) {
                echo '<div class="wrap-tags"><span id="seopress-tag-tax-title-' . $seopress_tax_key . '" data-tag="%%_category_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Category Title', 'wp-seopress') . '</span>';
            } elseif ('post_tag' == $seopress_tax_key) {
                echo '<div class="wrap-tags"><span id="seopress-tag-tax-title-' . $seopress_tax_key . '" data-tag="%%tag_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Tag Title', 'wp-seopress') . '</span>';
            } else {
                echo '<div class="wrap-tags"><span id="seopress-tag-tax-title-' . $seopress_tax_key . '" data-tag="%%term_title%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Term Title', 'wp-seopress') . '</span>';
            }

            echo '<span id="seopress-tag-sep-' . $seopress_tax_key . '" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';

            echo '<span id="seopress-tag-tax-sitetitle-' . $seopress_tax_key . '" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';

            echo seopress_render_dyn_variables('tag-title');

            echo '</div>';

            //Tax Meta Description
            echo '<div class="seopress_wrap_tax">';

            $check2 = isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['description']) ? $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['description'] : null;

            _e('Meta description template', 'wp-seopress');
            echo '<br/>';

            echo "<script>
					jQuery(document).ready(function($) {
						$('#seopress-tag-tax-desc-" . $seopress_tax_key . "').click(function() {
							$('#seopress_titles_tax_desc_" . $seopress_tax_key . "').val(sp_get_field_length($('#seopress_titles_tax_desc_" . $seopress_tax_key . "')) + $('#seopress-tag-tax-desc-" . $seopress_tax_key . "').attr('data-tag'));
						});
					});
				</script>";

            printf(
                '<textarea id="seopress_titles_tax_desc_' . $seopress_tax_key . '" name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][description]">%s</textarea>',
                esc_html($check2)
                );

            if ('category' == $seopress_tax_key) {
                echo '<div class="wrap-tags"><span id="seopress-tag-tax-desc-' . $seopress_tax_key . '" data-tag="%%_category_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Category Description', 'wp-seopress') . '</span>';
            } elseif ('post_tag' == $seopress_tax_key) {
                echo '<div class="wrap-tags"><span id="seopress-tag-tax-desc-' . $seopress_tax_key . '" data-tag="%%tag_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Tag Description', 'wp-seopress') . '</span>';
            } else {
                echo '<div class="wrap-tags"><span id="seopress-tag-tax-desc-' . $seopress_tax_key . '" data-tag="%%term_description%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Term Description', 'wp-seopress') . '</span>';
            }

            echo seopress_render_dyn_variables('tag-title');

            echo '</div>';

            //Tax No-Index
            echo '<div class="seopress_wrap_tax">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);

            echo '<input id="seopress_titles_tax_noindex[' . $seopress_tax_key . ']" name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][noindex]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_tax_noindex[' . $seopress_tax_key . ']">' . __('Do not display this taxonomy archive in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

            $tax_in_sitemap = seopress_get_service('SitemapOption')->getTaxonomiesList();

            if ('1' == $check && isset($tax_in_sitemap[$seopress_tax_key]) && '1' === $tax_in_sitemap[$seopress_tax_key]['include']) {
                echo '<p class="seopress-notice error notice">' . __('This custom taxonomy is <strong>NOT</strong> excluded from your XML sitemaps despite the fact that it is set to <strong>NOINDEX</strong>. We recommend that you check this out.', 'wp-seopress') . '</p><br>';
            }

            if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'])) {
                esc_attr($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);
            }

            echo '</div>';

            //Tax No-Follow
            echo '<div class="seopress_wrap_tax">';

            $options = get_option('seopress_titles_option_name');

            $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);

            echo '<input id="seopress_titles_tax_nofollow[' . $seopress_tax_key . ']" name="seopress_titles_option_name[seopress_titles_tax_titles][' . $seopress_tax_key . '][nofollow]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_titles_tax_nofollow[' . $seopress_tax_key . ']">' . __('Do not follow links for this taxonomy archive <strong>(nofollow)</strong>', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow'])) {
                esc_attr($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);
            }

            echo '</div>';
        }
    }

    //Archives
    public function seopress_titles_archives_titles_callback() {
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            if ( ! in_array($seopress_cpt_key, ['post', 'page'])) {
                $check = isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['title']) ? $this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['title'] : null;
                echo '<h2>' . $seopress_cpt_value->labels->name . ' <em><small>[' . $seopress_cpt_value->name . ']</small></em> ';

                if (get_post_type_archive_link($seopress_cpt_value->name)) {
                    echo '<span class="link-archive"><span class="dashicons dashicons-external"></span><a href="' . get_post_type_archive_link($seopress_cpt_value->name) . '" target="_blank">' . __('See archive', 'wp-seopress') . '</a></span>';
                }

                echo '</h2>';

                //Archive Title CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                _e('Title template', 'wp-seopress');
                echo '<br/>';

                echo "<script>
						jQuery(document).ready(function($) {
							$('#seopress-tag-archive-title-" . $seopress_cpt_key . "').click(function() {
								$('#seopress_titles_archive_titles_" . $seopress_cpt_key . "').val(sp_get_field_length($('#seopress_titles_archive_titles_" . $seopress_cpt_key . "')) + $('#seopress-tag-archive-title-" . $seopress_cpt_key . "').attr('data-tag'));
							});
							$('#seopress-tag-archive-sep-" . $seopress_cpt_key . "').click(function() {
								$('#seopress_titles_archive_titles_" . $seopress_cpt_key . "').val(sp_get_field_length($('#seopress_titles_archive_titles_" . $seopress_cpt_key . "')) + $('#seopress-tag-archive-sep-" . $seopress_cpt_key . "').attr('data-tag'));
							});
							$('#seopress-tag-archive-sitetitle-" . $seopress_cpt_key . "').click(function() {
								$('#seopress_titles_archive_titles_" . $seopress_cpt_key . "').val(sp_get_field_length($('#seopress_titles_archive_titles_" . $seopress_cpt_key . "')) + $('#seopress-tag-archive-sitetitle-" . $seopress_cpt_key . "').attr('data-tag'));
							});
						});
					</script>";

                printf(
                    '<input type="text" id="seopress_titles_archive_titles_' . $seopress_cpt_key . '" name="seopress_titles_option_name[seopress_titles_archive_titles][' . $seopress_cpt_key . '][title]" value="%s"/>',
                    esc_html($check)
                    );

                echo '<div class="wrap-tags"><span id="seopress-tag-archive-title-' . $seopress_cpt_key . '" data-tag="%%cpt_plural%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Post Type Archive Name', 'wp-seopress') . '</span>';

                echo '<span id="seopress-tag-archive-sep-' . $seopress_cpt_key . '" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';

                echo '<span id="seopress-tag-archive-sitetitle-' . $seopress_cpt_key . '" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';

                echo seopress_render_dyn_variables('tag-title');

                echo '</div>';

                //Archive Meta Description CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                _e('Meta description template', 'wp-seopress');
                echo '<br/>';

                $check = isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['description']) ? $this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['description'] : null;

                printf(
                    '<textarea name="seopress_titles_option_name[seopress_titles_archive_titles][' . $seopress_cpt_key . '][description]">%s</textarea>',
                    esc_html($check)
                    );

                echo '</div>';

                //Archive No-Index CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                $options = get_option('seopress_titles_option_name');

                $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']);

                echo '<input id="seopress_titles_archive_cpt_noindex[' . $seopress_cpt_key . ']" name="seopress_titles_option_name[seopress_titles_archive_titles][' . $seopress_cpt_key . '][noindex]" type="checkbox"';
                if ('1' == $check) {
                    echo 'checked="yes"';
                }
                echo ' value="1"/>';

                echo '<label for="seopress_titles_archive_cpt_noindex[' . $seopress_cpt_key . ']">' . __('Do not display this post type archive in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

                if (isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'])) {
                    esc_attr($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']);
                }

                echo '</div>';

                //Archive No-Follow CPT
                echo '<div class="seopress_wrap_archive_cpt">';

                $options = get_option('seopress_titles_option_name');

                $check = isset($options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow']);

                echo '<input id="seopress_titles_archive_cpt_nofollow[' . $seopress_cpt_key . ']" name="seopress_titles_option_name[seopress_titles_archive_titles][' . $seopress_cpt_key . '][nofollow]" type="checkbox"';
                if ('1' == $check) {
                    echo 'checked="yes"';
                }
                echo ' value="1"/>';

                echo '<label for="seopress_titles_archive_cpt_nofollow[' . $seopress_cpt_key . ']">' . __('Do not follow links for this post type archive <strong>(nofollow)</strong>', 'wp-seopress') . '</label>';

                if (isset($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow'])) {
                    esc_attr($this->options['seopress_titles_archive_titles'][$seopress_cpt_key]['nofollow']);
                }

                echo '</div>';
            }
        }
    }

    public function seopress_titles_archives_author_title_callback() {
        echo '<h2>' . __('Author archives', 'wp-seopress') . '</h2>';

        _e('Title template', 'wp-seopress');
        echo '<br/>';

        printf(
        '<input id="seopress_titles_archive_post_author" type="text" name="seopress_titles_option_name[seopress_titles_archives_author_title]" value="%s"/>',
        esc_html($this->options['seopress_titles_archives_author_title'])
        );

        echo '<div class="wrap-tags"><span id="seopress-tag-post-author" data-tag="%%post_author%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Post author', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-sep-author" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-site-title-author" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';
        echo seopress_render_dyn_variables('tag-title');
    }

    public function seopress_titles_archives_author_desc_callback() {
        _e('Meta description template', 'wp-seopress');
        echo '<br/>';

        $check = isset($this->options['seopress_titles_archives_author_desc']) ? $this->options['seopress_titles_archives_author_desc'] : null;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_author_desc]">%s</textarea>',
        esc_html($check)
        );
    }

    public function seopress_titles_archives_author_noindex_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_archives_author_noindex']);

        echo '<input id="seopress_titles_archives_author_noindex" name="seopress_titles_option_name[seopress_titles_archives_author_noindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_archives_author_noindex">' . __('Do not display author archives in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_titles_archives_author_noindex'])) {
            esc_attr($this->options['seopress_titles_archives_author_noindex']);
        }
    }

    public function seopress_titles_archives_author_disable_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_archives_author_disable']);

        echo '<input id="seopress_titles_archives_author_disable" name="seopress_titles_option_name[seopress_titles_archives_author_disable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_archives_author_disable">' . __('Disable author archives', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_titles_archives_author_disable'])) {
            esc_attr($this->options['seopress_titles_archives_author_disable']);
        }
    }

    public function seopress_titles_archives_date_title_callback() {
        echo '<h2>' . __('Date archives', 'wp-seopress') . '</h2>';

        _e('Title template', 'wp-seopress');
        echo '<br/>';

        printf(
        '<input id="seopress_titles_archives_date_title" type="text" name="seopress_titles_option_name[seopress_titles_archives_date_title]" value="%s"/>',
        esc_html($this->options['seopress_titles_archives_date_title'])
        );

        echo '<div class="wrap-tags"><span id="seopress-tag-archive-date" data-tag="%%archive_date%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Date archives', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-sep-date" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-site-title-date" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';
        echo seopress_render_dyn_variables('tag-title');
    }

    public function seopress_titles_archives_date_desc_callback() {
        _e('Meta description template', 'wp-seopress');
        echo '<br/>';

        $check = isset($this->options['seopress_titles_archives_date_desc']) ? $this->options['seopress_titles_archives_date_desc'] : null;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_date_desc]">%s</textarea>',
        esc_html($check)
        );
    }

    public function seopress_titles_archives_date_noindex_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_archives_date_noindex']);

        echo '<input id="seopress_titles_archives_date_noindex" name="seopress_titles_option_name[seopress_titles_archives_date_noindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_archives_date_noindex">' . __('Do not display date archives in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_titles_archives_date_noindex'])) {
            esc_attr($this->options['seopress_titles_archives_date_noindex']);
        }
    }

    public function seopress_titles_archives_date_disable_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_archives_date_disable']);

        echo '<input id="seopress_titles_archives_date_disable" name="seopress_titles_option_name[seopress_titles_archives_date_disable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_archives_date_disable">' . __('Disable date archives', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_titles_archives_date_disable'])) {
            esc_attr($this->options['seopress_titles_archives_date_disable']);
        }
    }

    public function seopress_titles_archives_search_title_callback() {
        echo '<h2>' . __('Search archives', 'wp-seopress') . '</h2>';

        _e('Title template', 'wp-seopress');
        echo '<br/>';

        printf(
        '<input id="seopress_titles_archives_search_title" type="text" name="seopress_titles_option_name[seopress_titles_archives_search_title]" value="%s"/>',
        esc_html($this->options['seopress_titles_archives_search_title'])
        );

        echo '<div class="wrap-tags"><span id="seopress-tag-search-keywords" data-tag="%%search_keywords%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Search Keywords', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-sep-search" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-site-title-search" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';
        echo seopress_render_dyn_variables('tag-title');
    }

    public function seopress_titles_archives_search_desc_callback() {
        _e('Meta description template', 'wp-seopress');
        echo '<br/>';

        $check = isset($this->options['seopress_titles_archives_search_desc']) ? $this->options['seopress_titles_archives_search_desc'] : null;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_search_desc]">%s</textarea>',
        esc_html($check)
        );
    }

    public function seopress_titles_archives_search_title_noindex_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_archives_search_title_noindex']);

        echo '<input id="seopress_titles_archives_search_title_noindex" name="seopress_titles_option_name[seopress_titles_archives_search_title_noindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_archives_search_title_noindex">' . __('Do not display search archives in search engine results <strong>(noindex)</strong>', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_titles_archives_search_title_noindex'])) {
            esc_attr($this->options['seopress_titles_archives_search_title_noindex']);
        }
    }

    public function seopress_titles_archives_404_title_callback() {
        echo '<h2>' . __('404 archives', 'wp-seopress') . '</h2>';

        _e('Title template', 'wp-seopress');
        echo '<br/>';

        printf(
        '<input id="seopress_titles_archives_404_title" type="text" name="seopress_titles_option_name[seopress_titles_archives_404_title]" value="%s"/>',
        esc_html($this->options['seopress_titles_archives_404_title'])
        );
        echo '<div class="wrap-tags"><span id="seopress-tag-site-title-404" data-tag="%%sitetitle%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Site Title', 'wp-seopress') . '</span>';
        echo '<span id="seopress-tag-sep-404" data-tag="%%sep%%" class="tag-title"><span class="dashicons dashicons-plus"></span>' . __('Separator', 'wp-seopress') . '</span>';
        echo seopress_render_dyn_variables('tag-title');
    }

    public function seopress_titles_archives_404_desc_callback() {
        _e('Meta description template', 'wp-seopress');
        echo '<br/>';

        $check = isset($this->options['seopress_titles_archives_404_desc']) ? $this->options['seopress_titles_archives_404_desc'] : null;

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_404_desc]">%s</textarea>',
        esc_html($check)
        );
    }

    //Advanced
    public function seopress_titles_noindex_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_noindex']);

        echo '<input id="seopress_titles_noindex" name="seopress_titles_option_name[seopress_titles_noindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_noindex">' . __('noindex', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Do not display all pages of the site in Google search results and do not display "Cached" links in search results.', 'wp-seopress') . '</p>';

        echo '<p class="description">' . sprintf(__('Check also the <strong>"Search engine visibility"</strong> setting from the <a href="%s">WordPress Reading page</a>.', 'wp-seopress'), admin_url('options-reading.php')) . '</p>';

        if (isset($this->options['seopress_titles_noindex'])) {
            esc_attr($this->options['seopress_titles_noindex']);
        }
    }

    public function seopress_titles_nofollow_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_nofollow']);

        echo '<input id="seopress_titles_nofollow" name="seopress_titles_option_name[seopress_titles_nofollow]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_nofollow">' . __('nofollow', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Do not follow links for all pages.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_nofollow'])) {
            esc_attr($this->options['seopress_titles_nofollow']);
        }
    }

    public function seopress_titles_noodp_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_noodp']);

        echo '<input id="seopress_titles_noodp" name="seopress_titles_option_name[seopress_titles_noodp]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_noodp">' . __('noodp', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Do not use Open Directory project metadata for titles or excerpts for all pages.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_noodp'])) {
            esc_attr($this->options['seopress_titles_noodp']);
        }
    }

    public function seopress_titles_noimageindex_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_noimageindex']);

        echo '<input id="seopress_titles_noimageindex" name="seopress_titles_option_name[seopress_titles_noimageindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_noimageindex">' . __('noimageindex', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Do not index images from the entire site.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_noimageindex'])) {
            esc_attr($this->options['seopress_titles_noimageindex']);
        }
    }

    public function seopress_titles_noarchive_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_noarchive']);

        echo '<input id="seopress_titles_noarchive" name="seopress_titles_option_name[seopress_titles_noarchive]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_noarchive">' . __('noarchive', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Do not display a "Cached" link in the Google search results.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_noarchive'])) {
            esc_attr($this->options['seopress_titles_noarchive']);
        }
    }

    public function seopress_titles_nosnippet_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_nosnippet']);

        echo '<input id="seopress_titles_nosnippet" name="seopress_titles_option_name[seopress_titles_nosnippet]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_nosnippet">' . __('nosnippet', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Do not display a description in the Google search results for all pages.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_nosnippet'])) {
            esc_attr($this->options['seopress_titles_nosnippet']);
        }
    }

    public function seopress_titles_nositelinkssearchbox_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_nositelinkssearchbox']);

        echo '<input id="seopress_titles_nositelinkssearchbox" name="seopress_titles_option_name[seopress_titles_nositelinkssearchbox]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_nositelinkssearchbox">' . __('nositelinkssearchbox', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Prevents Google to display a sitelinks searchbox in search results. Enable this option will remove the "Website" schema from your source code.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_nositelinkssearchbox'])) {
            esc_attr($this->options['seopress_titles_nositelinkssearchbox']);
        }
    }

    public function seopress_titles_paged_rel_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_paged_rel']);

        echo '<input id="seopress_titles_paged_rel" name="seopress_titles_option_name[seopress_titles_paged_rel]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_paged_rel">' . __('Add rel next/prev link in head of paginated archive pages', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_titles_paged_rel'])) {
            esc_attr($this->options['seopress_titles_paged_rel']);
        }
    }

    public function seopress_titles_paged_noindex_callback() {
        $options = get_option('seopress_titles_option_name');

        $check = isset($options['seopress_titles_paged_noindex']);

        echo '<input id="seopress_titles_paged_noindex" name="seopress_titles_option_name[seopress_titles_paged_noindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_titles_paged_noindex">' . __('Add a "noindex" meta robots for all paginated archive pages', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('eg: https://example.com/category/my-category/page/2/', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_titles_paged_noindex'])) {
            esc_attr($this->options['seopress_titles_paged_noindex']);
        }
    }

    public function seopress_xml_sitemap_general_enable_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_general_enable']);

        echo '<input id="seopress_xml_sitemap_general_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_general_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_xml_sitemap_general_enable">' . __('Enable XML Sitemap', 'wp-seopress') . '</label>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/support/guides/enable-xml-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a href="' . $seopress_docs_link['support']['sitemaps'] . '" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Guide to enable XML Sitemaps - new window', 'wp-seopress') . '</span></a>';

        if (isset($this->options['seopress_xml_sitemap_general_enable'])) {
            esc_attr($this->options['seopress_xml_sitemap_general_enable']);
        }
    }

    public function seopress_xml_sitemap_img_enable_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_img_enable']);

        echo '<input id="seopress_xml_sitemap_img_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_img_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_xml_sitemap_img_enable">' . __('Enable Image Sitemaps (standard images, image galleries, featured image, WooCommerce product images)', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Images in XML sitemaps are visible only from the source code.', 'wp-seopress') . '</p>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['sitemaps']['image'] = 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml-images/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['sitemaps']['image'] = 'https://www.seopress.org/support/guides/enable-xml-image-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a href="' . $seopress_docs_link['support']['sitemaps']['image'] . '" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Guide to enable XML image sitemaps - new window', 'wp-seopress') . '</span></a>';

        if (isset($this->options['seopress_xml_sitemap_img_enable'])) {
            esc_attr($this->options['seopress_xml_sitemap_img_enable']);
        }
    }

    public function seopress_xml_sitemap_video_enable_callback() {
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $options = get_option('seopress_xml_sitemap_option_name');

            $check = isset($options['seopress_xml_sitemap_video_enable']);

            echo '<input id="seopress_xml_sitemap_video_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_video_enable]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_xml_sitemap_video_enable">' . __('Enable Video Sitemaps', 'wp-seopress') . '</label>';

            if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
                $seopress_docs_link['support']['sitemaps']['video'] = 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-video/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            } else {
                $seopress_docs_link['support']['sitemaps']['video'] = 'https://www.seopress.org/support/guides/enable-video-xml-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
            }

            printf('<p class="description">' . __('Your video sitemap is empty? Read our guide to learn more about <a href="%s" target="_blank">adding videos to your sitemap.</a>', 'wp-seopress') . '</p>', $seopress_docs_link['support']['sitemaps']['video']);

            echo '<a href="' . $seopress_docs_link['support']['sitemaps']['video'] . '" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Guide to enable XML video sitemaps - new window', 'wp-seopress') . '</span></a>';

            if (isset($this->options['seopress_xml_sitemap_video_enable'])) {
                esc_attr($this->options['seopress_xml_sitemap_video_enable']);
            }
        }
    }

    public function seopress_xml_sitemap_author_enable_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_author_enable']);

        echo '<input id="seopress_xml_sitemap_author_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_author_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_xml_sitemap_author_enable">' . __('Enable Author Sitemap', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Make sure to enable author archive from SEO, titles and metas, archives tab.</a>', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_xml_sitemap_author_enable'])) {
            esc_attr($this->options['seopress_xml_sitemap_author_enable']);
        }
    }

    public function seopress_xml_sitemap_html_enable_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_html_enable']);

        echo '<input id="seopress_xml_sitemap_html_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_xml_sitemap_html_enable">' . __('Enable HTML Sitemap', 'wp-seopress') . '</label>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['sitemaps']['html'] = 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-html/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['sitemaps']['html'] = 'https://www.seopress.org/support/guides/enable-html-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a href="' . $seopress_docs_link['support']['sitemaps']['html'] . '" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Guide to enable a HTML Sitemap - new window', 'wp-seopress') . '</span></a>';

        if (isset($this->options['seopress_xml_sitemap_html_enable'])) {
            esc_attr($this->options['seopress_xml_sitemap_html_enable']);
        }
    }

    public function seopress_xml_sitemap_post_types_list_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_post_types_list']);

        global $wp_post_types;

        $args = [
            'show_ui' => true,
            'public'  => true,
        ];

        $output   = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types($args, $output, $operator);

        foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) {
            echo '<h2>' . $seopress_cpt_value->labels->name . ' <em><small>[' . $seopress_cpt_value->name . ']</small></em></h2>';

            //List all post types
            echo '<div class="seopress_wrap_single_cpt">';

            $options = get_option('seopress_xml_sitemap_option_name');

            $check = isset($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);

            echo '<input id="seopress_xml_sitemap_post_types_list_include[' . $seopress_cpt_key . ']" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_post_types_list][' . $seopress_cpt_key . '][include]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_xml_sitemap_post_types_list_include[' . $seopress_cpt_key . ']">' . __('Include', 'wp-seopress') . '</label>';

            if ('attachment' == $seopress_cpt_value->name) {
                echo '<p class="description">' . __('You should never include attachment post type in your sitemap. Be careful if you checked this.', 'wp-seopress') . '</p>';
            }

            if (isset($this->options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'])) {
                esc_attr($this->options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
            }

            echo '</div>';
        }
    }

    public function seopress_xml_sitemap_taxonomies_list_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_taxonomies_list']);

        $args = [
            'show_ui' => true,
            'public'  => true,
        ];
        $output     = 'objects'; // or objects
        $operator   = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies($args, $output, $operator);

        foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
            echo '<h2>' . $seopress_tax_value->labels->name . ' <em><small>[' . $seopress_tax_value->name . ']</small></em></h2>';

            //List all taxonomies
            echo '<div class="seopress_wrap_single_tax">';

            $options = get_option('seopress_xml_sitemap_option_name');

            $check = isset($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);

            echo '<input id="seopress_xml_sitemap_taxonomies_list_include[' . $seopress_tax_key . ']" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_taxonomies_list][' . $seopress_tax_key . '][include]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_xml_sitemap_taxonomies_list_include[' . $seopress_tax_key . ']">' . __('Include', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'])) {
                esc_attr($this->options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);
            }

            echo '</div>';
        }
    }

    public function seopress_xml_sitemap_html_mapping_callback() {
        $check = isset($this->options['seopress_xml_sitemap_html_mapping']) ? $this->options['seopress_xml_sitemap_html_mapping'] : null;

        printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_mapping]" placeholder="' . esc_html__('eg: 2, 28, 68', 'wp-seopress') . '" aria-label="' . __('Enter a post, page or custom post type ID(s) to display the sitemap', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<br><br><p>' . __('You can also use this shortcode:', 'wp-seopress') . '</p>';

        echo '<pre>[seopress_html_sitemap]</pre>';

        echo '<br><br><p>' . __('To include specific custom post types, use the CPT attribute:', 'wp-seopress') . '</p>';

        echo '<pre>[seopress_html_sitemap cpt="post,product"]</pre>';
    }

    public function seopress_xml_sitemap_html_exclude_callback() {
        $check = isset($this->options['seopress_xml_sitemap_html_exclude']) ? $this->options['seopress_xml_sitemap_html_exclude'] : null;

        printf(
        '<input type="text" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_exclude]" placeholder="' . esc_html__('eg: 13, 8, 38', 'wp-seopress') . '" aria-label="' . __('Exclude some Posts, Pages, Custom Post Types or Terms IDs', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_xml_sitemap_html_order_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $selected = isset($options['seopress_xml_sitemap_html_order']) ? $options['seopress_xml_sitemap_html_order'] : null;

        echo '<select id="seopress_xml_sitemap_html_order" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_order]">';
        echo ' <option ';
        if ('DESC' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="DESC">' . __('DESC (descending order from highest to lowest values (3, 2, 1; c, b, a))', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('ASC' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="ASC">' . __('ASC (ascending order from lowest to highest values (1, 2, 3; a, b, c))', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_xml_sitemap_html_order'])) {
            esc_attr($this->options['seopress_xml_sitemap_html_order']);
        }
    }

    public function seopress_xml_sitemap_html_orderby_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $selected = isset($options['seopress_xml_sitemap_html_orderby']) ? $options['seopress_xml_sitemap_html_orderby'] : null;

        echo '<select id="seopress_xml_sitemap_html_orderby" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_orderby]">';
        echo ' <option ';
        if ('date' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="date">' . __('Default (date)', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('title' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="title">' . __('Post Title', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('modified' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="modified">' . __('Modified date', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('ID' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="ID">' . __('Post ID', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('menu_order' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="menu_order">' . __('Menu order', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_xml_sitemap_html_orderby'])) {
            esc_attr($this->options['seopress_xml_sitemap_html_orderby']);
        }
    }

    public function seopress_xml_sitemap_html_date_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_html_date']);

        echo '<input id="seopress_xml_sitemap_html_date" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_date]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_xml_sitemap_html_date">' . __('Disable date after each post, page, post type?', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_xml_sitemap_html_date'])) {
            esc_attr($this->options['seopress_xml_sitemap_html_date']);
        }
    }

    public function seopress_xml_sitemap_html_archive_links_callback() {
        $options = get_option('seopress_xml_sitemap_option_name');

        $check = isset($options['seopress_xml_sitemap_html_archive_links']);

        echo '<input id="seopress_xml_sitemap_html_archive_links" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_html_archive_links]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_xml_sitemap_html_archive_links">' . __('Remove links from archive pages (eg: Products)', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_xml_sitemap_html_archive_links'])) {
            esc_attr($this->options['seopress_xml_sitemap_html_archive_links']);
        }
    }

    public function seopress_social_knowledge_type_callback() {
        $options = get_option('seopress_social_option_name');

        $selected = isset($options['seopress_social_knowledge_type']) ? $options['seopress_social_knowledge_type'] : null;

        echo '<select id="seopress_social_knowledge_type" name="seopress_social_option_name[seopress_social_knowledge_type]">';
        echo ' <option ';
        if ('None' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="none">' . __('None (will disable this feature)', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('Person' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="Person">' . __('Person', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('Organization' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="Organization">' . __('Organization', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_type'])) {
            esc_attr($this->options['seopress_social_knowledge_type']);
        }
    }

    public function seopress_social_knowledge_name_callback() {
        $check = isset($this->options['seopress_social_knowledge_name']) ? $this->options['seopress_social_knowledge_name'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_name]" placeholder="' . esc_html__('eg: Miremont', 'wp-seopress') . '" aria-label="' . __('Your name/organization', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_knowledge_img_callback() {
        $options = get_option('seopress_social_option_name');

        $options_set = isset($options['seopress_social_knowledge_img']) ? esc_attr($options['seopress_social_knowledge_img']) : null;

        $check = isset($options['seopress_social_knowledge_img']);

        echo '<input id="seopress_social_knowledge_img_meta" type="text" value="' . $options_set . '" name="seopress_social_option_name[seopress_social_knowledge_img]" aria-label="' . __('Your photo/organization logo', 'wp-seopress') . '" placeholder="' . esc_html__('Select your logo', 'wp-seopress') . '"/>

		<input id="seopress_social_knowledge_img_upload" class="button" type="button" value="' . __('Upload an Image', 'wp-seopress') . '" />';

        echo '<p class="description">' . __('JPG, PNG, and GIF allowed.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_social_knowledge_img'])) {
            esc_attr($this->options['seopress_social_knowledge_img']);
        }

        function seopress_social_knowledge_img_option() {
            $seopress_social_knowledge_img_option = get_option('seopress_social_option_name');
            if ( ! empty($seopress_social_knowledge_img_option)) {
                foreach ($seopress_social_knowledge_img_option as $key => $seopress_social_knowledge_img_value) {
                    $options[$key] = $seopress_social_knowledge_img_value;
                }
                if (isset($seopress_social_knowledge_img_option['seopress_social_knowledge_img'])) {
                    return $seopress_social_knowledge_img_option['seopress_social_knowledge_img'];
                }
            }
        }
        echo '<br>';
        echo '<br>';
        echo '<img style="width:200px;max-height:300px;" src="' . esc_attr(seopress_social_knowledge_img_option()) . '"/>';
    }

    public function seopress_social_knowledge_phone_callback() {
        $check = isset($this->options['seopress_social_knowledge_phone']) ? $this->options['seopress_social_knowledge_phone'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_phone]" placeholder="' . esc_html__('eg: +33123456789 (internationalized version required)', 'wp-seopress') . '" aria-label="' . __('Organization\'s phone number (only for Organizations)', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_knowledge_contact_type_callback() {
        $options = get_option('seopress_social_option_name');

        $selected = isset($options['seopress_social_knowledge_contact_type']) ? $options['seopress_social_knowledge_contact_type'] : null;

        echo '<select id="seopress_social_knowledge_contact_type" name="seopress_social_option_name[seopress_social_knowledge_contact_type]">';
        echo ' <option ';
        if ('customer support' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="customer support">' . __('Customer support', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('technical support' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="technical support">' . __('Technical support', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('billing support' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="billing support">' . __('Billing support', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('bill payment' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="bill payment">' . __('Bill payment', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('sales' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="sales">' . __('Sales', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('credit card support' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="credit card support">' . __('Credit card support', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('emergency' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="emergency">' . __('Emergency', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('baggage tracking' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="baggage tracking">' . __('Baggage tracking', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('roadside assistance' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="roadside assistance">' . __('Roadside assistance', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('package tracking' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="package tracking">' . __('Package tracking', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_contact_type'])) {
            esc_attr($this->options['seopress_social_knowledge_contact_type']);
        }
    }

    public function seopress_social_knowledge_contact_option_callback() {
        $options = get_option('seopress_social_option_name');

        $selected = isset($options['seopress_social_knowledge_contact_option']) ? $options['seopress_social_knowledge_contact_option'] : null;

        echo '<select id="seopress_social_knowledge_contact_option" name="seopress_social_option_name[seopress_social_knowledge_contact_option]">';
        echo ' <option ';
        if ('None' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="None">' . __('None', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('TollFree' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="TollFree">' . __('Toll Free', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('HearingImpairedSupported' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="HearingImpairedSupported">' . __('Hearing impaired supported', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_contact_option'])) {
            esc_attr($this->options['seopress_social_knowledge_contact_option']);
        }
    }

    public function seopress_social_accounts_facebook_callback() {
        $check = isset($this->options['seopress_social_accounts_facebook']) ? $this->options['seopress_social_accounts_facebook'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_facebook]" placeholder="' . esc_html__('eg: https://facebook.com/my-page-url', 'wp-seopress') . '" aria-label="' . __('Facebook Page URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_accounts_twitter_callback() {
        $check = isset($this->options['seopress_social_accounts_twitter']) ? $this->options['seopress_social_accounts_twitter'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_twitter]" placeholder="' . esc_html__('eg: @my_twitter_account', 'wp-seopress') . '" aria-label="' . __('Twitter Page URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_accounts_pinterest_callback() {
        $check = isset($this->options['seopress_social_accounts_pinterest']) ? $this->options['seopress_social_accounts_pinterest'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_pinterest]" placeholder="' . esc_html__('eg: https://pinterest.com/my-page-url/', 'wp-seopress') . '" aria-label="' . __('Pinterest URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_accounts_instagram_callback() {
        $check = isset($this->options['seopress_social_accounts_instagram']) ? $this->options['seopress_social_accounts_instagram'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_instagram]" placeholder="' . esc_html__('eg: https://www.instagram.com/my-page-url/', 'wp-seopress') . '" aria-label="' . __('Instagram URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_accounts_youtube_callback() {
        $check = isset($this->options['seopress_social_accounts_youtube']) ? $this->options['seopress_social_accounts_youtube'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_youtube]" placeholder="' . esc_html__('eg: https://www.youtube.com/my-channel-url', 'wp-seopress') . '" aria-label="' . __('YouTube URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_accounts_linkedin_callback() {
        $check = isset($this->options['seopress_social_accounts_linkedin']) ? $this->options['seopress_social_accounts_linkedin'] : null;

        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_linkedin]" placeholder="' . esc_html__('eg: http://linkedin.com/company/my-company-url/', 'wp-seopress') . '" aria-label="' . __('LinkedIn URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_social_facebook_og_callback() {
        $options = get_option('seopress_social_option_name');

        $check = isset($options['seopress_social_facebook_og']);

        echo '<input id="seopress_social_facebook_og" name="seopress_social_option_name[seopress_social_facebook_og]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_social_facebook_og">' . __('Enable OG data', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_social_facebook_og'])) {
            esc_attr($this->options['seopress_social_facebook_og']);
        }
    }

    public function seopress_social_facebook_img_callback() {
        $options = get_option('seopress_social_option_name');

        $options_set = isset($options['seopress_social_facebook_img']) ? esc_attr($options['seopress_social_facebook_img']) : null;

        echo '<input id="seopress_social_fb_img_meta" type="text" value="' . $options_set . '" name="seopress_social_option_name[seopress_social_facebook_img]" aria-label="' . __('Select a default image', 'wp-seopress') . '" placeholder="' . esc_html__('Select your default thumbnail', 'wp-seopress') . '"/>

		<input id="seopress_social_fb_img_upload" class="button" type="button" value="' . __('Upload an Image', 'wp-seopress') . '" />';

        echo '<p class="description">' . __('Minimum size: 200x200px, ideal ratio 1.91:1, 8Mb max. (eg: 1640x856px or 3280x1712px for retina screens)', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_social_facebook_img'])) {
            esc_attr($this->options['seopress_social_facebook_img']);
        }
    }

    public function seopress_social_facebook_img_default_callback() {
        $options = get_option('seopress_social_option_name');

        $check = isset($options['seopress_social_facebook_img_default']);

        echo '<input id="seopress_social_facebook_img_default" name="seopress_social_option_name[seopress_social_facebook_img_default]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_social_facebook_img_default">' . __('Override every <strong>og:image</strong> tag with this default image (except if a custom og:image has already been set from the SEO metabox).', 'wp-seopress') . '</label>';

        $def_og_img = isset($options['seopress_social_facebook_img']) ? $options['seopress_social_facebook_img'] : '';

        if ('' == $def_og_img) {
            echo '<br><br><p class="seopress-notice notice-error">' . __('Please define a default OG Image from the field above', 'wp-seopress') . '</p>';
        }

        if (isset($this->options['seopress_social_facebook_img_default'])) {
            esc_attr($this->options['seopress_social_facebook_img_default']);
        }
    }

    public function seopress_social_facebook_img_cpt_callback() {
        if ( ! empty(seopress_get_post_types())) {
            $post_types = seopress_get_post_types();
            unset($post_types['post'], $post_types['page']);

            if ( ! empty($post_types)) {
                foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) {
                    echo '<h2>' . $seopress_cpt_value->labels->name . ' <em><small>[' . $seopress_cpt_value->name . ']</small></em></h2>';

                    if ('product' === $seopress_cpt_value->name && is_plugin_active('woocommerce/woocommerce.php')) {
                        echo '<p>' . __('WooCommerce Shop Page.', 'wp-seopress') . '</p>';
                    }

                    $options = get_option('seopress_social_option_name');

                    $options_set = isset($options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url']) ? esc_attr($options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url']) : null;

                    echo '<p>
						<input id="seopress_social_facebook_img_cpt_meta[' . $seopress_cpt_key . ']" class="seopress_social_facebook_img_cpt_meta" type="text" value="' . $options_set . '" name="seopress_social_option_name[seopress_social_facebook_img_cpt][' . $seopress_cpt_key . '][url]" aria-label="' . __('Select a default image', 'wp-seopress') . '" placeholder="' . esc_html__('Select your default thumbnail', 'wp-seopress') . '"/>

						<input id="seopress_social_facebook_img_cpt[' . $seopress_cpt_key . ']" class="seopress_social_facebook_img_cpt button" type="button" value="' . __('Upload an Image', 'wp-seopress') . '" />
					</p>';

                    if (isset($this->options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url'])) {
                        esc_attr($this->options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url']);
                    }
                }
            } else {
                echo '<p>' . __('No custom post type to configure.', 'wp-seopress') . '</p>';
            }
        }
    }

    public function seopress_social_facebook_link_ownership_id_callback() {
        $check = isset($this->options['seopress_social_facebook_link_ownership_id']) ? $this->options['seopress_social_facebook_link_ownership_id'] : null;

        printf('<input type="text" name="seopress_social_option_name[seopress_social_facebook_link_ownership_id]" value="%s"/>',
        esc_html($check));

        echo '<p class="description">' . __('One or more Facebook Page IDs that are associated with a URL in order to enable link editing and instant article publishing.', 'wp-seopress') . '</p>';

        echo '<pre>&lt;meta property="fb:pages" content="page ID"/&gt;</pre>';

        echo '<br><span class="seopress-help dashicons dashicons-external"></span><a class="seopress-help" href="https://www.facebook.com/help/1503421039731588" target="_blank">' . __('How do I find my Facebook Page ID?', 'wp-seopress') . '</a>';
    }

    public function seopress_social_facebook_admin_id_callback() {
        $check = isset($this->options['seopress_social_facebook_admin_id']) ? $this->options['seopress_social_facebook_admin_id'] : null;

        printf('<input type="text" name="seopress_social_option_name[seopress_social_facebook_admin_id]" value="%s"/>',
        esc_html($check));

        echo '<p class="description">' . __('The ID (or comma-separated list for properties that can accept multiple IDs) of an app, person using the app, or Page Graph API object.', 'wp-seopress') . '</p>';

        echo '<pre>&lt;meta property="fb:admins" content="admins ID"/&gt;</pre>';
    }

    public function seopress_social_facebook_app_id_callback() {
        $check = isset($this->options['seopress_social_facebook_app_id']) ? $this->options['seopress_social_facebook_app_id'] : null;

        printf('<input type="text" name="seopress_social_option_name[seopress_social_facebook_app_id]" value="%s"/>',
        esc_html($check));

        echo '<p class="description">' . __('The Facebook app ID of the site\'s app. In order to use Facebook Insights you must add the app ID to your page. Insights lets you view analytics for traffic to your site from Facebook. Find the app ID in your App Dashboard. <a class="seopress-help" href="https://developers.facebook.com/apps/redirect/dashboard" target="_blank">More info here</a> <span class="seopress-help dashicons dashicons-external"></span>', 'wp-seopress') . '</p>';

        echo '<pre>&lt;meta property="fb:app_id" content="app ID"/&gt;</pre>';

        echo '<br><span class="seopress-help dashicons dashicons-external"></span><a class="seopress-help" href="https://developers.facebook.com/docs/apps/register" target="_blank">' . __('How to create a Facebook App ID', 'wp-seopress') . '</a>';
    }

    public function seopress_social_twitter_card_callback() {
        $options = get_option('seopress_social_option_name');

        $check = isset($options['seopress_social_twitter_card']);

        echo '<input id="seopress_social_twitter_card" name="seopress_social_option_name[seopress_social_twitter_card]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_social_twitter_card">' . __('Enable Twitter card', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_social_twitter_card'])) {
            esc_attr($this->options['seopress_social_twitter_card']);
        }
    }

    public function seopress_social_twitter_card_og_callback() {
        $options = get_option('seopress_social_option_name');

        $check = isset($options['seopress_social_twitter_card_og']);

        echo '<input id="seopress_social_twitter_card_og" name="seopress_social_option_name[seopress_social_twitter_card_og]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_social_twitter_card_og">' . __('Use OG if no Twitter Cards', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_social_twitter_card_og'])) {
            esc_attr($this->options['seopress_social_twitter_card_og']);
        }
    }

    public function seopress_social_twitter_card_img_callback() {
        $options = get_option('seopress_social_option_name');

        $options_set = isset($options['seopress_social_twitter_card_img']) ? esc_attr($options['seopress_social_twitter_card_img']) : null;

        $check = isset($options['seopress_social_twitter_card_img']);

        echo '<input id="seopress_social_twitter_img_meta" type="text" value="' . $options_set . '" name="seopress_social_option_name[seopress_social_twitter_card_img]" aria-label="' . __('Default Twitter Image', 'wp-seopress') . '" placeholder="' . esc_html__('Select your default thumbnail', 'wp-seopress') . '"/>

		<input id="seopress_social_twitter_img_upload" class="button" type="button" value="' . __('Upload an Image', 'wp-seopress') . '" />';

        echo '<p class="description">' . __('Minimum size: 144x144px (300x157px with large card enabled), ideal ratio 1:1 (2:1 with large card), 5Mb max.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_social_twitter_card_img'])) {
            esc_attr($this->options['seopress_social_twitter_card_img']);
        }
    }

    public function seopress_social_twitter_card_img_size_callback() {
        $options = get_option('seopress_social_option_name');

        $selected = isset($options['seopress_social_twitter_card_img_size']) ? $options['seopress_social_twitter_card_img_size'] : null;

        echo '<select id="seopress_social_twitter_card_img_size" name="seopress_social_option_name[seopress_social_twitter_card_img_size]">';
        echo ' <option ';
        if ('default' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="default">' . __('Default', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('large' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="large">' . __('Large', 'wp-seopress') . '</option>';
        echo '</select>';

        echo '<p class="description">' . __('The Summary Card with <strong>Large Image</strong> features a large, full-width prominent image alongside a tweet. It is designed to give the reader a rich photo experience, and clicking on the image brings the user to your website.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_social_twitter_card_img_size'])) {
            esc_attr($this->options['seopress_social_twitter_card_img_size']);
        }
    }

    public function seopress_google_analytics_enable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_enable']);

        echo '<input id="seopress_google_analytics_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_enable">' . __('Enable Google Analytics tracking (Global Site Tag: gtag.js)', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_enable'])) {
            esc_attr($this->options['seopress_google_analytics_enable']);
        }
    }

    public function seopress_google_analytics_ua_callback() {
        $check = isset($this->options['seopress_google_analytics_ua']) ? $this->options['seopress_google_analytics_ua'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ua]" placeholder="' . esc_html__('Enter your Tracking ID (UA-XXXX-XX)', 'wp-seopress') . '" aria-label="' . __('Enter your tracking ID', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<p class="seopress-help description"><span class="dashicons dashicons-external"></span><a href="https://support.google.com/analytics/answer/1032385?hl=en" target="_blank">' . __('Find your tracking ID', 'wp-seopress') . '</a></p>';
    }

    public function seopress_google_analytics_ga4_callback() {
        $check = isset($this->options['seopress_google_analytics_ga4']) ? $this->options['seopress_google_analytics_ga4'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ga4]" placeholder="' . esc_html__('Enter your measurement ID (G-XXXXXXXXXX)', 'wp-seopress') . '" aria-label="' . __('Enter your measurement ID', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<p class="seopress-help description"><span class="dashicons dashicons-external"></span><a href="https://support.google.com/analytics/answer/9539598?hl=en&ref_topic=9303319" target="_blank">' . __('Find your measurement ID', 'wp-seopress') . '</a></p>';
    }

    public function seopress_google_analytics_hook_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_hook']) ? $options['seopress_google_analytics_hook'] : null;

        echo '<select id="seopress_google_analytics_hook" name="seopress_google_analytics_option_name[seopress_google_analytics_hook]">';
        echo ' <option ';
        if ('wp_body_open' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="wp_body_open">' . __('After the opening body tag (recommended)', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('wp_footer' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="wp_footer">' . __('Footer', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('wp_head' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="wp_head">' . __('Head (not recommended)', 'wp-seopress') . '</option>';
        echo '</select>';

        echo '<p class="description">'.__('Your theme must be compatible with wp_body_open hook introduced in WordPress 5.2 if "opening body tag" option selected.').'</p>';

        if (isset($this->options['seopress_google_analytics_hook'])) {
            esc_attr($this->options['seopress_google_analytics_hook']);
        }
    }

    public function seopress_google_analytics_disable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_disable']);

        echo '<input id="seopress_google_analytics_disable" name="seopress_google_analytics_option_name[seopress_google_analytics_disable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_disable">' . __('Request user\'s consent for analytics tracking (required by GDPR)', 'wp-seopress') . '</label>';

        echo '<p class="advise" style="margin:10px 0 0 0">' . __('<strong>The user must click the Accept button to allow tracking.</strong>', 'wp-seopress') . '</p>';

        echo '<p class="description">' . __('User roles excluded from tracking will not see the consent message.<br> If you use a caching plugin, you have to exclude this JS file in your settings: <br><strong>/wp-content/plugins/wp-seopress/assets/js/seopress-cookies-ajax.js</strong> <br>and this cookie <strong>seopress-user-consent-accept</strong>', 'wp-seopress') . '</p>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['analytics']['custom_tracking'] = 'https://www.seopress.org/fr/support/hooks/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['analytics']['custom_tracking'] = 'https://www.seopress.org/support/hooks/add-custom-tracking-code-with-user-consent/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a class="seopress-doc" href="' . $seopress_docs_link['support']['analytics']['custom_tracking'] . '" target="_blank"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Hook to add custom tracking code with user consent - new window', 'wp-seopress') . '</span></a></p>';

        if (isset($this->options['seopress_google_analytics_disable'])) {
            esc_attr($this->options['seopress_google_analytics_disable']);
        }
    }

    public function seopress_google_analytics_half_disable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_half_disable']);

        echo '<input id="seopress_google_analytics_half_disable" name="seopress_google_analytics_option_name[seopress_google_analytics_half_disable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_half_disable">' . __('Display and automatically accept the user‘s consent on page load (not fully GDPR)', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('The previous option must be checked to use this.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_google_analytics_half_disable'])) {
            esc_attr($this->options['seopress_google_analytics_half_disable']);
        }
    }

    public function seopress_google_analytics_opt_out_edit_choice_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_opt_out_edit_choice']);

        echo '<input id="seopress_google_analytics_opt_out_edit_choice" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_edit_choice]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_opt_out_edit_choice">' . __('Allow user to change its choice about cookies', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_opt_out_edit_choice'])) {
            esc_attr($this->options['seopress_google_analytics_opt_out_edit_choice']);
        }
    }

    public function seopress_google_analytics_opt_out_msg_callback() {
        $options = get_option('seopress_google_analytics_option_name');
        $check   = isset($options['seopress_google_analytics_opt_out_msg']) ? $options['seopress_google_analytics_opt_out_msg'] : null;

        printf(
        '<textarea id="seopress_google_analytics_opt_out_msg" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg]" rows="4" placeholder="' . esc_html__('Enter your message (HTML allowed)', 'wp-seopress') . '" aria-label="' . __('This message will only appear if request user\'s consent is enabled.', 'wp-seopress') . '">%s</textarea>',
        esc_html($check));

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['analytics']['consent_msg'] = 'https://www.seopress.org/fr/support/hooks/filtrer-le-message-du-consentement-utilisateur/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['analytics']['consent_msg'] = 'https://www.seopress.org/support/hooks/filter-user-consent-message/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a class="seopress-doc" href="' . $seopress_docs_link['support']['analytics']['consent_msg'] . '" target="_blank"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text">' . __('Hook to filter user consent message - new window', 'wp-seopress') . '</span></a></p>';

        echo '<p class="description">' . __('HTML tags allowed: strong, em, br, a href / target', 'wp-seopress') . '</p>';
        echo '<p class="description">' . __('Shortcode allowed to get the privacy page set in WordPress settings: [seopress_privacy_page]', 'wp-seopress') . '</p>';
    }

    public function seopress_google_analytics_opt_out_msg_ok_callback() {
        $check = isset($this->options['seopress_google_analytics_opt_out_msg_ok']) ? $this->options['seopress_google_analytics_opt_out_msg_ok'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg_ok]" placeholder="' . esc_html__('Accept', 'wp-seopress') . '" aria-label="' . __('Change the button value', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_opt_out_msg_close_callback() {
        $check = isset($this->options['seopress_google_analytics_opt_out_msg_close']) ? $this->options['seopress_google_analytics_opt_out_msg_close'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg_close]" placeholder="' . esc_html__('default: X', 'wp-seopress') . '" aria-label="' . __('Change the close button value', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_opt_out_msg_edit_callback() {
        $check = isset($this->options['seopress_google_analytics_opt_out_msg_edit']) ? $this->options['seopress_google_analytics_opt_out_msg_edit'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg_edit]" placeholder="' . esc_html__('default: Manage cookies', 'wp-seopress') . '" aria-label="' . __('Change the edit button value', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_exp_date_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_cb_exp_date']);

        echo '<input type="number" min="1" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_exp_date]"';
        if ('1' == $check) {
            echo 'value="' . esc_attr($options['seopress_google_analytics_cb_exp_date']) . '"';
        }
        echo ' value="30"/>';

        if (isset($this->options['seopress_google_analytics_cb_exp_date'])) {
            esc_html($this->options['seopress_google_analytics_cb_exp_date']);
        }

        echo '<p class="description">' . __('Default: 30 days before the cookie expiration.', 'wp-seopress') . '</p>';
    }

    public function seopress_google_analytics_cb_pos_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cb_pos']) ? $options['seopress_google_analytics_cb_pos'] : null;

        echo '<select id="seopress_google_analytics_cb_pos" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_pos]">';
        echo ' <option ';
        if ('bottom' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="bottom">' . __('Bottom (default)', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('center' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="center">' . __('Middle', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('top' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="top">' . __('Top', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cb_pos'])) {
            esc_attr($this->options['seopress_google_analytics_cb_pos']);
        }
    }

    public function seopress_google_analytics_cb_txt_align_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cb_txt_align']) ? $options['seopress_google_analytics_cb_txt_align'] : 'center';

        echo '<select id="seopress_google_analytics_cb_txt_align" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_txt_align]">';
        echo ' <option ';
        if ('left' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="left">' . __('Left', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('center' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="center">' . __('Center (default)', 'wp-seopress') . '</option>';
        echo ' <option ';
        if ('top' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="right">' . __('Right', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cb_txt_align'])) {
            esc_attr($this->options['seopress_google_analytics_cb_txt_align']);
        }
    }

    public function seopress_google_analytics_cb_width_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_width']) ? $this->options['seopress_google_analytics_cb_width'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_width]" aria-label="' . __('Change the cookie bar width', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<p class="description">' . __('Default unit is Pixels. Add % just after your custom value to use percentages (eg: 80%).', 'wp-seopress') . '</p>';
    }

    public function seopress_google_analytics_cb_backdrop_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_cb_backdrop']);

        echo '<hr><h2>' . __('Backdrop', 'wp-seopress') . '</h2>';

        echo '<p>' . __('Customize the cookie bar <strong>backdrop</strong>.', 'wp-seopress') . '</p><br>';

        echo '<input id="seopress_google_analytics_cb_backdrop" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_backdrop]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_cb_backdrop">' . __('Display a backdrop with the cookie bar', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_cb_backdrop'])) {
            esc_attr($this->options['seopress_google_analytics_cb_backdrop']);
        }
    }

    public function seopress_google_analytics_cb_backdrop_bg_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_backdrop_bg']) ? $this->options['seopress_google_analytics_cb_backdrop_bg'] : null;

        echo '<p class="description">' . __('Background color: ', 'wp-seopress') . '</p>';
        printf(
        '<input type="text" data-default-color="rgba(255,255,255,0.8)" data-alpha="true" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_backdrop_bg]" aria-label="' . __('Change the background color of the backdrop', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker color-picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_bg_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_bg']) ? $this->options['seopress_google_analytics_cb_bg'] : null;

        echo '<hr><h2>' . __('Main settings', 'wp-seopress') . '</h2>';

        echo '<p>' . __('Customize the general settings of the <strong>cookie bar</strong>.', 'wp-seopress') . '</p><br>';

        echo '<p class="description">' . __('Background color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" data-alpha="true" data-default-color="#F1F1F1" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_bg]" aria-label="' . __('Change the color of the cookie bar background', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker color-picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_txt_col_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_txt_col']) ? $this->options['seopress_google_analytics_cb_txt_col'] : null;

        echo '<p class="description">' . __('Text color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_txt_col]" aria-label="' . __('Change the color of the cookie bar text', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_lk_col_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_lk_col']) ? $this->options['seopress_google_analytics_cb_lk_col'] : null;

        echo '<p class="description">' . __('Link color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_lk_col]" aria-label="' . __('Change the color of the cookie bar link', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_bg_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_bg']) ? $this->options['seopress_google_analytics_cb_btn_bg'] : null;
        echo '<hr><h2>' . __('Primary button', 'wp-seopress') . '</h2>';

        echo '<p>' . __('Customize the <strong>Accept button</strong>.', 'wp-seopress') . '</p><br>';

        echo '<p class="description">' . __('Background color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" data-alpha="true" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_bg]" aria-label="' . __('Change the color of the cookie bar button background', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker color-picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_bg_hov_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_bg_hov']) ? $this->options['seopress_google_analytics_cb_btn_bg_hov'] : null;

        echo '<p class="description">' . __('Background color on hover: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" data-alpha="true" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_bg_hov]" aria-label="' . __('Change the color of the cookie bar button hover background', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker color-picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_col_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_col']) ? $this->options['seopress_google_analytics_cb_btn_col'] : null;

        echo '<p class="description">' . __('Text color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_col]" aria-label="' . __('Change the color of the cookie bar button', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_col_hov_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_col_hov']) ? $this->options['seopress_google_analytics_cb_btn_col_hov'] : null;

        echo '<p class="description">' . __('Text color on hover: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_col_hov]" aria-label="' . __('Change the color of the cookie bar button hover', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_sec_bg_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_sec_bg']) ? $this->options['seopress_google_analytics_cb_btn_sec_bg'] : null;

        echo '<hr><h2>' . __('Secondary button', 'wp-seopress') . '</h2>';

        echo '<p>' . __('Customize the <strong>Close button</strong>.', 'wp-seopress') . '</p><br>';

        echo '<p class="description">' . __('Background color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" data-alpha="true" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_bg]" aria-label="' . __('Change the color of the cookie bar secondary button background', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker color-picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_sec_col_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_sec_col']) ? $this->options['seopress_google_analytics_cb_btn_sec_col'] : null;

        echo '<p class="description">' . __('Background color on hover: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_col]" aria-label="' . __('Change the color of the cookie bar secondary button hover background', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_sec_bg_hov_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_sec_bg_hov']) ? $this->options['seopress_google_analytics_cb_btn_sec_bg_hov'] : null;

        echo '<p class="description">' . __('Text color: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" data-alpha="true" data-default-color="#222222" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_bg_hov]" aria-label="' . __('Change the color of the cookie bar secondary button', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker color-picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_cb_btn_sec_col_hov_callback() {
        $check = isset($this->options['seopress_google_analytics_cb_btn_sec_col_hov']) ? $this->options['seopress_google_analytics_cb_btn_sec_col_hov'] : null;

        echo '<p class="description">' . __('Text color on hover: ', 'wp-seopress') . '</p>';

        printf(
        '<input type="text" data-default-color="#FFFFFF" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_col_hov]" aria-label="' . __('Change the color of the cookie bar secondary button hover', 'wp-seopress') . '" value="%s" class="seopress_admin_color_picker"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_roles_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        global $wp_roles;

        if ( ! isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        foreach ($wp_roles->get_names() as $key => $value) {
            $check = isset($options['seopress_google_analytics_roles'][$key]);

            echo '<input id="seopress_google_analytics_roles_' . $key . '" name="seopress_google_analytics_option_name[seopress_google_analytics_roles][' . $key . ']" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_google_analytics_roles_' . $key . '"><strong>' . $value . '</strong> (<em>' . translate_user_role($value,  'default') . '</em>)' . '</label><br/>';

            if (isset($this->options['seopress_google_analytics_roles'][$key])) {
                esc_attr($this->options['seopress_google_analytics_roles'][$key]);
            }
        }
    }

    public function seopress_google_analytics_optimize_callback() {
        $check = isset($this->options['seopress_google_analytics_optimize']) ? $this->options['seopress_google_analytics_optimize'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_optimize]" placeholder="' . esc_html__('Enter your Google Optimize container ID', 'wp-seopress') . '" value="%s" aria-label="' . __('GTM-XXXXXXX', 'wp-seopress') . '"/>',
        esc_html($check));

        echo '<p class="description">' . __('Google Optimize offers A/B testing, website testing & personalization tools.', 'wp-seopress') . '
		<a class="seopress-help" href="https://marketingplatform.google.com/about/optimize/" target="_blank">
			' . __('Learn more', 'wp-seopress') . '
		</a>
		<span class="seopress-help dashicons dashicons-external"></span></p>';
    }

    public function seopress_google_analytics_ads_callback() {
        $check = isset($this->options['seopress_google_analytics_ads']) ? $this->options['seopress_google_analytics_ads'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ads]" placeholder="' . esc_html__('Enter your Google Ads conversion ID (eg: AW-123456789)', 'wp-seopress') . '" value="%s" aria-label="' . __('AW-XXXXXXXXX', 'wp-seopress') . '"/>',
        esc_html($check));

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['analytics']['gads'] = 'https://www.seopress.org/fr/support/guides/trouver-votre-id-de-conversion-google-ads/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['analytics']['gads'] = 'https://www.seopress.org/support/guides/how-to-find-your-google-ads-conversions-id/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<p class="description">
				<a class="seopress-help" href="' . $seopress_docs_link['support']['analytics']['gads'] . '" target="_blank">
					' . __('Learn how to find your Google Ads Conversion ID', 'wp-seopress') . '
				</a>
			<span class="seopress-help dashicons dashicons-external"></span></p>';
    }

    public function seopress_google_analytics_other_tracking_callback() {
        $check = isset($this->options['seopress_google_analytics_other_tracking']) ? $this->options['seopress_google_analytics_other_tracking'] : null;

        printf(
        '<textarea id="seopress_google_analytics_other_tracking" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking]" rows="16" placeholder="' . esc_html__('Paste your tracking code here like Google Tag Manager (head)', 'wp-seopress') . '" aria-label="' . __('Additional tracking code field', 'wp-seopress') . '">%s</textarea>',
        esc_textarea($check));

        echo '<p class="description">' . __('This code will be added in the head section of your page.', 'wp-seopress') . '</p>';
    }

    public function seopress_google_analytics_other_tracking_body_callback() {
        $check = isset($this->options['seopress_google_analytics_other_tracking_body']) ? $this->options['seopress_google_analytics_other_tracking_body'] : null;

        printf(
        '<textarea id="seopress_google_analytics_other_tracking_body" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking_body]" rows="16" placeholder="' . esc_html__('Paste your tracking code here like Google Tag Manager (body)', 'wp-seopress') . '" aria-label="' . __('Additional tracking code field added to body', 'wp-seopress') . '">%s</textarea>',
        esc_textarea($check));

        echo '<p class="description">' . __('This code will be added just after the opening body tag of your page.', 'wp-seopress') . '</p>';
        echo '<p class="description">' . __('You don‘t see your code? Make sure to call <strong>wp_body_open();</strong> just after the opening body tag in your theme.', 'wp-seopress') . '</p>';

        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['analytics']['gtm'] = 'https://www.seopress.org/fr/support/guides/google-tag-manager-site-wordpress-seopress/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['analytics']['gtm'] = 'https://www.seopress.org/support/guides/google-tag-manager-wordpress-seopress/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        }

        echo '<a class="seopress-help" href="' . $seopress_docs_link['support']['analytics']['gtm'] . '" target="_blank">' . __('Learn more', 'wp-seopress') . '</span></a><span class="seopress-help dashicons dashicons-external"></span></p>';
    }

    public function seopress_google_analytics_other_tracking_footer_callback() {
        $check = isset($this->options['seopress_google_analytics_other_tracking_footer']) ? $this->options['seopress_google_analytics_other_tracking_footer'] : null;

        printf(
        '<textarea id="seopress_google_analytics_other_tracking_footer" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking_footer]" rows="16" placeholder="' . esc_html__('Paste your tracking code here (body footer)', 'wp-seopress') . '" aria-label="' . __('Additional tracking code field added to body footer', 'wp-seopress') . '">%s</textarea>',
        esc_textarea($check));

        echo '<p class="description">' . __('This code will be added just after the closing body tag of your page.', 'wp-seopress') . '</p>';
    }

    public function seopress_google_analytics_remarketing_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_remarketing']);

        echo '<input id="seopress_google_analytics_remarketing" name="seopress_google_analytics_option_name[seopress_google_analytics_remarketing]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_remarketing">' . __('Enable remarketing, demographics, and interests reporting', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('A remarketing audience is a list of cookies or mobile-advertising IDs that represents a group of users you want to re-engage because of their likelihood to convert.', 'wp-seopress') . '
			<a class="seopress-help" href="https://support.google.com/analytics/answer/2611268?hl=en" target="_blank">' . __('Learn more', 'wp-seopress') . '</a>
			<span class="seopress-help dashicons dashicons-external"></span>
			</p>';

        if (isset($this->options['seopress_google_analytics_remarketing'])) {
            esc_attr($this->options['seopress_google_analytics_remarketing']);
        }
    }

    public function seopress_google_analytics_ip_anonymization_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_ip_anonymization']);

        echo '<input id="seopress_google_analytics_ip_anonymization" name="seopress_google_analytics_option_name[seopress_google_analytics_ip_anonymization]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_ip_anonymization">' . __('Enable IP Anonymization', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('When a customer of Analytics requests IP address anonymization, Analytics anonymizes the address as soon as technically feasible at the earliest possible stage of the collection network.', 'wp-seopress') . '
			<a class="seopress-help" href="https://developers.google.com/analytics/devguides/collection/gtagjs/ip-anonymization" target="_blank">' . __('Learn more', 'wp-seopress') . '</a>
			<span class="seopress-help dashicons dashicons-external"></span>
			</p>';

        if (isset($this->options['seopress_google_analytics_ip_anonymization'])) {
            esc_attr($this->options['seopress_google_analytics_ip_anonymization']);
        }
    }

    public function seopress_google_analytics_link_attribution_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_link_attribution']);

        echo '<input id="seopress_google_analytics_link_attribution" name="seopress_google_analytics_option_name[seopress_google_analytics_link_attribution]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_link_attribution">' . __('Enhanced Link Attribution', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Enhanced Link Attribution improves the accuracy of your In-Page Analytics report by automatically differentiating between multiple links to the same URL on a single page by using link element IDs.', 'wp-seopress') . '
			<a class="seopress-help" href="https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-link-attribution" target="_blank">' . __('Learn more', 'wp-seopress') . '</a>
			<span class="seopress-help dashicons dashicons-external"></span>
			</p>';

        if (isset($this->options['seopress_google_analytics_link_attribution'])) {
            esc_attr($this->options['seopress_google_analytics_link_attribution']);
        }
    }

    public function seopress_google_analytics_cross_enable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_cross_enable']);

        echo '<input id="seopress_google_analytics_cross_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_cross_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_cross_enable">' . __('Enable cross-domain tracking', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Cross domain tracking makes it possible for Analytics to see sessions on two related sites (such as an ecommerce site and a separate shopping cart site) as a single session. This is sometimes called site linking.', 'wp-seopress') . '
			<a class="seopress-help" href="https://developers.google.com/analytics/devguides/collection/gtagjs/cross-domain" target="_blank">' . __('Learn more', 'wp-seopress') . '</a>
			<span class="seopress-help dashicons dashicons-external"></span>
			</p>';

        if (isset($this->options['seopress_google_analytics_cross_enable'])) {
            esc_attr($this->options['seopress_google_analytics_cross_enable']);
        }
    }

    public function seopress_google_analytics_cross_domain_callback() {
        $check = isset($this->options['seopress_google_analytics_cross_domain']) ? $this->options['seopress_google_analytics_cross_domain'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cross_domain]" placeholder="' . esc_html__('Enter your domains: seopress.org,sub.seopress.org,sub2.seopress.org', 'wp-seopress') . '" value="%s" aria-label="' . __('Cross domains', 'wp-seopress') . '"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_link_tracking_enable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_link_tracking_enable']);

        echo '<input id="seopress_google_analytics_link_tracking_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_link_tracking_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_link_tracking_enable">' . __('Enable external links tracking', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_link_tracking_enable'])) {
            esc_attr($this->options['seopress_google_analytics_link_tracking_enable']);
        }
    }

    public function seopress_google_analytics_download_tracking_enable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_download_tracking_enable']);

        echo '<input id="seopress_google_analytics_download_tracking_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_download_tracking_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_download_tracking_enable">' . __('Enable download tracking', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_download_tracking_enable'])) {
            esc_attr($this->options['seopress_google_analytics_download_tracking_enable']);
        }
    }

    public function seopress_google_analytics_download_tracking_callback() {
        $check = isset($this->options['seopress_google_analytics_download_tracking']) ? $this->options['seopress_google_analytics_download_tracking'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_download_tracking]" placeholder="' . esc_html__('pdf|docx|pptx|zip', 'wp-seopress') . '" aria-label="' . __('Track downloads\' clicks', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<p class="description">' . __('Separate each file type extensions with a pipe "|"', 'wp-seopress') . '</a>
			</p>';
    }

    public function seopress_google_analytics_affiliate_tracking_enable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_affiliate_tracking_enable']);

        echo '<input id="seopress_google_analytics_affiliate_tracking_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_affiliate_tracking_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_affiliate_tracking_enable">' . __('Enable affiliate/outbound tracking', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_affiliate_tracking_enable'])) {
            esc_attr($this->options['seopress_google_analytics_affiliate_tracking_enable']);
        }
    }

    public function seopress_google_analytics_affiliate_tracking_callback() {
        $check = isset($this->options['seopress_google_analytics_affiliate_tracking']) ? $this->options['seopress_google_analytics_affiliate_tracking'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_affiliate_tracking]" placeholder="' . esc_html__('aff|go|out', 'wp-seopress') . '" aria-label="' . __('Track affiliate/outbound links', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<p class="description">' . __('Separate each keyword with a pipe "|"', 'wp-seopress') . '</a>
			</p>';
    }

    public function seopress_google_analytics_cd_author_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cd_author']) ? $options['seopress_google_analytics_cd_author'] : null;

        echo '<select id="seopress_google_analytics_cd_author" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_author]">';
        echo ' <option ';
        if ('none' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="none">' . __('None', 'wp-seopress') . '</option>';

        for ($i=1; $i <= 20; ++$i) {
            echo ' <option ';
            if ('dimension' . $i . '' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="dimension' . $i . '">' . sprintf(__('Custom Dimension #%d', 'wp-seopress'), $i) . '</option>';
        }
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_author'])) {
            esc_attr($this->options['seopress_google_analytics_cd_author']);
        }
    }

    public function seopress_google_analytics_cd_category_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cd_category']) ? $options['seopress_google_analytics_cd_category'] : null;

        echo '<select id="seopress_google_analytics_cd_category" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_category]">';
        echo ' <option ';
        if ('none' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="none">' . __('None', 'wp-seopress') . '</option>';

        for ($i=1; $i <= 20; ++$i) {
            echo ' <option ';
            if ('dimension' . $i . '' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="dimension' . $i . '">' . sprintf(__('Custom Dimension #%d', 'wp-seopress'), $i) . '</option>';
        }
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_category'])) {
            esc_attr($this->options['seopress_google_analytics_cd_category']);
        }
    }

    public function seopress_google_analytics_cd_tag_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cd_tag']) ? $options['seopress_google_analytics_cd_tag'] : null;

        echo '<select id="seopress_google_analytics_cd_tag" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_tag]">';
        echo ' <option ';
        if ('none' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="none">' . __('None', 'wp-seopress') . '</option>';

        for ($i=1; $i <= 20; ++$i) {
            echo ' <option ';
            if ('dimension' . $i . '' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="dimension' . $i . '">' . sprintf(__('Custom Dimension #%d', 'wp-seopress'), $i) . '</option>';
        }
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_tag'])) {
            esc_attr($this->options['seopress_google_analytics_cd_tag']);
        }
    }

    public function seopress_google_analytics_cd_post_type_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cd_post_type']) ? $options['seopress_google_analytics_cd_post_type'] : null;

        echo '<select id="seopress_google_analytics_cd_post_type" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_post_type]">';
        echo ' <option ';
        if ('none' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="none">' . __('None', 'wp-seopress') . '</option>';

        for ($i=1; $i <= 20; ++$i) {
            echo ' <option ';
            if ('dimension' . $i . '' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="dimension' . $i . '">' . sprintf(__('Custom Dimension #%d', 'wp-seopress'), $i) . '</option>';
        }
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_post_type'])) {
            esc_attr($this->options['seopress_google_analytics_cd_post_type']);
        }
    }

    public function seopress_google_analytics_cd_logged_in_user_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $selected = isset($options['seopress_google_analytics_cd_logged_in_user']) ? $options['seopress_google_analytics_cd_logged_in_user'] : null;

        echo '<select id="seopress_google_analytics_cd_logged_in_user" name="seopress_google_analytics_option_name[seopress_google_analytics_cd_logged_in_user]">';
        echo ' <option ';
        if ('none' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="none">' . __('None', 'wp-seopress') . '</option>';

        for ($i=1; $i <= 20; ++$i) {
            echo ' <option ';
            if ('dimension' . $i . '' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="dimension' . $i . '">' . sprintf(__('Custom Dimension #%d', 'wp-seopress'), $i) . '</option>';
        }
        echo '</select>';

        if (isset($this->options['seopress_google_analytics_cd_logged_in_user'])) {
            esc_attr($this->options['seopress_google_analytics_cd_logged_in_user']);
        }
    }

    public function seopress_google_analytics_matomo_enable_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_enable']);

        echo '<input id="seopress_google_analytics_matomo_enable" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_enable]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_enable">' . __('Enable Matomo tracking (Matomo account required)', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_matomo_enable'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_enable']);
        }
    }

    public function seopress_google_analytics_matomo_id_callback() {
        $check = isset($this->options['seopress_google_analytics_matomo_id']) ? $this->options['seopress_google_analytics_matomo_id'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_id]" placeholder="' . esc_html__('Enter "example" if you Matomo account URL is "example.matomo.cloud"', 'wp-seopress') . '" value="%s" aria-label="' . __('Matomo Cloud URL', 'wp-seopress') . '"/>',
        esc_html($check)
        );

        echo '<p class="description">' . __('Enter only the <strong>host</strong> like this example.matomo.cloud') . '</p>';
    }

    public function seopress_google_analytics_matomo_site_id_callback() {
        $check = isset($this->options['seopress_google_analytics_matomo_site_id']) ? $this->options['seopress_google_analytics_matomo_site_id'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_site_id]" placeholder="' . esc_html__('Enter your site ID here', 'wp-seopress') . '" value="%s" aria-label="' . __('Matomo Site ID', 'wp-seopress') . '"/>',
        esc_html($check)
        );

        echo '<p class="description">' . __('To find your site ID, go to your <strong>Matomo Cloud account, Websites, Manage page</strong>. Look at "Site ID" on the right part.', 'wp-seopress') . '</p>';
    }

    public function seopress_google_analytics_matomo_subdomains_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_subdomains']);

        echo '<input id="seopress_google_analytics_matomo_subdomains" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_subdomains]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_subdomains">' . __('Tracking one domain and its subdomains in the same website', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('If one visitor visits x.example.com and y.example.com, they will be counted as a unique visitor.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_google_analytics_matomo_subdomains'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_subdomains']);
        }
    }

    public function seopress_google_analytics_matomo_site_domain_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_site_domain']);

        echo '<input id="seopress_google_analytics_matomo_site_domain" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_site_domain]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_site_domain">' . __('Prepend the site domain to the page title when tracking', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('If someone visits the \'About\' page on blog.example.com it will be recorded as \'blog / About\'. This is the easiest way to get an overview of your traffic by sub-domain.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_google_analytics_matomo_site_domain'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_site_domain']);
        }
    }

    public function seopress_google_analytics_matomo_no_js_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_no_js']);

        echo '<input id="seopress_google_analytics_matomo_no_js" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_no_js]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_no_js">' . __('Track users with JavaScript disabled', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_matomo_no_js'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_no_js']);
        }
    }

    public function seopress_google_analytics_matomo_cross_domain_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_cross_domain']);

        echo '<input id="seopress_google_analytics_matomo_cross_domain" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_cross_domain]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_cross_domain">' . __('Enables cross domain linking', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('By default, the visitor ID that identifies a unique visitor is stored in the browser\'s first party cookies which can only be accessed by pages on the same domain. Enabling cross domain linking lets you track all the actions and pageviews of a specific visitor into the same visit even when they view pages on several domains. Whenever a user clicks on a link to one of your website\'s alias URLs, it will append a URL parameter pk_vid forwarding the Visitor ID.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_google_analytics_matomo_cross_domain'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_cross_domain']);
        }
    }

    public function seopress_google_analytics_matomo_cross_domain_sites_callback() {
        $check = isset($this->options['seopress_google_analytics_matomo_cross_domain_sites']) ? $this->options['seopress_google_analytics_matomo_cross_domain_sites'] : null;

        printf(
        '<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_cross_domain_sites]" placeholder="' . esc_html__('Enter your domains: seopress.org,sub.seopress.org,sub2.seopress.org', 'wp-seopress') . '" value="%s" aria-label="' . __('Cross domains', 'wp-seopress') . '"/>',
        esc_html($check)
        );
    }

    public function seopress_google_analytics_matomo_dnt_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_dnt']);

        echo '<input id="seopress_google_analytics_matomo_dnt" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_dnt]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_dnt">' . __('Enable client side DoNotTrack detection', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Tracking requests will not be sent if visitors do not wish to be tracked.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_google_analytics_matomo_dnt'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_dnt']);
        }
    }

    public function seopress_google_analytics_matomo_no_cookies_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_no_cookies']);

        echo '<input id="seopress_google_analytics_matomo_no_cookies" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_no_cookies]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_no_cookies">' . __('Disables all first party cookies. Existing Matomo cookies for this website will be deleted on the next page view.', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_matomo_no_cookies'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_no_cookies']);
        }
    }

    public function seopress_google_analytics_matomo_link_tracking_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_link_tracking']);

        echo '<input id="seopress_google_analytics_matomo_link_tracking" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_link_tracking]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_link_tracking">' . __('Enabling Download & Outlink tracking', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('By default, any file ending with one of these extensions will be considered a "download" in the Matomo interface: 7z|aac|arc|arj|apk|asf|asx|avi|bin|bz|bz2|csv|deb|dmg|doc|
		exe|flv|gif|gz|gzip|hqx|jar|jpg|jpeg|js|mp2|mp3|mp4|mpg|
		mpeg|mov|movie|msi|msp|odb|odf|odg|odp|ods|odt|ogg|ogv|
		pdf|phps|png|ppt|qt|qtm|ra|ram|rar|rpm|sea|sit|tar|
		tbz|tbz2|tgz|torrent|txt|wav|wma|wmv|wpd|xls|xml|z|zip', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_google_analytics_matomo_link_tracking'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_link_tracking']);
        }
    }

    public function seopress_google_analytics_matomo_no_heatmaps_callback() {
        $options = get_option('seopress_google_analytics_option_name');

        $check = isset($options['seopress_google_analytics_matomo_no_heatmaps']);

        echo '<input id="seopress_google_analytics_matomo_no_heatmaps" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_no_heatmaps]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_google_analytics_matomo_no_heatmaps">' . __('Disabling all heatmaps and session recordings', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_google_analytics_matomo_no_heatmaps'])) {
            esc_attr($this->options['seopress_google_analytics_matomo_no_heatmaps']);
        }
    }

    public function seopress_advanced_advanced_attachments_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_attachments']);

        echo '<input id="seopress_advanced_advanced_attachments" name="seopress_advanced_option_name[seopress_advanced_advanced_attachments]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_attachments">' . __('Redirect attachment pages to post parent (or homepage if none)', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_attachments'])) {
            esc_attr($this->options['seopress_advanced_advanced_attachments']);
        }
    }

    public function seopress_advanced_advanced_attachments_file_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_attachments_file']);

        echo '<input id="seopress_advanced_advanced_attachments_file" name="seopress_advanced_option_name[seopress_advanced_advanced_attachments_file]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_attachments_file">' . __('Redirect attachment pages to their file URL (https://www.example.com/my-image-file.jpg)', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('If this option is checked, it will take precedence over the redirection of attachments to the post\'s parent.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_advanced_advanced_attachments_file'])) {
            esc_attr($this->options['seopress_advanced_advanced_attachments_file']);
        }
    }

    public function seopress_advanced_advanced_replytocom_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_replytocom']);

        echo '<input id="seopress_advanced_advanced_replytocom" name="seopress_advanced_option_name[seopress_advanced_advanced_replytocom]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_replytocom">' . __('Remove ?replytocom link in source code', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_replytocom'])) {
            esc_attr($this->options['seopress_advanced_advanced_replytocom']);
        }
    }

    public function seopress_advanced_advanced_image_auto_title_editor_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_image_auto_title_editor']);

        echo '<input id="seopress_advanced_advanced_image_auto_title_editor" name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_title_editor]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_image_auto_title_editor">' . __('When sending an image file, automatically set the title based on the filename', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_image_auto_title_editor'])) {
            esc_attr($this->options['seopress_advanced_advanced_image_auto_title_editor']);
        }
    }

    public function seopress_advanced_advanced_image_auto_alt_editor_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_image_auto_alt_editor']);

        echo '<input id="seopress_advanced_advanced_image_auto_alt_editor" name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_alt_editor]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_image_auto_alt_editor">' . __('When sending an image file, automatically set the alternative text based on the filename', 'wp-seopress') . '</label>';

        if ( ! is_plugin_active('imageseo/imageseo.php')) {
            if (function_exists('seopress_get_toggle_white_label_option') && '1' != seopress_get_toggle_white_label_option()) {
                echo '<p class="seopress-help description"><a href="https://www.seopress.org/go/image-seo" target="_blank">' . __('We recommend Image SEO plugin to optimize your image ALT texts and names for Search Engines using AI and Machine Learning. Starting from just €4.99.', 'wp-seopress') . '</a><span class="dashicons dashicons-external"></span></p>';
            }
        }

        if (isset($this->options['seopress_advanced_advanced_image_auto_alt_editor'])) {
            esc_attr($this->options['seopress_advanced_advanced_image_auto_alt_editor']);
        }
    }

    public function seopress_advanced_advanced_image_auto_alt_target_kw_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_image_auto_alt_target_kw']);

        echo '<input id="seopress_advanced_advanced_image_auto_alt_target_kw" name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_alt_target_kw]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_image_auto_alt_target_kw">' . __('Use the target keywords if not alternative text set for the image', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('This setting will be applied to images without any alt text on frontend only. This setting is retroactive. If you turn it off, alt texts that were previously empty will be empty again.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_advanced_advanced_image_auto_alt_target_kw'])) {
            esc_attr($this->options['seopress_advanced_advanced_image_auto_alt_target_kw']);
        }
    }

    public function seopress_advanced_advanced_image_auto_caption_editor_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_image_auto_caption_editor']);

        echo '<input id="seopress_advanced_advanced_image_auto_caption_editor" name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_caption_editor]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_image_auto_caption_editor">' . __('When sending an image file, automatically set the caption based on the filename', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_image_auto_caption_editor'])) {
            esc_attr($this->options['seopress_advanced_advanced_image_auto_caption_editor']);
        }
    }

    public function seopress_advanced_advanced_image_auto_desc_editor_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_image_auto_desc_editor']);

        echo '<input id="seopress_advanced_advanced_image_auto_desc_editor" name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_desc_editor]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_image_auto_desc_editor">' . __('When sending an image file, automatically set the description based on the filename', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_image_auto_desc_editor'])) {
            esc_attr($this->options['seopress_advanced_advanced_image_auto_desc_editor']);
        }
    }

    public function seopress_advanced_advanced_tax_desc_editor_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_tax_desc_editor']);

        echo '<input id="seopress_advanced_advanced_tax_desc_editor" name="seopress_advanced_option_name[seopress_advanced_advanced_tax_desc_editor]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_tax_desc_editor">' . __('Add TINYMCE editor to term description', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_tax_desc_editor'])) {
            esc_attr($this->options['seopress_advanced_advanced_tax_desc_editor']);
        }
    }

    public function seopress_advanced_advanced_category_url_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_category_url']);

        echo '<input id="seopress_advanced_advanced_category_url" name="seopress_advanced_option_name[seopress_advanced_advanced_category_url]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_category_url">' . __('Remove /category/ in your permalinks', 'wp-seopress') . '</label><span class="dashicons dashicons-info" title="' . __('You have to flush your permalinks each time you change this settings', 'wp-seopress') . '"></span>';

        if (isset($this->options['seopress_advanced_advanced_category_url'])) {
            esc_attr($this->options['seopress_advanced_advanced_category_url']);
        }
    }

    public function seopress_advanced_advanced_trailingslash_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_trailingslash']);

        echo '<input id="seopress_advanced_advanced_trailingslash" name="seopress_advanced_option_name[seopress_advanced_advanced_trailingslash]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_trailingslash">' . __('Disable trailing slash for metas', 'wp-seopress') . '</label><span class="dashicons dashicons-info" title="' . __('You must check this box if the structure of your permalinks DOES NOT contain a slash at the end (eg: /%postname%)', 'wp-seopress') . '"></span>';

        if (isset($this->options['seopress_advanced_advanced_trailingslash'])) {
            esc_attr($this->options['seopress_advanced_advanced_trailingslash']);
        }
    }

    public function seopress_advanced_advanced_wp_generator_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_wp_generator']);

        echo '<input id="seopress_advanced_advanced_wp_generator" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_generator]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_wp_generator">' . __('Remove WordPress meta generator in source code', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_generator'])) {
            esc_attr($this->options['seopress_advanced_advanced_wp_generator']);
        }
    }

    public function seopress_advanced_advanced_hentry_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_hentry']);

        echo '<input id="seopress_advanced_advanced_hentry" name="seopress_advanced_option_name[seopress_advanced_advanced_hentry]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_hentry">' . __('Remove hentry post class to prevent Google from seeing this as structured data (schema)', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_hentry'])) {
            esc_attr($this->options['seopress_advanced_advanced_hentry']);
        }
    }

    public function seopress_advanced_advanced_comments_author_url_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_comments_author_url']);

        echo '<input id="seopress_advanced_advanced_comments_author_url" name="seopress_advanced_option_name[seopress_advanced_advanced_comments_author_url]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_comments_author_url">' . __('Remove comment author URL in comments if the website is filled from profile page', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_comments_author_url'])) {
            esc_attr($this->options['seopress_advanced_advanced_comments_author_url']);
        }
    }

    public function seopress_advanced_advanced_comments_website_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_comments_website']);

        echo '<input id="seopress_advanced_advanced_comments_website" name="seopress_advanced_option_name[seopress_advanced_advanced_comments_website]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_comments_website">' . __('Remove website field from comment form to reduce spam', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_advanced_comments_website'])) {
            esc_attr($this->options['seopress_advanced_advanced_comments_website']);
        }
    }

    public function seopress_advanced_advanced_wp_shortlink_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_wp_shortlink']);

        echo '<input id="seopress_advanced_advanced_wp_shortlink" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_shortlink]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_wp_shortlink">' . __('Remove WordPress shortlink meta tag in source code (eg:', 'wp-seopress') . '<em>' . esc_attr('<link rel="shortlink" href="https://www.seopress.org/"/>') . '</em>)</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_shortlink'])) {
            esc_attr($this->options['seopress_advanced_advanced_wp_shortlink']);
        }
    }

    public function seopress_advanced_advanced_wp_wlw_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_wp_wlw']);

        echo '<input id="seopress_advanced_advanced_wp_wlw" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_wlw]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_wp_wlw">' . __('Remove Windows Live Writer meta tag in source code (eg:', 'wp-seopress') . '<em>' . esc_attr('<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="https://www.seopress.org/wp-includes/wlwmanifest.xml" />') . '</em>)</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_wlw'])) {
            esc_attr($this->options['seopress_advanced_advanced_wp_wlw']);
        }
    }

    public function seopress_advanced_advanced_wp_rsd_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_advanced_wp_rsd']);

        echo '<input id="seopress_advanced_advanced_wp_rsd" name="seopress_advanced_option_name[seopress_advanced_advanced_wp_rsd]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_advanced_wp_rsd">' . __('Remove Really Simple Discovery meta tag in source code (eg:', 'wp-seopress') . '<em>' . esc_attr('<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://www.seopress.dev/xmlrpc.php?rsd" />') . '</em>)</label>';

        if (isset($this->options['seopress_advanced_advanced_wp_rsd'])) {
            esc_attr($this->options['seopress_advanced_advanced_wp_rsd']);
        }
    }

    public function seopress_advanced_advanced_google_callback() {
        $check = isset($this->options['seopress_advanced_advanced_google']) ? $this->options['seopress_advanced_advanced_google'] : null;

        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_google]" placeholder="' . esc_html__('Enter Google meta value site verification', 'wp-seopress') . '" aria-label="' . __('Google site verification', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );

        echo '<p class="description">' . __('If your site is already verified in <strong>Google Search Console</strong>, you can leave this field empty.', 'wp-seopress') . '</p>';
    }

    public function seopress_advanced_advanced_bing_callback() {
        $check = isset($this->options['seopress_advanced_advanced_bing']) ? $this->options['seopress_advanced_advanced_bing'] : null;

        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_bing]" placeholder="' . esc_html__('Enter Bing meta value site verification', 'wp-seopress') . '" aria-label="' . __('Bing site verification', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
        echo '<p class="description">' . __('If your site is already verified in <strong>Bing Webmaster tools</strong>, you can leave this field empty.', 'wp-seopress') . '</p>';
    }

    public function seopress_advanced_advanced_pinterest_callback() {
        $check = isset($this->options['seopress_advanced_advanced_pinterest']) ? $this->options['seopress_advanced_advanced_pinterest'] : null;

        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_pinterest]" placeholder="' . esc_html__('Enter Pinterest meta value site verification', 'wp-seopress') . '" aria-label="' . __('Pinterest site verification', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_advanced_advanced_yandex_callback() {
        $check = isset($this->options['seopress_advanced_advanced_yandex']) ? $this->options['seopress_advanced_advanced_yandex'] : null;

        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_yandex]" aria-label="' . __('Yandex site verification', 'wp-seopress') . '" placeholder="' . esc_html__('Enter Yandex meta value site verification', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
        );
    }

    public function seopress_advanced_appearance_adminbar_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_adminbar']);

        echo '<input id="seopress_advanced_appearance_adminbar" name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_adminbar">' . __('Remove SEO from Admin Bar in backend and frontend', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_adminbar'])) {
            esc_attr($this->options['seopress_advanced_appearance_adminbar']);
        }
    }

    public function seopress_advanced_appearance_adminbar_noindex_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_adminbar_noindex']);

        echo '<input id="seopress_advanced_appearance_adminbar_noindex" name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar_noindex]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_adminbar_noindex">' . __('Remove noindex item from Admin Bar in backend and frontend', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_adminbar_noindex'])) {
            esc_attr($this->options['seopress_advanced_appearance_adminbar_noindex']);
        }
    }

    public function seopress_advanced_appearance_metaboxe_position_callback() {
        $options = get_option('seopress_advanced_option_name');

        $selected = isset($options['seopress_advanced_appearance_metaboxe_position']) ? $options['seopress_advanced_appearance_metaboxe_position'] : null;

        echo '<select id="seopress_advanced_appearance_metaboxe_position" name="seopress_advanced_option_name[seopress_advanced_appearance_metaboxe_position]">';
        echo ' <option ';
        if ('high' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="high">' . __('High priority (top)', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('default' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="default">' . __('Normal priority (default)', 'wp-seopress') . '</option>';
        echo '<option ';
        if ('low' == $selected) {
            echo 'selected="selected"';
        }
        echo ' value="low">' . __('Low priority', 'wp-seopress') . '</option>';
        echo '</select>';

        if (isset($this->options['seopress_advanced_appearance_metaboxe_position'])) {
            esc_attr($this->options['seopress_advanced_appearance_metaboxe_position']);
        }
    }

    public function seopress_advanced_appearance_schema_default_tab_callback() {
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $options = get_option('seopress_advanced_option_name');

            $selected = isset($options['seopress_advanced_appearance_schema_default_tab']) ? $options['seopress_advanced_appearance_schema_default_tab'] : null;

            echo '<select id="seopress_advanced_appearance_schema_default_tab" name="seopress_advanced_option_name[seopress_advanced_appearance_schema_default_tab]">';
            echo '<option ';
            if ('automatic' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="automatic">' . __('Automatic tab (default)', 'wp-seopress') . '</option>';
            echo ' <option ';
            if ('manual' == $selected) {
                echo 'selected="selected"';
            }
            echo ' value="manual">' . __('Manual tab', 'wp-seopress') . '</option>';
            echo '</select>';

            if (isset($this->options['seopress_advanced_appearance_schema_default_tab'])) {
                esc_attr($this->options['seopress_advanced_appearance_schema_default_tab']);
            }
        }
    }

    public function seopress_advanced_appearance_notifications_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_notifications']);

        echo '<input id="seopress_advanced_appearance_notifications" name="seopress_advanced_option_name[seopress_advanced_appearance_notifications]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_notifications">' . __('Hide Notifications Center in SEO Dashboard page', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_notifications'])) {
            esc_attr($this->options['seopress_advanced_appearance_notifications']);
        }
    }

    public function seopress_advanced_appearance_seo_tools_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_seo_tools']);

        echo '<input id="seopress_advanced_appearance_seo_tools" name="seopress_advanced_option_name[seopress_advanced_appearance_seo_tools]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_seo_tools">' . __('Hide SEO tools in SEO Dashboard page', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_seo_tools'])) {
            esc_attr($this->options['seopress_advanced_appearance_seo_tools']);
        }
    }

    public function seopress_advanced_appearance_useful_links_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_useful_links']);

        echo '<input id="seopress_advanced_appearance_useful_links" name="seopress_advanced_option_name[seopress_advanced_appearance_useful_links]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_useful_links">' . __('Hide Useful Links in SEO dashboard page', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_useful_links'])) {
            esc_attr($this->options['seopress_advanced_appearance_useful_links']);
        }
    }

    public function seopress_advanced_appearance_title_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_title_col']);

        echo '<input id="seopress_advanced_appearance_title_col" name="seopress_advanced_option_name[seopress_advanced_appearance_title_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_title_col">' . __('Add title column', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_title_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_title_col']);
        }
    }

    public function seopress_advanced_appearance_meta_desc_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_meta_desc_col']);

        echo '<input id="seopress_advanced_appearance_meta_desc_col" name="seopress_advanced_option_name[seopress_advanced_appearance_meta_desc_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_meta_desc_col">' . __('Add meta description column', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_meta_desc_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_meta_desc_col']);
        }
    }

    public function seopress_advanced_appearance_redirect_enable_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_redirect_enable_col']);

        echo '<input id="seopress_advanced_appearance_redirect_enable_col" name="seopress_advanced_option_name[seopress_advanced_appearance_redirect_enable_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_redirect_enable_col">' . __('Add redirection enable column', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_redirect_enable_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_redirect_enable_col']);
        }
    }

    public function seopress_advanced_appearance_redirect_url_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_redirect_url_col']);

        echo '<input id="seopress_advanced_appearance_redirect_url_col" name="seopress_advanced_option_name[seopress_advanced_appearance_redirect_url_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_redirect_url_col">' . __('Add redirection URL column', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_redirect_url_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_redirect_url_col']);
        }
    }

    public function seopress_advanced_appearance_canonical_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_canonical']);

        echo '<input id="seopress_advanced_appearance_canonical" name="seopress_advanced_option_name[seopress_advanced_appearance_canonical]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_canonical">' . __('Add canonical URL column', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_canonical'])) {
            esc_attr($this->options['seopress_advanced_appearance_canonical']);
        }
    }

    public function seopress_advanced_appearance_target_kw_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_target_kw_col']);

        echo '<input id="seopress_advanced_appearance_target_kw_col" name="seopress_advanced_option_name[seopress_advanced_appearance_target_kw_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_target_kw_col">' . __('Add target keyword column', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_target_kw_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_target_kw_col']);
        }
    }

    public function seopress_advanced_appearance_noindex_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_noindex_col']);

        echo '<input id="seopress_advanced_appearance_noindex_col" name="seopress_advanced_option_name[seopress_advanced_appearance_noindex_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_noindex_col">' . __('Display noindex status', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_noindex_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_noindex_col']);
        }
    }

    public function seopress_advanced_appearance_nofollow_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_nofollow_col']);

        echo '<input id="seopress_advanced_appearance_nofollow_col" name="seopress_advanced_option_name[seopress_advanced_appearance_nofollow_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_nofollow_col">' . __('Display nofollow status', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_nofollow_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_nofollow_col']);
        }
    }

    public function seopress_advanced_appearance_words_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_words_col']);

        echo '<input id="seopress_advanced_appearance_words_col" name="seopress_advanced_option_name[seopress_advanced_appearance_words_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_words_col">' . __('Display total number of words in content', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_words_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_words_col']);
        }
    }

    public function seopress_advanced_appearance_w3c_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_w3c_col']);

        echo '<input id="seopress_advanced_appearance_w3c_col" name="seopress_advanced_option_name[seopress_advanced_appearance_w3c_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_w3c_col">' . __('Display W3C column to check code quality', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_w3c_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_w3c_col']);
        }
    }

    public function seopress_advanced_appearance_ps_col_callback() {
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $options = get_option('seopress_advanced_option_name');

            $check = isset($options['seopress_advanced_appearance_ps_col']);

            echo '<input id="seopress_advanced_appearance_ps_col" name="seopress_advanced_option_name[seopress_advanced_appearance_ps_col]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_advanced_appearance_ps_col">' . __('Display Page Speed column to check performances', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_advanced_appearance_ps_col'])) {
                esc_attr($this->options['seopress_advanced_appearance_ps_col']);
            }
        }
    }

    public function seopress_advanced_appearance_insights_col_callback() {
        if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
            $options = get_option('seopress_advanced_option_name');

            $check = isset($options['seopress_advanced_appearance_insights_col']);

            echo '<input id="seopress_advanced_appearance_insights_col" name="seopress_advanced_option_name[seopress_advanced_appearance_insights_col]" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_advanced_appearance_insights_col">' . __('Display SEO Insights column to check rankings', 'wp-seopress') . '</label>';

            if (isset($this->options['seopress_advanced_appearance_insights_col'])) {
                esc_attr($this->options['seopress_advanced_appearance_insights_col']);
            }
        }
    }

    public function seopress_advanced_appearance_score_col_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_score_col']);

        echo '<input id="seopress_advanced_appearance_score_col" name="seopress_advanced_option_name[seopress_advanced_appearance_score_col]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_score_col">' . __('Display Content Analysis results column ("Good" or "Should be improved")', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_score_col'])) {
            esc_attr($this->options['seopress_advanced_appearance_score_col']);
        }
    }

    public function seopress_advanced_appearance_genesis_seo_metaboxe_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_genesis_seo_metaboxe']);

        echo '<input id="seopress_advanced_appearance_genesis_seo_metaboxe" name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_metaboxe]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_genesis_seo_metaboxe">' . __('Remove Genesis SEO Metabox', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_genesis_seo_metaboxe'])) {
            esc_attr($this->options['seopress_advanced_appearance_genesis_seo_metaboxe']);
        }
    }

    public function seopress_advanced_appearance_genesis_seo_menu_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_genesis_seo_menu']);

        echo '<input id="seopress_advanced_appearance_genesis_seo_menu" name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_menu]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_genesis_seo_menu">' . __('Remove Genesis SEO link in WP Admin Menu', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_genesis_seo_menu'])) {
            esc_attr($this->options['seopress_advanced_appearance_genesis_seo_menu']);
        }
    }

    public function seopress_advanced_appearance_advice_schema_callback() {
        $options = get_option('seopress_advanced_option_name');

        $check = isset($options['seopress_advanced_appearance_advice_schema']);

        echo '<input id="seopress_advanced_appearance_advice_schema" name="seopress_advanced_option_name[seopress_advanced_appearance_advice_schema]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_advanced_appearance_advice_schema">' . __('Remove the advice if None schema selected', 'wp-seopress') . '</label>';

        if (isset($this->options['seopress_advanced_appearance_advice_schema'])) {
            esc_attr($this->options['seopress_advanced_appearance_advice_schema']);
        }
    }

    public function seopress_advanced_security_metaboxe_role_callback() {
        $options = get_option('seopress_advanced_option_name');

        global $wp_roles;

        if ( ! isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        foreach ($wp_roles->get_names() as $key => $value) {
            $check = isset($options['seopress_advanced_security_metaboxe_role'][$key]);

            echo '<input id="seopress_advanced_security_metaboxe_role_' . $key . '" name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_role][' . $key . ']" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_advanced_security_metaboxe_role_' . $key . '"><strong>' . $value . '</strong> (<em>' . translate_user_role($value,  'default') . '</em>)' . '</label><br/>';

            if (isset($this->options['seopress_advanced_security_metaboxe_role'][$key])) {
                esc_attr($this->options['seopress_advanced_security_metaboxe_role'][$key]);
            }
        }
        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['security']['metaboxe_seo'] = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-seo-par-types-de-contenu/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['security']['metaboxe_seo'] = 'https://www.seopress.org/support/hooks/filter-seo-metaboxe-call-by-post-type/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } ?>
		<a href="<?php echo $seopress_docs_link['support']['security']['metaboxe_seo']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Hook to filter structured data types metabox call by post type - new window', 'wp-seopress'); ?></span></a>
		<?php
    }

    public function seopress_advanced_security_metaboxe_ca_role_callback() {
        $options = get_option('seopress_advanced_option_name');

        global $wp_roles;

        if ( ! isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        foreach ($wp_roles->get_names() as $key => $value) {
            $check = isset($options['seopress_advanced_security_metaboxe_ca_role'][$key]);

            echo '<input id="seopress_advanced_security_metaboxe_ca_role_' . $key . '" name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_ca_role][' . $key . ']" type="checkbox"';
            if ('1' == $check) {
                echo 'checked="yes"';
            }
            echo ' value="1"/>';

            echo '<label for="seopress_advanced_security_metaboxe_ca_role_' . $key . '"><strong>' . $value . '</strong> (<em>' . translate_user_role($value,  'default') . '</em>)' . '</label><br/>';

            if (isset($this->options['seopress_advanced_security_metaboxe_ca_role'][$key])) {
                esc_attr($this->options['seopress_advanced_security_metaboxe_ca_role'][$key]);
            }
        }
        if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
            $seopress_docs_link['support']['security']['metaboxe_ca'] = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-danalyse-de-contenu-par-types-de-contenu/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } else {
            $seopress_docs_link['support']['security']['metaboxe_ca'] = 'https://www.seopress.org/support/hooks/filter-content-analysis-metaboxe-call-by-post-type/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
        } ?>
		<a href="<?php echo $seopress_docs_link['support']['security']['metaboxe_ca']; ?>" target="_blank" class="seopress-doc"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Hook to filter structured data types metabox call by post type - new window', 'wp-seopress'); ?></span></a>
		<?php
    }

    public function seopress_setting_section_tools_compatibility_oxygen_callback() {
        $options = get_option('seopress_tools_option_name');

        $check = isset($options['seopress_setting_section_tools_compatibility_oxygen']);

        echo '<input id="seopress_setting_section_tools_compatibility_oxygen" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_oxygen]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_setting_section_tools_compatibility_oxygen">' . __('Enable Oxygen Builder compatibility', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Enable automatic meta description with <strong>%%oxygen%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_setting_section_tools_compatibility_oxygen'])) {
            esc_attr($this->options['seopress_setting_section_tools_compatibility_oxygen']);
        }
    }

    public function seopress_setting_section_tools_compatibility_divi_callback() {
        $options = get_option('seopress_tools_option_name');

        $check = isset($options['seopress_setting_section_tools_compatibility_divi']);

        echo '<input id="seopress_setting_section_tools_compatibility_divi" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_divi]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_setting_section_tools_compatibility_divi">' . __('Enable Divi Builder compatibility', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Enable automatic meta description with <strong>%%divi%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_setting_section_tools_compatibility_divi'])) {
            esc_attr($this->options['seopress_setting_section_tools_compatibility_divi']);
        }
    }

    public function seopress_setting_section_tools_compatibility_bakery_callback() {
        $options = get_option('seopress_tools_option_name');

        $check = isset($options['seopress_setting_section_tools_compatibility_bakery']);

        echo '<input id="seopress_setting_section_tools_compatibility_bakery" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_bakery]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_setting_section_tools_compatibility_bakery">' . __('Enable WP Bakery Builder compatibility', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Enable automatic meta description with <strong>%%wpbakery%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_setting_section_tools_compatibility_bakery'])) {
            esc_attr($this->options['seopress_setting_section_tools_compatibility_bakery']);
        }
    }

    public function seopress_setting_section_tools_compatibility_avia_callback() {
        $options = get_option('seopress_tools_option_name');

        $check = isset($options['seopress_setting_section_tools_compatibility_avia']);

        echo '<input id="seopress_setting_section_tools_compatibility_avia" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_avia]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_setting_section_tools_compatibility_avia">' . __('Enable Avia Layout Builder compatibility', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Enable automatic meta description with <strong>%%aviabuilder%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_setting_section_tools_compatibility_avia'])) {
            esc_attr($this->options['seopress_setting_section_tools_compatibility_avia']);
        }
    }

    public function seopress_setting_section_tools_compatibility_fusion_callback() {
        $options = get_option('seopress_tools_option_name');

        $check = isset($options['seopress_setting_section_tools_compatibility_fusion']);

        echo '<input id="seopress_setting_section_tools_compatibility_fusion" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_fusion]" type="checkbox"';
        if ('1' == $check) {
            echo 'checked="yes"';
        }
        echo ' value="1"/>';

        echo '<label for="seopress_setting_section_tools_compatibility_fusion">' . __('Enable Fusion Builder compatibility', 'wp-seopress') . '</label>';

        echo '<p class="description">' . __('Enable automatic meta description with <strong>%%fusionbuilder%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

        if (isset($this->options['seopress_setting_section_tools_compatibility_fusion'])) {
            esc_attr($this->options['seopress_setting_section_tools_compatibility_fusion']);
        }
    }
}

if (is_admin()) {
    $my_settings_page = new seopress_options();
}
