/**
 * Acreline Gutenberg blocks — editor registration.
 *
 * Each block uses ServerSideRender for a true WYSIWYG canvas preview (PHP
 * render_callback output via REST API). Attribute editing lives in the
 * Inspector Controls sidebar panels.
 *
 * Images use the WordPress media picker (MediaUpload), not raw URL fields.
 * Advanced per-block settings are surfaced in dedicated "Advanced" panels.
 */

import { registerBlockType, setCategories, getCategories } from '@wordpress/blocks';
import { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, ToggleControl, RangeControl, Button, Spinner } from '@wordpress/components';
import { createElement as el, Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

// ---------------------------------------------------------------------------
// Block category
// ---------------------------------------------------------------------------
setCategories([
    ...getCategories(),
    ...(getCategories().find((c) => c.slug === 'acreline')
        ? []
        : [{ slug: 'acreline', title: 'Acreline', icon: null }]),
]);

// ---------------------------------------------------------------------------
// Shared UI helpers
// ---------------------------------------------------------------------------

/**
 * WordPress media picker panel row.
 * onSelect(url, id) — called when the user picks an image from the library.
 */
function MediaPicker({ url, id, onSelect, onRemove }) {
    return el(
        MediaUploadCheck,
        null,
        el(MediaUpload, {
            onSelect: (media) => onSelect(media.url, media.id),
            allowedTypes: ['image'],
            value: id || 0,
            render: ({ open }) =>
                el(
                    'div',
                    { style: { marginBottom: 8 } },
                    url &&
                        el('img', {
                            src: url,
                            alt: '',
                            style: { maxWidth: '100%', borderRadius: 4, display: 'block', marginBottom: 8 },
                        }),
                    el('div', { style: { display: 'flex', gap: 8, flexWrap: 'wrap' } },
                        el(Button, { onClick: open, variant: 'secondary', size: 'compact' },
                            url ? __('Change image', 'acreline') : __('Select image', 'acreline'),
                        ),
                        url && el(Button, { onClick: onRemove, variant: 'link', isDestructive: true, size: 'compact' },
                            __('Remove', 'acreline'),
                        ),
                    ),
                ),
        }),
    );
}

/**
 * Standard SsrEdit wrapper: puts a ServerSideRender block in the canvas and
 * accepts a sidebar render function.
 */
function SsrEdit({ blockName, attributes, sidebarFn }) {
    const blockProps = useBlockProps({ className: 'ks-block-ssr' });
    return el(
        Fragment,
        null,
        el(InspectorControls, null, sidebarFn()),
        el(
            'div',
            blockProps,
            el(ServerSideRender, {
                block: blockName,
                attributes,
                LoadingResponsePlaceholder: () =>
                    el('div', { style: { padding: '24px', textAlign: 'center', opacity: 0.4 } }, el(Spinner)),
            }),
        ),
    );
}

/** Shared typography panel (heading size, weight, body size, alignment). */
function TypographyPanel({ attrs, s }) {
    return el(
        PanelBody,
        { title: __('Typography', 'acreline'), initialOpen: false },
        el(SelectControl, {
            label: __('Heading size', 'acreline'),
            value: attrs.headingSize || 'default',
            options: [
                { value: 'sm',      label: __('Small',        'acreline') },
                { value: 'default', label: __('Default',      'acreline') },
                { value: 'lg',      label: __('Large',        'acreline') },
                { value: 'xl',      label: __('Extra large',  'acreline') },
            ],
            onChange: (v) => s({ headingSize: v }),
        }),
        el(SelectControl, {
            label: __('Heading weight', 'acreline'),
            value: attrs.headingWeight || 'default',
            options: [
                { value: 'default',  label: __('Default',  'acreline') },
                { value: 'medium',   label: __('Medium',   'acreline') },
                { value: 'semibold', label: __('Semibold', 'acreline') },
                { value: 'bold',     label: __('Bold',     'acreline') },
            ],
            onChange: (v) => s({ headingWeight: v }),
        }),
        el(SelectControl, {
            label: __('Body text size', 'acreline'),
            value: attrs.bodySize || 'default',
            options: [
                { value: 'sm',      label: __('Small',   'acreline') },
                { value: 'default', label: __('Default', 'acreline') },
                { value: 'lg',      label: __('Large',   'acreline') },
            ],
            onChange: (v) => s({ bodySize: v }),
        }),
        el(SelectControl, {
            label: __('Text alignment', 'acreline'),
            value: attrs.headingAlign || 'left',
            options: [
                { value: 'left',   label: __('Left',   'acreline') },
                { value: 'center', label: __('Center', 'acreline') },
            ],
            onChange: (v) => s({ headingAlign: v }),
        }),
    );
}

function BandPanel({ attrs, s, extra }) {
    return el(
        PanelBody,
        { title: __('Band & spacing', 'acreline'), initialOpen: false },
        el(SelectControl, {
            label: __('Background', 'acreline'),
            value: attrs.bandStyle || 'paper',
            options: [
                { value: 'paper',  label: __('Paper (default)', 'acreline') },
                { value: 'alt',    label: __('Alt / cream',     'acreline') },
                { value: 'accent', label: __('Accent',          'acreline') },
                { value: 'dark',   label: __('Dark (ink)',      'acreline') },
            ],
            onChange: (v) => s({ bandStyle: v }),
        }),
        el(SelectControl, {
            label: __('Section padding', 'acreline'),
            value: attrs.sectionPad || 'default',
            options: [
                { value: 'compact', label: __('Compact', 'acreline') },
                { value: 'default', label: __('Default', 'acreline') },
                { value: 'tall',    label: __('Tall',    'acreline') },
            ],
            onChange: (v) => s({ sectionPad: v }),
        }),
        el(SelectControl, {
            label: __('Heading level', 'acreline'),
            value: attrs.headingLevel || 'h2',
            options: [
                { value: 'h2', label: 'H2' },
                { value: 'h3', label: 'H3' },
                { value: 'h4', label: 'H4' },
            ],
            onChange: (v) => s({ headingLevel: v }),
            help: __('Use H2 for a main section. Use H3 if this sits under another heading.', 'acreline'),
        }),
        extra || null,
    );
}

/** Hero image + overlay panel (shared by home-hero and page-hero). */
function HeroImagePanel({ attrs, s }) {
    return el(
        Fragment,
        null,
        el(
            PanelBody,
            { title: __('Hero image', 'acreline'), initialOpen: false },
            el(MediaPicker, {
                url: attrs.imageUrl || '',
                id: attrs.imageId || 0,
                onSelect: (url, id) => s({ imageUrl: url, imageId: id }),
                onRemove: () => s({ imageUrl: '', imageId: 0 }),
            }),
            el(SelectControl, {
                label: __('Image focal point', 'acreline'),
                value: attrs.imagePosition || 'center',
                options: [
                    { value: 'center', label: __('Center (default)', 'acreline') },
                    { value: 'top',    label: __('Top',              'acreline') },
                    { value: 'bottom', label: __('Bottom',           'acreline') },
                    { value: 'left',   label: __('Left',             'acreline') },
                    { value: 'right',  label: __('Right',            'acreline') },
                ],
                onChange: (v) => s({ imagePosition: v }),
            }),
        ),
        el(
            PanelBody,
            { title: __('Overlay & hero size', 'acreline'), initialOpen: false },
            el(SelectControl, {
                label: __('Overlay strength', 'acreline'),
                value: attrs.overlayPreset || 'default',
                options: [
                    { value: 'light',   label: __('Light',          'acreline') },
                    { value: 'default', label: __('Default',        'acreline') },
                    { value: 'dark',    label: __('Heavy (dark bg)', 'acreline') },
                ],
                onChange: (v) => s({ overlayPreset: v }),
            }),
            el(RangeControl, {
                label: __('Fine-tune overlay opacity (%)', 'acreline'),
                value: attrs.overlayOpacity !== undefined ? attrs.overlayOpacity : 60,
                onChange: (v) => s({ overlayOpacity: v }),
                min: 0, max: 100, step: 5,
            }),
            el(SelectControl, {
                label: __('Hero height', 'acreline'),
                value: attrs.heroHeight || 'default',
                options: [
                    { value: 'compact', label: __('Compact',          'acreline') },
                    { value: 'default', label: __('Default',          'acreline') },
                    { value: 'tall',    label: __('Tall (near-full)', 'acreline') },
                ],
                onChange: (v) => s({ heroHeight: v }),
            }),
            el(SelectControl, {
                label: __('Text alignment', 'acreline'),
                value: attrs.textAlign || 'left',
                options: [
                    { value: 'left',   label: __('Left',   'acreline') },
                    { value: 'center', label: __('Center', 'acreline') },
                ],
                onChange: (v) => s({ textAlign: v }),
            }),
        ),
    );
}

// ---------------------------------------------------------------------------
// 1. Home Hero
// ---------------------------------------------------------------------------
registerBlockType('acreline/home-hero', {
    title: __('Home Hero', 'acreline'),
    description: __('Full-width hero with integrated listing search form.', 'acreline'),
    category: 'acreline',
    icon: 'cover-image',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:        { type: 'string',  default: 'Homes, neighborhoods, and local agents' },
        title:          { type: 'string',  default: 'Homes worth <em>walking through.</em>' },
        text:           { type: 'string',  default: '' },
        imageUrl:       { type: 'string',  default: 'https://images.unsplash.com/photo-1570129477492-45c003edd2be' },
        imageId:        { type: 'integer', default: 0 },
        imagePosition:  { type: 'string',  default: 'center' },
        primaryLabel:   { type: 'string',  default: 'Show matches' },
        secondaryLabel: { type: 'string',  default: 'Browse all listings' },
        heroHeight:     { type: 'string',  default: 'default' },
        overlayPreset:  { type: 'string',  default: 'default' },
        overlayOpacity: { type: 'integer', default: 60 },
        textAlign:      { type: 'string',  default: 'left' },
        primaryBtnStyle: { type: 'string', default: 'primary' },
        headingSize:    { type: 'string',  default: 'default' },
        headingWeight:  { type: 'string',  default: 'default' },
        bodySize:       { type: 'string',  default: 'default' },
        headingAlign:   { type: 'string',  default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/home-hero',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Hero copy', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'),               value: a.eyebrow,        onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title (use <em> for italics)','acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Intro text', 'acreline'),            value: a.text,           onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Search buttons', 'acreline'), initialOpen: false },
                    el(TextControl, { label: __('Submit button label',   'acreline'), value: a.primaryLabel,   onChange: (v) => s({ primaryLabel: v }) }),
                    el(TextControl, { label: __('Browse link label',     'acreline'), value: a.secondaryLabel, onChange: (v) => s({ secondaryLabel: v }) }),
                    el(SelectControl, {
                        label: __('Submit button style', 'acreline'),
                        value: a.primaryBtnStyle || 'primary',
                        options: [
                            { value: 'primary',       label: __('Forest green (default)', 'acreline') },
                            { value: 'outline-light', label: __('White outline',          'acreline') },
                            { value: 'white',         label: __('White fill',             'acreline') },
                            { value: 'gold',          label: __('Dark / charcoal',        'acreline') },
                            { value: 'ghost',         label: __('Ghost (minimal)',        'acreline') },
                        ],
                        onChange: (v) => s({ primaryBtnStyle: v }),
                    }),
                ),
                el(HeroImagePanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 2. Page Hero
// ---------------------------------------------------------------------------
registerBlockType('acreline/page-hero', {
    title: __('Page Hero', 'acreline'),
    description: __('Standard page hero with photo, eyebrow, title, and CTA buttons.', 'acreline'),
    category: 'acreline',
    icon: 'admin-page',
    supports: { html: false },
    attributes: {
        brand:          { type: 'string',  default: '' },
        eyebrow:        { type: 'string',  default: '' },
        title:          { type: 'string',  default: '' },
        text:           { type: 'string',  default: '' },
        imageUrl:       { type: 'string',  default: '' },
        imageId:        { type: 'integer', default: 0 },
        imagePosition:  { type: 'string',  default: 'center' },
        primaryLabel:   { type: 'string',  default: 'Book a showing' },
        primaryUrl:     { type: 'string',  default: '' },
        secondaryLabel: { type: 'string',  default: '' },
        secondaryUrl:   { type: 'string',  default: '' },
        heroHeight:     { type: 'string',  default: 'default' },
        overlayPreset:  { type: 'string',  default: 'default' },
        overlayOpacity: { type: 'integer', default: 60 },
        textAlign:      { type: 'string',  default: 'left' },
        primaryBtnStyle:   { type: 'string', default: 'primary' },
        secondaryBtnStyle: { type: 'string', default: 'outline-light' },
        headingSize:    { type: 'string',  default: 'default' },
        headingWeight:  { type: 'string',  default: 'default' },
        bodySize:       { type: 'string',  default: 'default' },
        headingAlign:   { type: 'string',  default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/page-hero',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Hero copy', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Brand override (blank = site name)', 'acreline'), value: a.brand,   onChange: (v) => s({ brand: v }) }),
                    el(TextControl,     { label: __('Eyebrow', 'acreline'),                           value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title (use <em> for italics)', 'acreline'),      value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Intro text', 'acreline'),                        value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('CTA buttons', 'acreline'), initialOpen: false },
                    el(TextControl, { label: __('Primary label', 'acreline'), value: a.primaryLabel,   onChange: (v) => s({ primaryLabel: v }) }),
                    el(TextControl, { label: __('Primary URL',   'acreline'), value: a.primaryUrl,    onChange: (v) => s({ primaryUrl: v }),   type: 'url' }),
                    el(SelectControl, {
                        label: __('Primary button style', 'acreline'),
                        value: a.primaryBtnStyle || 'primary',
                        options: [
                            { value: 'primary',       label: __('Forest green (default)', 'acreline') },
                            { value: 'outline-light', label: __('White outline',          'acreline') },
                            { value: 'white',         label: __('White fill',             'acreline') },
                            { value: 'gold',          label: __('Dark / charcoal',        'acreline') },
                            { value: 'ghost',         label: __('Ghost (minimal)',        'acreline') },
                        ],
                        onChange: (v) => s({ primaryBtnStyle: v }),
                    }),
                    el(TextControl, { label: __('Secondary label', 'acreline'), value: a.secondaryLabel, onChange: (v) => s({ secondaryLabel: v }) }),
                    el(TextControl, { label: __('Secondary URL',   'acreline'), value: a.secondaryUrl,   onChange: (v) => s({ secondaryUrl: v }),   type: 'url' }),
                    el(SelectControl, {
                        label: __('Secondary button style', 'acreline'),
                        value: a.secondaryBtnStyle || 'outline-light',
                        options: [
                            { value: 'outline-light', label: __('White outline (default)', 'acreline') },
                            { value: 'white',         label: __('White fill',              'acreline') },
                            { value: 'primary',       label: __('Forest green',            'acreline') },
                            { value: 'gold',          label: __('Dark / charcoal',         'acreline') },
                            { value: 'ghost',         label: __('Ghost (minimal)',         'acreline') },
                        ],
                        onChange: (v) => s({ secondaryBtnStyle: v }),
                    }),
                ),
                el(HeroImagePanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 3. Intent Cards
// ---------------------------------------------------------------------------
registerBlockType('acreline/intent-cards', {
    title: __('Intent Cards', 'acreline'),
    description: __('Buy / Sell / Tour three-card section with help notes.', 'acreline'),
    category: 'acreline',
    icon: 'grid-view',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:    { type: 'string', default: 'Start here' },
        title:      { type: 'string', default: 'Pick the path' },
        text:       { type: 'string', default: 'Then we match a listing or a tour. Neighborhood first — the rest follows.' },
        buyKicker:  { type: 'string', default: 'Buy' },
        buyTitle:   { type: 'string', default: 'Scan homes and neighborhoods' },
        buyLead:    { type: 'string', default: '' },
        buyCta:     { type: 'string', default: 'Browse listings' },
        sellKicker: { type: 'string', default: 'Sell' },
        sellTitle:  { type: 'string', default: 'Price it before you list' },
        sellLead:   { type: 'string', default: '' },
        sellCta:    { type: 'string', default: 'Estimate value' },
        tourKicker: { type: 'string', default: 'Tour' },
        tourTitle:  { type: 'string', default: 'Walk it on the ground' },
        tourLead:   { type: 'string', default: '' },
        tourCta:    { type: 'string', default: 'Book a showing' },
        notesLabel: { type: 'string', default: 'Good to know' },
        note1Title: { type: 'string', default: 'Neighborhood first' },
        note1Text:  { type: 'string', default: '' },
        note2Title: { type: 'string', default: 'Well and perc' },
        note2Text:  { type: 'string', default: '' },
        note3Title: { type: 'string', default: 'Boots for showings' },
        note3Text:  { type: 'string', default: '' },
        cardStyle:  { type: 'string',  default: 'photo' },
        intentCols: { type: 'string',  default: '3' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/intent-cards',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Buy card', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Kicker',    'acreline'), value: a.buyKicker, onChange: (v) => s({ buyKicker: v }) }),
                    el(TextControl,     { label: __('Title',     'acreline'), value: a.buyTitle,  onChange: (v) => s({ buyTitle: v }) }),
                    el(TextareaControl, { label: __('Lead text', 'acreline'), value: a.buyLead,   onChange: (v) => s({ buyLead: v }) }),
                    el(TextControl,     { label: __('CTA label', 'acreline'), value: a.buyCta,    onChange: (v) => s({ buyCta: v }) }),
                ),
                el(PanelBody, { title: __('Sell card', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Kicker',    'acreline'), value: a.sellKicker, onChange: (v) => s({ sellKicker: v }) }),
                    el(TextControl,     { label: __('Title',     'acreline'), value: a.sellTitle,  onChange: (v) => s({ sellTitle: v }) }),
                    el(TextareaControl, { label: __('Lead text', 'acreline'), value: a.sellLead,   onChange: (v) => s({ sellLead: v }) }),
                    el(TextControl,     { label: __('CTA label', 'acreline'), value: a.sellCta,    onChange: (v) => s({ sellCta: v }) }),
                ),
                el(PanelBody, { title: __('Tour card', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Kicker',    'acreline'), value: a.tourKicker, onChange: (v) => s({ tourKicker: v }) }),
                    el(TextControl,     { label: __('Title',     'acreline'), value: a.tourTitle,  onChange: (v) => s({ tourTitle: v }) }),
                    el(TextareaControl, { label: __('Lead text', 'acreline'), value: a.tourLead,   onChange: (v) => s({ tourLead: v }) }),
                    el(TextControl,     { label: __('CTA label', 'acreline'), value: a.tourCta,    onChange: (v) => s({ tourCta: v }) }),
                ),
                el(PanelBody, { title: __('Help notes', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Notes label',  'acreline'), value: a.notesLabel, onChange: (v) => s({ notesLabel: v }) }),
                    el(TextControl,     { label: __('Note 1 title', 'acreline'), value: a.note1Title, onChange: (v) => s({ note1Title: v }) }),
                    el(TextareaControl, { label: __('Note 1 text',  'acreline'), value: a.note1Text,  onChange: (v) => s({ note1Text: v }) }),
                    el(TextControl,     { label: __('Note 2 title', 'acreline'), value: a.note2Title, onChange: (v) => s({ note2Title: v }) }),
                    el(TextareaControl, { label: __('Note 2 text',  'acreline'), value: a.note2Text,  onChange: (v) => s({ note2Text: v }) }),
                    el(TextControl,     { label: __('Note 3 title', 'acreline'), value: a.note3Title, onChange: (v) => s({ note3Title: v }) }),
                    el(TextareaControl, { label: __('Note 3 text',  'acreline'), value: a.note3Text,  onChange: (v) => s({ note3Text: v }) }),
                ),
                el(PanelBody, { title: __('Layout', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Grid columns', 'acreline'),
                        value: a.intentCols || '3',
                        options: [
                            { value: '3', label: __('3 columns (default)', 'acreline') },
                            { value: '2', label: __('2 columns',           'acreline') },
                        ],
                        onChange: (v) => s({ intentCols: v }),
                    }),
                    el(SelectControl, {
                        label: __('Card style', 'acreline'),
                        value: a.cardStyle || 'photo',
                        options: [
                            { value: 'photo',   label: __('Photo cards (default)', 'acreline') },
                            { value: 'minimal', label: __('Minimal / text only',   'acreline') },
                        ],
                        onChange: (v) => s({ cardStyle: v }),
                    }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 4. Spotlight
// ---------------------------------------------------------------------------
registerBlockType('acreline/spotlight', {
    title: __('Featured Listings Spotlight', 'acreline'),
    description: __('Grid of featured listings pulled dynamically from WP.', 'acreline'),
    category: 'acreline',
    icon: 'star-filled',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:    { type: 'string',  default: 'Spotlight' },
        title:      { type: 'string',  default: 'Three sample homes to scan' },
        text:       { type: 'string',  default: 'Price · beds · acres — then book a fictional walk-through.' },
        itemCount:  { type: 'integer', default: 3 },
        gridCols:   { type: 'string',  default: 'auto' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/spotlight',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Display', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Number of listings', 'acreline'),
                        value: String(a.itemCount || 3),
                        options: ['2','3','4','6'].map((n) => ({ value: n, label: `${n} listings` })),
                        onChange: (v) => s({ itemCount: parseInt(v, 10) }),
                    }),
                    el(SelectControl, {
                        label: __('Grid columns', 'acreline'),
                        value: a.gridCols || 'auto',
                        options: [
                            { value: 'auto', label: __('Auto (matches item count)', 'acreline') },
                            { value: '2',    label: __('2 columns',                 'acreline') },
                            { value: '4',    label: __('4 columns',                 'acreline') },
                        ],
                        onChange: (v) => s({ gridCols: v }),
                    }),
                    el('p', { style: { fontSize: '0.75rem', color: '#646970', margin: '4px 0 0' } },
                        __('Mark listings as Featured in WP Admin → Listings.', 'acreline'),
                    ),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 5. Booking Section
// ---------------------------------------------------------------------------
registerBlockType('acreline/booking-section', {
    title: __('Booking Form Section', 'acreline'),
    description: __('Showing request form with section header.', 'acreline'),
    category: 'acreline',
    icon: 'calendar-alt',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:      { type: 'string',  default: 'Appointments' },
        title:        { type: 'string',  default: 'Book a house showing' },
        text:         { type: 'string',  default: 'Demo scheduler for touring sample homes.' },
        showSidePhoto:{ type: 'boolean', default: true },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/booking-section',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Display options', 'acreline'), initialOpen: false },
                    el(ToggleControl, {
                        label: __('Show side photo', 'acreline'),
                        checked: a.showSidePhoto !== false,
                        onChange: (v) => s({ showSidePhoto: v }),
                    }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 6. Market Stats
// ---------------------------------------------------------------------------
registerBlockType('acreline/market-stats', {
    title: __('Market Stats', 'acreline'),
    description: __('Four editable market-statistic tiles.', 'acreline'),
    category: 'acreline',
    icon: 'chart-line',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:     { type: 'string', default: 'Sample market' },
        title:       { type: 'string', default: 'Pulse at a glance' },
        text:        { type: 'string', default: '' },
        stat1Val:    { type: 'string', default: '$398k' },
        stat1Lbl:    { type: 'string', default: 'Median sale price' },
        stat1Sub:    { type: 'string', default: '↑ 2.1% vs last quarter' },
        stat2Val:    { type: 'string', default: '32' },
        stat2Lbl:    { type: 'string', default: 'Days on market' },
        stat2Sub:    { type: 'string', default: '↓ 5 days vs last quarter' },
        stat3Val:    { type: 'string', default: '1.6' },
        stat3Lbl:    { type: 'string', default: 'Months of inventory' },
        stat3Sub:    { type: 'string', default: 'Limited active supply' },
        stat4Val:    { type: 'string', default: '95%' },
        stat4Lbl:    { type: 'string', default: 'List-to-sale ratio' },
        stat4Sub:    { type: 'string', default: 'Offers near asking' },
        statsLayout: { type: 'string', default: '4-col' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/market-stats',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Statistics', 'acreline'), initialOpen: false },
                    ...[1, 2, 3, 4].flatMap((i) => [
                        el(TextControl, { key: `v${i}`, label: `Stat ${i} value`,    value: a[`stat${i}Val`], onChange: (v) => s({ [`stat${i}Val`]: v }) }),
                        el(TextControl, { key: `l${i}`, label: `Stat ${i} label`,    value: a[`stat${i}Lbl`], onChange: (v) => s({ [`stat${i}Lbl`]: v }) }),
                        el(TextControl, { key: `s${i}`, label: `Stat ${i} sub-line`, value: a[`stat${i}Sub`], onChange: (v) => s({ [`stat${i}Sub`]: v }) }),
                    ]),
                ),
                el(PanelBody, { title: __('Layout', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Column layout', 'acreline'),
                        value: a.statsLayout || '4-col',
                        options: [
                            { value: '4-col', label: __('4 columns (default)', 'acreline') },
                            { value: '2-col', label: __('2 columns',           'acreline') },
                        ],
                        onChange: (v) => s({ statsLayout: v }),
                    }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 7. How It Works
// ---------------------------------------------------------------------------
registerBlockType('acreline/how-it-works', {
    title: __('How It Works', 'acreline'),
    description: __('Four-step tour process: filter → read → book → walk.', 'acreline'),
    category: 'acreline',
    icon: 'list-view',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:    { type: 'string', default: 'How a tour starts' },
        title:      { type: 'string', default: 'From search to showing' },
        text:       { type: 'string', default: '' },
        stepLayout: { type: 'string', default: 'grid' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/how-it-works',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 8. Agent Tools
// ---------------------------------------------------------------------------
registerBlockType('acreline/agent-tools', {
    title: __('Agent Tools', 'acreline'),
    description: __('Demo home value estimator and listing alert forms.', 'acreline'),
    category: 'acreline',
    icon: 'admin-tools',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:        { type: 'string',  default: 'Agent tools' },
        title:          { type: 'string',  default: 'Value range and listing alerts' },
        text:           { type: 'string',  default: '' },
        showValueTool:  { type: 'boolean', default: true },
        showAlertTool:  { type: 'boolean', default: true },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/agent-tools',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Tools to show', 'acreline'), initialOpen: false },
                    el(ToggleControl, { label: __('Show home value estimator', 'acreline'), checked: a.showValueTool !== false, onChange: (v) => s({ showValueTool: v }) }),
                    el(ToggleControl, { label: __('Show listing alerts form',  'acreline'), checked: a.showAlertTool !== false, onChange: (v) => s({ showAlertTool: v }) }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 9. SEO Content
// ---------------------------------------------------------------------------
registerBlockType('acreline/seo-content', {
    title: __('SEO Content Block', 'acreline'),
    description: __('Buying guide copy and scan cards for real estate SEO.', 'acreline'),
    category: 'acreline',
    icon: 'search',
    supports: { html: false, multiple: false },
    attributes: {},
    edit() {
        return el(SsrEdit, {
            blockName: 'acreline/seo-content',
            attributes: {},
            sidebarFn: () =>
                el(PanelBody, { title: __('SEO Content', 'acreline'), initialOpen: true },
                    el('p', { style: { fontSize: '0.8rem', color: '#646970', margin: 0 } },
                        __('Static buying guide copy. Edit body text in app/blocks.php or replace with your own market-specific content.', 'acreline'),
                    ),
                ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 10. Reviews
// ---------------------------------------------------------------------------
registerBlockType('acreline/reviews', {
    title: __('Client Reviews', 'acreline'),
    description: __('Demo review cards shaped like a Google Places response.', 'acreline'),
    category: 'acreline',
    icon: 'star-half',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:      { type: 'string',  default: 'Samples' },
        title:        { type: 'string',  default: 'What clients might say' },
        text:         { type: 'string',  default: '' },
        reviewLayout: { type: 'string',  default: 'grid' },
        reviewCols:   { type: 'string',  default: '3' },
        showRating:   { type: 'boolean', default: true },
        showPhoto:    { type: 'boolean', default: true },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/reviews',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Layout & display', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Columns', 'acreline'),
                        value: a.reviewCols || '3',
                        options: [
                            { value: '1', label: __('1 (single card)', 'acreline') },
                            { value: '2', label: __('2 columns',       'acreline') },
                            { value: '3', label: __('3 columns (default)', 'acreline') },
                        ],
                        onChange: (v) => s({ reviewCols: v }),
                    }),
                    el(ToggleControl, { label: __('Show star rating',   'acreline'), checked: a.showRating !== false, onChange: (v) => s({ showRating: v }) }),
                    el(ToggleControl, { label: __('Show reviewer photo','acreline'), checked: a.showPhoto  !== false, onChange: (v) => s({ showPhoto: v }) }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 11. FAQ List
// ---------------------------------------------------------------------------
registerBlockType('acreline/faq-list', {
    title: __('FAQ List', 'acreline'),
    description: __('Frequently asked questions, auto-loaded by page context.', 'acreline'),
    category: 'acreline',
    icon: 'editor-help',
    supports: { html: false, multiple: false },
    attributes: {
        title:       { type: 'string',  default: 'Questions buyers ask first' },
        text:        { type: 'string',  default: 'Practical answers for house and neighborhood shoppers.' },
        headClass:   { type: 'string',  default: 'left' },
        faqStyle:    { type: 'string',  default: 'dl' },
        listIcon:    { type: 'string',  default: 'none' },
        showNumbers: { type: 'boolean', default: false },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/faq-list',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Title', 'acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',  'acreline'), value: a.text,  onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Display style', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('List format', 'acreline'),
                        value: a.faqStyle || 'dl',
                        options: [
                            { value: 'dl',        label: __('Definition list (default)', 'acreline') },
                            { value: 'flat',      label: __('Flat list',                 'acreline') },
                            { value: 'accordion', label: __('Accordion (expand/collapse)','acreline') },
                        ],
                        onChange: (v) => s({ faqStyle: v }),
                    }),
                    a.faqStyle !== 'accordion' && el(Fragment, null,
                        el(SelectControl, {
                            label: __('List icon', 'acreline'),
                            value: a.listIcon || 'none',
                            options: [
                                { value: 'none',  label: __('None (default)',     'acreline') },
                                { value: 'arrow', label: __('→ Arrow',            'acreline') },
                                { value: 'check', label: __('✓ Checkmark',        'acreline') },
                            ],
                            onChange: (v) => s({ listIcon: v }),
                            help: __('Icon prepended to each question.', 'acreline'),
                        }),
                        el(ToggleControl, {
                            label: __('Number the questions', 'acreline'),
                            checked: !!a.showNumbers,
                            onChange: (v) => s({ showNumbers: v }),
                        }),
                    ),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 12. CTA Band
// ---------------------------------------------------------------------------
registerBlockType('acreline/cta-band', {
    title: __('CTA Band', 'acreline'),
    description: __('Full-width call-to-action section with two buttons.', 'acreline'),
    category: 'acreline',
    icon: 'megaphone',
    supports: { html: false },
    attributes: {
        title:         { type: 'string', default: 'Tour a sample home next.' },
        text:          { type: 'string', default: 'Pick an address, choose a slot, and see how a modern realtor booking flow feels.' },
        primaryLabel:  { type: 'string', default: 'Book a showing' },
        primaryUrl:    { type: 'string', default: '' },
        secondaryLabel:{ type: 'string', default: 'Browse samples' },
        secondaryUrl:  { type: 'string', default: '' },
        bandStyle:     { type: 'string', default: 'light' },
        contentAlign:  { type: 'string', default: 'left' },
        headingSize:   { type: 'string', default: 'default' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/cta-band',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('CTA copy', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Title', 'acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',  'acreline'), value: a.text,  onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Buttons', 'acreline'), initialOpen: false },
                    el(TextControl, { label: __('Primary label',   'acreline'), value: a.primaryLabel,   onChange: (v) => s({ primaryLabel: v }) }),
                    el(TextControl, { label: __('Primary URL',     'acreline'), value: a.primaryUrl,     onChange: (v) => s({ primaryUrl: v }),    type: 'url' }),
                    el(TextControl, { label: __('Secondary label', 'acreline'), value: a.secondaryLabel, onChange: (v) => s({ secondaryLabel: v }) }),
                    el(TextControl, { label: __('Secondary URL',   'acreline'), value: a.secondaryUrl,   onChange: (v) => s({ secondaryUrl: v }),  type: 'url' }),
                ),
                el(PanelBody, { title: __('Style & alignment', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Background', 'acreline'),
                        value: a.bandStyle || 'light',
                        options: [
                            { value: 'light',  label: __('Light (default)', 'acreline') },
                            { value: 'accent', label: __('Accent', 'acreline') },
                            { value: 'dark',   label: __('Dark (ink)',       'acreline') },
                        ],
                        onChange: (v) => s({ bandStyle: v }),
                    }),
                    el(SelectControl, {
                        label: __('Content alignment', 'acreline'),
                        value: a.contentAlign || 'left',
                        options: [
                            { value: 'left',   label: __('Left (default)', 'acreline') },
                            { value: 'center', label: __('Centered',       'acreline') },
                        ],
                        onChange: (v) => s({ contentAlign: v }),
                    }),
                    el(SelectControl, {
                        label: __('Heading size', 'acreline'),
                        value: a.headingSize || 'default',
                        options: [
                            { value: 'sm',      label: __('Small',       'acreline') },
                            { value: 'default', label: __('Default',     'acreline') },
                            { value: 'lg',      label: __('Large',       'acreline') },
                            { value: 'xl',      label: __('Extra large', 'acreline') },
                        ],
                        onChange: (v) => s({ headingSize: v }),
                    }),
                ),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 13. Intro Section
// ---------------------------------------------------------------------------
registerBlockType('acreline/intro-section', {
    title: __('Intro Section', 'acreline'),
    description: __('Eyebrow / title / lede text intro.', 'acreline'),
    category: 'acreline',
    icon: 'align-left',
    supports: { html: false },
    attributes: {
        eyebrow:       { type: 'string', default: '' },
        title:         { type: 'string', default: '' },
        text:          { type: 'string', default: '' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/intro-section',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Content', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 14. Listing Grid
// ---------------------------------------------------------------------------
registerBlockType('acreline/listing-grid', {
    title: __('Listing Grid', 'acreline'),
    description: __('Filter toolbar + listing card grid + map view.', 'acreline'),
    category: 'acreline',
    icon: 'table-row-before',
    supports: { html: false, multiple: false },
    attributes: {
        introTitle:  { type: 'string', default: 'Buying in this sample market' },
        introText:   { type: 'string', default: '' },
        defaultView: { type: 'string', default: 'grid' },
        gridCols:    { type: 'string', default: '3' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/listing-grid',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Local note', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Title', 'acreline'), value: a.introTitle, onChange: (v) => s({ introTitle: v }) }),
                    el(TextareaControl, { label: __('Text',  'acreline'), value: a.introText,  onChange: (v) => s({ introText: v }) }),
                ),
                el(PanelBody, { title: __('Grid settings', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Default view', 'acreline'),
                        value: a.defaultView || 'grid',
                        options: [
                            { value: 'grid', label: __('Grid (default)', 'acreline') },
                            { value: 'map',  label: __('Map',            'acreline') },
                        ],
                        onChange: (v) => s({ defaultView: v }),
                    }),
                    el(SelectControl, {
                        label: __('Cards per row', 'acreline'),
                        value: a.gridCols || '3',
                        options: [
                            { value: '2', label: __('2 cards', 'acreline') },
                            { value: '3', label: __('3 cards (default)', 'acreline') },
                            { value: '4', label: __('4 cards', 'acreline') },
                        ],
                        onChange: (v) => s({ gridCols: v }),
                    }),
                    el('p', { style: { fontSize: '0.75rem', color: '#646970', margin: '4px 0 0' } },
                        __('Filter and grid are non-interactive in the editor.', 'acreline'),
                    ),
                ),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 15. Agent List
// ---------------------------------------------------------------------------
registerBlockType('acreline/agent-list', {
    title: __('Agent List', 'acreline'),
    description: __('Grid of sample agents with photos, stats, and contact links.', 'acreline'),
    category: 'acreline',
    icon: 'groups',
    supports: { html: false, multiple: true },
    attributes: {
        eyebrow:       { type: 'string', default: 'The sample team' },
        title:         { type: 'string', default: 'Agents who know this ground' },
        text:          { type: 'string', default: 'Three demo profiles — buyers, sellers, and first-time homeowners in this sample market.' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/agent-list',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 16. Area Grid
// ---------------------------------------------------------------------------
registerBlockType('acreline/area-grid', {
    title: __('Area Grid', 'acreline'),
    description: __('Up to six area cards with local market copy.', 'acreline'),
    category: 'acreline',
    icon: 'location',
    supports: { html: false, multiple: false },
    attributes: {
        gridEyebrow: { type: 'string', default: 'Neighborhood by neighborhood' },
        gridTitle:   { type: 'string', default: 'Where the sample office works' },
        gridText:    { type: 'string', default: '' },
        area1Meta: { type: 'string', default: 'West ridge · established streets' }, area1Title: { type: 'string', default: 'North Ridge' }, area1Body: { type: 'string', default: '' },
        area2Meta: { type: 'string', default: '' }, area2Title: { type: 'string', default: 'Mill Creek' }, area2Body: { type: 'string', default: '' },
        area3Meta: { type: 'string', default: '' }, area3Title: { type: 'string', default: 'Oak Hollow' }, area3Body: { type: 'string', default: '' },
        area4Meta: { type: 'string', default: '' }, area4Title: { type: 'string', default: 'Riverbend' }, area4Body: { type: 'string', default: '' },
        area5Meta: { type: 'string', default: '' }, area5Title: { type: 'string', default: 'Midtown' }, area5Body: { type: 'string', default: '' },
        area6Meta: { type: 'string', default: '' }, area6Title: { type: 'string', default: 'Southgate' }, area6Body: { type: 'string', default: '' },
        area1ImageUrl: { type: 'string', default: '' }, area1ImageId: { type: 'integer', default: 0 }, area1Cta: { type: 'string', default: '' }, area1Url: { type: 'string', default: '' },
        area2ImageUrl: { type: 'string', default: '' }, area2ImageId: { type: 'integer', default: 0 }, area2Cta: { type: 'string', default: '' }, area2Url: { type: 'string', default: '' },
        area3ImageUrl: { type: 'string', default: '' }, area3ImageId: { type: 'integer', default: 0 }, area3Cta: { type: 'string', default: '' }, area3Url: { type: 'string', default: '' },
        area4ImageUrl: { type: 'string', default: '' }, area4ImageId: { type: 'integer', default: 0 }, area4Cta: { type: 'string', default: '' }, area4Url: { type: 'string', default: '' },
        area5ImageUrl: { type: 'string', default: '' }, area5ImageId: { type: 'integer', default: 0 }, area5Cta: { type: 'string', default: '' }, area5Url: { type: 'string', default: '' },
        area6ImageUrl: { type: 'string', default: '' }, area6ImageId: { type: 'integer', default: 0 }, area6Cta: { type: 'string', default: '' }, area6Url: { type: 'string', default: '' },
        gridCols:  { type: 'string',  default: '3' },
        showIndex: { type: 'boolean', default: true },
        cardStyle: { type: 'string', default: 'classic' },
        bandStyle: { type: 'string', default: 'alt' },
        headingLevel: { type: 'string', default: 'h2' },
        sectionPad: { type: 'string', default: 'default' },
        primaryLabel: { type: 'string', default: '' },
        primaryUrl: { type: 'string', default: '' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/area-grid',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Grid header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.gridEyebrow, onChange: (v) => s({ gridEyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.gridTitle,   onChange: (v) => s({ gridTitle: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.gridText,    onChange: (v) => s({ gridText: v }) }),
                ),
                ...[1, 2, 3, 4, 5, 6].map((i) =>
                    el(PanelBody, { key: i, title: `Area ${i}: ${a[`area${i}Title`] || '(empty)'}`, initialOpen: i === 1 },
                        el(TextControl,     { label: __('Meta / subtitle', 'acreline'), value: a[`area${i}Meta`],  onChange: (v) => s({ [`area${i}Meta`]: v }) }),
                        el(TextControl,     { label: __('Name',            'acreline'), value: a[`area${i}Title`], onChange: (v) => s({ [`area${i}Title`]: v }) }),
                        el(TextareaControl, { label: __('Description',     'acreline'), value: a[`area${i}Body`],  onChange: (v) => s({ [`area${i}Body`]: v }) }),
                        el(MediaPicker, {
                            url: a[`area${i}ImageUrl`] || '',
                            id: a[`area${i}ImageId`] || 0,
                            onSelect: (url, id) => s({ [`area${i}ImageUrl`]: url, [`area${i}ImageId`]: id }),
                            onRemove: () => s({ [`area${i}ImageUrl`]: '', [`area${i}ImageId`]: 0 }),
                        }),
                        el(TextControl, { label: __('Card CTA label', 'acreline'), value: a[`area${i}Cta`] || '', onChange: (v) => s({ [`area${i}Cta`]: v }) }),
                        el(TextControl, { label: __('Card CTA URL', 'acreline'), value: a[`area${i}Url`] || '', onChange: (v) => s({ [`area${i}Url`]: v }), type: 'url' }),
                    ),
                ),
                el(PanelBody, { title: __('Grid layout', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Columns', 'acreline'),
                        value: a.gridCols || '3',
                        options: [
                            { value: '2', label: __('2 columns',           'acreline') },
                            { value: '3', label: __('3 columns (default)', 'acreline') },
                            { value: '4', label: __('4 columns',           'acreline') },
                        ],
                        onChange: (v) => s({ gridCols: v }),
                    }),
                    el(SelectControl, {
                        label: __('Card style', 'acreline'),
                        value: a.cardStyle || 'classic',
                        options: [
                            { value: 'classic',  label: __('Classic',              'acreline') },
                            { value: 'featured', label: __('Featured first (bento)', 'acreline') },
                            { value: 'compact',  label: __('Compact',               'acreline') },
                        ],
                        onChange: (v) => s({ cardStyle: v }),
                    }),
                    el(ToggleControl, {
                        label: __('Show card numbers (01, 02…)', 'acreline'),
                        checked: a.showIndex !== false,
                        onChange: (v) => s({ showIndex: v }),
                    }),
                    el(TextControl, { label: __('Section CTA label', 'acreline'), value: a.primaryLabel || '', onChange: (v) => s({ primaryLabel: v }) }),
                    el(TextControl, { label: __('Section CTA URL', 'acreline'), value: a.primaryUrl || '', onChange: (v) => s({ primaryUrl: v }), type: 'url' }),
                ),
                el(BandPanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 16. Tools Section
// ---------------------------------------------------------------------------
registerBlockType('acreline/tools-section', {
    title: __('Tools Section', 'acreline'),
    description: __('Land loan + pre-qual calculators with intro copy.', 'acreline'),
    category: 'acreline',
    icon: 'calculator',
    supports: { html: false, multiple: false },
    attributes: {
        // Intro paragraph
        showIntro:       { type: 'boolean', default: true },
        introTitle:      { type: 'string',  default: "What's different about buying land" },
        introText:       { type: 'string',  default: '' },
        // Tools header
        eyebrow:         { type: 'string',  default: 'Run Your Numbers' },
        title:           { type: 'string',  default: 'Land-loan & pre-qualification tools' },
        text:            { type: 'string',  default: '' },
        // Which tools to show
        showLoanTool:    { type: 'boolean', default: true },
        showPrequalTool: { type: 'boolean', default: true },
        // Loan estimator labels
        loanTitle:       { type: 'string',  default: 'Land loan estimator' },
        loanLede:        { type: 'string',  default: 'Sample monthly payment — not a loan offer.' },
        loanBtn:         { type: 'string',  default: 'Estimate payment' },
        // Pre-qual labels
        prequalTitle:    { type: 'string',  default: 'Pre-qualification check' },
        prequalLede:     { type: 'string',  default: 'Rough income check for land loans. Not a lender quote.' },
        prequalBtn:      { type: 'string',  default: 'Check eligibility' },
        // Design
        sectionStyle:    { type: 'string',  default: 'alt' },
        panelStyle:      { type: 'string',  default: 'card' },
        toolsLayout:     { type: 'string',  default: 'side' },
        // Typography
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/tools-section',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Intro paragraph', 'acreline'), initialOpen: true },
                    el(ToggleControl, {
                        label: __('Show intro paragraph', 'acreline'),
                        checked: a.showIntro !== false,
                        onChange: (v) => s({ showIntro: v }),
                    }),
                    a.showIntro !== false && el(Fragment, null,
                        el(TextControl,     { label: __('Intro title', 'acreline'), value: a.introTitle, onChange: (v) => s({ introTitle: v }) }),
                        el(TextareaControl, { label: __('Intro text',  'acreline'), value: a.introText,  onChange: (v) => s({ introText: v }) }),
                    ),
                ),
                el(PanelBody, { title: __('Tools header', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Loan estimator labels', 'acreline'), initialOpen: false },
                    el(ToggleControl, {
                        label: __('Show loan estimator', 'acreline'),
                        checked: a.showLoanTool !== false,
                        onChange: (v) => s({ showLoanTool: v }),
                    }),
                    a.showLoanTool !== false && el(Fragment, null,
                        el(TextControl,     { label: __('Panel title',   'acreline'), value: a.loanTitle, onChange: (v) => s({ loanTitle: v }) }),
                        el(TextControl,     { label: __('Description',   'acreline'), value: a.loanLede,  onChange: (v) => s({ loanLede: v }) }),
                        el(TextControl,     { label: __('Submit button', 'acreline'), value: a.loanBtn,   onChange: (v) => s({ loanBtn: v }) }),
                    ),
                ),
                el(PanelBody, { title: __('Pre-qualification labels', 'acreline'), initialOpen: false },
                    el(ToggleControl, {
                        label: __('Show pre-qualification check', 'acreline'),
                        checked: a.showPrequalTool !== false,
                        onChange: (v) => s({ showPrequalTool: v }),
                    }),
                    a.showPrequalTool !== false && el(Fragment, null,
                        el(TextControl,     { label: __('Panel title',   'acreline'), value: a.prequalTitle, onChange: (v) => s({ prequalTitle: v }) }),
                        el(TextControl,     { label: __('Description',   'acreline'), value: a.prequalLede,  onChange: (v) => s({ prequalLede: v }) }),
                        el(TextControl,     { label: __('Submit button', 'acreline'), value: a.prequalBtn,   onChange: (v) => s({ prequalBtn: v }) }),
                    ),
                ),
                el(PanelBody, { title: __('Design', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Section background', 'acreline'),
                        value: a.sectionStyle || 'alt',
                        options: [
                            { value: 'default', label: __('White',             'acreline') },
                            { value: 'alt',     label: __('Paper / alt',       'acreline') },
                            { value: 'dark',    label: __('Dark (ink)',         'acreline') },
                        ],
                        onChange: (v) => s({ sectionStyle: v }),
                    }),
                    el(SelectControl, {
                        label: __('Panel style', 'acreline'),
                        value: a.panelStyle || 'card',
                        options: [
                            { value: 'card',    label: __('Card (shadow)',      'acreline') },
                            { value: 'outline', label: __('Outline (bordered)', 'acreline') },
                            { value: 'flat',    label: __('Flat (minimal)',     'acreline') },
                        ],
                        onChange: (v) => s({ panelStyle: v }),
                    }),
                    el(SelectControl, {
                        label: __('Tools layout', 'acreline'),
                        value: a.toolsLayout || 'side',
                        options: [
                            { value: 'side',  label: __('Side by side (default)', 'acreline') },
                            { value: 'stack', label: __('Stacked (single column)','acreline') },
                        ],
                        onChange: (v) => s({ toolsLayout: v }),
                    }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 17. How We Work
// ---------------------------------------------------------------------------
registerBlockType('acreline/how-we-work', {
    title: __('How We Work', 'acreline'),
    description: __('Three-step office process section.', 'acreline'),
    category: 'acreline',
    icon: 'groups',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:     { type: 'string', default: 'How we work' },
        title:       { type: 'string', default: 'What working with this office looks like' },
        text:        { type: 'string', default: '' },
        stepsStyle:  { type: 'string', default: 'numbered' },
        stepsLayout: { type: 'string', default: 'row' },
        bandStyle:   { type: 'string', default: 'paper' },
        headingLevel:{ type: 'string', default: 'h2' },
        sectionPad:  { type: 'string', default: 'default' },
        step1Title:  { type: 'string', default: 'Listen first' },
        step1Text:   { type: 'string', default: '' },
        step2Title:  { type: 'string', default: 'Match the inventory' },
        step2Text:   { type: 'string', default: '' },
        step3Title:  { type: 'string', default: 'Walk it with you' },
        step3Text:   { type: 'string', default: '' },
        step4Title:  { type: 'string', default: '' },
        step4Text:   { type: 'string', default: '' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/how-we-work',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                ...[1, 2, 3, 4].map((n) =>
                    el(PanelBody, { key: n, title: `${__('Step', 'acreline')} ${n}${a[`step${n}Title`] ? `: ${a[`step${n}Title`]}` : ''}`, initialOpen: n === 1 },
                        el(TextControl,     { label: __('Title', 'acreline'), value: a[`step${n}Title`] || '', onChange: (v) => s({ [`step${n}Title`]: v }) }),
                        el(TextareaControl, { label: __('Text',  'acreline'), value: a[`step${n}Text`] || '',  onChange: (v) => s({ [`step${n}Text`]: v }) }),
                    ),
                ),
                el(PanelBody, { title: __('Step style', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Step marker', 'acreline'),
                        value: a.stepsStyle || 'numbered',
                        options: [
                            { value: 'numbered',   label: __('Numbered (01, 02…)',  'acreline') },
                            { value: 'checkmark',  label: __('Checkmark icons',     'acreline') },
                            { value: 'arrow',      label: __('Arrow icons',         'acreline') },
                            { value: 'dot',        label: __('Minimal dots',        'acreline') },
                        ],
                        onChange: (v) => s({ stepsStyle: v }),
                    }),
                    el(SelectControl, {
                        label: __('Layout', 'acreline'),
                        value: a.stepsLayout || 'row',
                        options: [
                            { value: 'row',    label: __('Horizontal row (default)', 'acreline') },
                            { value: 'column', label: __('Vertical stack',           'acreline') },
                        ],
                        onChange: (v) => s({ stepsLayout: v }),
                    }),
                ),
                el(BandPanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 18. Office Info
// ---------------------------------------------------------------------------
registerBlockType('acreline/office-info', {
    title: __('Office Info', 'acreline'),
    description: __('Address, phone, email, and hours from Customizer → Identity.', 'acreline'),
    category: 'acreline',
    icon: 'building',
    supports: { html: false, multiple: false },
    attributes: {
        officeTitle: { type: 'string',  default: '' },
        showMap:     { type: 'boolean', default: true },
        infoLayout:  { type: 'string',  default: 'vertical' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/office-info',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Office', 'acreline'), initialOpen: true },
                    el(TextControl, {
                        label: __('Heading override (blank = brand name)', 'acreline'),
                        value: a.officeTitle || '',
                        onChange: (v) => s({ officeTitle: v }),
                    }),
                    el('p', { style: { fontSize: '0.75rem', color: '#646970', margin: '8px 0 0' } },
                        __('Phone, address, email, and hours come from Appearance → Customize → Identity.', 'acreline'),
                    ),
                ),
                el(PanelBody, { title: __('Display options', 'acreline'), initialOpen: false },
                    el(ToggleControl, {
                        label: __('Show illustrative map', 'acreline'),
                        checked: a.showMap !== false,
                        onChange: (v) => s({ showMap: v }),
                    }),
                    el(SelectControl, {
                        label: __('Info layout', 'acreline'),
                        value: a.infoLayout || 'vertical',
                        options: [
                            { value: 'vertical',   label: __('Vertical (default)', 'acreline') },
                            { value: 'horizontal', label: __('Horizontal (two-col)', 'acreline') },
                        ],
                        onChange: (v) => s({ infoLayout: v }),
                    }),
                ),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 19. Contact Form
// ---------------------------------------------------------------------------
registerBlockType('acreline/contact-form', {
    title: __('Contact Form', 'acreline'),
    description: __('Office info + contact message form side by side.', 'acreline'),
    category: 'acreline',
    icon: 'email',
    supports: { html: false, multiple: false },
    attributes: {
        formTitle: { type: 'string', default: 'Send us a message' },
        formText:  { type: 'string', default: "Tell us what you're looking for and we'll be in touch." },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/contact-form',
            attributes: a,
            sidebarFn: () =>
                el(PanelBody, { title: __('Form copy', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Form title', 'acreline'), value: a.formTitle, onChange: (v) => s({ formTitle: v }) }),
                    el(TextareaControl, { label: __('Form intro', 'acreline'), value: a.formText,  onChange: (v) => s({ formText: v }) }),
                ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 20. Book Note + Form
// ---------------------------------------------------------------------------
registerBlockType('acreline/book-note', {
    title: __('Booking Note + Form', 'acreline'),
    description: __('Demo disclaimer note above the standalone booking form.', 'acreline'),
    category: 'acreline',
    icon: 'sticky',
    supports: { html: false, multiple: false },
    attributes: {
        note:          { type: 'string',  default: 'Demo only — no emails, texts or calendar invites are sent.' },
        noteStyle:     { type: 'string',  default: 'plain' },
        showSidePhoto: { type: 'boolean', default: false },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/book-note',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Disclaimer note', 'acreline'), initialOpen: true },
                    el(TextareaControl, { label: __('Note text', 'acreline'), value: a.note, onChange: (v) => s({ note: v }) }),
                    el(SelectControl, {
                        label: __('Note style', 'acreline'),
                        value: a.noteStyle || 'plain',
                        options: [
                            { value: 'plain',   label: __('Plain text',       'acreline') },
                            { value: 'info',    label: __('Info (green tint)', 'acreline') },
                            { value: 'warning', label: __('Warning (amber)',   'acreline') },
                        ],
                        onChange: (v) => s({ noteStyle: v }),
                    }),
                ),
                el(PanelBody, { title: __('Layout', 'acreline'), initialOpen: false },
                    el(ToggleControl, {
                        label: __('Show side photo next to form', 'acreline'),
                        checked: !!a.showSidePhoto,
                        onChange: (v) => s({ showSidePhoto: v }),
                    }),
                ),
            ),
        });
    },
    save: () => null,
});

function itemFieldPanels(a, s, count, prefix, heading) {
    return Array.from({ length: count }, (_, i) => {
        const n = i + 1;
        const titleVal = a[`${prefix}${n}Title`] || '';
        return el(PanelBody, {
            key: `${prefix}-${n}`,
            title: `${heading} ${n}${titleVal ? `: ${titleVal}` : ''}`,
            initialOpen: n === 1,
        },
            el(TextControl,     { label: __('Title', 'acreline'), value: titleVal, onChange: (v) => s({ [`${prefix}${n}Title`]: v }) }),
            el(TextareaControl, { label: __('Text',  'acreline'), value: a[`${prefix}${n}Text`] || '', onChange: (v) => s({ [`${prefix}${n}Text`]: v }) }),
        );
    });
}

function itemPairAttrs(count, prefix) {
    const attrs = {};
    for (let i = 1; i <= count; i += 1) {
        attrs[`${prefix}${i}Title`] = { type: 'string', default: '' };
        attrs[`${prefix}${i}Text`] = { type: 'string', default: '' };
    }
    return attrs;
}

// ---------------------------------------------------------------------------
// 22. Trust Strip
// ---------------------------------------------------------------------------
registerBlockType('acreline/trust-strip', {
    title: __('Trust Strip', 'acreline'),
    description: __('Four contact commitments — reply time, specialist match, no obligation, privacy.', 'acreline'),
    category: 'acreline',
    icon: 'shield',
    supports: { html: false, multiple: false },
    attributes: {
        ...itemPairAttrs(4, 'item'),
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/trust-strip',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                ...itemFieldPanels(a, s, 4, 'item', __('Promise', 'acreline')),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 23. Checklist
// ---------------------------------------------------------------------------
registerBlockType('acreline/checklist', {
    title: __('Buyer Checklist', 'acreline'),
    description: __('Numbered property checklist with optional CTAs.', 'acreline'),
    category: 'acreline',
    icon: 'editor-ol',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:         { type: 'string', default: 'Before you make an offer' },
        title:           { type: 'string', default: 'The property checklist' },
        text:            { type: 'string', default: '' },
        primaryLabel:    { type: 'string', default: 'Book a showing' },
        primaryUrl:      { type: 'string', default: '' },
        secondaryLabel:  { type: 'string', default: 'Browse listings' },
        secondaryUrl:    { type: 'string', default: '' },
        headingSize:     { type: 'string', default: 'default' },
        headingWeight:   { type: 'string', default: 'default' },
        bodySize:        { type: 'string', default: 'default' },
        headingAlign:    { type: 'string', default: 'left' },
        ...itemPairAttrs(8, 'item'),
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/checklist',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                ...itemFieldPanels(a, s, 8, 'item', __('Step', 'acreline')),
                el(PanelBody, { title: __('Buttons', 'acreline'), initialOpen: false },
                    el(TextControl, { label: __('Primary label',   'acreline'), value: a.primaryLabel,   onChange: (v) => s({ primaryLabel: v }) }),
                    el(TextControl, { label: __('Primary URL',     'acreline'), value: a.primaryUrl,     onChange: (v) => s({ primaryUrl: v }) }),
                    el(TextControl, { label: __('Secondary label', 'acreline'), value: a.secondaryLabel, onChange: (v) => s({ secondaryLabel: v }) }),
                    el(TextControl, { label: __('Secondary URL',   'acreline'), value: a.secondaryUrl,   onChange: (v) => s({ secondaryUrl: v }) }),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 24. Prep Checklist
// ---------------------------------------------------------------------------
registerBlockType('acreline/prep-checklist', {
    title: __('Showing Prep Checklist', 'acreline'),
    description: __('Two-column buyer / agent prep lists for the booking page.', 'acreline'),
    category: 'acreline',
    icon: 'yes-alt',
    supports: { html: false, multiple: false },
    attributes: {
        leftHeading:   { type: 'string', default: 'Come prepared' },
        leftLead:      { type: 'string', default: '' },
        rightHeading:  { type: 'string', default: 'What your agent brings' },
        rightLead:     { type: 'string', default: '' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
        ...itemPairAttrs(5, 'left'),
        ...itemPairAttrs(4, 'right'),
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/prep-checklist',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Buyer column', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Heading', 'acreline'), value: a.leftHeading, onChange: (v) => s({ leftHeading: v }) }),
                    el(TextareaControl, { label: __('Lead',    'acreline'), value: a.leftLead,    onChange: (v) => s({ leftLead: v }) }),
                ),
                ...itemFieldPanels(a, s, 5, 'left', __('Buyer item', 'acreline')),
                el(PanelBody, { title: __('Agent column', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Heading', 'acreline'), value: a.rightHeading, onChange: (v) => s({ rightHeading: v }) }),
                    el(TextareaControl, { label: __('Lead',    'acreline'), value: a.rightLead,    onChange: (v) => s({ rightLead: v }) }),
                ),
                ...itemFieldPanels(a, s, 4, 'right', __('Agent item', 'acreline')),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 25. Compare Table
// ---------------------------------------------------------------------------
registerBlockType('acreline/compare-table', {
    title: __('Area Compare Table', 'acreline'),
    description: __('Side-by-side sample-market comparison table.', 'acreline'),
    category: 'acreline',
    icon: 'editor-table',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:       { type: 'string', default: 'Side-by-side' },
        title:         { type: 'string', default: 'Area comparison at a glance' },
        text:          { type: 'string', default: '' },
        disclaimer:    { type: 'string', default: 'All figures are sample ranges for concept demonstration only. Not real MLS data or licensed appraisal values.' },
        col1:          { type: 'string', default: 'Area' },
        col2:          { type: 'string', default: 'Housing mix' },
        col3:          { type: 'string', default: 'Typical price range' },
        col4:          { type: 'string', default: 'Utilities' },
        col5:          { type: 'string', default: 'Best for' },
        row1Col1: { type: 'string', default: 'Oak Hollow' },  row1Col2: { type: 'string', default: 'Older homes, creek lots' },      row1Col3: { type: 'string', default: '$290K–$620K' }, row1Col4: { type: 'string', default: 'Public water + sewer' }, row1Col5: { type: 'string', default: 'First-time buyers' },
        row2Col1: { type: 'string', default: 'North Ridge' }, row2Col2: { type: 'string', default: 'Single-family, larger lots' },  row2Col3: { type: 'string', default: '$380K–$1.1M' }, row2Col4: { type: 'string', default: 'Public water + sewer' }, row2Col5: { type: 'string', default: 'Move-up buyers' },
        row3Col1: { type: 'string', default: 'Mill Creek' },  row3Col2: { type: 'string', default: 'Mixed homes, quiet streets' },  row3Col3: { type: 'string', default: '$195K–$480K' }, row3Col4: { type: 'string', default: 'Public water + sewer' }, row3Col5: { type: 'string', default: 'Value buyers' },
        row4Col1: { type: 'string', default: 'Riverbend' },   row4Col2: { type: 'string', default: 'Townhomes, walkable streets' }, row4Col3: { type: 'string', default: '$420K–$890K' }, row4Col4: { type: 'string', default: 'Public water + sewer' }, row4Col5: { type: 'string', default: 'Commuters' },
        row5Col1: { type: 'string', default: 'Midtown' },     row5Col2: { type: 'string', default: 'Condos, small lots' },          row5Col3: { type: 'string', default: '$110K–$390K' }, row5Col4: { type: 'string', default: 'Public water + sewer' }, row5Col5: { type: 'string', default: 'Investors, downsizers' },
        row6Col1: { type: 'string', default: 'Southgate' },   row6Col2: { type: 'string', default: 'Newer homes, cul-de-sacs' },    row6Col3: { type: 'string', default: '$265K–$540K' }, row6Col4: { type: 'string', default: 'Public water + sewer' }, row6Col5: { type: 'string', default: 'Growing families' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/compare-table',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow',    'acreline'), value: a.eyebrow,    onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',      'acreline'), value: a.title,      onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',       'acreline'), value: a.text,       onChange: (v) => s({ text: v }) }),
                    el(TextareaControl, { label: __('Disclaimer', 'acreline'), value: a.disclaimer, onChange: (v) => s({ disclaimer: v }) }),
                ),
                el(PanelBody, { title: __('Column labels', 'acreline'), initialOpen: false },
                    [1, 2, 3, 4, 5].map((n) =>
                        el(TextControl, { key: n, label: `${__('Column', 'acreline')} ${n}`, value: a[`col${n}`], onChange: (v) => s({ [`col${n}`]: v }) }),
                    ),
                ),
                ...[1, 2, 3, 4, 5, 6].map((n) =>
                    el(PanelBody, { key: `row-${n}`, title: `${__('Row', 'acreline')} ${n}: ${a[`row${n}Col1`] || ''}`, initialOpen: n === 1 },
                        [1, 2, 3, 4, 5].map((c) =>
                            el(TextControl, { key: c, label: a[`col${c}`] || `${__('Col', 'acreline')} ${c}`, value: a[`row${n}Col${c}`] || '', onChange: (v) => s({ [`row${n}Col${c}`]: v }) }),
                        ),
                    ),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 26. Topic Cards
// ---------------------------------------------------------------------------
registerBlockType('acreline/topic-cards', {
    title: __('Topic Cards', 'acreline'),
    description: __('Three scan cards for the blog index (showings, checklists, search).', 'acreline'),
    category: 'acreline',
    icon: 'screenoptions',
    supports: { html: false, multiple: false },
    attributes: {
        eyebrow:       { type: 'string', default: 'What these notes cover' },
        title:         { type: 'string', default: 'Short reads you can adapt for your market' },
        text:          { type: 'string', default: '' },
        card1Kicker:   { type: 'string', default: 'Showings' },
        card1Title:    { type: 'string', default: 'How a tour should feel' },
        card1Text:     { type: 'string', default: 'What to book, what to wear, and why a showing is not a 20-minute photo scroll.' },
        card2Kicker:   { type: 'string', default: 'Checklists' },
        card2Title:    { type: 'string', default: 'First-time buyers' },
        card2Text:     { type: 'string', default: 'Payment, inspection, and well/septic questions in an order you can scan before you call.' },
        card3Kicker:   { type: 'string', default: 'Search' },
        card3Title:    { type: 'string', default: 'House vs condo' },
        card3Text:     { type: 'string', default: 'Different card hierarchy so house shoppers and condo shoppers do not share one muddy filter.' },
        headingSize:   { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize:      { type: 'string', default: 'default' },
        headingAlign:  { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/topic-cards',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                ...[1, 2, 3].map((n) =>
                    el(PanelBody, { key: n, title: `${__('Card', 'acreline')} ${n}`, initialOpen: n === 1 },
                        el(TextControl,     { label: __('Kicker', 'acreline'), value: a[`card${n}Kicker`], onChange: (v) => s({ [`card${n}Kicker`]: v }) }),
                        el(TextControl,     { label: __('Title',  'acreline'), value: a[`card${n}Title`],  onChange: (v) => s({ [`card${n}Title`]: v }) }),
                        el(TextareaControl, { label: __('Text',   'acreline'), value: a[`card${n}Text`],   onChange: (v) => s({ [`card${n}Text`]: v }) }),
                    ),
                ),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 27. Post Grid
// ---------------------------------------------------------------------------
registerBlockType('acreline/post-grid', {
    title: __('Post Grid', 'acreline'),
    description: __('Blog post cards pulled from published posts, with pagination.', 'acreline'),
    category: 'acreline',
    icon: 'grid-view',
    supports: { html: false, multiple: false },
    attributes: {
        emptyText: { type: 'string', default: 'Sample posts load with Tools → Seed Acreline demo.' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/post-grid',
            attributes: a,
            sidebarFn: () =>
                el(PanelBody, { title: __('Empty state', 'acreline'), initialOpen: true },
                    el(TextareaControl, { label: __('Message when no posts exist', 'acreline'), value: a.emptyText, onChange: (v) => s({ emptyText: v }) }),
                ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 29. Region Coverage
// ---------------------------------------------------------------------------
registerBlockType('acreline/region-coverage', {
    title: __('Region Coverage', 'acreline'),
    description: __('How we cover the region — neighborhoods, methods, and a coverage map.', 'acreline'),
    category: 'acreline',
    icon: 'location-alt',
    supports: { html: false },
    attributes: {
        eyebrow: { type: 'string', default: 'Area service' },
        title: { type: 'string', default: 'How we cover the region' },
        text: { type: 'string', default: '' },
        layout: { type: 'string', default: 'split' },
        bandStyle: { type: 'string', default: 'alt' },
        headingLevel: { type: 'string', default: 'h2' },
        sectionPad: { type: 'string', default: 'default' },
        iconStyle: { type: 'string', default: 'pin' },
        areaCols: { type: 'string', default: '3' },
        showMap: { type: 'boolean', default: true },
        showMethods: { type: 'boolean', default: true },
        showIndex: { type: 'boolean', default: true },
        showSchema: { type: 'boolean', default: true },
        maxAreas: { type: 'integer', default: 3 },
        primaryLabel: { type: 'string', default: 'Browse neighborhoods' },
        primaryUrl: { type: 'string', default: '' },
        secondaryLabel: { type: 'string', default: 'Book a showing' },
        secondaryUrl: { type: 'string', default: '' },
        method1Title: { type: 'string', default: 'Listen first' },
        method1Text: { type: 'string', default: '' },
        method2Title: { type: 'string', default: 'Match inventory' },
        method2Text: { type: 'string', default: '' },
        method3Title: { type: 'string', default: 'Walk the property' },
        method3Text: { type: 'string', default: '' },
        area1Kicker: { type: 'string', default: '' }, area1Title: { type: 'string', default: 'North Ridge' }, area1Body: { type: 'string', default: '' }, area1Stat: { type: 'string', default: '12 active' }, area1Url: { type: 'string', default: '' },
        area2Kicker: { type: 'string', default: '' }, area2Title: { type: 'string', default: 'Mill Creek' }, area2Body: { type: 'string', default: '' }, area2Stat: { type: 'string', default: '9 active' }, area2Url: { type: 'string', default: '' },
        area3Kicker: { type: 'string', default: '' }, area3Title: { type: 'string', default: 'Oak Hollow' }, area3Body: { type: 'string', default: '' }, area3Stat: { type: 'string', default: '7 active' }, area3Url: { type: 'string', default: '' },
        area4Kicker: { type: 'string', default: '' }, area4Title: { type: 'string', default: 'Riverbend' }, area4Body: { type: 'string', default: '' }, area4Stat: { type: 'string', default: '6 active' }, area4Url: { type: 'string', default: '' },
        area5Kicker: { type: 'string', default: '' }, area5Title: { type: 'string', default: 'Midtown' }, area5Body: { type: 'string', default: '' }, area5Stat: { type: 'string', default: '5 active' }, area5Url: { type: 'string', default: '' },
        area6Kicker: { type: 'string', default: '' }, area6Title: { type: 'string', default: 'Southgate' }, area6Body: { type: 'string', default: '' }, area6Stat: { type: 'string', default: '8 active' }, area6Url: { type: 'string', default: '' },
        headingSize: { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize: { type: 'string', default: 'default' },
        headingAlign: { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/region-coverage',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl, { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl, { label: __('Title', 'acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text', 'acreline'), value: a.text, onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Layout', 'acreline'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Layout', 'acreline'),
                        value: a.layout || 'split',
                        options: [
                            { value: 'split', label: __('Split + map (default)', 'acreline') },
                            { value: 'bento', label: __('Bento (featured first)', 'acreline') },
                            { value: 'grid',  label: __('Equal grid', 'acreline') },
                        ],
                        onChange: (v) => s({ layout: v }),
                    }),
                    el(RangeControl, {
                        label: __('Neighborhoods to show', 'acreline'),
                        value: a.maxAreas || 3,
                        onChange: (v) => s({ maxAreas: v }),
                        min: 1, max: 6, step: 1,
                    }),
                    el(SelectControl, {
                        label: __('Grid columns (grid layout)', 'acreline'),
                        value: a.areaCols || '3',
                        options: [
                            { value: '2', label: '2' },
                            { value: '3', label: '3' },
                        ],
                        onChange: (v) => s({ areaCols: v }),
                    }),
                    el(SelectControl, {
                        label: __('Card icon', 'acreline'),
                        value: a.iconStyle || 'pin',
                        options: [
                            { value: 'pin',  label: __('Pin', 'acreline') },
                            { value: 'none', label: __('None', 'acreline') },
                        ],
                        onChange: (v) => s({ iconStyle: v }),
                    }),
                    el(ToggleControl, { label: __('Show coverage map (split)', 'acreline'), checked: a.showMap !== false, onChange: (v) => s({ showMap: v }) }),
                    el(ToggleControl, { label: __('Show method steps', 'acreline'), checked: a.showMethods !== false, onChange: (v) => s({ showMethods: v }) }),
                    el(ToggleControl, { label: __('Show card numbers', 'acreline'), checked: a.showIndex !== false, onChange: (v) => s({ showIndex: v }) }),
                    el(ToggleControl, { label: __('Output ItemList schema', 'acreline'), checked: a.showSchema !== false, onChange: (v) => s({ showSchema: v }) }),
                ),
                ...[1, 2, 3, 4, 5, 6].map((n) =>
                    el(PanelBody, { key: `area-${n}`, title: `${__('Neighborhood', 'acreline')} ${n}: ${a[`area${n}Title`] || ''}`, initialOpen: n === 1 },
                        el(TextControl, { label: __('Kicker', 'acreline'), value: a[`area${n}Kicker`] || '', onChange: (v) => s({ [`area${n}Kicker`]: v }) }),
                        el(TextControl, { label: __('Name', 'acreline'), value: a[`area${n}Title`] || '', onChange: (v) => s({ [`area${n}Title`]: v }) }),
                        el(TextareaControl, { label: __('Description', 'acreline'), value: a[`area${n}Body`] || '', onChange: (v) => s({ [`area${n}Body`]: v }) }),
                        el(TextControl, { label: __('Stat line', 'acreline'), value: a[`area${n}Stat`] || '', onChange: (v) => s({ [`area${n}Stat`]: v }) }),
                        el(TextControl, { label: __('Link URL', 'acreline'), value: a[`area${n}Url`] || '', onChange: (v) => s({ [`area${n}Url`]: v }), type: 'url' }),
                    ),
                ),
                el(PanelBody, { title: __('Methods', 'acreline'), initialOpen: false },
                    ...[1, 2, 3].flatMap((n) => [
                        el(TextControl, { key: `mt${n}`, label: `${__('Method', 'acreline')} ${n} title`, value: a[`method${n}Title`] || '', onChange: (v) => s({ [`method${n}Title`]: v }) }),
                        el(TextareaControl, { key: `mx${n}`, label: `${__('Method', 'acreline')} ${n} text`, value: a[`method${n}Text`] || '', onChange: (v) => s({ [`method${n}Text`]: v }) }),
                    ]),
                ),
                el(PanelBody, { title: __('Buttons', 'acreline'), initialOpen: false },
                    el(TextControl, { label: __('Primary label', 'acreline'), value: a.primaryLabel, onChange: (v) => s({ primaryLabel: v }) }),
                    el(TextControl, { label: __('Primary URL', 'acreline'), value: a.primaryUrl, onChange: (v) => s({ primaryUrl: v }), type: 'url' }),
                    el(TextControl, { label: __('Secondary label', 'acreline'), value: a.secondaryLabel, onChange: (v) => s({ secondaryLabel: v }) }),
                    el(TextControl, { label: __('Secondary URL', 'acreline'), value: a.secondaryUrl, onChange: (v) => s({ secondaryUrl: v }), type: 'url' }),
                ),
                el(BandPanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 30. Pricing Plans
// ---------------------------------------------------------------------------
registerBlockType('acreline/pricing-plans', {
    title: __('Pricing Plans', 'acreline'),
    description: __('Three editable service plans with a featured highlight.', 'acreline'),
    category: 'acreline',
    icon: 'money-alt',
    supports: { html: false },
    attributes: {
        eyebrow: { type: 'string', default: 'Ways to work together' },
        title: { type: 'string', default: 'Sample plans for buyers and sellers' },
        text: { type: 'string', default: '' },
        bandStyle: { type: 'string', default: 'paper' },
        headingLevel: { type: 'string', default: 'h2' },
        sectionPad: { type: 'string', default: 'default' },
        planCols: { type: 'string', default: '3' },
        plan1Kicker: { type: 'string', default: 'Buyers' }, plan1Title: { type: 'string', default: 'Buyer consult' }, plan1Price: { type: 'string', default: 'Complimentary' }, plan1Period: { type: 'string', default: '' }, plan1Text: { type: 'string', default: '' }, plan1Features: { type: 'string', default: '' }, plan1Cta: { type: 'string', default: 'Book a consult' }, plan1Url: { type: 'string', default: '' }, plan1Featured: { type: 'boolean', default: false },
        plan2Kicker: { type: 'string', default: 'Sellers' }, plan2Title: { type: 'string', default: 'Listing launch' }, plan2Price: { type: 'string', default: '2.5%' }, plan2Period: { type: 'string', default: 'sample rate' }, plan2Text: { type: 'string', default: '' }, plan2Features: { type: 'string', default: '' }, plan2Cta: { type: 'string', default: 'Talk listing prep' }, plan2Url: { type: 'string', default: '' }, plan2Featured: { type: 'boolean', default: true },
        plan3Kicker: { type: 'string', default: 'Relocation' }, plan3Title: { type: 'string', default: 'Relocation desk' }, plan3Price: { type: 'string', default: 'Custom' }, plan3Period: { type: 'string', default: '' }, plan3Text: { type: 'string', default: '' }, plan3Features: { type: 'string', default: '' }, plan3Cta: { type: 'string', default: 'Start a move plan' }, plan3Url: { type: 'string', default: '' }, plan3Featured: { type: 'boolean', default: false },
        headingSize: { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize: { type: 'string', default: 'default' },
        headingAlign: { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/pricing-plans',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl, { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl, { label: __('Title', 'acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text', 'acreline'), value: a.text, onChange: (v) => s({ text: v }) }),
                    el(SelectControl, {
                        label: __('Columns', 'acreline'),
                        value: a.planCols || '3',
                        options: [
                            { value: '2', label: __('2 columns', 'acreline') },
                            { value: '3', label: __('3 columns', 'acreline') },
                        ],
                        onChange: (v) => s({ planCols: v }),
                    }),
                ),
                ...[1, 2, 3].map((n) =>
                    el(PanelBody, { key: n, title: `${__('Plan', 'acreline')} ${n}: ${a[`plan${n}Title`] || ''}`, initialOpen: n === 1 },
                        el(TextControl, { label: __('Kicker', 'acreline'), value: a[`plan${n}Kicker`] || '', onChange: (v) => s({ [`plan${n}Kicker`]: v }) }),
                        el(TextControl, { label: __('Name', 'acreline'), value: a[`plan${n}Title`] || '', onChange: (v) => s({ [`plan${n}Title`]: v }) }),
                        el(TextControl, { label: __('Price', 'acreline'), value: a[`plan${n}Price`] || '', onChange: (v) => s({ [`plan${n}Price`]: v }) }),
                        el(TextControl, { label: __('Period / note', 'acreline'), value: a[`plan${n}Period`] || '', onChange: (v) => s({ [`plan${n}Period`]: v }) }),
                        el(TextareaControl, { label: __('Description', 'acreline'), value: a[`plan${n}Text`] || '', onChange: (v) => s({ [`plan${n}Text`]: v }) }),
                        el(TextareaControl, { label: __('Features (one per line)', 'acreline'), value: a[`plan${n}Features`] || '', onChange: (v) => s({ [`plan${n}Features`]: v }) }),
                        el(TextControl, { label: __('Button label', 'acreline'), value: a[`plan${n}Cta`] || '', onChange: (v) => s({ [`plan${n}Cta`]: v }) }),
                        el(TextControl, { label: __('Button URL', 'acreline'), value: a[`plan${n}Url`] || '', onChange: (v) => s({ [`plan${n}Url`]: v }), type: 'url' }),
                        el(ToggleControl, { label: __('Featured plan', 'acreline'), checked: !!a[`plan${n}Featured`], onChange: (v) => s({ [`plan${n}Featured`]: v }) }),
                    ),
                ),
                el(BandPanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 31. Logo Strip
// ---------------------------------------------------------------------------
registerBlockType('acreline/logo-strip', {
    title: __('Logo Strip', 'acreline'),
    description: __('Partner or “as seen in” logos — images or wordmarks.', 'acreline'),
    category: 'acreline',
    icon: 'images-alt2',
    supports: { html: false },
    attributes: {
        eyebrow: { type: 'string', default: 'Local partners' },
        title: { type: 'string', default: 'The sample county desk we call' },
        text: { type: 'string', default: '' },
        bandStyle: { type: 'string', default: 'paper' },
        headingLevel: { type: 'string', default: 'h2' },
        sectionPad: { type: 'string', default: 'compact' },
        grayscale: { type: 'boolean', default: true },
        logo1Label: { type: 'string', default: 'Sample Credit Union' }, logo1Url: { type: 'string', default: '' }, logo1ImageUrl: { type: 'string', default: '' }, logo1ImageId: { type: 'integer', default: 0 },
        logo2Label: { type: 'string', default: 'County Title Co.' }, logo2Url: { type: 'string', default: '' }, logo2ImageUrl: { type: 'string', default: '' }, logo2ImageId: { type: 'integer', default: 0 },
        logo3Label: { type: 'string', default: 'North Ridge Inspect' }, logo3Url: { type: 'string', default: '' }, logo3ImageUrl: { type: 'string', default: '' }, logo3ImageId: { type: 'integer', default: 0 },
        logo4Label: { type: 'string', default: 'Mill Creek Lending' }, logo4Url: { type: 'string', default: '' }, logo4ImageUrl: { type: 'string', default: '' }, logo4ImageId: { type: 'integer', default: 0 },
        logo5Label: { type: 'string', default: 'Oak Hollow Photo' }, logo5Url: { type: 'string', default: '' }, logo5ImageUrl: { type: 'string', default: '' }, logo5ImageId: { type: 'integer', default: 0 },
        logo6Label: { type: 'string', default: 'Borough Insurance' }, logo6Url: { type: 'string', default: '' }, logo6ImageUrl: { type: 'string', default: '' }, logo6ImageId: { type: 'integer', default: 0 },
        headingSize: { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize: { type: 'string', default: 'default' },
        headingAlign: { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/logo-strip',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Section header', 'acreline'), initialOpen: true },
                    el(TextControl, { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl, { label: __('Title', 'acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text', 'acreline'), value: a.text, onChange: (v) => s({ text: v }) }),
                    el(ToggleControl, { label: __('Grayscale partner images', 'acreline'), checked: a.grayscale !== false, onChange: (v) => s({ grayscale: v }) }),
                ),
                ...[1, 2, 3, 4, 5, 6].map((n) =>
                    el(PanelBody, { key: n, title: `${__('Logo', 'acreline')} ${n}: ${a[`logo${n}Label`] || ''}`, initialOpen: n === 1 },
                        el(TextControl, { label: __('Wordmark / alt', 'acreline'), value: a[`logo${n}Label`] || '', onChange: (v) => s({ [`logo${n}Label`]: v }) }),
                        el(TextControl, { label: __('Link URL', 'acreline'), value: a[`logo${n}Url`] || '', onChange: (v) => s({ [`logo${n}Url`]: v }), type: 'url' }),
                        el(MediaPicker, {
                            url: a[`logo${n}ImageUrl`] || '',
                            id: a[`logo${n}ImageId`] || 0,
                            onSelect: (url, id) => s({ [`logo${n}ImageUrl`]: url, [`logo${n}ImageId`]: id }),
                            onRemove: () => s({ [`logo${n}ImageUrl`]: '', [`logo${n}ImageId`]: 0 }),
                        }),
                    ),
                ),
                el(BandPanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 32. Newsletter
// ---------------------------------------------------------------------------
registerBlockType('acreline/newsletter', {
    title: __('Newsletter / Lead Capture', 'acreline'),
    description: __('Email capture band. Demo confirm stays on the page.', 'acreline'),
    category: 'acreline',
    icon: 'email-alt',
    supports: { html: false },
    attributes: {
        eyebrow: { type: 'string', default: 'New listings' },
        title: { type: 'string', default: 'Get the weekly sample market note' },
        text: { type: 'string', default: 'A short digest of new addresses in North Ridge, Mill Creek, and Oak Hollow.' },
        placeholder: { type: 'string', default: 'you@acreline-concept.test' },
        buttonLabel: { type: 'string', default: 'Join the list' },
        note: { type: 'string', default: 'Concept capture — confirmation stays on this page. Nothing is emailed.' },
        emailLabel: { type: 'string', default: 'Email' },
        formEyebrow: { type: 'string', default: 'Weekly digest' },
        formTitle: { type: 'string', default: '' },
        highlightsLabel: { type: 'string', default: 'Covered this week' },
        highlight1: { type: 'string', default: 'North Ridge' },
        highlight2: { type: 'string', default: 'Mill Creek' },
        highlight3: { type: 'string', default: 'Oak Hollow' },
        layout: { type: 'string', default: 'split' },
        showHighlights: { type: 'boolean', default: true },
        showDisclaimer: { type: 'boolean', default: true },
        bandStyle: { type: 'string', default: 'accent' },
        headingLevel: { type: 'string', default: 'h2' },
        sectionPad: { type: 'string', default: 'default' },
        headingSize: { type: 'string', default: 'default' },
        headingWeight: { type: 'string', default: 'default' },
        bodySize: { type: 'string', default: 'default' },
        headingAlign: { type: 'string', default: 'left' },
    },
    edit({ attributes: a, setAttributes: s }) {
        return el(SsrEdit, {
            blockName: 'acreline/newsletter',
            attributes: a,
            sidebarFn: () => el(Fragment, null,
                el(PanelBody, { title: __('Copy', 'acreline'), initialOpen: true },
                    el(TextControl, { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl, { label: __('Title', 'acreline'), value: a.title, onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text', 'acreline'), value: a.text, onChange: (v) => s({ text: v }) }),
                    el(TextControl, { label: __('Form kicker', 'acreline'), value: a.formEyebrow, onChange: (v) => s({ formEyebrow: v }), help: __('Small label above the email field.', 'acreline') }),
                    el(TextControl, { label: __('Form title', 'acreline'), value: a.formTitle, onChange: (v) => s({ formTitle: v }) }),
                    el(TextControl, { label: __('Email label', 'acreline'), value: a.emailLabel, onChange: (v) => s({ emailLabel: v }) }),
                    el(TextControl, { label: __('Email placeholder', 'acreline'), value: a.placeholder, onChange: (v) => s({ placeholder: v }) }),
                    el(TextControl, { label: __('Button label', 'acreline'), value: a.buttonLabel, onChange: (v) => s({ buttonLabel: v }) }),
                    el(TextareaControl, { label: __('Fine print', 'acreline'), value: a.note, onChange: (v) => s({ note: v }), help: __('Concept / privacy note under the form. Hide it from Layout if you only want the live confirmation.', 'acreline') }),
                ),
                el(PanelBody, { title: __('Layout', 'acreline'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Layout', 'acreline'),
                        value: a.layout || 'split',
                        options: [
                            { value: 'split',   label: __('Split + form card (default)', 'acreline') },
                            { value: 'center',  label: __('Centered magazine',           'acreline') },
                            { value: 'compact', label: __('Compact bar',                 'acreline') },
                        ],
                        onChange: (v) => s({ layout: v }),
                    }),
                    el(ToggleControl, { label: __('Show neighborhood chips', 'acreline'), checked: a.showHighlights !== false, onChange: (v) => s({ showHighlights: v }) }),
                    el(ToggleControl, { label: __('Show fine print', 'acreline'), checked: a.showDisclaimer !== false, onChange: (v) => s({ showDisclaimer: v }) }),
                    el(TextControl, { label: __('Chips label', 'acreline'), value: a.highlightsLabel, onChange: (v) => s({ highlightsLabel: v }) }),
                    el(TextControl, { label: __('Chip 1', 'acreline'), value: a.highlight1, onChange: (v) => s({ highlight1: v }) }),
                    el(TextControl, { label: __('Chip 2', 'acreline'), value: a.highlight2, onChange: (v) => s({ highlight2: v }) }),
                    el(TextControl, { label: __('Chip 3', 'acreline'), value: a.highlight3, onChange: (v) => s({ highlight3: v }) }),
                ),
                el(BandPanel, { attrs: a, s }),
                el(TypographyPanel, { attrs: a, s }),
            ),
        });
    },
    save: () => null,
});

// ---------------------------------------------------------------------------
// 28. Custom Block (acreline/custom — from Block Generator)
// ---------------------------------------------------------------------------
registerBlockType('acreline/custom', {
    title: __('Custom Block', 'acreline'),
    description: __('A block created via Tools → Block Generator.', 'acreline'),
    category: 'acreline',
    icon: 'lightbulb',
    supports: { html: false },
    attributes: {
        blockId: { type: 'string', default: '' },
        fields:  { type: 'object', default: {} },
    },
    edit({ attributes, setAttributes }) {
        const { blockId, fields } = attributes;
        const customBlocks = (window.ACRELINE_BLOCKS || {}).customBlocks || {};
        const def = customBlocks[blockId];

        if (!blockId || !def) {
            const options = Object.entries(customBlocks);
            const blockProps = useBlockProps();
            return el(
                'div',
                blockProps,
                el('div', { style: { padding: '20px', textAlign: 'center', background: '#f9f9f9', borderRadius: '4px' } },
                    el('span', { style: { fontSize: '1.5rem' } }, '🧩'),
                    el('p', { style: { fontWeight: 600, margin: '8px 0 4px' } }, __('Custom Block', 'acreline')),
                    options.length === 0
                        ? el('p', { style: { fontSize: '0.8rem', color: '#646970' } },
                            __('No custom blocks yet. Create one in Tools → Block Generator.', 'acreline'),
                        )
                        : el('select', {
                            value: blockId,
                            onChange: (e) => setAttributes({ blockId: e.target.value, fields: {} }),
                            style: { marginTop: 8, display: 'block', width: '100%' },
                        },
                            el('option', { value: '' }, __('— choose a block —', 'acreline')),
                            ...options.map(([id, d]) => el('option', { key: id, value: id }, d.title || id)),
                        ),
                ),
            );
        }

        const setField = (k) => (v) => setAttributes({ fields: { ...fields, [k]: v } });

        return el(SsrEdit, {
            blockName: 'acreline/custom',
            attributes,
            sidebarFn: () =>
                el(PanelBody, { title: def.title || blockId, initialOpen: true },
                    ...(def.fields || []).map((f) => {
                        const val = fields[f.name] || '';
                        if (f.type === 'textarea') {
                            return el(TextareaControl, { key: f.name, label: f.label || f.name, value: val, onChange: setField(f.name) });
                        }
                        if (f.type === 'image') {
                            return el(Fragment, { key: f.name },
                                el('p', { style: { fontSize: '0.8rem', fontWeight: 600, marginBottom: 4 } }, f.label || f.name),
                                el(MediaPicker, {
                                    url: val,
                                    id: 0,
                                    onSelect: (url) => setField(f.name)(url),
                                    onRemove: () => setField(f.name)(''),
                                }),
                            );
                        }
                        return el(TextControl, { key: f.name, label: f.label || f.name, value: val, type: f.type === 'url' ? 'url' : 'text', onChange: setField(f.name) });
                    }),
                ),
        });
    },
    save: () => null,
});
