export default function CardListTrigger({active, onClick, children}) {

	const classNames = [
		'card-list-trigger'
	];

	if (active) {
		classNames.push('card-list-trigger--active', 'active');
	}

	return (
		<button type="button" className={classNames.join(' ')} onClick={onClick}>
			{children}
		</button>
	);
}
