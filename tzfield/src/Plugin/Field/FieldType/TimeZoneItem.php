<?php

namespace Drupal\tzfield\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the time zone field type.
 *
 * @FieldType(
 *   id = "tzfield",
 *   label = @Translation("Time zone"),
 *   description = @Translation("This field stores a time zone in the database."),
 *   default_widget = "tzfield_default",
 *   default_formatter = "basic_string"
 * )
 */
class TimeZoneItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 50,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Time zone'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $max_length = 50;
    $constraints[] = $constraint_manager->create('ComplexData', [
      'value' => [
        'Length' => [
          'max' => $max_length,
          'maxMessage' => $this->t('%name: The time zone name may not be longer than @max characters.', [
            '%name' => $this->getFieldDefinition()->getLabel(),
            '@max' => $max_length,
          ]),
        ],
      ],
    ]);

    return $constraints;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = array_rand(system_time_zones());
    return $values;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public static function defaultFieldSettings() {
    return ['exclude' => []] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);

    $element['exclude'] = [
      '#title' => $this->t('Time zones to be excluded from the option list'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => system_time_zones(FALSE, TRUE),
      '#default_value' => $this->getSetting('exclude'),
      '#size' => 20,
      '#description' => $this->t('Any time zones selected here will be excluded from the allowed values.'),
    ];

    return $element;
  }

}
