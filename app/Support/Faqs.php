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
                'q' => 'What should I compare first on a rural listing?',
                'a' => 'Start with area, price, and usable acres, then water, septic, and legal access. Bedrooms and commute matter on houses; perc status and road frontage matter on land. Inventory on this site is sample data; the review order is what a working farm buyer uses.',
            ],
            [
                'q' => 'How is buying land different from buying a house here?',
                'a' => 'A house usually has utilities in place. Raw acreage often needs a well, a perc test, and a recorded driveway. Zoning and farmland-tax enrollment change from North Ridge to Oak Hollow. Review the buyer guide before you write an offer on a parcel without a house.',
            ],
            [
                'q' => 'Do I need a showing to walk a farm or historic house?',
                'a' => 'Yes for occupied homes and most working farms — lanes, livestock, and locked shops are common. Schedule a sample showing to see the flow: choose a listing, a date, and a time. Wear boots, and mention pets or if you are new to land.',
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
                'q' => 'What should I bring to a rural showing?',
                'a' => 'Boots, a notebook, and questions about the well, septic, and access. If you are shopping land, ask where a house could sit and whether a perc report exists. This demo saves the request only — it does not email or text.',
            ],
            [
                'q' => 'How long is a typical farm or acreage walk-through?',
                'a' => 'Plan 45–90 minutes. A historic house can be shorter; a 30-acre farm with a barn and lane takes longer. Evening slots on this form match how working buyers actually tour after commute hours.',
            ],
            [
                'q' => 'Can I tour land and a house on the same request?',
                'a' => 'Pick one listing per request so the agent preps the right file. Want both a farmhouse and a vacant parcel? Send two showing requests or note it in the comments after you choose the first address.',
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
                'q' => 'Why filter by township before city?',
                'a' => 'Zoning, lot-size rules, and preferential farmland-tax programs sit at the township or county. Two parcels a mile apart can have different well, septic, and subdivision answers. Pick an area first, then price and acres.',
            ],
            [
                'q' => 'What do Land, Farm, and Historic mean?',
                'a' => 'Land is acreage to build or hold. Farm includes working ground, barns, or orchard. Historic is an older house where the building is the product. Home is a turnkey dwelling. All eight cards are fictional samples.',
            ],
            [
                'q' => 'How do I get from a card to a showing?',
                'a' => 'Open a listing for beds, acres, and the write-up, then Book a showing — that address is preselected. You can also start from the homepage or /book/ and pick the listing there.',
            ],
            [
                'q' => 'Is this a live MLS feed?',
                'a' => 'No. Prices, addresses, and township labels are concept data for this demo. Use the filters to see how a working farm search should feel, then replace the sample inventory with a real feed.',
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
                'q' => 'Why does township matter more than the nearest borough?',
                'a' => 'Zoning, minimum lot size, and Clean and Green (Act 319) sit at the township. Two parcels a mile apart — one toward Cashtown, one toward Biglerville — can have different well, septic, and subdivision answers. Read the township card first, then the borough for groceries and commute.',
            ],
            [
                'q' => 'What should I compare if I want orchard ground vs a wooded lot?',
                'a' => 'Orchard and fruit-belt townships (Menallen, Butler, parts of Franklin) ask about packing access, spray neighbors, and tillable split. Mountain woodlots (Hamiltonban, Liberty toward Michaux) ask about driveway grade, well yield in rock, and recreational use. Same county — different product.',
            ],
            [
                'q' => 'Can I commute from these townships and still buy acreage?',
                'a' => 'Yes. Liberty and Fairfield lean toward the Maryland line; New Oxford and Hanover sit east toward York. Tell us the drive you will actually make on a Tuesday, and we will point you at the townships that fit — this demo uses sample inventory only.',
            ],
            [
                'q' => 'Are these real listings tied to each township?',
                'a' => 'No. The profiles are written for Adams County patterns so the area page is useful to scan. Inventory on Listings is fictional. Use the township filter there, then book a sample showing if you want to walk the flow.',
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
                'q' => 'How much land do I need for a house, well, and septic?',
                'a' => 'It depends on the township minimum lot size and the perc results. For a conventional on-lot system in rural Adams County, buyers commonly look at one to two acres or more. An agent can tell you what a specific township requires before you write an offer.',
            ],
            [
                'q' => 'What is a perc test, and who pays for it?',
                'a' => 'A percolation test checks whether the soil will absorb septic effluent and where a system can go. On raw land it is usually a buyer contingency, and the buyer typically pays — unless the seller already has a valid soils report. Do not skip it.',
            ],
            [
                'q' => 'Can I get a normal mortgage on raw land?',
                'a' => 'Often not a standard 30-year home mortgage. Land and farm purchases usually run through a land loan, construction loan, or farm-credit lender, with a larger down payment. The estimators on this page are planning math only — a lender gives real terms.',
            ],
            [
                'q' => 'What is Act 319 Clean and Green?',
                'a' => 'A Pennsylvania program that taxes qualifying farm and forest land at use value instead of market value. It saves money annually, but subdividing or changing the use can trigger a rollback tax of up to seven years. We flag enrollment on any sample parcel that would carry it.',
            ],
            [
                'q' => 'Do I need public water to buy acreage here?',
                'a' => 'No. Most rural Adams County parcels use a private well. Ask whether a well exists and has been flow-tested, or whether neighboring yields suggest a new well is likely. Public water at the road is a bonus, not the default.',
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
                'a' => 'Match the ground: farms and orchards, raw land and perc questions, or a century house. Read specialties on the card, then book a showing or message the office. This roster is fictional — 555 numbers and concept bios.',
            ],
            [
                'q' => 'What does a rural agent actually check on a walk?',
                'a' => 'Access (recorded vs handshake lane), well and septic feasibility, zoning, easements, and whether the barn or shop still works. Photo-first shopping misses wet corners and rollback risk. That is the job — not a condo punch list.',
            ],
            [
                'q' => 'Can I request a showing with a specific agent?',
                'a' => 'On a live site, yes — pick the listing and note the agent. This demo saves a Booking as Requested and does not email or assign a calendar. Use Book a showing, then Contact if you want the office path.',
            ],
            [
                'q' => 'Is Keystone Real Estate a licensed brokerage?',
                'a' => 'No. This is a concept demo. Agent names, licenses, and phones are sample data so you can see how a small rural team page should read.',
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
                'a' => 'Form: a question or a sell conversation. Valuation: a demo price range for acreage or a house. Book: a date and time on a sample listing. None of these send email on this concept site.',
            ],
            [
                'q' => 'Where is the office, really?',
                'a' => 'Fiction only: 100 Concept Way, Sample Borough, PA 00000. The map pin is illustrative. Hours and the 555 line are demo chrome so a buyer page has somewhere to look.',
            ],
            [
                'q' => 'What happens when I submit a message or estimate?',
                'a' => 'The confirmation stays on the page. Nothing is emailed, texted, or stored as a lead. A live Keystone install would route the message to the team.',
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
                'a' => 'Type first: houses lead with beds and systems; land leads with acres, perc, and access; farms mix both plus barn use. Then township — zoning and Clean and Green change from one ridge to the next. Wear boots.',
            ],
            [
                'q' => 'How do I request a showing for this address?',
                'a' => 'Use Book a showing on this page. The listing is preselected. Pick a date and a time slot. This demo stores a Booking as Requested — it does not email or text the agent.',
            ],
            [
                'q' => 'Is this a live MLS listing?',
                'a' => 'No. Sample inventory for layout and booking flow. Addresses, prices, and MLS numbers are fictional. Use the write-up to practice how a rural listing page should answer water, access, and next steps.',
            ],
            [
                'q' => 'Where do I read more about wells, perc, or the township?',
                'a' => 'The buyer guide covers wells, septic, access, and Act 319. Areas has township-by-township reads. Both stay useful even when this card is a concept demo.',
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
                'a' => 'No. Profiles, license numbers, and phones are fictional concept data. The page is here to show how a rural agent bio, specialties, and listings should scan.',
            ],
        ];
    }
}
