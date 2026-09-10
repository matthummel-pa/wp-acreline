@php
  $listings = $catalogListings;
  $selected = $selectedListingId;
@endphp
<form id="showingForm" novalidate>
  <div class="form-grid two">
    <div class="field field-span">
      <label for="showProperty">Property to tour</label>
      <select id="showProperty" name="listing_id" required>
        <option value="">Select a sample home…</option>
        @forelse ($listings as $listing)
          <option value="{{ $listing['id'] }}" @selected($selected === $listing['id'])>
            {{ $listing['title'] }} — {{ \App\Support\Catalog::formatMoney((int) $listing['price']) }}
          </option>
        @empty
          <option value="" disabled>Add listings in WP Admin → Listings</option>
        @endforelse
      </select>
    </div>
    <div class="field">
      <label for="showDate">Preferred date</label>
      <input id="showDate" name="date" type="date" required>
    </div>
    <div class="field">
      <label for="showType">Showing type</label>
      <select id="showType" name="type">
        @foreach ($showingTypes as $value => $label)
          <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <fieldset class="booking-times">
    <legend>Available times</legend>
    <div class="slot-grid" id="slotGrid" role="group" aria-label="Time slots">
      <button type="button" class="slot" data-time="9:00 AM">9:00 AM</button>
      <button type="button" class="slot" data-time="10:30 AM">10:30 AM</button>
      <button type="button" class="slot" data-time="12:00 PM">12:00 PM</button>
      <button type="button" class="slot" data-time="1:30 PM">1:30 PM</button>
      <button type="button" class="slot" data-time="3:00 PM">3:00 PM</button>
      <button type="button" class="slot" data-time="4:30 PM">4:30 PM</button>
      <button type="button" class="slot" data-time="5:30 PM">5:30 PM</button>
      <button type="button" class="slot" data-time="6:30 PM">6:30 PM</button>
    </div>
    <input type="hidden" id="showTime" name="time" value="" required>
  </fieldset>

  <div class="form-grid two form-block">
    <div class="field">
      <label for="showName">Your name</label>
      <input id="showName" name="name" type="text" autocomplete="name" placeholder="Alex Buyer" required>
    </div>
    <div class="field">
      <label for="showPhone">Phone</label>
      <input id="showPhone" name="phone" type="tel" autocomplete="tel" placeholder="(555) 010-0199" required>
    </div>
    <div class="field field-span">
      <label for="showEmail">Email</label>
      <input id="showEmail" name="email" type="email" autocomplete="email" placeholder="{{ 'you@acreline-concept.test' }}" required>
    </div>
    <div class="field field-span">
      <label for="showNotes">Notes (optional)</label>
      <textarea id="showNotes" name="notes" rows="3" placeholder="Gate code questions, pets, first-time buyer…"></textarea>
    </div>
    @include('partials.consent-field', ['inputId' => 'leadConsent'])
    <div class="field field-span">
      <button type="submit" class="btn btn-primary btn-block">Request showing</button>
    </div>
  </div>
  <p class="form-note">{{ $copy['book_note'] ?? $copy['book_text'] ?? 'Demo only — requests are saved to Bookings as Requested.' }}</p>
  <div class="confirm-msg" id="showingConfirm" role="status" aria-live="polite"></div>
</form>
