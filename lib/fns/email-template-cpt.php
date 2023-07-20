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

/**
 * Adds the HTML to the Rendered HTML field.
 */
function email_template_rendered_html(){
  add_meta_box( 'rendered-html-meta-box', 'Final HTML', function( $post ){
    $template = get_post_meta( $post->ID, 'template', true );
    $stored_post_ids = get_post_meta( $post->ID, 'posts', true );

    // Build an array of $posts from the Posts saved to this Email Template
    $posts = [];
    if( $stored_post_ids && is_array( $stored_post_ids ) && 0 < count( $stored_post_ids ) && ! empty( $template ) ){
      $x = 1;
      foreach( $stored_post_ids as $stored_post_id ){
        $post_id = $stored_post_id['post'];
        $thumbnail_size = ( 1 === $x )? 'large' : 'medium';
        $posts[$x] = [
          'post_title'      => get_the_title( $post_id ),
          'featured_image'  => get_the_post_thumbnail_url( $post_id, $thumbnail_size ),
          'permalink'       => get_permalink( $post_id ),
          'date'            => get_the_date( 'm/d/Y', $post_id ),
        ];
        $x++;
      }

      // Replace the post data variables in the template (i.e. {{post_1_post_title}}, {{post_1_permalink}}, etc.).
      foreach( $posts as $post_number => $post_data ){
        foreach( $post_data as $key => $value ){
          $template = str_replace( '{{posts_' . $post_number . '_' . $key . '}}', $value, $template );
        }
      }

      // General Replacements
      $template = str_replace( ['{{current_year}}'], [ current_time( 'Y' )], $template );

      // Final Output
      $renderedhtml = '<textarea id="rendered-html">' . $template . '</textarea>';
      $preview = '<iframe srcdoc="'.$template.'" style="width: 100%; height: 600px; border: 1px solid #999;"></iframe>';
      echo '<style>.CodeMirror{height: 600px; border: 1px solid #eee;} .Parent{display: flex; flex-direction: row;} .child1, .child2{width: 50%; padding: 10px;}</style>';
      echo '<div class="Parent"><div class="child1"><p>Copy-and-paste the below HTML into your preferred email sending service:</p>' . $renderedhtml . '</div><div class="child2"><p><strong>Preview</strong></p>' . $preview . '</div></div>';
    } else {
      echo '<p><strong>Instructions</strong><br><ol><li>Add an HTML email template with variables like <code>{{posts_1_post_title}}</code>, <code>{{posts_1_permalink}}</code>, etc. See below for more details on the variables you may use.</li><li>Select the posts you wish to populate your template.</li><li>Once you\'ve selected your posts and added a template, save this "Email Template", and your "Final HTML" will appear here.</li></ol></p><p><strong>Variable Setup</strong><br>For every Post you save under "Template Options &gt; Posts", you will have the following variables available to use in your template. The variable\'s number will correspond to order of the post under "Posts". Available variables:</p><ul style="list-style-type: disc; margin-left: 2em;"><li><code>{{posts_1_post_title}}</code></li><li><code>{{posts_1_featured_image}}</code></li><li><code>{{posts_1_permalink}}</code></li><li><code>{{posts_1_date}}</code></li></ul>';
    }


  }, 'email-template', 'normal', 'high', 1 );
}
add_action( 'add_meta_boxes', 'email_template_rendered_html' );

// Build the custom fields for the Email Template CPT
$fields = MakeitWorkPress\WP_Custom_Fields\Framework::instance();
$fields->add( 'meta', [
  'screen'    => ['email-template'],
  'title'     => __( 'Template Options', 'email-generator' ),
  'id'        => 'email-template-options',
  'context'   => 'normal',
  'priority'  => 'high',
  'type'      => 'post',
  'single'    => true,
  'sections'  => [
    [
      'id'            => 'section-1',
      'title'         => __( 'Template', 'email-generator' ),
      'tabs'          => false,
      'display_title' => false,
      'fields'  => [
        [
          'id'  => 'posts',
          'title' => __( 'Posts', 'email-generator' ),
          'description' => __( 'Any posts you select here will be replaced in the Template below wherever a corresponding post variable is found.', 'email-generator' ),
          'type'  => 'repeatable',
          'columns' => 'half',
          'fields'  => [
            [
              'id'  => 'post',
              'title' => __( 'Post', 'email-generator' ),
              'description' => __( 'Select a post to be inserted in the Template below.', 'email-generator' ),
              'type'  => 'select',
              'mode'  => 'advanced',
              'object'  => 'post',
              'source'  => 'post',
              'posts_per_page'  => 100,
              'orderby' => 'date',
              'order' => 'DESC',
            ],
          ],
        ],
        [
          'id'  => 'template',
          'title' => __( 'Template', 'email-generator' ),
          'description' => __( 'Insert your HTML email template here. Use variables like these to populate your template with data from the posts you select: {{posts_1_post_title}}, {{posts_1_featured_image}}, {{posts_1_permalink}}, {{posts_1_date}}. Increment your variables according to the number of posts you have setup in your template.', 'email-generator' ),
          'type'  => 'code',
          'columns' => 'half',
        ],
      ],
    ],
  ],
]);

/**
 * Enqueue the Code Editor and JS
 *
 * @param string $hook
 */
function add_page_scripts_enqueue_script( $hook ) {
    global $post;

    if ( ! $post )
      return;

    if ( ! 'email-template' === $post->post_type )
      return;

    // Enqueue code editor and settings for manipulating HTML.
    $settings = wp_enqueue_code_editor( [ 'type' => 'text/html', 'codemirror' => [ 'name' => 'handlebars' ] ] ); //

    if( 'post.php' === $hook || 'post-new.php' === $hook ) {
        wp_enqueue_code_editor( ['type' => 'text/html'] );
        //wp_enqueue_script( 'js-code-editor', plugin_dir_url( __FILE__ ) . '/lib/js/code-editor.js', [ 'jquery' ], '', true );
        wp_add_inline_script(
            'code-editor',
            sprintf(
              'jQuery( function() { wp.codeEditor.initialize( "rendered-html", %s ); } );',
              wp_json_encode( $settings )
            )
          );
    }
}
add_action( 'admin_enqueue_scripts', 'add_page_scripts_enqueue_script' );