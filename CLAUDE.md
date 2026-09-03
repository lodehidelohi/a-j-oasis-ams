# A & J OASIS — Web-Based and Mobile Rental Apartment Management System

## Project Overview
IT capstone thesis: Web-Based and Mobile Rental Apartment Management System.
Client: A & J OASIS, a sole proprietorship owned by Jose Valle, Koronadal City.
Manages 53 rental rooms across two properties. Covers tenant management, payment
monitoring, occupancy tracking, and maintenance management. Includes an Android
companion app for tenants. iOS is explicitly excluded (no Mac / Apple Developer access).

## Locked Naming Conventions (must be used exactly, everywhere)
- Client name: **A & J OASIS** — NOT "A & J City Oasis"
- System name: **Web-Based and Mobile Rental Apartment Management System** — NOT "Property..."

## Locked Business Rules (must be preserved consistently across all docs and code)
- Booking requires full three-month upfront payment (advance + deposit + security) via
  Xendit; move-in date must be selected within 7 days of booking.
- Billing starts on the scheduled move-in date, regardless of physical arrival.
- Full refunds for cancellations made before the move-in date.
- One-month grace period for rent and utility payments (justified by the existing deposit).
- Utility bills are manually encoded by admin at least 10 days before the due date.
- Room transfer deposit adjustment: tenant pays the difference for upgrades; for
  downgrades, excess is credited or refunded manually (outside the system).
- Early move-out refund is calculated by the system but disbursed manually outside
  the system.

## Tech Stack
- Backend: PHP, Laravel, Blade templating, MySQL
- Mobile: Flutter/Dart (Android only)
- Frontend: HTML5, CSS3, Bootstrap 5, JavaScript
- Payments: Xendit (payment gateway)
- Email: Laravel Mail / SMTP
- Real-time: Laravel Echo / Pusher

## Dev Tools
Visual Studio Code, Composer, Git/GitHub, XAMPP/Apache

## Manuscript/Chapter Status (as of last sync)
- Chapter I: Complete — rebuilt after panel requested a hotel-booking-style
  reservation flow.
- Chapters II & III: Finalized, including literature verification and AI-detection
  rewrite of flagged passages (target: below 30% AI score on Quillbot detector).
- Chapter IV (Requirements Analysis and System Design): In progress. Completed so far:
  - Use Case Diagram (SVG, 3 actors + Xendit as external actor, 26 use cases)
  - Use Case Description table (26 rows)
  - Database Design and Normalization (UNF → 3NF, 11-table schema)
  - Activity Diagram prompts for all five processes (delivered as Claude Design prompts)
  - Low-fidelity wireframes (21 SVGs with REQ callouts, extracted as PNGs via Playwright)
  - System Architecture: three-tier description and mapping table
  - Remaining: Event Decomposition tables (handled independently in Word)

## Working Style Notes
- Direct and minimal — short confirmations between deliverables, quick pivots.
- Prefers copy-paste-ready outputs (prompts, tables, sections) over iterative drafting.
- Corrections and scope decisions apply retroactively across the whole manuscript
  for consistency.

## Implementation Status (as of 2026-08-30)

The Laravel backend is built and functional against a live MySQL database
(`aj_oasis`, run via XAMPP/`php artisan serve`). Chapter IV design docs above are
independent of this — this section tracks the actual codebase.

### What's built
- **Schema**: full 11-table 3NF schema (users, properties, rooms, bookings, leases,
  payments, utility_bills, maintenance_requests, room_transfers, move_outs,
  notifications) plus `room_images` and room detail columns (`size_sqm`,
  `description`, `inclusions` JSON) added later.
- **Auth & roles**: custom login/register (no Breeze), `admin` / `staff` / `tenant`
  roles, admin can create/deactivate any account (`admin.users.*`).
- **Booking → payment → lease flow**: `BookingService` handles the full lifecycle
  (3-month upfront split, 7-day move-in-date window, lease auto-creation, full
  refund pre-move-in, stale-booking expiry). Race conditions guarded with
  `lockForUpdate()` (booking creation, room-transfer approval).
