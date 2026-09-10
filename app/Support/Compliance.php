<?php

namespace App\Support;

/**
 * Configurable website-compliance tools (brokerage ID, Fair Housing, IDX slots, form consent).
 *
 * Not legal advice. Fields stay empty until the buyer fills them. Rules vary by state and MLS.
 */
class Compliance
{
    /**
     * Theme_mod keys and defaults. Booleans are stored as theme_mod bools.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'ks_brokerage_legal_name' => '',
            'ks_license_number' => '',
            'ks_broker_license' => '',
            'ks_license_jurisdiction' => '',
            'ks_office_city' => '',
            'ks_office_state' => '',
            'ks_show_license_footer' => true,
            'ks_show_license_header' => false,
            'ks_eho_enable' => true,
            'ks_eho_show_logo' => true,
            'ks_eho_statement' => '',
            'ks_idx_enable' => false,
            'ks_idx_disclaimer' => '',
            'ks_idx_copyright' => '',
            'ks_idx_logo_url' => '',
            'ks_idx_attribution' => false,
            'ks_privacy_url' => '',
            'ks_terms_url' => '',
            'ks_consent_enable' => true,
            'ks_consent_disclosure' => '',
            'ks_consent_sms' => '',
        ];
    }

    /**
     * @return list<string>
     */
    public static function boolKeys(): array
    {
        return [
            'ks_show_license_footer',
            'ks_show_license_header',
            'ks_eho_enable',
            'ks_eho_show_logo',
            'ks_idx_enable',
            'ks_idx_attribution',
            'ks_consent_enable',
        ];
    }

    /**
     * @return list<string>
     */
    public static function urlKeys(): array
    {
        return ['ks_idx_logo_url', 'ks_privacy_url', 'ks_terms_url'];
    }

    /**
     * @return list<string>
     */
    public static function textareaKeys(): array
    {
        return ['ks_eho_statement', 'ks_idx_disclaimer', 'ks_consent_disclosure', 'ks_consent_sms'];
    }

    public static function text(string $key): string
    {
        $defaults = self::defaults();
        $default = is_string($defaults[$key] ?? '') ? $defaults[$key] : '';
        $value = trim((string) get_theme_mod($key, $default));

        return $value;
    }

    public static function flag(string $key): bool
    {
        $defaults = self::defaults();
        $default = (bool) ($defaults[$key] ?? false);

        return \App\ks_hero_value_on(get_theme_mod($key, $default));
    }

    public static function brokerageLegalName(): string
    {
        return self::text('ks_brokerage_legal_name');
    }

    /**
     * Licensed name for ads: legal name when set, otherwise the public brand.
     */
    public static function licensedName(): string
    {
        $legal = self::brokerageLegalName();

        return $legal !== '' ? $legal : Identity::brandName();
    }

    public static function defaultEhoStatement(): string
    {
        return __('We are committed to the letter and spirit of U.S. policy for the achievement of equal housing opportunity. We encourage and support an affirmative advertising and marketing program in which there are no barriers to obtaining housing because of race, color, religion, sex, handicap, familial status, or national origin.', 'acreline');
    }

    public static function ehoStatement(): string
    {
        $custom = self::text('ks_eho_statement');

        return $custom !== '' ? $custom : self::defaultEhoStatement();
    }

    public static function defaultConsentDisclosure(): string
    {
        return __('By checking this box, I agree that {brokerage} may contact me by phone, email, or text about this inquiry, including with an autodialer or prerecorded message. Consent is not required to purchase any property or service.', 'acreline');
    }

    public static function consentDisclosure(): string
    {
        $raw = self::text('ks_consent_disclosure');
        if ($raw === '') {
            $raw = self::defaultConsentDisclosure();
        }

        return str_replace('{brokerage}', self::licensedName(), $raw);
    }

    public static function consentSms(): string
    {
        return self::text('ks_consent_sms');
    }

    /**
     * Non-empty brokerage ID parts for the footer / header strip.
     *
     * @return list<string>
     */
    public static function idParts(): array
    {
        $parts = [];
        $legal = self::brokerageLegalName();
        if ($legal !== '') {
            $parts[] = $legal;
        }

        $license = self::text('ks_license_number');
        if ($license !== '') {
            $parts[] = sprintf(
                /* translators: %s: license number */
                __('Lic. %s', 'acreline'),
                $license
            );
        }

        $broker = self::text('ks_broker_license');
        if ($broker !== '') {
            $parts[] = sprintf(
                /* translators: %s: broker license number */
                __('Broker lic. %s', 'acreline'),
                $broker
            );
        }

        $jurisdiction = self::text('ks_license_jurisdiction');
        if ($jurisdiction !== '') {
            $parts[] = $jurisdiction;
        }

        $city = self::text('ks_office_city');
        $state = self::text('ks_office_state');
        $place = trim($city.($city !== '' && $state !== '' ? ', ' : '').$state);
        if ($place !== '') {
            $parts[] = $place;
        }

        return $parts;
    }

