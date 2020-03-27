<?php

namespace Drupal\Tests\react_paragraphs\Unit;

use Drupal\Component\Serialization\YamlPecl;
use Drupal\Tests\UnitTestCase;

/**
 * Class YamlLint.
 *
 * @package Drupal\Tests\react_paragraphs\Unit
 */
class YamlLint extends UnitTestCase {

  /**
   * Validate all yaml files using pecl library that acquia has installed.
   */
  public function testYamlLintFiles() {
    $yaml_files = $this->rglob(dirname(__DIR__, 3) . '/*.yml');
    foreach ($yaml_files as $file_path) {
      $file_data = NULL;
      $message = '';
      try {
        $file_data = YamlPecl::decode(file_get_contents($file_path));
      }
      catch (\Exception $e) {
        // We want to provide our own error message.
        $message = sprintf('%s linting failed. Message: %s', $file_path, $e->getMessage());
      }
      $this->assertIsArray($file_data, $message);
    }
  }

  protected function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
      $files = array_merge($files, $this->rglob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
  }

}
