<?php
$fields = MakeitWorkPress\WP_Custom_Fields\Framework::instance();

$fields->add( 'options', [
  'title' => __( 'Template Options', 'email-generator' ),
  'id'  => 'email_template_options',
  'menu_title'  => __( 'Template Options', 'email-generator' ),
  'capability' => 'manage_options',
  'location'  => 'submenu',
  'slug'  => 'edit.php?post_type=email-template',
  'sections'  => [
    [
      'id'  => 'section-1',
      'title'         => __( 'Global Options', 'email-generator' ),
      'fields'  => [
        [
          'id'  => 'post_selector_excludes',
          'title' => __( 'Categories to Exclude', 'email-generator' ),
          'description' => __( 'Select categories you wish to exclude from the Post selectors.', 'email-generator' ),
          'type'  => 'select',
          'multiple' => true,
          'object'  => 'term',
          'source'  => 'category',
          'columns' => 'full',
          'class'   => 'post-selector-excludes',
        ],
      ],
    ],
  ],
]);

/**
 * Adds styling for this plugin's options page.
 */
function add_email_template_admin_head(){
  echo '<style>.post-selector-excludes .select2.select2-container.select2-container--default{width: 100% !important;}</style>';
}
add_action( 'admin_head-email-template_page_email_template_options', 'add_email_template_admin_head' );