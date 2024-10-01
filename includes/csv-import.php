<?php
/**
 * File that contains code to import CSV file and assign category and tags from it.
 * This involves defining a PHP class that WP-CLI can call.
 * The class will read a CSV file and process each row.
 * It will check if the categories and tags exist and create them if they don't.
 * It will then assign the categories and tags to the specified posts.
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    class WP_CLI_CSV_Assign_Command {
        
        /**
         * Handle the WP-CLI command execution.
         *
         * This method reads the provided CSV file and assigns categories and tags to posts based on the data in the CSV.
         * It checks for various conditions such as post existence, non-empty category and tag fields, and ensures not to 
         * overwrite existing terms unnecessarily. If the post is trashed or if the CSV data is incomplete, it skips the post.
         *
         * @param array $args Positional arguments.
         * @param array $assoc_args Associative arguments.
         */
        
        public function __invoke( $args, $assoc_args ) {
            $csv_file = $assoc_args['file'];

            if ( ! file_exists( $csv_file ) ) {
                WP_CLI::error( "File not found: $csv_file" );
                return;
            }

            // Initialize WP_Filesystem
            WP_Filesystem();

            global $wp_filesystem;

            $handle = $wp_filesystem->get_contents( $csv_file );

            if ( ! $handle ) {
                WP_CLI::error( "Could not open file: $csv_file" );
                return;
            }

            $lines = explode( "\n", $handle );

            foreach ( $lines as $line ) {
                // Skip empty lines
                if ( trim( $line ) === '' ) {
                    continue;
                }

                $data = str_getcsv( $line );

                // Ensure CSV line has enough data
                if ( count( $data ) < 1 ) {
                    WP_CLI::warning( "CSV line does not have enough data. Skipping." );
                    continue;
                }

                $post_id = isset($data[0]) ? intval($data[0]) : 0;
                $category_name = isset($data[1]) ? trim( $data[1], ' "\'' ) : ''; // Sanitize category name
                $tag_name = isset($data[2]) ? trim( $data[2], ' "\'' ) : ''; // Sanitize tag name

                // Check if post_id is provided and valid
                if ( empty( $post_id ) || ! get_post_status( $post_id ) ) {
                    WP_CLI::warning( "Post ID $post_id is missing, invalid, or does not exist. Skipping." );
                    continue;
                }

                // Check if the post has been trashed
                $post_status = get_post_status( $post_id );
                if ( $post_status === 'trash' ) {
                    WP_CLI::warning( "Post ID $post_id has been trashed. Skipping." );
                    continue;
                }

                // If both category and tag are empty, remove all categories and tags
                if ( empty( $category_name ) && empty( $tag_name ) ) {
                    // Remove all categories
                    $current_categories = wp_get_post_categories( $post_id );
                    if ( ! empty( $current_categories ) ) {
                        wp_remove_object_terms( $post_id, $current_categories, 'category' );
                    }

                    // Remove all tags
                    $current_tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
                    if ( ! empty( $current_tags ) ) {
                        wp_remove_object_terms( $post_id, $current_tags, 'post_tag' );
                    }

                    WP_CLI::success( "Removed all categories and tags from post ID $post_id." );
                    continue;
                }

                // If category is provided but tag is empty, remove all tags
                if ( ! empty( $category_name ) && empty( $tag_name ) ) {
                    // Remove all tags
                    $current_tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
                    if ( ! empty( $current_tags ) ) {
                        wp_remove_object_terms( $post_id, $current_tags, 'post_tag' );
                    }

                    // Assign new category
                    $category = get_term_by( 'name', $category_name, 'category' );
                    if ( ! $category ) {
                        $category = wp_insert_term( $category_name, 'category' );
                        if ( is_wp_error( $category ) ) {
                            WP_CLI::warning( "Could not create category: $category_name" );
                            continue;
                        }
                        $category_id = $category['term_id'];
                    } else {
                        $category_id = $category->term_id;
                    }

                    $category_assignment = wp_set_post_terms( $post_id, array( $category_id ), 'category', false );
                    if ( is_wp_error( $category_assignment ) ) {
                        WP_CLI::warning( "Failed to assign category '$category_name' to post ID $post_id" );
                        continue;
                    }
                }

                // If tag is provided but category is empty, remove all categories
                if ( empty( $category_name ) && ! empty( $tag_name ) ) {
                    // Remove all categories
                    $current_categories = wp_get_post_categories( $post_id );
                    if ( ! empty( $current_categories ) ) {
                        wp_remove_object_terms( $post_id, $current_categories, 'category' );
                    }

                    // Assign new tag
                    $tag = get_term_by( 'name', $tag_name, 'post_tag' );
                    if ( ! $tag ) {
                        $tag = wp_insert_term( $tag_name, 'post_tag' );
                        if ( is_wp_error( $tag ) ) {
                            WP_CLI::warning( "Could not create tag: $tag_name" );
                            continue;
                        }
                        $tag_id = $tag['term_id'];
                    } else {
                        $tag_id = $tag->term_id;
                    }

                    $tag_assignment = wp_set_post_terms( $post_id, array( $tag_id ), 'post_tag', false );
                    if ( is_wp_error( $tag_assignment ) ) {
                        WP_CLI::warning( "Failed to assign tag '$tag_name' to post ID $post_id" );
                        continue;
                    }
                }

                // If both category and tag are provided, assign them
                if ( ! empty( $category_name ) && ! empty( $tag_name ) ) {
                    // Assign new category
                    $category = get_term_by( 'name', $category_name, 'category' );
                    if ( ! $category ) {
                        $category = wp_insert_term( $category_name, 'category' );
                        if ( is_wp_error( $category ) ) {
                            WP_CLI::warning( "Could not create category: $category_name" );
                            continue;
                        }
                        $category_id = $category['term_id'];
                    } else {
                        $category_id = $category->term_id;
                    }

                    $category_assignment = wp_set_post_terms( $post_id, array( $category_id ), 'category', false );
                    if ( is_wp_error( $category_assignment ) ) {
                        WP_CLI::warning( "Failed to assign category '$category_name' to post ID $post_id" );
                        continue;
                    }

                    // Assign new tag
                    $tag = get_term_by( 'name', $tag_name, 'post_tag' );
                    if ( ! $tag ) {
                        $tag = wp_insert_term( $tag_name, 'post_tag' );
                        if ( is_wp_error( $tag ) ) {
                            WP_CLI::warning( "Could not create tag: $tag_name" );
                            continue;
                        }
                        $tag_id = $tag['term_id'];
                    } else {
                        $tag_id = $tag->term_id;
                    }

                    $tag_assignment = wp_set_post_terms( $post_id, array( $tag_id ), 'post_tag', false );
                    if ( is_wp_error( $tag_assignment ) ) {
                        WP_CLI::warning( "Failed to assign tag '$tag_name' to post ID $post_id" );
                        continue;
                    }
                }

                WP_CLI::success( "Assigned category '$category_name' and tag '$tag_name' to post ID $post_id" );
            }
        }
    }

    // Register the command
    WP_CLI::add_command( 'assign-terms-from-csv', 'WP_CLI_CSV_Assign_Command' );
}
