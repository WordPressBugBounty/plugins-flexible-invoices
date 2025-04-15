import { InnerBlocks } from "@wordpress/block-editor";

export default function save( { className } ) {
	return (
		<>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {PriceSummaryBlockBegin} -->" } }/>
			<div className={ className }>
				<InnerBlocks.Content/>
			</div>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {PriceSummaryBlockEnd} -->" } }/>
		</>
	);
}