    public static function hasBrokerId(): bool
    {
        return self::idParts() !== [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        $demo = Identity::showDemoChrome();

        return [
            'brokerageLegalName' => self::brokerageLegalName(),
            'licensedName' => self::licensedName(),
            'licenseNumber' => self::text('ks_license_number'),
            'brokerLicense' => self::text('ks_broker_license'),
            'jurisdiction' => self::text('ks_license_jurisdiction'),
            'officeCity' => self::text('ks_office_city'),
            'officeState' => self::text('ks_office_state'),
            'idParts' => self::idParts(),
            'hasBrokerId' => self::hasBrokerId(),
            'showLicenseFooter' => self::flag('ks_show_license_footer'),
            'showLicenseHeader' => self::flag('ks_show_license_header'),
            'ehoEnabled' => self::flag('ks_eho_enable'),
            'ehoShowLogo' => self::flag('ks_eho_show_logo'),
            'ehoStatement' => self::ehoStatement(),
            'ehoLabel' => $demo
                ? __('Equal Housing Opportunity (concept)', 'acreline')
                : __('Equal Housing Opportunity', 'acreline'),
            'idxEnabled' => self::flag('ks_idx_enable'),
            'idxDisclaimer' => self::text('ks_idx_disclaimer'),
            'idxCopyright' => self::text('ks_idx_copyright'),
            'idxLogoUrl' => self::text('ks_idx_logo_url'),
            'idxAttribution' => self::flag('ks_idx_attribution'),
            'privacyUrl' => self::text('ks_privacy_url'),
            'termsUrl' => self::text('ks_terms_url'),
            'consentEnabled' => self::flag('ks_consent_enable'),
            'consentDisclosure' => self::consentDisclosure(),
            'consentSms' => self::consentSms(),
            'showDemoChrome' => $demo,
        ];
    }

    /**
     * Public listing JS payload (IDX slots + consent flag).
     *
     * @return array<string, mixed>
     */
    public static function forJs(): array
    {
        return [
            'idxEnabled' => self::flag('ks_idx_enable'),
            'idxDisclaimer' => self::text('ks_idx_disclaimer'),
            'idxCopyright' => self::text('ks_idx_copyright'),
            'idxLogoUrl' => self::text('ks_idx_logo_url'),
            'idxAttribution' => self::flag('ks_idx_attribution'),
            'brokerageLegalName' => self::brokerageLegalName(),
            'consentEnabled' => self::flag('ks_consent_enable'),
        ];
    }

    /**
     * Whether listing cards / singles should render the IDX slot.
     */
    public static function showIdxSlot(?array $listing = null): bool
    {
        if (! self::flag('ks_idx_enable')) {
            return false;
        }

        if (self::text('ks_idx_disclaimer') !== ''
            || self::text('ks_idx_copyright') !== ''
            || self::text('ks_idx_logo_url') !== '') {
            return true;
        }

        if (self::flag('ks_idx_attribution')) {
            $office = is_array($listing) ? trim((string) ($listing['listing_office'] ?? '')) : '';
            if ($office !== '' || self::brokerageLegalName() !== '') {
                return true;
            }
        }

        return is_array($listing) && trim((string) ($listing['updated'] ?? '')) !== '';
    }

    /**
     * Save theme_mods from the Acreline Settings Compliance tab or the setup wizard.
     *
     * @param  array<string, mixed>  $raw
     * @param  list<string>|null  $only  Limit to these keys (wizard). Null = all defaults.
     */
    public static function saveFromPost(array $raw, ?array $only = null): void
    {
        $defaults = self::defaults();
        $keys = $only ?? array_keys($defaults);
        $boolKeys = self::boolKeys();
        $urlKeys = self::urlKeys();
        $areaKeys = self::textareaKeys();

        foreach ($keys as $key) {
            if (! array_key_exists($key, $defaults)) {
                continue;
            }
            if (in_array($key, $boolKeys, true)) {
                set_theme_mod($key, ! empty($raw[$key]));

                continue;
            }

            $posted = $raw[$key] ?? '';
            if (! is_scalar($posted)) {
                $posted = '';
            }
            $posted = wp_unslash((string) $posted);

            if (in_array($key, $urlKeys, true)) {
                set_theme_mod($key, esc_url_raw($posted));
            } elseif (in_array($key, $areaKeys, true)) {
                set_theme_mod($key, sanitize_textarea_field($posted));
            } else {
                set_theme_mod($key, sanitize_text_field($posted));
            }
        }
    }
}
