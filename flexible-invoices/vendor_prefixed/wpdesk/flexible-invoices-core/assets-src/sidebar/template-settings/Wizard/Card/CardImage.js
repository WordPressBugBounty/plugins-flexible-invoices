import {__} from "@wordpress/i18n";

export default function CardImage({title, gifUrl}) {
	return (
		<div className="card-content-image-wrapper">
			<img
				src={gifUrl}
				alt={title}
				className="card-content-image"
			/>
			<div className="card-content-image-signature">
				{__('Demo GIF','flexible-invoices')}
			</div>
		</div>
	);
}
