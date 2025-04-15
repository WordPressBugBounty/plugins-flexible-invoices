import { InnerBlocks } from "@wordpress/block-editor";

export default function save( { className } ) {
	return (
		<>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {PaymentLinkBlockBegin} -->" } }/>
			<div className={ className }>
				<a href={"{PaymentLink}"}><InnerBlocks.Content/></a>
			</div>
			<div dangerouslySetInnerHTML={ { __html: "<!-- {PaymentLinkBlockEnd} -->" } }/>
		</>
	);
}
