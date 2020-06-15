<?php
use CRM_Civisualize_ExtensionUtil as E;
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 * This class defines the `civisualise-bundle.js` asset, which combines `dc.js`,
 * `d3.js`, and `crossfilter.js` into one asset -- and puts the services
 * in the `CRM.civisualise` namespace.
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 * $Id$
 *
 */
class CRM_Civisualize_VisualBundle {

  public static function register() {
    Civi::resources()->addScriptUrl(Civi::service('asset_builder')->getUrl('civisualize-bundle.js'));
    Civi::resources()->addStyleUrl(Civi::service('asset_builder')->getUrl('civisualize-bundle.css'));
  }

  /**
   * Generate asset content (when accessed via AssetBuilder).
   *
   * @param \Civi\Core\Event\GenericHookEvent $event
   * @see CRM_Utils_hook::buildAsset()
   * @see \Civi\Core\AssetBuilder
   */
  public static function buildAssetJs($event) {
    if ($event->asset !== 'civisualize-bundle.js') {
      return;
    }

    $content = "CRM.civisualize = {v:'1.0'};(function(){\n";

    // Add crossfilter
    $file = E::path('js/dc/crossfilter.min.js');
    $content .= "// File: $file\n" . file_get_contents($file) . "\n";

    // Add d3
    $file = E::path('js/d3.min.v5.7.0.js');
    $content .= "// File: $file\n" . file_get_contents($file) . "\n";

    // Add dc
    $file = E::path('js/dc/dc.min.js');
    $content .= "// File: $file\n" . file_get_contents($file) . "\n";

    $content .= "this.dc = dc;\n";

    // End the anon function and call it with CRM.civisualize as its 'this' object.
    $content .= "}).call(CRM.civisualize);";

    $event->mimeType = 'application/javascript';
    $event->content = $content;
  }

  /**
   * Generate asset content (when accessed via AssetBuilder).
   *
   * @param \Civi\Core\Event\GenericHookEvent $event
   * @see CRM_Utils_hook::buildAsset()
   * @see \Civi\Core\AssetBuilder
   */
  public static function buildAssetCss($event) {
    if ($event->asset !== 'civisualize-bundle.css') {
      return;
    }

    $files = [ 'dc' =>  'js/dc/dc.min.css' ];

    $content = [];
    foreach ($files as $file) {
      $file = E::path($file);
      $content[] = "// File: $file";
      $content[] = file_get_contents($file);
    }

    $event->mimeType = 'text/css';
    $event->content = implode("\n", $content);
  }

}

