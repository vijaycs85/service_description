<?php

namespace Drupal\service_description\Discovery;

use Drupal\Component\Discovery\DiscoverableInterface;
use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Component\Serialization\Json;

/**
 * Provides discovery for JSON files within a given set of directories.
 */
class JsonDiscovery implements DiscoverableInterface {

  /**
   * The base filename to look for in each directory.
   *
   * @var string
   */
  protected $name;

  /**
   * An array of directories to scan, keyed by the provider.
   *
   * @var array
   */
  protected $directories = [];

  /**
   * Constructs a JsonDiscovery object.
   *
   * @param string $name
   *   The base filename to look for in each directory. The format will be
   *   $provider.$name.yml.
   * @param array $directories
   *   An array of directories to scan, keyed by the provider.
   */
  public function __construct($name, array $directories) {
    $this->name = $name;
    $this->directories = $directories;
  }

  /**
   * {@inheritdoc}
   */
  public function findAll() {
    $all = [];

    $files = $this->findFiles();
    $provider_by_files = array_flip($files);

    $file_cache = FileCacheFactory::get(Json::getFileExtension() . '_discovery:' . $this->name);

    // Try to load from the file cache first.
    foreach ($file_cache->getMultiple($files) as $file => $data) {
      $all[$provider_by_files[$file]] = $data;
      unset($provider_by_files[$file]);
    }

    // If there are files left that were not returned from the cache, load and
    // parse them now. This list was flipped above and is keyed by filename.
    if ($provider_by_files) {
      foreach ($provider_by_files as $file => $provider) {
        // If a file is empty or its contents are commented out, return an empty
        // array instead of NULL for type consistency.
        $all[$provider] = $this->decode($file);
        $file_cache->set($file, $all[$provider]);
      }
    }

    return $all;
  }

  /**
   * Decode a JSON file.
   *
   * @param string $file
   *   JSON file path.
   * @return array
   */
  protected function decode($file) {
    return Json::decode(file_get_contents($file)) ?: [];
  }

  /**
   * Returns an array of file paths, keyed by provider.
   *
   * @return array
   */
  protected function findFiles() {
    $files = [];
    foreach ($this->directories as $provider => $directory) {
      $file = $directory . '/' . $provider . '.' . $this->name . '.' .  Json::getFileExtension();
      if (file_exists($file)) {
        $files[$provider] = $file;
      }
    }
    return $files;
  }

}