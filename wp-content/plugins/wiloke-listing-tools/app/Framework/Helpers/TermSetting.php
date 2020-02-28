<?php

namespace WilokeListingTools\Framework\Helpers;

class TermSetting
{
    public static function getTermIcon($oTerm)
    {
        $iconURL    = '';
        $termIconID = GetSettings::getTermMeta($oTerm->term_id, 'icon_img_id');
        if (!empty($termIconID)) {
            $iconURL = GetSettings::getAttachmentURL($termIconID, 'thumbnail', true);
        }

        if (!empty($iconURL)) {
            return $iconURL;
        }

        $iconURL = GetSettings::getAttachmentURL($oTerm->term_id, 'icon_img');

        if (!empty($iconURL)) {
            return $iconURL;
        }

        return apply_filters('wiloke-listing-tools/map-icon-url-default',
            get_template_directory_uri().'/assets/img/map-icon.png');
    }

    /**
     * @param $termID
     * @param $taxonomy
     *
     * @return mixed
     */
    public static function getDefaultPostType($termID, $taxonomy)
    {
        if ($termID instanceof \WP_Term) {
            $termID = $termID->term_id;
        } else if (!is_numeric($termID)) {
            $oTerm  = get_term_by('slug', $termID, $taxonomy);
            $termID = $oTerm->slug;
        }

        $aPostTypes = GetSettings::getTermMeta($termID, 'belongs_to');
        if (!empty($aPostTypes)) {
            return $aPostTypes[0];
        }

        return GetSettings::getDefaultPostType(true, false);
    }
}
