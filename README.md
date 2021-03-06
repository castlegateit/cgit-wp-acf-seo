# Castlegate IT WP ACF SEO #

**Development of Castlegate IT WP ACF SEO has now stopped. We recommend [SEO Redux](https://github.com/castlegateit/cgit-wp-seo-redux) or [Yoast](https://yoast.com/) instead.**

----

Simple SEO fields for titles, headings, and descriptions. The title and description fields (`seo_title` and `seo_description`) are displayed automatically. The heading `seo_heading` must be added the (child) theme template manually. The plugin provides the function `cgit_seo_heading()` to output the heading with a fallback to a string of your choice or to a heading based on the page title, site description, and site name.

Requires [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/).

## Filters ##

You can override the ACF settings using filters:

*   `cgit_seo_title` string, default `SEO`.
*   `cgit_seo_position` string, default `side`.
*   `cgit_seo_fields` array of ACF field definitions.
*   `cgit_seo_location` array of ACF location parameters.
