import { InnerBlocks } from "@wordpress/block-editor";

export default function save( { className } ) {
	return (
		<>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {NotesBlockBegin} -->" } }/>
			<div className={ className }>
				<InnerBlocks.Content/>
			</div>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {NotesBlockEnd} -->" } }/>
		</>
	);
}
