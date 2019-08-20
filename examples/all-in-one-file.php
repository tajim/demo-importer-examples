<?php
/**
 * All available hooks and filters in one file. Include this file in your theme to enable
 * compatibility with Demo Importer plugin and keep your code clean.
 *
 * @link https://wordpress.org/plugins/demo-importer/
 * @package demo-importer-example
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDPDI_Installer' ) ) {
	return;
}

add_action( 'wdpdi_install_demo_data', array( 'MyThemeLoadDemoData', 'install_demo_data' ) );

add_filter( 'wdpdi_required_plugins', array( 'MyThemeLoadDemoData', 'required_plugins' ) );
add_filter( 'wdpdi_documentation_url', array( 'MyThemeLoadDemoData', 'documentation_url' ) );
add_filter( 'wdpdi_screenshot', array( 'MyThemeLoadDemoData', 'screenshot_url' ) );
add_filter( 'wdpdi_demo_data_available', '__return_true' );

/**
 * Class definition
 */
class MyThemeLoadDemoData {

	/**
	 * Installation errors array
	 *
	 * @var array
	 */
	public static $errors = array();

	/**
	 * Return required plugins array
	 *
	 * @param array $plugins Original required plugins array.
	 */
	public static function required_plugins( $plugins ) {

		return array_merge(
			$plugins,
			array(
				'contact-form-7',
				'advanced-custom-fields',
				'w3-total-cache',
			)
		);
	}

	/**
	 * Return documentation URL
	 */
	public static function documentation_url() {
		return 'https://docs.example.com/my-theme-documentation/';
	}

	/**
	 * Return screenshot URL
	 */
	public static function screenshot_url() {
		return get_template_directory_uri() . '/screenshot.jpg';
	}

	/**
	 * Return installation demo data errors
	 */
	public static function return_installation_errors() {
		return self::$errors;
	}

