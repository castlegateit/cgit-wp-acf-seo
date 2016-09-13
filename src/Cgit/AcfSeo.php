<?php

namespace Cgit;

class AcfSeo
{
    /**
     * Singleton instance
     *
     * @var AcfSeo
     */
    private static $instance;

    /**
     * Page ID for "page for posts"
     *
     * @var int
     */
    private $indexId;

    /**
     * Default site description
     *
     * @var string
     */
    private $defaultDescription = 'Just another WordPress site';

    /**
     * Private constructor
     *
     * @return void
     */
    private function __construct()
    {
        $this->indexId = get_option('page_for_posts');

        // Register custom fields
        add_action('acf/init', [$this, 'registerFields']);

        // Register user guide sections
        add_filter('cgit_user_guide_sections', [$this, 'registerGuide'], 100);

        // Apply optimizations
        add_filter('wp_title', [$this, 'optimizeTitle'], 999, 3);
        add_filter('wp_title', [$this, 'sanitizeTitle'], 999, 3);
        add_action('wp_head', [$this, 'writeDescription'], 0);
    }

    /**
     * Return singleton instance
     *
     * @return SecurityAudit
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register custom fields
     *
     * @return void
     */
    public function registerFields()
    {
        acf_add_local_field_group([
            'key' => 'cgit_wp_seo',
            'title' => 'SEO',
            'fields' => [
                [
                    'key' => 'seo_title',
                    'name' => 'seo_title',
                    'label' => 'Title',
                    'type' => 'text',
                ],
                [
                    'key' => 'seo_heading',
                    'name' => 'seo_heading',
                    'label' => 'Heading',
                    'type' => 'text',
                ],
                [
                    'key' => 'seo_description',
                    'name' => 'seo_description',
                    'label' => 'Description',
                    'type' => 'text',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '!=',
                        'value' => '0', // Show on all posts
                    ],
                ],
            ],
            'position' => 'side',
        ]);
    }

    /**
     * Register user guide section
     *
     * @param array $sections
     * @return array
     */
    public function registerGuide($sections)
    {
        $file = dirname(CGIT_ACF_SEO_FILE) . '/content/user-guide.php';
        $sections['cgit-wp-acf-seo'] = UserGuide::getFile($file);

        return $sections;
    }

    /**
     * Optimize title
     *
     * Allows the title to be overridden by the custom title field for single
     * posts and pages.
     *
     * @param string $title
     * @param string $sep
     * @param string $location
     * @return string
     */
    public function optimizeTitle($title, $sep, $location)
    {
        $seo_title = get_field('seo_title', $this->getId());

        if (!$this->isPost() || !$seo_title) {
            return $title;
        }

        return $seo_title;
    }

    /**
     * Strip default description from generate title
     *
     * Removes the default description and its adjacent separator character from
     * the HTML title.
     *
     * @param string $title
     * @param string $sep
     * @param string $location
     * @return string
     */
    public function sanitizeTitle($title, $sep, $location)
    {
        $pattern = $this->defaultDescription . ' ' . $sep;

        if ($location == 'right') {
            $pattern = $sep . ' ' . $this->defaultDescription;
        }

        return str_replace($pattern, '', $title);
    }

    /**
     * Write optimized description
     *
     * Adds an HTML meta element to the head of the page containing a
     * description based on the value of the custom description field.
     *
     * @return void
     */
    public function writeDescription()
    {
        $seo_description = get_field('seo_description', $this->getId());

        if (!$this->isPost() || !$seo_description) {
            return;
        }

        echo '<meta name="description" content="' . $seo_description . '" />';
    }

    /**
     * Return optimized heading
     *
     * This can be used to write the main page heading, which might be placed in
     * the alt attribute of the site logo. The default heading, which is used
     * when the SEO heading field is empty or when you are viewing something
     * other than a post or a page, can be any string, including an empty one.
     * However, if it is null (the default default), then the heading will be
     * generated based on the post or page title (if applicable), the site
     * description (if available and not set to the default value) and the site
     * name.
     *
     * @param string $default
     * @return string
     */
    public function getHeading($default = null)
    {
        $seo_heading = get_field('seo_heading', $this->getId());

        // The default heading can be any string, including an empty string. If
        // there is really no default heading, generate one from the post or
        // page title, the site description, and the site name.
        if (is_null($default)) {
            $default = $this->getDefaultHeading();
        }

        // If this is something that can be optimized, check for the SEO heading
        // field and return it if possible.
        if ($this->isPost() && $seo_heading) {
            return $seo_heading;
        }

        return $default;
    }

    /**
     * Return default heading
     *
     * @return string
     */
    private function getDefaultHeading()
    {
        $segments = [];
        $description = get_bloginfo('description');

        // If this is something that should have a title, like a post or page,
        // make the first part of the heading the title.
        if ($this->isPost()) {
            $segments[] = get_the_title($this->getId());
        }

        // If the site has a description and it is not the default description
        // string, add it to the heading.
        if ($description && $description != $this->defaultDescription) {
            $segments[] = $description;
        }

        // The last part of the heading is the site name.
        $segments[] = get_bloginfo('name');

        // Return the various parts of the heading, combined into a single
        // string and with each part separated by pipes.
        return implode(' | ', $segments);
    }

    /**
     * Return post or page ID
     *
     * This is usually the ID of the global post variable. However, if the site
     * uses a static home page and we are looking at the blog index, this should
     * return the ID of the "page for posts".
     *
     * @return int
     */
    private function getId()
    {
        global $post;

        if ($this->indexId && is_home()) {
            return $this->indexId;
        }

        if (isset($post->ID)) {
            return $post->ID;
        }

        return 0;
    }

    /**
     * Is this is a post or page?
     *
     * @return boolean
     */
    private function isPost()
    {
        return is_singular() || is_page() || (is_home() && $this->indexId);
    }
}
