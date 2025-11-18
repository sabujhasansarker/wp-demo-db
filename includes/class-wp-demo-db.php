<?php
if (!defined('ABSPATH'))
     exit;

class WPDemoDb_Database
{
     public static $table_name = '';

     public static function table_name()
     {
          global $wpdb;
          if (!self::$table_name) {
               self::$table_name = $wpdb->prefix . 'demo_db';
          }
          return self::$table_name;
     }

     public static function create_table()
     {
          global $wpdb;
          $table = self::table_name();
          $charset_collate = $wpdb->get_charset_collate();

          $sql = "CREATE TABLE $table (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    cover_image VARCHAR(255),
                    title VARCHAR(255) NOT NULL,
                    author VARCHAR(255) NOT NULL,
                    genre VARCHAR(100),
                    copies INT NOT NULL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
               ) $charset_collate;";

          require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
          dbDelta($sql);
     }

     // Get all books
     public static function get_data()
     {
          global $wpdb;
          $table = self::table_name();

          // Check if table exists
          if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
               return [];
          }

          return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
     }

     // Create a new book
     public static function create($data)
     {
          global $wpdb;
          $table = self::table_name();

          $inserted = $wpdb->insert(
               $table,
               [
                    'cover_image' => sanitize_text_field($data['cover_image'] ?? ''),
                    'title' => sanitize_text_field($data['title']),
                    'author' => sanitize_text_field($data['author']),
                    'genre' => sanitize_text_field($data['genre'] ?? ''),
                    'copies' => intval($data['copies'] ?? 0)
               ],
               ['%s', '%s', '%s', '%s', '%d']
          );

          if ($inserted) {
               return [
                    'success' => true,
                    'id' => $wpdb->insert_id,
                    'message' => 'Book created successfully'
               ];
          }

          return [
               'success' => false,
               'message' => 'Failed to create book: ' . $wpdb->last_error
          ];
     }

     // Update a book
     public static function update($id, $data)
     {
          global $wpdb;
          $table = self::table_name();

          $updated = $wpdb->update(
               $table,
               [
                    'cover_image' => sanitize_text_field($data['cover_image'] ?? ''),
                    'title' => sanitize_text_field($data['title']),
                    'author' => sanitize_text_field($data['author']),
                    'genre' => sanitize_text_field($data['genre'] ?? ''),
                    'copies' => intval($data['copies'] ?? 0)
               ],
               ['id' => $id],
               ['%s', '%s', '%s', '%s', '%d'],
               ['%d']
          );

          if ($updated !== false) {
               return [
                    'success' => true,
                    'message' => 'Book updated successfully'
               ];
          }

          return [
               'success' => false,
               'message' => 'Failed to update book: ' . $wpdb->last_error
          ];
     }

     // Delete a book
     public static function delete($id)
     {
          global $wpdb;
          $table = self::table_name();

          $deleted = $wpdb->delete(
               $table,
               ['id' => $id],
               ['%d']
          );

          if ($deleted) {
               return [
                    'success' => true,
                    'message' => 'Book deleted successfully'
               ];
          }

          return [
               'success' => false,
               'message' => 'Failed to delete book: ' . $wpdb->last_error
          ];
     }

     // Get single book by ID
     public static function get_by_id($id)
     {
          global $wpdb;
          $table = self::table_name();

          return $wpdb->get_row(
               $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id)
          );
     }

     // Check if table exists
     public static function table_exists()
     {
          global $wpdb;
          $table = self::table_name();
          return $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
     }
}
