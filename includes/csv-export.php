<?php
/**
 * File that contains code to export published posts to a CSV file.
 * This involves defining a PHP class that WP-CLI can call.
 * The class will fetch all published posts, gather their categories and tags,
 * and then write this data to a CSV file in the wp-content/exported_posts_csv directory.
 * Each time the command is run, it creates a new CSV file with a unique name.
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    class WP_CLI_Export_Published_Posts_Command {
        
        /**
         * Handle the WP-CLI command execution.
         *
         * This method exports the data of all published posts to a CSV file. It fetches all published posts,
         * gathers their categories and tags, and writes this data to a CSV file in the `wp-content/exported_posts_csv` directory.
         * The CSV file includes the post ID, category names, and tag names. The method ensures the export directory exists 
         * and creates a new CSV file with a unique name each time the command is run. It uses the WP_Filesystem API for file 
         * operations to adhere to WordPress best practices.
         *
         */
        
         public function __invoke() {
            // Initialize export data array
            $export_data = array();
        
            // Declare the global wp_filesystem variable.
            global $wp_filesystem;
        
            // Fetch all published posts using WP_Query with caching
            $query = new WP_Query( array(
                'post_type'      => 'post',          // Specify the post type
                'post_status'    => 'publish',       // Only get published posts
                'posts_per_page' => -1,              // Retrieve all posts
            ) );
        
            // Check if there are any published posts
            if ( ! $query->have_posts() ) {
                WP_CLI::error( "No published posts found." );
                return;
            }
        
            // Loop through each post
            while ( $query->have_posts() ) {
                $query->the_post(); // Set up post data
        
                // Get post ID, category names, and tag names
                $post_id        = get_the_ID();
                $category_names = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
                $tag_names      = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
        
                // Add data to export array
                $export_data[] = array(
                    'post_id'           => $post_id,
                    'category_names'    => implode( ', ', $category_names ),
                    'tag_names'         => implode( ', ', $tag_names ),
                );
        
                WP_CLI::line( "Added post ID $post_id to export data." );
            }
        
            // Reset post data
            wp_reset_postdata();
        
            // Initialize WP_Filesystem
            if ( ! WP_Filesystem() ) {
                WP_CLI::error( "Unable to initialize WP_Filesystem." );
                return;
            }
        
            // Define export directory and ensure it exists
            $export_dir = WP_CONTENT_DIR . '/exported_posts_csv';
            if ( ! $wp_filesystem->is_dir( $export_dir ) ) {
                if ( ! $wp_filesystem->mkdir( $export_dir, FS_CHMOD_DIR ) ) {
                    WP_CLI::error( "Failed to create directory: $export_dir" );
                    return;
                }
            }
        
            // Find the next available file name
            $i = 1;
            do {
                $export_file = $export_dir . '/exported_published_posts' . $i . '.csv';
                $i++;
            } while ( $wp_filesystem->exists( $export_file ) );
        
            // Write CSV headers and data
            $csv_content = '';
            $csv_content .= $this->array_to_csv_line( array( 'Post ID', 'Category Names', 'Tag Names' ) );
            foreach ( $export_data as $data ) {
                $csv_content .= $this->array_to_csv_line( $data );
            }
        
            // Write content to the file
            if ( ! $wp_filesystem->put_contents( $export_file, $csv_content, FS_CHMOD_FILE ) ) {
                WP_CLI::error( "Failed to write data to file: $export_file" );
                return;
            }
        
            WP_CLI::success( "Exported published posts data to CSV file: $export_file" );
        }
        
        /**
         * Convert an array to a CSV line
         *
         * @param array $fields Array of fields to convert
         * @return string CSV line
         */
        private function array_to_csv_line( $fields ) {
            $escaped_fields = array_map( function( $field ) {
                if ( strpos( $field, ',' ) !== false || strpos( $field, '"' ) !== false ) {
                    $field = '"' . str_replace( '"', '""', $field ) . '"';
                }
                return $field;
            }, $fields );

            return implode( ',', $escaped_fields ) . "\n";
        }
    }

    // Register the command
    WP_CLI::add_command( 'export-published-posts', 'WP_CLI_Export_Published_Posts_Command' );
}
