/**
 * Guest Author URL
 */

/**
 * External dependencies
 */
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

 /**
  * Guest Author URL
  */
export default function GuestAuthorURL() {

	/**
	 * Add Author Archive URL
	 */
	const noticesDispatch = useDispatch('core/notices');
	const { getCurrentPost } = useSelect('core/editor');

	const [ slug, setSlug ] = useState( '' );
	const [ post, setPost ] = useState( null );
	const [ url, setUrl ] = useState( '' );

	useEffect( () => {
		setPost(getCurrentPost());
	}, []);

	useEffect( () => {
		setSlug(post?.slug || '');
	}, [post]);

	useEffect( () => {
		if ( 'string' !== typeof slug || '' === slug ) {
			return;
		}
		apiFetch( {
			path: `/wp/v2/coauthors?slug=${slug}`
		})
			.then( handleResponse )
			.catch( handleError )
	}, [slug] );

	/**
	 * Handle Response
	 *
	 * @param {Array} response
	 */
	function handleResponse( response ) {
		if ( ! Array.isArray(response) || 0 === response.length ) {
			setUrl( '' );
			handleError( new Error(`Author ${slug} not found`) );
		} else {
			setUrl( response[0].profile.link );
		}
	}

	/**
	 * Handle Error
	 * 
	 * @param {Error} error
	 */
	function handleError( error ) {
		noticesDispatch.createErrorNotice(
			error.message,
			{
				isDismissible: true
			}
		);
	}

	return (
		<PluginDocumentSettingPanel title="Profile URL" name="cata-cap-guest-author-url">
			{ url && 0 < url.length && (
				<p>
					<a target="_blank" href={url}>{url}</a>
				</p>
			) }
		</PluginDocumentSettingPanel>
	);
}
 