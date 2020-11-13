<?php


namespace Drupal\Tests\react_paragraphs_behaviors\Kernel\Plugin\paragraphs\Behavior;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Class ReactBehaviorsTests
 *
 * @group react_paragraphs
 * @coversDefaultClass \Drupal\react_paragraphs_behaviors\Plugin\paragraphs\Behavior\ReactBehaviors
 */
class ReactBehaviorsTest extends KernelTestBase {

  /**
   * @var \Drupal\paragraphs\ParagraphsTypeInterface
   */
  protected $paragraphType;

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'react_paragraphs_behaviors',
    'test_react_paragraphs_behaviors',
    'paragraphs',
    'file',
    'user',
  ];
  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('file');
    ParagraphsType::create([
      'id' => 'card',
      'label' => 'Card',
    ])->save();
    $this->paragraphType = ParagraphsType::load('card');
  }

  /**
   * The paragraph type matches the test yml, behaviors should be enabled.
   */
  public function testBehaviorEnabled() {
    $enabled_behaviors = $this->paragraphType->getEnabledBehaviorPlugins();
    $this->assertArrayHasKey('react_paragraphs:first', $enabled_behaviors);
    $this->assertArrayNotHasKey('react_paragraphs:second', $enabled_behaviors);
  }

  /**
   * The behavior form should have the elements form the yaml and default value.
   */
  public function testBehaviorForm() {
    $form = [];
    $form_state = new FormState();
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('getBehaviorSetting')
      ->will($this->returnCallback([$this, 'getBehaviorSettingCallback']));
    $element = $this->paragraphType->getEnabledBehaviorPlugins()['react_paragraphs:first']
      ->buildBehaviorForm($paragraph, $form, $form_state);

    $this->assertArrayHasKey('foo', $element);
    $this->assertArrayHasKey('bar', $element);
    $this->assertArrayHasKey('baz', $element);
    $this->assertEqual('FOOBARBAZ', $element['bar']['#default_value']);
  }

  /**
   * Mock paragraph get behaviors callback.
   */
  public function getBehaviorSettingCallback($plugin_id, $key) {
    if ($key == 'bar') {
      return 'FOOBARBAZ';
    }
  }

}
