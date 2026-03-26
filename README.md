# Flashblocks Coming Soon

Per-page coming soon toggle. When active on a page, logged-out visitors see the coming-soon page content served at the real URL.

## How it works

Uses a post meta key `_fb_coming_soon` instead of comment status or post visibility. This keeps the toggle explicit and independent of any other WordPress features.

- `_fb_coming_soon = 1` — coming soon is **on**, the coming-soon page content is shown at the real URL
- `_fb_coming_soon = 0` (or not set) — coming soon is **off**, page is public

The URL does not change, so visitors can bookmark the page and return when it goes live.

Search engine bots receive a **503 status** with a `Retry-After: 604800` header (one week), telling them the page is temporarily unavailable and to re-crawl later. Human visitors get a normal 200. A `noindex, nofollow` meta tag is also injected as a fallback for bots that ignore status codes.

The plugin is fully compatible with **Block Themes (FSE)** and can be configured via **Settings > Coming Soon** in the WordPress admin:

- **Global Status**: Choose between **Disabled**, **Site-wide** (entire site), or **Per-page** (individual toggles).
- **Source Page**: Choose any WordPress page as the content source for your "Coming Soon" screen.
- **Operation Mode**: 
    - **Redirect** (Recommended) — Performs a **307 Temporary Redirect**. Best for sites with heavy server-side caching like Kinsta.
    - **Replace** (Mask) — Shows coming-soon content at the original URL. Best for preserving bookmarks.

Logged-in users always see the real page regardless of the toggle.

## Toggling

**Block editor** — "Coming Soon" panel in the document settings sidebar (right side panel).

**Admin bar** — on the front end, a "Coming Soon On / Off" button appears in the top bar when viewing a singular page while logged in.

## Preview mode

Add `?preview-on` to any URL to set a cookie (`fb_preview_mode`) that bypasses the coming-soon display for one month. Add `?preview-off` to clear the cookie. Useful for sharing previews with clients without logging them in.

## Build

```bash
npm install
npm run build
```
