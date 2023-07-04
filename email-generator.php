<?php
/**
 * Plugin Name:     Email Generator
 * Plugin URI:      https://github.com/mwender/email-generator
 * Description:     Generate your own HTML emails using a combination of templates and variables. Then copy-and-paste the generated code into your favorite email service.
 * Author:          TheWebist
 * Author URI:      https://mwender.com
 * Text Domain:     email-generator
 * Domain Path:     /languages
 * Version:         1.0.2
 *
 * @package         Email_Generator
 */

if ( ! defined( 'ABSPATH' ) )
  return;

require_once( plugin_dir_path(__FILE__ ) . 'vendor/autoload.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/fns/email-template-cpt.php' );

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
        $posts[$x] = [
          'post_title'      => get_the_title( $post_id ),
          'featured_image'  => get_the_post_thumbnail_url( $post_id, 'medium' ),
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