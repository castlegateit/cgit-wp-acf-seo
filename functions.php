<?php

use Cgit\AcfSeo;

/**
 * Return page or post heading
 *
 * @return string
 */
function cgit_seo_heading($default = null) {
    $obj = AcfSeo::getInstance();
    return $obj->getHeading($default);
}
