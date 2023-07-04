<?php
function email_generator_register_post_type() {
  $args = [
    'label'  => esc_html__( 'Email Templates', 'text-domain' ),
    'labels' => [
      'menu_name'          => esc_html__( 'Email Templates', 'email-generator' ),
      'name_admin_bar'     => esc_html__( 'Email Template', 'email-generator' ),
      'add_new'            => esc_html__( 'Add Email Template', 'email-generator' ),
      'add_new_item'       => esc_html__( 'Add new Email Template', 'email-generator' ),
      'new_item'           => esc_html__( 'New Email Template', 'email-generator' ),
      'edit_item'          => esc_html__( 'Edit Email Template', 'email-generator' ),
      'view_item'          => esc_html__( 'View Email Template', 'email-generator' ),
      'update_item'        => esc_html__( 'View Email Template', 'email-generator' ),
      'all_items'          => esc_html__( 'All Email Templates', 'email-generator' ),
      'search_items'       => esc_html__( 'Search Email Templates', 'email-generator' ),
      'parent_item_colon'  => esc_html__( 'Parent Email Template', 'email-generator' ),
      'not_found'          => esc_html__( 'No Email Templates found', 'email-generator' ),
      'not_found_in_trash' => esc_html__( 'No Email Templates found in Trash', 'email-generator' ),
      'name'               => esc_html__( 'Email Templates', 'email-generator' ),
      'singular_name'      => esc_html__( 'Email Template', 'email-generator' ),
    ],
    'public'              => true,
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'show_ui'             => true,
    'show_in_nav_menus'   => false,
    'show_in_admin_bar'   => true,
    'show_in_rest'        => false,
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'has_archive'         => 'emails',
    'query_var'           => true,
    'can_export'          => true,
    'rewrite_no_front'    => false,
    'show_in_menu'        => true,
    'menu_position'       => 15,
    'menu_icon'           => 'dashicons-email',
    'supports' => [
      'title',
    ],

    'rewrite' => true
  ];

  register_post_type( 'email-template', $args );
}
add_action( 'init', 'email_generator_register_post_type' );