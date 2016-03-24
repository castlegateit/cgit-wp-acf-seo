# Castlegate IT WP ACF SEO #

Simple SEO fields for titles, headings, and descriptions. The title and description fields (`seo_title` and `seo_description`) are displayed automatically. The heading `seo_heading` must be added the (child) theme template manually. The plugin provides the function `cgit_seo_heading()` to output the heading with a fallback to the post title or the site name. Alternatively, you can access the SEO heading directly with `get_field('seo_heading')`.

Requires [Advanced Custom Fields](http://www.advancedcustomfields.com/).
