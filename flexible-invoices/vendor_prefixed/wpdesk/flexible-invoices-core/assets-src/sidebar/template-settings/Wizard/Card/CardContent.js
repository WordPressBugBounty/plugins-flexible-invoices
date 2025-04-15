import CardImage from "./CardImage";
import "./card-styles.scss";

export default function CardContent({gifUrl, title, description, active, children}) {

	const classNames = [
		'card-content',
	]
	if (active) {
		classNames.push('card-content--active', 'active');
	}

	return (
		<>
			<div className={classNames.join(' ')}>
				{gifUrl && (
					<CardImage gifUrl={gifUrl} title={title}></CardImage>)
				}
				<div>
					<h3 className="card-content-title">{title}</h3>
					<p className="card-content-description">{description}</p>
					<div className="card-content-content"
						 dangerouslySetInnerHTML={{__html: children}}>
					</div>
				</div>
			</div>
		</>
	);

}
