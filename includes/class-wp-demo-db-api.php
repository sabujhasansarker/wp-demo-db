<?php
if (!defined('ABSPATH'))
     exit;

class WPDemoDb_API
{
     public static function init()
     {
          add_action('rest_api_init', [__CLASS__, 'register_routes']);
     }

     public static function register_routes()
     {
          $namespace = 'books';

          // Get all books
          register_rest_route($namespace, '/list', [
               'methods' => 'GET',
               'callback' => [__CLASS__, 'get_books'],
               'permission_callback' => [__CLASS__, 'check_permission']
          ]);

          // Create book
          register_rest_route($namespace, '/create', [
               'methods' => 'POST',
               'callback' => [__CLASS__, 'create_book'],
               'permission_callback' => [__CLASS__, 'check_permission']
          ]);

          // Update book
          register_rest_route($namespace, '/update/(?P<id>\d+)', [
               'methods' => 'PUT',
               'callback' => [__CLASS__, 'update_book'],
               'permission_callback' => [__CLASS__, 'check_permission'],
               'args' => [
                    'id' => [
                         'required' => true,
                         'validate_callback' => function ($param) {
                              return is_numeric($param);
                         }
                    ]
               ]
          ]);

          // Delete book
          register_rest_route($namespace, '/delete/(?P<id>\d+)', [
               'methods' => 'DELETE',
               'callback' => [__CLASS__, 'delete_book'],
               'permission_callback' => [__CLASS__, 'check_permission'],
               'args' => [
                    'id' => [
                         'required' => true,
                         'validate_callback' => function ($param) {
                              return is_numeric($param);
                         }
                    ]
               ]
          ]);

          // Get single book
          register_rest_route($namespace, '/book/(?P<id>\d+)', [
               'methods' => 'GET',
               'callback' => [__CLASS__, 'get_book_by_id'],
               'permission_callback' => [__CLASS__, 'check_permission'],
               'args' => [
                    'id' => [
                         'required' => true,
                         'validate_callback' => function ($param) {
                              return is_numeric($param);
                         }
                    ]
               ]
          ]);
     }

     // Permission check
     public static function check_permission()
     {
          return current_user_can('manage_options');
     }

     // Get all books
     public static function get_books($request)
     {
          $books = WPDemoDb_Database::get_data();

          if (empty($books)) {
               return rest_ensure_response([
                    'success' => true,
                    'data' => [],
                    'message' => 'No books found'
               ]);
          }

          return rest_ensure_response([
               'success' => true,
               'data' => $books,
               'count' => count($books)
          ]);
     }

     // Get single book by ID
     public static function get_book_by_id($request)
     {
          $id = $request['id'];
          $book = WPDemoDb_Database::get_by_id($id);

          if (!$book) {
               return new WP_Error(
                    'not_found',
                    'Book not found',
                    ['status' => 404]
               );
          }

          return rest_ensure_response([
               'success' => true,
               'data' => $book
          ]);
     }

     // Create book
     public static function create_book($request)
     {
          $data = json_decode($request->get_body(), true);

          // Validation
          if (empty($data['title']) || empty($data['author'])) {
               return new WP_Error(
                    'invalid_data',
                    'Title and Author are required',
                    ['status' => 400]
               );
          }

          // Validate copies (must be non-negative)
          if (isset($data['copies']) && intval($data['copies']) < 0) {
               return new WP_Error(
                    'invalid_data',
                    'Copies cannot be negative',
                    ['status' => 400]
               );
          }

          $result = WPDemoDb_Database::create($data);

          if (!$result['success']) {
               return new WP_Error(
                    'create_failed',
                    $result['message'],
                    ['status' => 500]
               );
          }

          return rest_ensure_response([
               'success' => true,
               'id' => $result['id'],
               'message' => $result['message']
          ]);
     }

     // Update book
     public static function update_book($request)
     {
          $id = $request['id'];
          $data = json_decode($request->get_body(), true);

          // Check if book exists
          $existing = WPDemoDb_Database::get_by_id($id);
          if (!$existing) {
               return new WP_Error(
                    'not_found',
                    'Book not found',
                    ['status' => 404]
               );
          }

          // Validation
          if (empty($data['title']) || empty($data['author'])) {
               return new WP_Error(
                    'invalid_data',
                    'Title and Author are required',
                    ['status' => 400]
               );
          }

          // Validate copies
          if (isset($data['copies']) && intval($data['copies']) < 0) {
               return new WP_Error(
                    'invalid_data',
                    'Copies cannot be negative',
                    ['status' => 400]
               );
          }

          $result = WPDemoDb_Database::update($id, $data);

          if (!$result['success']) {
               return new WP_Error(
                    'update_failed',
                    $result['message'],
                    ['status' => 500]
               );
          }

          return rest_ensure_response([
               'success' => true,
               'message' => $result['message']
          ]);
     }

     // Delete book
     public static function delete_book($request)
     {
          $id = $request['id'];

          // Check if book exists
          $existing = WPDemoDb_Database::get_by_id($id);
          if (!$existing) {
               return new WP_Error(
                    'not_found',
                    'Book not found',
                    ['status' => 404]
               );
          }

          $result = WPDemoDb_Database::delete($id);

          if (!$result['success']) {
               return new WP_Error(
                    'delete_failed',
                    $result['message'],
                    ['status' => 500]
               );
          }

          return rest_ensure_response([
               'success' => true,
               'message' => $result['message']
          ]);
     }
}

WPDemoDb_API::init();
