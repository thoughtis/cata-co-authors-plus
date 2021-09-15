<?php
/**
 * Fields
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.1.2
 */

namespace Cata\CoAuthors_Plus;

/**
 * Fields
 */
class Fields {
	/**
	 * Field to Remove
	 * 
	 * @var array $fields_to_remove
	 */
	private static $fields_to_remove = array(
		'aim',
		'yahooim',
		'jabber',
	);

	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'coauthors_guest_author_fields', array( __CLASS__, 'remove_unused_meta_fields' ) );
		add_filter( 'coauthors_guest_author_fields', array( __CLASS__, 'add_custom_meta_fields' ), 20, 2 );

		// @priority 20 because CoAuthors_Guest_Authors->action_add_meta_boxes is at 10.
		add_action( 'add_meta_boxes', array( __CLASS__, 'replace_guest_author_bio_metabox' ), 20, 2 );

	}

	public static function replace_guest_author_bio_metabox() : void {
		if ( 'guest-author' !== get_post_type() ) {
			return;
		}
		remove_meta_box( 'coauthors-manage-guest-author-bio', 'guest-author', 'normal' );
		add_meta_box( 'cata-coauthors-manage-guest-author-bio', 'Biographical Info', array( __CLASS__, 'metabox_guest_author_bio' ), 'guest-author', 'normal', 'default' );
	}

	public static function metabox_guest_author_bio() : void {

		global $post, $coauthors_plus;

		$fields = $coauthors_plus->guest_authors->get_guest_author_fields( 'about' );

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {

			$meta_key = 'cap-' . $field['key'];
			$input_id = 'cata-' . $meta_key;
			$settings = array(
				'media_buttons' => false,
				'textarea_name' => $meta_key,
				'teeny'         => true,
				'tinymce'       => array(
					'theme_advanced_path'               => false,
					'theme_advanced_statusbar_location' => 'none',
				),
				'quicktags'     => array(
					'buttons' => 'link,em,strong,close',
				),
			);

			?>
			<label for="<?php echo esc_attr( $input_id ) ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php

			wp_editor(
				get_post_meta( $post->ID, $meta_key, true ),
				$input_id,
				$settings
			);

		}

	}

	/**
	 * Remove Unused Meta Fields
	 * 
	 * @param array $fields_to_return Provided array of fields CAP is looking for.
	 * @return array Updated array of fields CAP should return.
	 */
	public static function remove_unused_meta_fields( array $fields_to_return ) : array {
		return array_values(
			array_filter(
				$fields_to_return,
				array( __CLASS__, 'should_keep_field' )
			)
		);
	}

	/**
	 * Should Keep Field?
	 *
	 * @param array $field Field we might want CAP to return.
	 * @return bool Whether this is a field to keep, by virture of NOT being a $field_to_remove.
	 */
	public static function should_keep_field( array $field ) : bool {
		return ! in_array( $field['key'], self::$fields_to_remove, true );
	}

	/**
	 * Add Custom Meta Fields
	 * 
	 * @param array $fields_to_return Provided array of fields CAP is looking for.
	 * @param array $groups Groups of fields CAP is looking for. Don't add fields if their group hasn't been requested.
	 * @return array Updated array of fields.
	 */
	public static function add_custom_meta_fields( array $fields_to_return, array $groups ) : array {
		// Everything is in contact-info for now.
		// Accept all and contact-info.
		if ( ! in_array( 'all', $groups, true ) && ! in_array( 'contact-info', $groups, true ) ) {
			return $fields_to_return;
		}

		$new_fields = array(
			array(
				'key'               => 'instagram',
				'label'             => 'Instagram URL',
				'group'             => 'contact-info',
				'sanitize_callback' => 'sanitize_text_field',
			),
			array(
				'key'               => 'tiktok',
				'label'             => 'TikTok URL',
				'group'             => 'contact-info',
				'sanitize_callback' => 'sanitize_text_field',
			),
			array(
				'key'               => 'twitter',
				'label'             => 'Twitter URL',
				'group'             => 'contact-info',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);

		return array_merge( $fields_to_return, $new_fields );
	}
}
