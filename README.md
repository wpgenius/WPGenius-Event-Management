# WPGenius Event Plugin

**Contributors:** WPGenius  
**Tags:** events, sessions, speakers, organizers, sponsors, Elementor, shortcodes  
**Requires at least:** 5.0  
**Tested up to:** 6.0  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

## Description

WPGenius Event Plugin manages events using custom post types and taxonomies, enabling flexible data sorting and filtering. It integrates Elementor widgets and shortcodes for frontend rendering and includes detailed documentation and user guidance.

## Features

- Custom Post Types: Sessions, Speakers, Organizers, Sponsors.
- Shared Event Taxonomy for content association.
- Elementor Widgets and Shortcodes for frontend display.
- Settings page to control CPT visibility.
- Role-based permissions.
- Inline help texts for meta boxes and fields.

## Installation

1. Upload the `wpgenius-event-plugin` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Setup

- Navigate to **Settings > Permalinks > Event Settings** to configure custom post type visibility.
- Create events under the Event Taxonomy.
- Add Sessions, Speakers, Organizers, and Sponsors, associating them with events.

## Shortcodes Reference

- **Organizers:** `[wpgenius_organizers event="event-slug"]`
- **Speakers:** `[wpgenius_speakers event="event-slug" featured="true" show_all="false"]`
- **Agenda:** `[wpgenius_agenda event="event-slug" date="YYYY-MM-DD"]`
- **Sponsors:** `[wpgenius_sponsors event="event-slug"]`

## Elementor Widgets Guide

- **Organizers Widget:** Displays organizers filtered by event.
- **Speakers Widget:** Displays speakers filtered by event, with options for featured speakers.
- **Agenda Widget:** Displays sessions as an agenda, filtered by event and date.
- **Sponsors Widget:** Displays sponsor logos sorted by sponsor level.

## Meta Box Details

- **Event:** Associates content with an event for filtering.
- **Session Details:** Includes date, time, length, type, links, and speakers.
- **Speaker Details:** Includes designation, company, logo, email, and featured status.

## Permissions

- **Editors** and **Authors** can create and manage content.
- **Administrators** can access the settings page.

## Frequently Asked Questions

**Q:** Can I use the plugin without Elementor?
**A:** Yes, you can use the shortcodes provided to display content on the frontend.

**Q:** How do I add help texts to meta boxes?
**A:** Help texts are automatically added under each meta box and field.

## Changelog

### 1.0.0

- Initial release with core features.

## Upgrade Notice

N/A

## License

This plugin is licensed under the GPLv2 or later.