- **Xendit integration**: real SDK wired (`XenditService`), but **currently running
  in fake mode** — see Known Blockers below.
- **Notifications + email**: `NotificationService` + 5 Mailables
  (`BookingConfirmedMail`, `PaymentReceiptMail`, `PaymentOverdueMail`,
  `LeaseStartedMail`, `UtilityBillEncodedMail`) fire at every booking/payment/lease
  event. `MAIL_MAILER=log` — check `storage/logs/laravel.log` to see sent mail.
- **Admin panel**: sidebar layout (`layouts.admin`, wireframe-driven — persistent
  left sidebar, matches screen A1–A12 style), full CRUD for properties, rooms
  (with per-property room-count auto-generation via `RoomGenerationService`),
  accounts, bookings/leases/payments/utility-bills read views, maintenance status
  updates, room-transfer approve/reject, move-out finalization.
- **Tenant portal**: top-nav layout (`layouts.app`, matches wireframe's "User Web"
  spec), booking flow, lease/payment/maintenance views, transfer/move-out requests.
- **Public site**: landing page (`/`, `HomeController`) with live stats (not
  fabricated), room browse grid with multi-image carousel cards, and a dedicated
  room-details page (`/rooms/{room}`) with a Booking.com-style photo gallery grid +
  lightbox modal, icon-mapped amenities, sticky price card. No fake reviews/ratings
  — this app has no review system, so none were added.
- **Room images**: admin can upload/remove multiple photos per room
  (`admin.rooms.images.destroy`), stored on the `public` disk (`storage:link`
  already run). `RoomImageSeeder` seeds 3 showcase rooms per property with sample
  SVG placeholder photos + realistic inclusions — safe to re-run standalone
  (`php artisan db:seed --class=RoomImageSeeder`), only touches rooms without
  existing details.

### Known blockers / temporary states
- **`XENDIT_FAKE_MODE=true`** in `.env` — the real Xendit account hit a persistent
  `403 REQUEST_FORBIDDEN_ERROR` (confirmed account-level, not a key-scope issue;
  happens even on the read-only Balance API). Until that's resolved with Xendit
  support, "paying" an invoice auto-completes via `PaymentCompletionService`
  (same code path the real webhook uses) instead of hitting the real API. Flip to
  `false` once a working secret key is confirmed — no other code changes needed.
- **Room image URLs**: `RoomImage::url()` uses `asset()`, not
  `Storage::disk('public')->url()` — this was a deliberate fix. The latter builds
  URLs from the static `APP_URL` config, which breaks the moment the app is served
  on a different host/port than `APP_URL` says (e.g. `php artisan serve` on :8000
  while `APP_URL=http://localhost`). `asset()` reads the actual request host, so
  it's robust regardless of how the app is being served. Keep this pattern for any
  future file/image URL generation in this project.

### Environment notes
- **Always run new migrations against the real DB too.** This session repeatedly
  tested migrations against a throwaway SQLite swap (copy `.env`, switch
  `DB_CONNECTION=sqlite`, test, restore) without remembering to also
  `php artisan migrate --force` against the real MySQL `aj_oasis` database — this
  caused a live "table not found" error once. Always confirm pending migrations
  with `php artisan migrate --pretend` against the real `.env` when a session ends.
- MySQL via XAMPP must be running for the real app to work; `php artisan serve`
  was used for browser-based verification throughout — the site was reachable at
  `127.0.0.1:8000` in that mode.
- Seeded logins: `admin@ajoasis.test` / `staff@ajoasis.test`, password `password`.

### Not yet built (raised earlier, not yet prioritized)
- Automated tests (PHPUnit/Pest) — zero coverage currently.
- Tenant/staff account self-management beyond what admin provides.
- File uploads for tenant ID/lease documents (only room photos exist so far).
- Real-time broadcasting (Echo/Pusher is in the locked tech stack but unused).
- Flutter mobile app — entirely separate, unstarted.
- The wireframe's fuller admin modules (Statement-of-Account generation, a
  color-coded Occupancy grid, an Announcements module, a Reports/export module)
  — current admin panel covers the functional equivalent of most of these via
  existing CRUD screens, but not in the wireframe's exact dedicated-screen form.
