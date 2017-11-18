<?php

namespace Drupal\service_description;

use Symfony\Component\Config\FileLocator as BaseFileLocator;

/**
 * Class FileLocator.
 *
 * @package service_description
 */
class FileLocator extends BaseFileLocator {

  /**
   * {@inheritdoc}
   */
  public function __construct($paths = array()) {
    if (empty($paths)) {
      $this->paths = $this->getDirectoryPath();
    }
    else {
      $this->paths = (array) $paths;
    }
  }

  /**
   * Get directory of description files.
   */
  public function getDirectoryPath() {
    return [
      '/www/htdocs/drupal8-composer/docroot/modules/contrib/service_description/modules/httpbin/service_description',
    ];
  }

}
