import {Button} from "@wordpress/components";
import {useState} from "react";
import WizardTitle from "./WizardTitle";
import WizardDescription from "./WizardDescription";
import WizardFooter from "./WizardFooter";
import CardContent from "./Card/CardContent";
import CardListTrigger from "./Card/CardListTrigger";
import WizardContent from "./WizardContent";
import CardList from "./Card/CardList";
import {__} from "@wordpress/i18n";
import "./wizard-styles.scss"

export default function Wizard({steps, onWizardClose}) {

	const [currentStep, setCurrentStep] = useState(0)

	const handleNext = () => {
		if (currentStep < steps.length - 1) {
			setCurrentStep(currentStep + 1)
		}
	}

	const handlePrevious = () => {
		if (currentStep > 0) {
			setCurrentStep(currentStep - 1)
		}
	}

	const handleClose = () => {
		onWizardClose();
	}

	return (
		<>
			<div className='fi-template-wizard-background'>
				<div className='fi-template-wizard'>
					<WizardTitle closeCallback={ handleClose }>
						{__('Invoice editing tutorial', 'flexible-invoices')}
					</WizardTitle>

					<WizardDescription>
						{__('Explore the capabilities of the WooCommerce Blocks invoice editor', 'flexible-invoices')}
					</WizardDescription>

					<WizardContent>
						<CardList className="wizard-card-list">
							{steps.map((step, index) => (
								<CardListTrigger active={currentStep === index} key={index} onClick={ () => setCurrentStep(index)}>
									{step.title}
								</CardListTrigger>
							))}
						</CardList>
						{steps.map((step, index) => (
							<CardContent key={index} active={currentStep === index} title={step.title} gifUrl={step.gifUrl} description={step.description} className="wizard-card-content">
								{step.content}
							</CardContent>
						))}
					</WizardContent>

					<WizardFooter>
						<div className='column-1'>
							<Button onClick={handleClose}
									className="button secondary">
								{__('Close permanently', 'flexible-invoices')}
							</Button>
						</div>
						<div className='column-2'>
							<Button
								onClick={handlePrevious}
								disabled={currentStep === 0}
								className="button secondary"
							>
								<span className="dashicons dashicons-arrow-left-alt2"></span>
								{__('Back', 'flexible-invoices')}
							</Button>
							<Button onClick={handleNext} disabled={currentStep === steps.length - 1}
									className="button primary">
								{__('Next', 'flexible-invoices')}
								<span className="dashicons dashicons-arrow-right-alt2"></span>
							</Button>
						</div>
					</WizardFooter>
				</div>
			</div>
		</>
	);
}
