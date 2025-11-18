<?php
if (!defined('ABSPATH'))
     exit;

class WPDemoDB_Enqueue
{
     public static function init()
     {
          add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_script']);
     }
     public static function enqueue_script($hook)
     {
          if ($hook !== 'toplevel_page_wp-demo-db') {
               return;
          }

          wp_enqueue_script(
               'react-admin-js',
               'http://localhost:3000/src/main.jsx',
               [],
               null,
               false
          );

          add_filter('script_loader_tag', function ($tag, $handle) {
               if ($handle === 'react-admin-js') {
                    $tag = str_replace('<script', '<script type="module" crossorigin', $tag);
               }
               return $tag;
          }, 10, 2);

          wp_add_inline_script(
               'react-admin-js',
               'window.wpReactData = ' . json_encode([
                    'page_title' => 'Book Management System',
                    'apiUrl' => rest_url('books'),
                    'nonce' => wp_create_nonce('wp_rest'),
               ]),
               'before'
          );
     }
}

WPDemoDB_Enqueue::init();
