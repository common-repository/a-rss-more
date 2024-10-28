<?php
/*
Plugin Name: (a) RSS More
Plugin URI: http://anton.shevchuk.name/
Description: This is a plugin that allows you to additionally export RSS with the full text of the articles. Your reader can now select what RSS he wants to read himself.
Version: 0.0.2
Author: Anton Shevchuk
Author URI: http://anton.shevchuk.name/
*/
/*  Copyright 2010  Anton Shevchuk  (email : AntonShevchuk@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class aRssMore
{

    /**
     * init
     */
    function init()
    {
        add_filter("the_content_feed", array(__CLASS__, "feed"));
        add_filter('query_vars', array(__CLASS__, "query_vars"));

        add_action('template_redirect', array(__CLASS__, "noredirect"), 9);
        add_action("wp_head", array(__CLASS__, "link"), -5);

        add_action("admin_menu", array(__CLASS__, "admin_menu"));

        register_activation_hook(__FILE__, array(__CLASS__, "activate"));
        register_deactivation_hook(__FILE__, array(__CLASS__, "deactivate"));
    }

    /**
     * Activate plugin
     * @return void
     */
    function activate()
    {
        if (!get_option('rssopt_announce_feed_name')) {
            update_option('rssopt_announce_rss_link', true);
//            update_option('rssopt_full_rss_link', false);
            update_option('rssopt_announce_feed_name', __('%1$s %2$s Announce Feed'));
//            update_option('rssopt_full_feed_name', __('%1$s %2$s Full Feed'));
            update_option('rssopt_more_link_text', __('(more)'));
        }
    }

    /**
     * DeActivate plugin
     * @return void
     */
    function deactivate()
    {
        if (get_option('rssopt_announce_feed_name')) {
            delete_option('rssopt_announce_rss_link');
//            delete_option('rssopt_full_rss_link');
            delete_option('rssopt_announce_feed_name');
//            delete_option('rssopt_full_feed_name');
            delete_option('rssopt_more_link_text');
        }
    }

    /**
     * admin_menu action
     */
    function admin_menu()
    {
        add_settings_section('aRss', '(a) RSS More', array(__CLASS__, "settings_section"), 'reading');
        add_filter('whitelist_options', array(__CLASS__, "whitelist_options"));
    }

    /**
     * Reading settings section
     */
    function whitelist_options($whitelist_options)
    {
        $whitelist_options['reading'][] = 'rssopt_announce_rss_link';
//        $whitelist_options['reading'][] = 'rssopt_full_rss_link';
        $whitelist_options['reading'][] = 'rssopt_announce_feed_name';
//        $whitelist_options['reading'][] = 'rssopt_full_feed_name';
        $whitelist_options['reading'][] = 'rssopt_more_link_text';
        return $whitelist_options;
    }

    function settings_section()
    {
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Insert "announce feed" link'); ?></th>
                <td>
                    <input name="rssopt_announce_rss_link" type="checkbox" id="rssopt_announce_rss_link"
                           value="1" <?php if (get_option('rssopt_announce_rss_link')) echo 'checked'; ?>" />
                    <label for="rssopt_announce_rss_link"><?php _e('Add "Announce RSS feed" link to the Html header'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="rssopt_announce_feed_name"><?php _e('Announce feed name'); ?></label></th>
                <td>
                    <input name="rssopt_announce_feed_name" type="text" id="rssopt_announce_feed_name"
                           value="<?php form_option('rssopt_announce_feed_name'); ?>" class="regular-text"/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="rssopt_more_link_text"><?php _e('More link text'); ?></label></th>
                <td>
                    <input name="rssopt_more_link_text" type="text" id="rssopt_more_link_text"
                           value="<?php form_option('rssopt_more_link_text'); ?>" class="regular-text"/>
                </td>
            </tr>
        </table>
        <?php

    }

    /**
     *
     * @access public
     * @param  $content Content of post
     * @return string
     */
    function feed($content)
    {
        global $wp, $id;
        // makes the short feed
        $more_link_text = get_option('rssopt_more_link_text');
        $more_link = apply_filters('the_content_more_link',
                                   ' <a href="' . get_permalink() . "#more-$id\" class=\"more-link\">$more_link_text</a>",
                                   $more_link_text);

        if (isset($wp->query_vars['announce'])
                && $wp->query_vars['announce']) {
            if (preg_match('/<span id="more-([0-9]+)"><\/span>/', $content, $matches)) {
                $content = substr($content, 0, strpos($content, $matches[0], 1));
                return $content . $more_link;
            }
            return $content;
        }

        $pos = strpos($content, "<!--more-->", 1);
        if ($pos !== false) {
            return substr($content, 0, $pos) . $more_link;
        }
        return $content;
    }

    /**
     * @param  $link
     * @return string
     */
    function link($args = array())
    {
        if (!current_theme_supports('automatic-feed-links'))
            return;

        if (!get_option('rssopt_announce_rss_link') && !get_option('rssopt_full_rss_link'))
            return;

        $defaults = array(
            /* translators: Separator between blog name and feed type in feed links */
            'separator' => _x('&raquo;', 'feed link'),
            /* translators: 1: blog title, 2: separator (raquo) */
            'feedtitle' => get_option('rssopt_announce_feed_name'),
        );

        $args = wp_parse_args($args, $defaults);

        $link = get_feed_link();

        if (false != strpos($link, '?')) {
            $link .= '&announce=1';
        } else {
            $link .= '?announce=1';
        }
        echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr(sprintf(
            $args['feedtitle'], get_bloginfo('name'), $args['separator'])) . '" href="' . $link . "\" />\n";
    }

    /**
     * register query vars
     *
     * @param array $vars
     * @return array
     */
    function query_vars($vars)
    {
        $vars[] = 'announce';
        return $vars;
    }

    /**
     * noredirect
     *
     * @return array
     */
    function noredirect()
    {
        global $wp;
        if (isset($wp->query_vars['announce'])
                && $wp->query_vars['announce']) {
            remove_action('template_redirect', 'ol_feed_redirect');
        }
    }
}

aRssMore::init();