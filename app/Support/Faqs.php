<?php

namespace App\Support;

/**
 * Buyer-facing FAQ copy + FAQPage JSON-LD. Fiction-only answers stay honest.
 */
class Faqs
{
    /**
     * @return list<array{q: string, a: string}>
     */
    public static function forContext(): array
    {
        if (function_exists('is_404') && is_404()) {
            return [];
        }
        if (function_exists('is_singular')) {
            if (is_singular('listing')) {
                return self::listing();
            }
            if (is_singular('agent')) {
                return self::agent();
            }
        }

        return match (PageCopy::schemaKeyForContext()) {
            'home' => self::home(),
            'book' => self::book(),
            'listings' => self::listings(),
            'areas' => self::areas(),
            'guide' => self::guide(),
            'agents' => self::agents(),
            'contact' => self::contact(),
            default => [],
        };
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function home(): array
    {
        return [
            [
                'q' => 'What should I compare first on a listing?',
                'a' => 'Start with neighborhood, price, and property type, then bedrooms, commute, and HOA or systems. Condos add dues and parking; commercial adds permitted use. Inventory on this site is sample data; the review order is what a working buyer uses.',
            ],
            [
                'q' => 'How is buying a condo different from buying a house here?',
                'a' => 'A house usually means yard, systems, and a single owner. A condo adds dues, reserves, and building rules. Neighborhoods change from North Ridge to Oak Hollow. Review the buyer guide before you write.',
            ],
            [
                'q' => 'Do I need a showing to walk a house or condo?',
                'a' => 'Yes for occupied homes and most buildings — locks, parking, and neighbors matter. Schedule a sample showing to see the flow: choose a listing, a date, and a time. Mention pets or timing in the notes.',
            ],
            [
                'q' => 'Is this a live brokerage?',
                'a' => 'No. This is a concept demo of the Acreline theme. Listings, phone numbers, and market figures are fictional. Use it to evaluate a modern realtor website, then replace the sample data with your own inventory.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function book(): array
    {
        return [
            [
                'q' => 'What should I bring to a showing?',
                'a' => 'A notebook and questions about systems, HOA rules, and the block. If you are shopping a condo, ask about dues and parking. This demo saves the request only — it does not email or text.',
            ],
            [
                'q' => 'How long is a typical walk-through?',
                'a' => 'Plan 45–60 minutes for a house. A condo can be shorter; a commercial suite with build-out questions takes longer. Evening slots on this form match how working buyers actually tour after commute hours.',
            ],
            [
                'q' => 'Can I tour two listings on the same request?',
                'a' => 'Pick one listing per request so the agent preps the right file. Want a house and a condo? Send two showing requests or note it in the comments after you choose the first address.',
            ],
            [
                'q' => 'Does this form schedule a real appointment?',
                'a' => 'No. Submitting creates a Booking post in Requested status for the concept site. Nothing is emailed, texted, or added to a calendar.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function listings(): array
    {
        return [
            [
                'q' => 'Why filter by neighborhood before city?',
                'a' => 'Schools, commute, HOA rules, and inventory mix sit at the neighborhood. Two listings a mile apart can have different dues, parking, and buyer profiles. Pick an area first, then price and type.',
            ],
            [
                'q' => 'What do the property types mean?',
                'a' => 'Home is a turnkey house. Condo is a unit with shared building rules. Commercial is a storefront or suite. Sample cards may also include other labels you can rename. All eight cards are fictional samples.',
            ],
            [
                'q' => 'How do I get from a card to a showing?',
                'a' => 'Open a listing for beds, price, and the write-up, then Book a showing — that address is preselected. You can also start from the homepage or /book/ and pick the listing there.',
            ],
            [
                'q' => 'Is this a live MLS feed?',
                'a' => 'No. Prices, addresses, and neighborhood labels are concept data for this demo. Use the filters to see how a working search should feel, then replace the sample inventory with a real feed.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function areas(): array
    {
        return [
            [
                'q' => 'Why does neighborhood matter more than the nearest city name?',
                'a' => 'Schools, HOA rules, and commute sit at the neighborhood. Two listings a mile apart — one in North Ridge, one in Oak Hollow — can have different dues, parking, and buyer profiles. Read the neighborhood card first, then the borough for groceries and commute.',
            ],
            [
                'q' => 'What should I compare if I want a house vs a condo?',
                'a' => 'Houses ask about systems, yard, and resale on the block. Condos ask about dues, reserves, parking, and rental rules. Midtown adds commercial storefronts. Same sample county — different product.',
            ],
            [
                'q' => 'Can I commute from these neighborhoods?',
                'a' => 'Yes. Tell us the drive you will actually make on a Tuesday, and we will point you at the neighborhoods that fit — this demo uses sample inventory only.',
            ],
            [
                'q' => 'Are these real listings tied to each neighborhood?',
                'a' => 'No. The profiles are written so the area page is useful to scan. Inventory on Listings is fictional. Use the area filter there, then book a sample showing if you want to walk the flow.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function guide(): array
    {
        return [
            [
                'q' => 'What should I check before I write an offer?',
                'a' => 'Inspection path, HOA or condo docs, and a payment you can live with. An agent can tell you what a specific neighborhood typically requires before you write.',
            ],
            [
                'q' => 'What is a home inspection, and who pays for it?',
                'a' => 'An inspection checks systems, structure, and safety items. On a house it is usually a buyer contingency, and the buyer typically pays. Do not skip it.',
            ],
            [
                'q' => 'Can I get a normal mortgage on a condo?',
                'a' => 'Often yes, if the building is warrantable. Some associations or commercial suites need a different loan. The estimators on this page are planning math only — a lender gives real terms.',
            ],
            [
                'q' => 'What should I ask about an HOA?',
                'a' => 'Dues, reserves, rental caps, and what the association actually maintains. We flag HOA notes on any sample listing that would carry them.',
            ],
            [
                'q' => 'Do I need to live in the neighborhood to buy here?',
                'a' => 'No. Tell us the commute you will actually make. This demo uses sample inventory only.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function agents(): array
    {
        return [
            [
                'q' => 'How do I pick which sample agent to call?',
                'a' => 'Match the desk: buyers, sellers, or commercial. Read specialties on the card, then book a showing or message the office. This roster is fictional — 555 numbers and concept bios.',
            ],
            [
                'q' => 'What does an agent actually check on a walk?',
                'a' => 'Systems, the block, parking, HOA or building rules, and whether the layout still works. Photo-first shopping misses neighbors and street noise. That is the job.',
            ],
            [
                'q' => 'Can I request a showing with a specific agent?',
                'a' => 'On a live site, yes — pick the listing and note the agent. This demo saves a Booking as Requested and does not email or assign a calendar. Use Book a showing, then Contact if you want the office path.',
            ],
            [
                'q' => 'Is Acreline a licensed brokerage?',
                'a' => 'No. This is a concept demo. Agent names, licenses, and phones are sample data so you can see how a small team page should read.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function contact(): array
    {
        return [
            [
                'q' => 'What is the fastest way to reach the office?',
                'a' => 'Call (555) 010-0455 during posted hours, or send the message form if you can wait for a written reply. Prefer a walk-through? Book a showing and pick a sample address — that path is built for appointments.',
            ],
            [
                'q' => 'Should I use the form, the valuation tool, or the book page?',
                'a' => 'Form: a question or a sell conversation. Valuation: a demo price range for a house or condo. Book: a date and time on a sample listing. None of these send email on this concept site.',
            ],
            [
                'q' => 'Where is the office, really?',
                'a' => 'Fiction only: 100 Concept Way, Sample Borough, PA 00000. The map pin is illustrative. Hours and the 555 line are demo chrome so a buyer page has somewhere to look.',
            ],
            [
                'q' => 'What happens when I submit a message or estimate?',
                'a' => 'The confirmation stays on the page. Nothing is emailed, texted, or stored as a lead. A live Acreline install would route the message to the team.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function listing(): array
    {
        return [
            [
                'q' => 'What should I check before I book this walk?',
                'a' => 'Type first: houses lead with beds and systems; condos lead with dues and parking; commercial leads with permitted use. Then neighborhood — schools and commute change from one area to the next.',
            ],
            [
                'q' => 'How do I request a showing for this address?',
                'a' => 'Use Book a showing on this page. The listing is preselected. Pick a date and a time slot. This demo stores a Booking as Requested — it does not email or text the agent.',
            ],
            [
                'q' => 'Is this a live MLS listing?',
                'a' => 'No. Sample inventory for layout and booking flow. Addresses, prices, and MLS numbers are fictional. Use the write-up to practice how a listing page should answer systems, HOA, and next steps.',
            ],
            [
                'q' => 'Where do I read more about neighborhoods or the buyer path?',
                'a' => 'The buyer guide covers inspections, payments, and HOA questions. Areas has neighborhood-by-neighborhood reads. Both stay useful even when this card is a concept demo.',
            ],
        ];
    }

    /**
     * @return list<array{q: string, a: string}>
     */
    public static function agent(): array
    {
        return [
            [
                'q' => 'How do I reach this agent?',
                'a' => 'Use the 555 number or concept email on the card, or book a showing and mention who you want. This demo does not send messages — confirmations stay on the page.',
            ],
            [
                'q' => 'Are these listings actually theirs?',
                'a' => 'On a live site, the grid below is the agent’s inventory. Here they are sample listings assigned in WordPress so you can see the relationship. Open a card, then book with that listing selected.',
            ],
            [
                'q' => 'Is this a real licensed agent?',
                'a' => 'No. Profiles, license numbers, and phones are fictional concept data. The page is here to show how an agent bio, specialties, and listings should scan.',
            ],
        ];
    }
}
