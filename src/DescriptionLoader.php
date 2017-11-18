<?php

namespace Drupal\service_description;

use Guzzle\Service\Loader\JsonLoader;
use GuzzleHttp\Command\Guzzle\Description;

/**
 * Class DescriptionLoader.
 *
 * @package service_description
 */
class DescriptionLoader {

  protected $fileLocator;

  protected $fileLoader;

  public function __construct(JsonLoader $file_loader, FileLocator $file_locator) {
    $this->fileLocator = $file_locator;
    $this->fileLoader = $file_loader;
  }

  public function load() {
    $description = $this->fileLoader->load($this->fileLocator->locate('description.json'));
    return new Description($description);
  }

}
