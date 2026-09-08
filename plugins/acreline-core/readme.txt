=== Acreline Core ===
Contributors: matthummel
Requires at least: 6.6
Tested up to: 7.0.1
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Listings, agents, and showing bookings for the Acreline theme.

== Description ==

Acreline Core registers the `listing`, `agent`, and `booking` post types and the public `keystone/v1/bookings` REST route.

Switching themes leaves those posts and `ks_*` meta in the database. Acreline displays them; this plugin owns them.

This is sample / concept inventory — not a live MLS.

== Installation ==

1. Upload the `acreline-core` folder to `wp-content/plugins/`.
2. Activate Acreline Core.
3. Activate the Acreline theme.
4. Use Tools → Seed Acreline demo, or add listings from wp-admin.

== Frequently Asked Questions ==

= Do I need this plugin? =

The Acreline theme still registers listings, agents, and bookings if the plugin is off (concept-site fallback). Activate Acreline Core on a store install so those posts stay in the database after a theme switch.

= Is this a live MLS? =

No. Sample inventory is concept data. The plugin does not sync RETS, IDX, or any listing feed.

= Where do showing requests go? =

POST `keystone/v1/bookings` creates a Booking post in Requested status. Nothing is emailed.

= What is the booking query string? =

Use `?listing_id=123` (listing post ID). Do not use `?listing=123` as a public query.

== Changelog ==

= 1.0.0 =
* First public companion plugin for marketplace packs.
