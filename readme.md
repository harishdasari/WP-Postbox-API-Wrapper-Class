
##WordPress Postbox API Wrapper Class

Createing Option pages with Postboxes for WordPress is now hassel free. With this PHP class you can create Top level menu pages and sub menu pages.

This class uses WordPress native Settings API and Postboxes API and can be used in both Themes and Plugins.

You can add 10 different types of Input fields and Section.

###Types of Inputs Suported
1. Text
2. Textarea
3. Checkbox
4. Radio
5. Select
6. Multi-Select
7. Multi-Checkbox
8. Upload
9. Color
10. Editor

###Installation
Copy the Directory `hd-wp-postbox-api` into your theme or plugin folder.

Include the following code in your theme `functions.php` file or plugin file.

	require_once( 'hd-wp-postbox-api/class-hd-wp-postbox-api.php' );

###Usage
First you need to create

1. Options for Menu Page
2. Input fields array
3. Initializing Settings API Class using above options.

####Creating Options for Menu Page
To create a top level menu page use following

	$example_options = array(
		'page_title'  => 'Example Options Page',    // Page title
		'menu_title'  => 'Example Options',         // Menu title
		'menu_slug'   => 'hd_example_options',      // Menu page slug used in uri
		'capability'  => 'manage_options',          // User permission capability
		'icon'        => 'dashicons-admin-generic', // Icon URL or dash icons class
		'position'    => 61,                        // Postion of top level menu
		'num_columns' => 3,                         // Number of default visible columns
		'max_columns' => 4                          // Maximum number of columns that user can set
	);

**OR**

To Create a sub menu page use following

	$example_options = array(
		'page_title'  => 'Example Options Page', // Page title
		'menu_title'  => 'Example Options',      // Menu title
		'parent_slug' => 'themes.php',           // Parent page slug
		'menu_slug'   => 'hd_example_options',   // Menu page slug used in uri
		'capability'  => 'manage_options',       // User permission capability
		'num_columns' => 3,                      // Number of default visible columns
		'max_columns' => 4,                      // Maximum number of columns that user can set
	);

####Creating Input Field Options
Creating Postboxes and Input fields are easy and all should define in one single array. First we need to define postbox options and then inpit field options.
The input field options underneath the postbox option array will go into that postbox. Let's see how to define field options.

First create an empty array

	$example_fields = array();

Define Postbox options in an array with unique key

	$example_fields = array(
		'hd_postbox_id' => array(
			'title'  => 'General Options', // Give Postbox Title
			'type'   => 'postbox',         // Set type to 'postbox'
			'column' => 1,                 // Give column number in whichh this postbox goes. Value must between 1-4
		)
	);

Now add **input** field options and give unique `option name` as key.

	$example_fields = array(
		'hd_postbox_id' => array(
			'title'  => 'General Options',
			'type'   => 'postbox',
			'column' => 1,
		)
		'hd_text_setting' => array(
			'title'   => 'Text Input',
			'type'    => 'text',
			'default' => 'Hello World!',
			'desc'    => 'Example Text Input',
			'sanit'   => 'nohtml',
		)
	);

Now add the other input fields you want. but make sure the array key should be unique.


Full list of input field emamples and sections.

1. **To add Text Input**

		'hd_text_setting' => array(
			'title'   => 'Text Input',
			'type'    => 'text',
			'default' => 'Hello World!',
			'desc'    => 'Example Text Input',
			'sanit'   => 'nohtml',
		)

2. **To add Textarea Input**

		'hd_textarea_setting' => array(
			'title'   => 'Textarea Input',
			'type'    => 'textarea',
			'default' => 'Hello World!',
			'desc'    => 'Example Textarea Input',
			'sanit'   => 'nohtml',
		)

3. **To add Checkbox Input**

		'hd_checkbox_setting' => array(
			'title'   => 'Checkbox Input',
			'type'    => 'checkbox',
			'default' => 1,
			'desc'    => 'Example Checkbox Input',
			'sanit'   => 'nohtml',
		)

4. **To add Radio Input**

		'hd_radio_setting' => array(
			'title'   => 'Radio Input',
			'type'    => 'radio',
			'default' => 'one',
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'desc'    => 'Example Radio Input',
			'sanit'   => 'nohtml',
		)

5. **To add Select Input**

		'hd_select_setting' => array(
			'title'   => 'Select Input',
			'type'    => 'select',
			'default' => 'two',
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'desc'    => 'Example Select Input',
			'sanit'   => 'nohtml',
		)

6. **To add Multi-Select Input**

		'hd_multiselect_setting' => array(
			'title'   => 'Multi Select Input',
			'type'    => 'select',
			'default' => array( 'one', 'three' ),
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'multiple' => true,
			'desc'     => 'Example Multi Select Input',
			'sanit'    => 'nohtml',
		)

7. **To add Multi-Checkbox Input**

		'hd_multicheck_setting' => array(
			'title'   => 'Multi Checkbox Input',
			'type'    => 'multicheck',
			'default' => array( 'one', 'three' ),
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'desc'    => 'Example Multi Checkbox Input',
			'sanit'   => 'nohtml',
		)

8. **To add Upload Input**

		'hd_upload_setting' => array(
			'title'   => 'Upload Input',
			'type'    => 'upload',
			'default' => '',
			'desc'    => 'Example Upload Input',
			'sanit'   => 'url',
		)

9. **To add Color Input**

		'hd_color_setting' => array(
			'title'   => 'Color Input',
			'type'    => 'color',
			'default' => '#ffffff',
			'desc'    => 'Example Color Input',
			'sanit'   => 'color',
		)

