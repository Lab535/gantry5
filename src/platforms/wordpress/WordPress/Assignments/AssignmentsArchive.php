<?php

/**
 * @package   Gantry5
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   GNU/GPLv2 and later
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Gantry\WordPress\Assignments;

class AssignmentsArchive {
    var $type = 'archive';

    public function getTaxonomies($args = []) {
        $defaults = [
            'show_ui' => true
        ];

        $args = wp_parse_args($args, $defaults);

        $taxonomies = get_taxonomies(apply_filters('g5_assignments_get_taxonomies_args', $args), 'object');

        return $taxonomies;
    }

    public function getItems($tax, $args = []) {
        $items = [];

        $defaults = [
            'child_of'                 => 0,
            'exclude'                  => '',
            'hide_empty'               => false,
            'hierarchical'             => 1,
            'include'                  => '',
            'include_last_update_time' => false,
            'order'                    => 'ASC',
            'orderby'                  => 'name',
            'pad_counts'               => false,
        ];

        $args = wp_parse_args($args, $defaults);

        $terms = get_terms($tax->name, $args);

        if(empty($terms) || is_wp_error($terms)) {
            $items[] = [
                'name'     => '',
                'label'    => 'No items',
                'disabled' => true
            ];
        } else {
            $walker = new AssignmentsWalker;

            $new_terms = [];
            foreach($terms as $new_term) {
                $new_term->id           = $new_term->term_id;
                $new_term->parent_id    = $new_term->parent;
                $new_terms[] = $new_term;
            }

            $terms = $walker->walk($new_terms, 0);

            foreach($terms as $term) {
                $items[] = [
                    'name'     => $this->type . '[' . $term->term_id . ']',
                    'label'    => $term->level > 0 ? str_repeat('—', $term->level) . ' ' . $term->name : $term->name,
                    'disabled' => false
                ];
            }
        }

        return apply_filters('g5_assignments_' . $this->type . '_' . $tax->name . '_taxonomy_list_items', $items, $tax, $this->type);

    }

}