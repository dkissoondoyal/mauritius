# Civic Consultation Starter — experimental v0.1.0

A small WordPress plugin prototype for collecting **structured, manually moderated** legislative amendment proposals.

**Status: experimental / not independently security-audited. Do not deploy on a public production website yet.** This is a new reusable prototype, not copied production source from Mauritius.Name.

## What it does

- Adds shortcode `[civic_consultation]` to a published page with WordPress comments enabled.
- Asks for a public display name, private email, clause/topic, proposed amendment, rationale, and explicit consent.
- Stores each proposal as a **pending WordPress comment** attached to the page.
- Uses WordPress nonce verification, sanitisation, a honeypot field, and a basic transient throttle.
- Shows a confirmation after successful submission.

## Local test plan (staging only)

1. Install the plugin on a disposable WordPress test installation; activate it.
2. Create a **published test page**, enable comments on that page, and add `[civic_consultation]`.
3. Submit fictional data while logged out. Check that the submission appears under WordPress **Comments** as **Pending**.
4. Verify the submission does not appear publicly until an administrator approves it.
5. Check invalid nonce, missing consent, empty fields, spam honeypot, repeat submission, and closed comments.
6. Test on mobile and with a keyboard and screen reader.
7. Verify privacy notices, erasure/export workflows, anti-spam protections, notifications, and retention policy before any real pilot.

## Known limitations / release blockers

- No formal penetration test or complete code review.
- Basic transient throttle is insufficient for sustained abuse.
- Form labels, focus handling, accessibility, error reporting, and multilingual support need further work.
- WordPress comments are not a dedicated amendment database; proposals are stored in a single formatted comment body.
- Site administrators must configure privacy, retention, moderation, backup, and deletion policies.
- The plugin does not provide a dedicated administrative consultation window, role-based review workflow, or structured export.
- No automated test suite yet.
- Comment-display behaviour depends on the active theme and other plugins; inspect this on staging.
- The plugin's shortcode and endpoint must be reviewed for WordPress compatibility and security before any live deployment.

## Scope and independence

This project is independent civic technology work. It is not an official government or parliamentary submission service. No private production records or credentials are included.

**Licence:** GPL-2.0-or-later, as stated in the plugin header, for this newly authored prototype. This does not imply that other Mauritius.Name components have been released under that licence.
