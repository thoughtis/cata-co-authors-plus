<?php
/**
 * Meta Fields
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.1.2
 */

namespace Cata\CoAuthors_Plus;

/**
 * Meta Fields
 */
class Meta_Fields {
	/**
	 * Field to Remove
	 * 
	 * @var array $fields_to_remove
	 */
	private static $fields_to_remove = array(
		'aim',
		'yahooim',
		'jabber',
		'description',
	);

	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'coauthors_guest_author_fields', array( __CLASS__, 'remove_unused_meta_fields' ) );
		add_filter( 'coauthors_guest_author_fields', array( __CLASS__, 'add_custom_meta_fields' ), 20, 2 );
		// @priority 20 because CoAuthors_Guest_Authors->action_add_meta_boxes is at 10.
		add_action( 'add_meta_boxes', array( __CLASS__, 'replace_guest_author_bio_metabox' ), 20, 2 );
		add_action( 'do_meta_boxes', array( __CLASS__, 'remove_cutstom_fields_box' ), 10, 0 );
		add_action( 'edit_form_top', array( __CLASS__, 'remove_title_support' ) );
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

		$contact_info_fields = array(
			array(
				'key'   => 'instagram',
				'label' => 'Instagram URL',
				'group' => 'contact-info',
			),
			array(
				'key'   => 'tiktok',
				'label' => 'TikTok URL',
				'group' => 'contact-info',
			),
			array(
				'key'   => 'twitter',
				'label' => 'Twitter URL',
				'group' => 'contact-info',
			),
		);

		$about_fields = array(
			array(
				'group'             => 'about',
				'help_text'         => 'Short description. Approximately 70 - 140 characters. HTML is not included in character count.',
				'key'               => 'tagline',
				'label'             => 'Tagline',
				'sanitize_function' => 'wp_filter_post_kses',
			),
			array(
				'group'             => 'about',
				'help_text'         => 'Long description. Maximum 400 characters. HTML is not included in character count.',
				'key'               => 'description',
				'label'             => __( 'Biographical Info', 'co-authors-plus' ),
				'sanitize_function' => 'wp_filter_post_kses',
			),
		);
		
		if ( in_array( 'all', $groups, true ) || in_array( 'contact-info', $groups, true ) ) {
			$fields_to_return = array_merge( $fields_to_return, $contact_info_fields );
		}

		if ( in_array( 'all', $groups, true ) || in_array( 'about', $groups, true ) ) {
			$fields_to_return = array_merge( $fields_to_return, $about_fields );
		}

		return $fields_to_return;
	}

	/**
	 * Replace Guest Author Bio Metabox
	 */
	public static function replace_guest_author_bio_metabox() : void {
		if ( 'guest-author' !== get_post_type() ) {
			return;
		}
		remove_meta_box( 'coauthors-manage-guest-author-bio', 'guest-author', 'normal' );
		add_meta_box( 'cata-coauthors-manage-guest-author-bio', 'About The Author', array( __CLASS__, 'metabox_guest_author_bio' ), 'guest-author', 'normal', 'default' );
	}

	/**
	 * Metabox Guest Author Bio
	 * 
	 * @global WP_Post $post
	 * @global CoAuthors_Plus $coauthors_plus
	 */
	public static function metabox_guest_author_bio() : void {

		global $post, $coauthors_plus;

		$fields = $coauthors_plus->guest_authors->get_guest_author_fields( 'about' );

		if ( empty( $fields ) ) {
			return;
		}
		?>
		<div class="form-wrap">
			<table class="form-table">
				<?php foreach ( $fields as $field ) : ?>
					<?php
					$meta_key = 'cap-' . $field['key'];
					$input_id = 'cata-' . $meta_key;
					$settings = array(
						'media_buttons' => false,
						'quicktags'     => array(
							'buttons' => 'link,em,strong,close',
						),
						'textarea_name' => $meta_key,
						'teeny'         => true,
						'textarea_rows' => '5',
						'tinymce'       => false,
					);  
					?>
				<tr>
					<td>
						<label for="<?php echo esc_attr( $input_id ); ?>">
							<strong><?php echo esc_html( $field['label'] ); ?></strong>
						</label>
						<?php
							wp_editor(
								get_post_meta( $post->ID, $meta_key, true ),
								$input_id,
								$settings
							);
						?>
						<?php if ( isset( $field['help_text'] ) ) : ?>
						<p class="description">
							<?php echo esc_html( $field['help_text'] ); ?>
						</p>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php
	}

	/**
	 * Remove Custom Fields Box
	 * Custom Fields must be enabled to get meta data into the API,
	 * but we don't actually want it to be edited directly in the post editor.
	 */
	public static function remove_cutstom_fields_box() : void {
		remove_meta_box( 'postcustom', 'guest-author', 'normal' );
	}

	/**
	 * Remove Title Support
	 * The API needs title support, but the post editor does not.
	 */
	public static function remove_title_support() : void {
		remove_post_type_support( 'guest-author', 'title' );
	}
}
