# CSV Category and Tag Assignment Plugin

**Description:** This plugin allows you to assign categories and tags to WordPress posts using a CSV file upload. Additionally, the plugin enables the export of post category and tag data in CSV format using WP-CLI commands.

## Table of Contents
- [Purpose](#purpose)
- [Usage](#usage)
- [Commands](#commands)
- [Installation](#installation)
- [Coding Standards](#coding-standards)
- [Development Status](#development-status)

## Purpose
The main purpose of this plugin is to assign or update post categories and tags through a CSV file. Additionally, it allows users to export stored post data (including assigned categories and tags) via WP-CLI commands.

## Usage
Using WP-CLI commands, users can:
- Add or update any post's category and tag.
- Export post category and tag data into a CSV file from the database.

### CSV File Format Example:
The CSV file must have 3 columns:
 1. Post ID
 2. Category Name
3. Tag Name

Example CSV content:
```plaintext
3, Banana, Fruit
6, Potato, Vegetable
```
### WP-CLI Commands:
To assign or update categories and tags for posts using a CSV file: 

```plaintext
wp assign-terms-from-csv --file=CSV_FILE_PATH
```
To export post category and tag data in CSV format:

```plaintext
wp export-published-posts
```
### Installation
Download the Plugin: Get the latest version from the repository.

Upload to WordPress:
Go to Plugins > Add New in your dashboard.
Click Upload Plugin, then upload the plugin ZIP file.
Or, upload the extracted folder to /wp-content/plugins/ via FTP.

 Activate the Plugin:
Find the plugin in your Plugins list and click Activate.



## Coding Standards
The Profile Management plugin is developed using **Object-Oriented Programming (OOP)** methodologies. It adheres to the **PHP_CodeSniffer (PHPCS)** and Fixer **(PHPCBF) coding standards** for **WordPress VIP**. This commitment to coding standards ensures high-quality, maintainable, and consistent code throughout the project.

### Development Status
**Current Phase:** This plugin is in the initial development phase. New features and improvements will be added in the future, and feedback from users is welcome.