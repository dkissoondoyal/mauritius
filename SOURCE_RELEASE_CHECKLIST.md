# Production-source release checklist

This checklist must be completed before copying any code or data from Mauritius.Name into this public repository.

- [ ] Confirm ownership and authority to publish every component.
- [ ] Review third-party dependencies, fonts, media, and their licence terms.
- [ ] Exclude credentials, API tokens, keys, configuration secrets and environment files.
- [ ] Exclude all private citizen submissions, user accounts, emails, IP addresses, logs and backups.
- [ ] Replace production URLs and endpoints with clearly labelled examples where appropriate.
- [ ] Audit database queries, permissions, CSRF protection, output escaping, input validation and file uploads.
- [ ] Test mobile layout, keyboard navigation, labels and screen-reader behaviour.
- [ ] Provide clean installation instructions and synthetic test fixtures.
- [ ] Establish a compatible open-source licence before releasing reusable code.
- [ ] Verify the release is distinct from government-authored legal texts and does not imply official endorsement.
- [ ] Review the final diff and repository history for accidentally included sensitive data.

The standalone demo in `demo/` is newly authored illustrative code, not reviewed production code.
