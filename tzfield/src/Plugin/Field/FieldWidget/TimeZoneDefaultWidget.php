<?php

namespace Drupal\tzfield\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the time zone default widget.
 *
 * @FieldWidget(
 *   id = "tzfield_default",
 *   label = @Translation("Time zone"),
 *   field_types = {
 *     "tzfield"
 *   }
 * )
 */
class TimeZoneDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $timezones = system_time_zones(!$element['#required'], TRUE);
    if ($exclude = $this->getFieldSetting('exclude')) {
      foreach ($timezones as $group_key => $timezone_group) {
        foreach ($timezone_group as $timezone => $timezone_label) {
          if (in_array($timezone, $exclude)) {
            unset($timezones[$group_key][$timezone]);
          }
          if (empty($timezones[$group_key])) {
            unset($timezones[$group_key]);
          }
        }
      }
    }
    $element['value'] = $element + [
      '#type' => 'select',
      '#options' => $timezones,
      '#default_value' => $items[$delta]->value ?? NULL,
    ];
    return $element;
  }

}
