<?php
/**
 * Fields
 * 
 * @package Cata\CoAuthors_Plus
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
		add_filter( 'coauthors_guest_author_fields', array( __CLASS__, 'remove_unused_meta_fields' ), 10, 2 );
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
}
