export default function WizardTitle({children, closeCallback}) {
	return (
		<>
			<div className="wizard-title-container">
				<h3 className="wizard-title">{children}</h3>
				<button className="wizard-title-close" onClick={() => {
					closeCallback(true)
				}}>
					<span className="dashicons dashicons-no"></span>
				</button>
			</div>
		</>
	);
}
