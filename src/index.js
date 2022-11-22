import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Guest Author URL
 */
function CataCAPGuestAuthorURL() {

	/**
	 * Remove Post Status
	 */
	const { removeEditorPanel } = useDispatch('core/edit-post');

	useEffect( () => {
		removeEditorPanel( 'post-status' );
	}, [] );

	/**
	 * Add Author Archive URL
	 */
	const { authorPermalinkStructure } = window.cata;
	const { origin } = window.location;
	const { getCurrentPost } = useSelect('core/editor');

	const [ slug, setSlug ] = useState( '' );
	const [ post, setPost ] = useState( null );
	const [ url, setUrl ] = useState( '' );

	useEffect( () => {
		setPost(getCurrentPost());
	}, []);

	useEffect( () => {
		setSlug(post?.meta['cap-user_login'] || '');
	}, [post]);

	useEffect( () => {
		setUrl(
			'' === slug ? '' : origin + authorPermalinkStructure.replace( '%author%', slug )
		);
	}, [slug] );	

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

registerPlugin(
	'cata-cap-guest-author-url',
	{
		render: CataCAPGuestAuthorURL
	}
);