	/**
	 * Install demo data
	 */
	public static function install_demo_data() {

		require_once ABSPATH . 'wp-config.php';
		require_once ABSPATH . 'wp-includes/wp-db.php';
		require_once ABSPATH . 'wp-admin/includes/taxonomy.php';

		add_filter( 'wdpdi_install_demo_data_errors', array( get_class(), 'return_installation_errors' ) );

		$current_user = wp_get_current_user();
		$results      = array();

		/**
		 * Remove all mods of current theme (reset options)
		 */
		WDPDI_Installer::remove_theme_mods();

		/**
		 * Add images, set path in your theme directory
		 */
		$images = array(
			'screenshot'    => '/screenshot.jpg',
			'logo'          => '/assets/img/logo.jpg',
			'some-image'    => '/assets/img/some-image.jpg',
			'another-image' => '/assets/img/another-image.jpg',
		);

		foreach ( $images as $image_key => $image_path ) {

			$results['images'][ $image_key ] = array(
				'id'  => WDPDI_Installer::add_image( get_template_directory() . $image_path ),
				'url' => null,
			);

			if ( false === $results['images'][ $image_key ]['id'] ) {
				self::$errors[] = esc_html( __( 'Couldn\'t upload image: ', 'my-theme-text-domain' ) . $image_path );
				continue;
			}

			$image_url = wp_get_attachment_image_src( $results['images'][ $image_key ]['id'], 'full' );

			if ( false !== $image_url ) {
				$results['images'][ $image_key ]['url'] = $image_url[0];
			}
		}

		/**
		 * Set website logo
		 */
		WDPDI_Installer::set_logo( $results['images']['logo']['id'] );

		/**
		 * Add blog post
		 */
		$posts = array(
			'test-post'         => array(
				'title'     => __( 'Test post', 'my-theme-text-domain' ),
				'thumbnail' => $results['images']['some-image']['id'],
				'content'   => self::get_default_blog_post_content(),
				'category'  => array(
					wp_create_category( __( 'Test posts', 'my-theme-text-domain' ) ),
				),
				'tags'      => array(),
				'comments'  => array(),
			),
			'another-test-post' => array(
				'title'     => __( 'Another test post', 'my-theme-text-domain' ),
				'thumbnail' => $results['images']['another-image']['id'],
				'content'   => self::get_default_blog_post_content(),
				'category'  => array(
					wp_create_category( __( 'Test posts', 'my-theme-text-domain' ) ),
				),
				'tags'      => array(),
				'comments'  => array(
					array(
						'author'       => __( 'Susane Smith', 'my-theme-text-domain' ),
						'author-email' => __( 'susane@example.com', 'my-theme-text-domain' ),
						'content'      => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent fringilla auctor rhoncus. Nam mattis cursus vulputate. Praesent condimentum ornare augue, vel fringilla mauris cursus eu. In molestie, ligula at viverra vulputate, libero metus pretium felis, in accumsan turpis urna ac ex. Praesent sed finibus sapien, at sagittis tellus.',
						'replies'      => array(
							array(
								'author'       => $current_user->display_name,
								'author-email' => $current_user->user_email,
								'content'      => 'Aliquam vitae neque lorem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aliquam in quam egestas, imperdiet elit sed, finibus nulla. Quisque tristique nisi ligula, eget pellentesque ligula tincidunt at.',
								'user_id'      => $current_user->ID,
							),
						),
					),
					array(
						'author'       => __( 'John Doe', 'my-theme-text-domain' ),
						'author-email' => __( 'john@example.com', 'my-theme-text-domain' ),
						'content'      => 'Vestibulum vestibulum ante sit amet ante molestie, eget euismod est blandit. Fusce viverra, arcu eu porttitor maximus, sem dolor consequat dolor, nec ullamcorper purus urna eget turpis. Pellentesque at justo rhoncus, luctus est eget, posuere augue. Nam rhoncus quam venenatis ornare scelerisque. Nunc interdum semper laoreet.',
					),
				),
			),
		);

		foreach ( $posts as $post_key => $post_data ) {
			$results['posts'][ $post_key ] = WDPDI_Installer::add_blog_post( $post_data );
			if ( false === $results['posts'][ $post_key ] ) {
				self::$errors[] = esc_html( __( 'Couldn\'t add blog post:', 'my-theme-text-domain' ) ) . ' "' . esc_html( $post_data['title'] ) . '"';
			}
		}

		/**
		 * Add Contact Form 7 form
		 */
		$results['forms']['contact-form'] = WDPDI_Installer::add_contact_form(
			array(
				'plugin'  => 'wpcf7',
				'title'   => __( 'Contact form', 'my-theme-text-domain' ),
				'content' => '<label>' . esc_attr( __( 'Your Name (required)', 'my-theme-text-domain' ) ) . "\r\n[text* your-name] </label>\r\n\r\n<label>" . esc_attr( __( 'Your Email (required)', 'my-theme-text-domain' ) ) . "\r\n[email* your-email] </label>\r\n\r\n<label>" . esc_attr( __( 'Subject', 'my-theme-text-domain' ) ) . "\r\n[text your-subject] </label>\r\n\r\n<label>" . esc_attr( __( 'Your Message', 'my-theme-text-domain' ) ) . "\r\n[textarea your-message] </label>\r\n\r\n<p class=\"wpcf7-submit-container\">[submit \"" . esc_attr( __( 'Send a message', 'my-theme-text-domain' ) ) . '"]</p>',
			)
		);

		if ( false === $results['forms']['contact-form'] ) {
			self::$errors[] = esc_html( __( 'Couldn\'t add contact form', 'my-theme-text-domain' ) );
		}

		/**
		 * Add pages, but do not fill it with contents yet
		 */
		$pages = array(
			'about-us' => __( 'About us', 'my-theme-text-domain' ),
			'blog'     => __( 'Blog', 'my-theme-text-domain' ),
			'contact'  => __( 'Contact', 'my-theme-text-domain' ),
			'home'     => __( 'Home', 'my-theme-text-domain' ),
		);

		$results['pages'] = WDPDI_Installer::add_empty_pages( $pages );

		foreach ( $results['pages'] as $page_key => $page_id ) {
			if ( false === $page_id ) {
				// Translators: page title.
				self::$errors[] = esc_html( sprintf( __( 'Couldn\'t add page: %s', 'my-theme-text-domain' ), $pages[ $page_key ] ) );
			}
		}

		/**
		 * Update reading settings
		 */
		WDPDI_Installer::update_reading_settings( $results['pages']['home'], $results['pages']['blog'] );

		/**
		 * Create primary menu
		 */
		$results['menus']['primary'] = WDPDI_Installer::create_nav_menu(
			'my_menu_primary',
			__( 'Primary menu', 'my-theme-text-domain' ),
			array(
				array(
					'type'  => 'page',
					'id'    => $results['pages']['home'],
					'title' => __( 'Home', 'my-theme-text-domain' ),
				),
				array(
					'type'          => 'page',
					'id'            => $results['pages']['about-us'],
					'title'         => __( 'About us', 'my-theme-text-domain' ),
					'submenu-items' => array(
						array(
							'type'  => 'page',
							'id'    => $results['pages']['blog'],
							'title' => __( 'Blog', 'my-theme-text-domain' ),
						),
					),
				),
				array(
					'type'  => 'page',
					'id'    => $results['pages']['contact'],
					'title' => __( 'Contact', 'my-theme-text-domain' ),
				),
			)
		);

		if ( false === $results['menus']['primary'] ) {
			self::$errors[] = esc_html( __( 'Couldn\'t configure primary menu', 'my-theme-text-domain' ) );
		}

		/**
		 * Create footer menu
		 */
		$results['menus']['footer'] = WDPDI_Installer::create_nav_menu(
			'my_menu_footer',
			__( 'Footer menu', 'my-theme-text-domain' ),
			array(
				array(
					'type'  => 'page',
					'id'    => $results['pages']['home'],
					'title' => __( 'Home', 'my-theme-text-domain' ),
				),
				array(
					'type'  => 'page',
					'id'    => $results['pages']['about-us'],
					'title' => __( 'About us', 'my-theme-text-domain' ),
				),
				array(
					'type'  => 'page',
					'id'    => $results['pages']['blog'],
					'title' => __( 'Blog', 'my-theme-text-domain' ),
				),
				array(
					'type'  => 'page',
					'id'    => $results['pages']['contact'],
					'title' => __( 'Contact', 'my-theme-text-domain' ),
				),
			)
		);

		if ( false === $results['menus']['footer'] ) {
			self::$errors[] = esc_html( __( 'Couldn\'t configure footer menu', 'my-theme-text-domain' ) );
		}

		/**
		 * Fill pages with contents
		 */
		foreach ( $results['pages'] as $page_key => $page_id ) {

			if ( in_array( $page_id, array( false, 'blog' ), true ) ) {
				continue;
			}

			$updated = wp_update_post(
				wp_slash(
					array(
						'ID'           => $page_id,
						'post_content' => self::get_page_content( $page_key, $results ),
					)
				)
			);

			if ( 0 === $updated ) {
				// Translators: page title.
				self::$errors[] = esc_html( sprintf( __( 'Couldn\'t set content for %s page', 'my-theme-text-domain' ), $pages[ $page_key ] ) );
			}
		}
	}

