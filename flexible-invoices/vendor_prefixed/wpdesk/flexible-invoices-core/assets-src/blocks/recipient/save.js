import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";

export default function save( { className } ) {
	return (
		<>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {RecipientBlockBegin} -->" } }/>
			<div className={ className }>
				<InnerBlocks.Content/>
			</div>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {RecipientBlockEnd} -->" } }/>
		</>
	);
}
