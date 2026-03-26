# Flashblocks Coming Soon

Per-page coming soon toggle. When active on a page, logged-out visitors are redirected to a `/coming-soon` page (or a 404 if that page doesn't exist).

## How it works

Uses a post meta key `_fb_coming_soon` instead of comment status or post visibility. This keeps the toggle explicit and independent of any other WordPress features.

- `_fb_coming_soon = 1` — coming soon is **on**, page redirects
- `_fb_coming_soon = 0` (or not set) — coming soon is **off**, page is public

Logged-in users always see the page regardless of the toggle.

## Toggling

**Block editor** — "Coming Soon" panel in the document settings sidebar (right side panel).

**Admin bar** — on the front end, a "Coming Soon On / Off" button appears in the top bar when viewing a singular page while logged in.

## Preview mode

Add `?preview-on` to any URL to set a cookie (`fb_preview_mode`) that bypasses the redirect for one month. Useful for sharing previews with clients without logging them in.

## Migration

On first load after activation, the plugin runs a one-time migration that finds any posts/pages with `comment_status = open` (used by the previous redirect system), sets `_fb_coming_soon = 1` on them, and resets their comment status to `closed`. The migration is tracked via the `_fb_coming_soon_migrated` option and will not run again.

## Build

```bash
npm install
npm run build
```
