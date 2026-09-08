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
        eyebrow:        { type: 'string',  default: 'Farms, land, and historic homes' },
        title:          { type: 'string',  default: 'Homes worth <em>walking through.</em>' },
        text:           { type: 'string',  default: '' },
        imageUrl:       { type: 'string',  default: '' },
        imageId:        { type: 'integer', default: 0 },
        imagePosition:  { type: 'string',  default: 'center' },
        primaryLabel:   { type: 'string',  default: 'Show matches' },
        secondaryLabel: { type: 'string',  default: 'Browse all listings' },
        heroHeight:     { type: 'string',  default: 'default' },
        overlayPreset:  { type: 'string',  default: 'default' },
        overlayOpacity: { type: 'integer', default: 60 },
        textAlign:      { type: 'string',  default: 'left' },
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
                    el(TextControl, { label: __('Secondary label', 'acreline'), value: a.secondaryLabel, onChange: (v) => s({ secondaryLabel: v }) }),
                    el(TextControl, { label: __('Secondary URL',   'acreline'), value: a.secondaryUrl,   onChange: (v) => s({ secondaryUrl: v }),   type: 'url' }),
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
        text:       { type: 'string', default: 'Then we match a listing or a tour. Township first — the rest follows.' },
        buyKicker:  { type: 'string', default: 'Buy' },
        buyTitle:   { type: 'string', default: 'Scan homes, farms, and land' },
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
        note1Title: { type: 'string', default: 'Township first' },
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
    description: __('Buying guide copy and scan cards for rural real estate SEO.', 'acreline'),
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
        text:        { type: 'string',  default: 'Practical answers for house and acreage shoppers.' },
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
                            { value: 'accent', label: __('Accent (forest)', 'acreline') },
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
        introTitle:  { type: 'string', default: 'Buying rural property' },
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
// 15. Area Grid
// ---------------------------------------------------------------------------
registerBlockType('acreline/area-grid', {
    title: __('Area Grid', 'acreline'),
    description: __('Up to six area cards with local market copy.', 'acreline'),
    category: 'acreline',
    icon: 'location',
    supports: { html: false, multiple: false },
    attributes: {
        gridEyebrow: { type: 'string', default: 'Area by area' },
        gridTitle:   { type: 'string', default: 'Where the sample office works' },
        gridText:    { type: 'string', default: '' },
        area1Meta: { type: 'string', default: 'West ridge · orchards and stone houses' }, area1Title: { type: 'string', default: 'Oak Hollow' },    area1Body: { type: 'string', default: '' },
        area2Meta: { type: 'string', default: '' }, area2Title: { type: 'string', default: 'Orchard Belt' },  area2Body: { type: 'string', default: '' },
        area3Meta: { type: 'string', default: '' }, area3Title: { type: 'string', default: 'Mill Creek' },    area3Body: { type: 'string', default: '' },
        area4Meta: { type: 'string', default: '' }, area4Title: { type: 'string', default: 'Grain Country' }, area4Body: { type: 'string', default: '' },
        area5Meta: { type: 'string', default: '' }, area5Title: { type: 'string', default: 'Hill Country' },  area5Body: { type: 'string', default: '' },
        area6Meta: { type: 'string', default: '' }, area6Title: { type: 'string', default: 'Border Farms' },  area6Body: { type: 'string', default: '' },
        gridCols:  { type: 'string',  default: '3' },
        showIndex: { type: 'boolean', default: true },
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
                    el(ToggleControl, {
                        label: __('Show card numbers (01, 02…)', 'acreline'),
                        checked: a.showIndex !== false,
                        onChange: (v) => s({ showIndex: v }),
                    }),
                ),
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
        introTitle:      { type: 'string',  default: "What's different about buying land" },
        introText:       { type: 'string',  default: '' },
        eyebrow:         { type: 'string',  default: 'Run Your Numbers' },
        title:           { type: 'string',  default: 'Land-loan & pre-qualification tools' },
        text:            { type: 'string',  default: '' },
        showLoanTool:    { type: 'boolean', default: true },
        showPrequalTool: { type: 'boolean', default: true },
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
                el(PanelBody, { title: __('Intro copy', 'acreline'), initialOpen: true },
                    el(TextControl,     { label: __('Intro title', 'acreline'), value: a.introTitle, onChange: (v) => s({ introTitle: v }) }),
                    el(TextareaControl, { label: __('Intro text',  'acreline'), value: a.introText,  onChange: (v) => s({ introText: v }) }),
                ),
                el(PanelBody, { title: __('Tools header', 'acreline'), initialOpen: false },
                    el(TextControl,     { label: __('Eyebrow', 'acreline'), value: a.eyebrow, onChange: (v) => s({ eyebrow: v }) }),
                    el(TextControl,     { label: __('Title',   'acreline'), value: a.title,   onChange: (v) => s({ title: v }) }),
                    el(TextareaControl, { label: __('Text',    'acreline'), value: a.text,    onChange: (v) => s({ text: v }) }),
                ),
                el(PanelBody, { title: __('Tools to show', 'acreline'), initialOpen: false },
                    el(ToggleControl, { label: __('Show land loan estimator',    'acreline'), checked: a.showLoanTool    !== false, onChange: (v) => s({ showLoanTool: v }) }),
                    el(ToggleControl, { label: __('Show pre-qualification check','acreline'), checked: a.showPrequalTool !== false, onChange: (v) => s({ showPrequalTool: v }) }),
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
        eyebrow:     { type: 'string', default: 'How We Work' },
        title:       { type: 'string', default: 'What working with this office looks like' },
        text:        { type: 'string', default: '' },
        stepsStyle:  { type: 'string', default: 'numbered' },
        stepsLayout: { type: 'string', default: 'row' },
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

// ---------------------------------------------------------------------------
// 21. Custom Block (acreline/custom — from Block Generator)
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
