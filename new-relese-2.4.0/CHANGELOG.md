# Changelog

## [2.4.0] - 2026-04-18

### Added
- Full English and Ukrainian translations
- Detailed setup instructions for Grok xAI and Telegram in admin panel
- Live color preview in "Design" tab
- Black success message after saving settings
- Proper ownership and contributor information

### Changed
- Completely refactored code to meet WordPress.org standards
- All prefixes changed to `AICON_` / `aicon_` (no more "ai" prefix conflicts)
- Removed all inline `<style>` and `<script>` tags
- Improved admin UI and button styles
- Updated readme.txt with proper External Services section

### Fixed
- Plugin Check errors and warnings (prefixes, direct DB queries, inline styles)
- Settings saving issues
- Uninstall.php compliance
- Translation loading and textdomain issues

### Security
- Better escaping and sanitization
- Proper nonce verification
- Improved rate limiting

### Removed
- Old AI_CONSULTANT_ constants
- Unused and duplicate code

---

## [2.3.4] - 2026-04-xx
* Initial release
