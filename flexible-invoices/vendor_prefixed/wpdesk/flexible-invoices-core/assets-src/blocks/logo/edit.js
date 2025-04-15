import {useEffect, useState} from '@wordpress/element';
import {
	useBlockProps,
	MediaPlaceholder,
	BlockControls,
	MediaReplaceFlow,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {__} from '@wordpress/i18n';
import {useSelect} from '@wordpress/data';
import {isBlobURL, revokeBlobURL} from '@wordpress/blob';
import {
	Spinner,
	withNotices,
	ToolbarButton,
	PanelBody,
	TextareaControl,
	SelectControl, ToggleControl,
} from '@wordpress/components';
import { NumberWithUnit, AlignButtons } from "@wpdesk/wordpress-ui-components"

function Edit({attributes, setAttributes, noticeOperations, noticeUI}) {
	const {imgUrl, imgAlt, imgId, imgWidth, autoWidth, imgHeight, autoHeight, align} = attributes;
	const [blobURL, setBlobURL] = useState();

	const imageObject = useSelect(
		(select) => {
			const {getMedia} = select('core');
			return imgId ? getMedia(imgId) : null;
		},
		[imgId]
	);

	const imageSizes = useSelect((select) => {
		return select(blockEditorStore).getSettings().imageSizes;
	}, []);

	const getImageSizeOptions = () => {
		if (!imageObject) return [];
		return Object.entries(imageObject.media_details.sizes)
			.map(([key, size]) => {
				const imageSize = imageSizes.find((s) => s.slug === key);
				return imageSize ? { label: imageSize.name, value: size.source_url } : null;
			})
			.filter(Boolean);
	};

	const onChangeAlt = (newAlt) => {
		setAttributes({imgAlt: newAlt});
	};
	const onSelectImage = (image) => {
		if (!image || !image.url) {
			setAttributes({ imgUrl: undefined, imgId: undefined, imgAlt: '' });
			return;
		}
		setAttributes({ imgUrl: image.url, imgId: image.id, imgAlt: image.alt });
	};
	const onSelectURL = (newURL) => {
		setAttributes({
			imgUrl: newURL,
			imgId: undefined,
			imgAlt: '',
		});
	};
	const onChangeImageSize = (newURL) => {
		setAttributes({imgUrl: newURL});
	};
	const onUploadError = (message) => {
		noticeOperations.removeAllNotices();
		noticeOperations.createErrorNotice(message);
	};

	const removeImage = () => {
		setAttributes({
			imgUrl: undefined,
			imgAlt: '',
			imgId: undefined,
		});
	};

	if (!imgId && isBlobURL(imgUrl)) {
		setAttributes({imgUrl: undefined, imgAlt: ''})
	}

	const [previousBlob, setPreviousBlob] = useState(imgUrl);

	if (imgUrl !== previousBlob) {
		setPreviousBlob(imgUrl);
		revokeBlobURL(previousBlob);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Image Settings', 'flexible-invoices')}>
					{imgId && (
						<SelectControl
							label={__('Image Size', 'flexible-invoices')}
							options={getImageSizeOptions()}
							value={imgUrl}
							onChange={onChangeImageSize}
						/>
					)}
					{imgUrl && !isBlobURL(imgUrl) && (
						<TextareaControl
							label={__('Alt Text', 'flexible-invoices')}
							value={imgAlt}
							onChange={onChangeAlt}
							help={__(
								"Alternative text describes your image to people can't see it. Add a short description with its key details.",
								'flexible-invoices'
							)}
						/>
					)}
					<ToggleControl
						label={__('Auto width', 'flexible-invoices')}
						checked={autoWidth}
						onChange={(newValue) => setAttributes({autoWidth: newValue})}
					/>
					{!autoWidth &&
						<NumberWithUnit
							label={__('Image width', 'flexible-invoices')}
							value={imgWidth}
							onChange={(value) => setAttributes({imgWidth: value})}
						/>}
					<ToggleControl
						label={__('Auto height', 'flexible-invoices')}
						checked={autoHeight}
						onChange={(newValue) => setAttributes({autoHeight: newValue})}
					/>
					{!autoHeight &&
						<NumberWithUnit
							label={__('Image height', 'flexible-invoices')}
							value={imgHeight}
							onChange={(value) => setAttributes({imgHeight: value})}
						/>}
					<AlignButtons
						label={__('Image align', 'flexible-invoices')}
						onChange={(value) => setAttributes({align: value})}
					/>

				</PanelBody>
			</InspectorControls>
			{imgUrl && (
				<BlockControls group="inline">
					<MediaReplaceFlow
						name={__('Replace Image', 'flexible-invoices')}
						onSelect={onSelectImage}
						onSelectURL={onSelectURL}
						onError={onUploadError}
						accept="image/*"
						allowedTypes={['image']}
						mediaId={imgId}
						mediaURL={imgUrl}
					/>
					<ToolbarButton onClick={removeImage}>
						{__('Remove Image', 'flexible-invoices')}
					</ToolbarButton>
				</BlockControls>
			)}
			<div {...useBlockProps()}>
				{imgUrl && (
					<div
						className={`wp-block-flexible-invoices-img${
							isBlobURL(imgUrl) ? ' is-loading' : ''
						}`}
						style={{
							textAlign: align
						}}
					>
						<img
							src={imgUrl}
							alt={imgAlt}
							className={imgId ? `wp-image-${imgId}` : null}
							style={{
								display: 'inline-block',
								width: autoWidth || !imgWidth ? 'auto' : imgWidth,
								height: autoHeight || !imgHeight ? 'auto' : imgHeight,
							}}
						/>
						{isBlobURL(imgUrl) && <Spinner/>}
					</div>
				)}
				<MediaPlaceholder
					icon="admin-users"
					onSelect={onSelectImage}
					onSelectURL={onSelectURL}
					onError={onUploadError}
					accept="image/*"
					allowedTypes={['image']}
					disableMediaButtons={imgUrl}
					notices={noticeUI}
				/>
			</div>
		</>
	);
}

export default withNotices(Edit);
