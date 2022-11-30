/**
 * Cata Co-Authors Plus
 */

/**
 * External dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import { useDispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import GuestAuthorURL from './components/guest-author-url';

/**
 * Styles
 */
import './style.scss';


/**
 * Guest Author
 */
function CataCAPGuestAuthor() {
	/**
	 * Remove Post Status
	 */
	const { removeEditorPanel } = useDispatch('core/edit-post');

	useEffect( () => {
		removeEditorPanel( 'post-status' );
	}, [] );

	return (
		<GuestAuthorURL />
	);
}

registerPlugin(
	'cata-cap-guest-author',
	{
		render: CataCAPGuestAuthor
	}
);


