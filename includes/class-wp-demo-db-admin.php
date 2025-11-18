<?php
if (!defined('ABSPATH'))
     exit;

class WPDemoDb_Admin
{
     public static function init()
     {
          add_action('admin_menu', [__CLASS__, 'add_menu']);
     }

     public static function add_menu()
     {
          add_menu_page(
               'Demo Database',
               'Demo DB',
               'manage_options',
               'wp-demo-db',
               [__CLASS__, 'render_page'],
               'dashicons-email-alt2',
               26
          );
     }

     public static function render_page()
     {
?>
          <div class="wrap">
               <div id="root"></div>
          </div>
<?php
     }
}

WPDemoDb_Admin::init();