	/**
	 * Get default blog post content
	 */
	private static function get_default_blog_post_content() {

		return (
			'<!-- wp:paragraph -->
<p>Quisque non enim porttitor, lacinia ligula et, euismod dolor. Sed vel cursus quam. Ut vitae lorem ipsum. Nullam euismod tellus nec fringilla porttitor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ornare est eu sapien sagittis, vitae maximus dui pharetra. Morbi aliquam magna non consectetur tincidunt.</p>
<!-- /wp:paragraph -->

<!-- wp:more -->
<!--more-->
<!-- /wp:more -->

<!-- wp:paragraph -->
<p>Nam sem dui, consectetur et tortor vitae, volutpat mollis nibh. Pellentesque id tempor mauris. Vivamus pulvinar turpis viverra enim condimentum, sed varius justo mattis. Sed viverra eros at sem semper consectetur. Proin eu facilisis justo. Duis vehicula mi id diam faucibus laoreet. Duis nec venenatis libero, sit amet iaculis lacus. Praesent ac feugiat est. Vestibulum commodo.</p>
<!-- /wp:paragraph -->'
		);
	}

	/**
	 * Get content for given page
	 *
	 * @param string $page_key Page key.
	 * @param array  $results  Demo upload results.
	 */
	private static function get_page_content( $page_key, $results ) {

		switch ( $page_key ) {

			// Content for "About us" page.
			case 'about-us':
				return (
					'<!-- wp:paragraph -->
<p>' . esc_html( __( 'Content for "About us" page.', 'my-theme-text-domain' ) ) . '</p>
<!-- /wp:paragraph -->'
				);

			// Content for "Contact" page.
			case 'contact':
				return (
					'<!-- wp:paragraph -->
<p>' . esc_html( __( 'Content for "Contact" page.', 'my-theme-text-domain' ) ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[contact-form-7 id="' . esc_attr( $results['forms']['contact-form'] ) . '" title="' . esc_attr( __( 'Contact form', 'my-theme-text-domain' ) ) . '"]
<!-- /wp:shortcode -->'
				);

			// Content for "Home" page.
			case 'home':
				return (
					'<!-- wp:paragraph -->
<p>' . esc_html( __( 'Content for "Home" page.', 'my-theme-text-domain' ) ) . '</p>
<!-- /wp:paragraph -->'
				);
		}
	}
}
