/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function Save( { attributes } ) {
	const { imgUrl, imgAlt, imgId, autoWidth, autoHeight, imgWidth, imgHeight, align } = attributes;
	return (
		<>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {LogoBlockBegin} -->" } }/>
			<div { ...useBlockProps.save() } style={ {
				textAlign: align
			} }>
				{ imgUrl && (
					<img
						src={ imgUrl }
						alt={ imgAlt }
						className={ imgId ? `wp-image-${ imgId }` : null }
						style={ {
							display: 'inline-block',
							width: autoWidth || ! imgWidth ? 'auto' : imgWidth,
							height: autoHeight || ! imgHeight ? 'auto' : imgHeight,
						} }
					/>
				) }
			</div>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {LogoBlockEnd} -->" } }/>
		</>
	)
		;
}
