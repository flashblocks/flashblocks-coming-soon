import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { ToggleControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

const META_KEY = '_fb_coming_soon';

function ComingSoonPanel() {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );
	const isOn = meta?.[ META_KEY ] === '1';

	return (
		<PluginDocumentSettingPanel
			name="flashblocks-coming-soon"
			title="Coming Soon"
		>
			<ToggleControl
				label="Enable Coming Soon"
				help={ isOn ? 'On — Visitors are redirected to the coming-soon page.' : 'Off — This page is currently public.' }
				checked={ isOn }
				onChange={ ( value ) =>
					setMeta( { ...meta, [ META_KEY ]: value ? '1' : '0' } )
				}
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'flashblocks-coming-soon', { render: ComingSoonPanel } );
