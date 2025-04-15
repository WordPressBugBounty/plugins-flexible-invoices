import "./card-styles.scss";
export default function CardList({children}) {
	return (
		<div className='card-list'>
			{children}
		</div>
	);
}
