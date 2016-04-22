<?php

use Cgit\AcfSeo;

/**
 * Return page or post heading
 *
 * @return string
 */
function cgit_seo_heading() {
    $obj = AcfSeo::getInstance();
    return $obj->getHeading();
}
