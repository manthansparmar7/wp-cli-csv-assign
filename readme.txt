
Contributors: manthansparmar7
Tags: csv, categories, tags, wp-cli, custom post types
Requires at least: 5.0
Tested up to: 6.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to assign categories and tags to WordPress posts using a CSV file upload and export post category and tag data in CSV format using WP-CLI commands.

== Description ==
The CSV Category and Tag Assignment plugin provides a simple way to assign or update categories and tags for WordPress posts via a CSV upload and export category and tag data using WP-CLI commands.

== Purpose ==
The primary purpose of this plugin is to simplify post management by allowing users to assign or update categories and tags using a CSV file. Additionally, users can export post category and tag data to a CSV file via WP-CLI.

== Usage ==
Using WP-CLI commands, users can:
- Add or update any post's category and tag.
- Export post category and tag data into a CSV file from the database.

== CSV File Format Example ==
The CSV file must contain 3 columns:
1. Post ID
2. Category Name
3. Tag Name

Example CSV content:
3, Banana, Fruit 6, Potato, Vegetable

== WP-CLI Commands ==
To assign or update categories and tags for posts using a CSV file:
wp assign-terms-from-csv --file=CSV_FILE_PATH

To export post category and tag data in CSV format:
wp export-published-posts

== Installation ==
1. Download the Plugin: Get the latest version from the repository.
2. Upload to WordPress:
   - Go to Plugins > Add New in your WordPress dashboard.
   - Click Upload Plugin and upload the plugin ZIP file.
   - Or, upload the extracted plugin folder to the /wp-content/plugins/ directory via FTP.
3. Activate the Plugin:
   - Go to Plugins in your WordPress dashboard.
   - Find the plugin and click Activate.

== Coding Standards ==
The plugin is developed using Object-Oriented Programming (OOP) methodologies. It adheres to the PHP_CodeSniffer (PHPCS) and PHP Code Beautifier and Fixer (PHPCBF) coding standards for WordPress VIP, ensuring high-quality and maintainable code.

== Development Status ==
Current Phase: This plugin is in the initial development phase. New features and improvements will be added in the future. Feedback from users is welcome.