10. **To add TinyMCE Editor Input**

		'hd_editor_setting' => array(
			'title'   => 'Editor Input',
			'type'    => 'editor',
			'default' => '',
			'desc'    => 'Example Editor Input',
			'sanit'   => 'nohtml',
		)

11. **To add Section**

		'hd_section_id' => array(
			'title'   => 'Example Section',
			'type'    => 'section',
			'desc'    => 'Section Description goes here',
		)

###Full Example

	<?php

	require_once( 'hd-wp-postbox-api/hd-wp-postbox-api.php' );

	$example_options = array(
		'page_title'  => 'Example Postbox Options Page',
		'menu_title'  => 'Example Postbox',
		'parent_slug' => 'themes.php',
		'menu_slug'   => 'hd_example_postbox_options',
		'capability'  => 'manage_options',
		'num_columns' => 3,
		'max_columns' => 4,
	);

	$example_fields = array(
		'hd_postbox_1' => array(
			'title'  => 'Postbox 1',
			'type'   => 'postbox',
			'column' => 1,
		),
		'hd_text_setting' => array(
			'title'   => 'Text Input',
			'type'    => 'text',
			'default' => 'Hello World!',
			'desc'    => 'Example Text Input',
			'sanit'   => 'nohtml',
		),
		'hd_section_1' => array(
			'title'   => 'Example Section',
			'type'    => 'section',
			'desc'    => 'Section Description goes here',
		),
		'hd_textarea_setting' => array(
			'title'   => 'Textarea Input',
			'type'    => 'textarea',
			'default' => 'Hello World!',
			'desc'    => 'Example Textarea Input',
			'sanit'   => 'nohtml',
		),
		'hd_checkbox_setting' => array(
			'title'   => 'Checkbox Input',
			'type'    => 'checkbox',
			'default' => 1,
			'desc'    => 'Example Checkbox Input',
			'sanit'   => 'nohtml',
		),
		'hd_postbox_2' => array(
			'title'  => 'Postbox 2',
			'type'   => 'postbox',
			'column' => 2,
		),
		'hd_radio_setting' => array(
			'title'   => 'Radio Input',
			'type'    => 'radio',
			'default' => 'one',
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'desc'    => 'Example Radio Input',
			'sanit'   => 'nohtml',
		),
		'hd_select_setting' => array(
			'title'   => 'Select Input',
			'type'    => 'select',
			'default' => 'two',
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'desc'    => 'Example Select Input',
			'sanit'   => 'nohtml',
		),
		'hd_multiselect_setting' => array(
			'title'   => 'Multi Select Input',
			'type'    => 'select',
			'default' => array( 'one', 'three' ),
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'multiple' => true,
			'desc'     => 'Example Multi Select Input',
			'sanit'    => 'nohtml',
		),
		'hd_multicheck_setting' => array(
			'title'   => 'Multi Checkbox Input',
			'type'    => 'multicheck',
			'default' => array( 'one', 'three' ),
			'choices' => array(
				'one'   => 'Option 1',
				'two'   => 'Option 2',
				'three' => 'Option 3'
			),
			'desc'    => 'Example Multi Checkbox Input',
			'sanit'   => 'nohtml',
		),
		'hd_postbox_3' => array(
			'title'  => 'Postbox 3',
			'type'   => 'postbox',
			'column' => 2,
		),
		'hd_upload_setting' => array(
			'title'   => 'Upload Input',
			'type'    => 'upload',
			'default' => '',
			'desc'    => 'Example Upload Input',
			'sanit'   => 'url',
		),
		'hd_color_setting' => array(
			'title'   => 'Color Input',
			'type'    => 'color',
			'default' => '#ffffff',
			'desc'    => 'Example Color Input',
			'sanit'   => 'color',
		),
		'hd_editor_setting' => array(
			'title'   => 'Editor Input',
			'type'    => 'editor',
			'default' => '',
			'desc'    => 'Example Editor Input',
			'sanit'   => 'nohtml',
		),
	);

	$example_postbox = new HD_WP_Postbox_API( $example_options, $example_fields );


### Actions and Filters

**Actions**

1. `add_action( 'add_meta_boxes', 'function_name' );`

	Callback arguments : `$hook_suffix`, `$menu_slug`

2. `add_action( 'hd_postbox_api_page_before', 'function_name' );`

	Callback arguments : `$hook_suffix`, `$options`, `$fields`

3. `add_action( 'hd_postbox_api_page_before', 'function_name' );`

	Callback arguments : `$hook_suffix`, `$options`, `$fields`

4. `add_action( 'hd_postbox_api_metabox_before', 'function_name' );`

	Callback arguments : `$hook_suffix`, `$column`, `$options`, `$fields`

5. `add_action( 'hd_postbox_api_metabox_after', 'function_name' );`

	Callback arguments : `$hook_suffix`, `$column`, `$options`, `$fields`



**Filters**

1. `add_filter( 'hd_postbox_api_save_button_text', 'function_name' );`

	Callback arguments : `$button_text`

2. `add_filter( 'hd_postbox_api_sanitize_option', 'function_name' );`

	Callback arguments : `$new_value`, `$field`, `$setting`

3. `add_filter( 'hd_html_helper_input_field', 'function_name' );`

	Callback arguments : `$input_html`, `$field`, `$show_help`


Note: where `function_name` is a callback function

Please post your suggetions and requests in issues, and also help me to imrpove this documenration.


Thank You <br/>
-- _Harish Dasari_ <br/>
[@harishdasari](http://twitter.com/harishdasari)
