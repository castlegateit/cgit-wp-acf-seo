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
     * Private constructor
     *
     * @return void
     */
    private function __construct()
    {
        // Register custom fields
        add_action('acf/init', [$this, 'registerFields']);

        // Register user guide sections
        add_filter('cgit_user_guide_sections', [$this, 'registerGuide'], 100);

        // Apply optimizations
        add_filter('wp_title', [$this, 'optimizeTitle'], 999);
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
     * @param string $title
     * @return string
     */
    public function optimizeTitle($title)
    {
        $seo_title = get_field('seo_title');

        if (!$this->isPost() || !$seo_title) {
            return $title;
        }

        return $seo_title;
    }

    /**
     * Write optimized description
     *
     * @return void
     */
    public function writeDescription()
    {
        $seo_description = get_field('seo_description');

        if (!$this->isPost() || !$seo_description) {
            return;
        }

        echo '<meta name="description" content="' . $seo_description . '" />';
    }

    /**
     * Return optimized heading
     *
     * @return string
     */
    public function getHeading()
    {
        $seo_heading = get_field('seo_heading');

        if (!$this->isPost()) {
            return false;
        }

        if (!$seo_heading) {
            return get_the_title();
        }

        return $seo_heading;
    }

    /**
     * Is this is a post or page?
     *
     * @return boolean
     */
    private function isPost()
    {
        return is_single() || is_page();
    }
}
