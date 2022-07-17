<?php

namespace App\Admin;

use SilverStripe\View\SSViewer;

class ProjectAdmin extends \SilverStripe\Admin\ModelAdmin
{
    private static $managed_models = [
        \App\Model\Project::class,
    ];

    private static $url_segment = 'projects';
    private static $menu_title = 'Projects';
    private static $menu_icon_class = 'font-icon-code';

    private static $allowed_actions = [
        'cmsPreview',
    ];

    private static $url_handlers = [
        '$ModelClass/cmsPreview/$ID' => 'cmsPreview',
    ];

    public function cmsPreview()
    {
        $id = $this->urlParams['ID'];
        $obj = $this->modelClass::get_by_id($id);
        if (!$obj || !$obj->exists()) {
            return $this->httpError(404);
        }

        // Include use of a front-end theme temporarily.
        $oldThemes = SSViewer::get_themes();
        SSViewer::set_themes(SSViewer::config()->get('themes'));
        $preview = $obj->forTemplate();

        // Make sure to set back to backend themes.
        SSViewer::set_themes($oldThemes);

        return $preview;
    }
}
