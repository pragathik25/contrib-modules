<?php

namespace Drupal\tzfield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'tzfield_date' formatter.
 *
 * @FieldFormatter(
 *   id = "tzfield_date",
 *   label = @Translation("Formatted current date"),
 *   field_types = {
 *     "tzfield"
 *   }
 * )
 */
class TimeZoneFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = $this->t('Date format string: :format', [':format' => $this->getSetting('format')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $location = '';
    foreach ($items as $delta => $item) {
      if (!isset($item->value)) {
        continue;
      }
      if ($item->value == 'Asia/Bangalore') {
        $location = $item->value;
        $item->value = 'Asia/Kolkata';
      }
      if ($item->value == 'Asia/Beijing') {
        $location = $item->value;
        $item->value = 'Asia/Shanghai';
      }
      if ($item->value == 'Asia/Penang') {
        $location = $item->value;
        $item->value = 'Asia/Kuala_Lumpur';
      }
      if ($item->value == 'Europe/Munich') {
        $location = $item->value;
        $item->value = 'Europe/Berlin';
      }
      // Render each element as markup.
      $dateTime = new DrupalDateTime();
      $dateTime->setTimezone(new \DateTimeZone($item->value));
      $element[$delta] = ['#markup' => $dateTime->format($this->getSetting('format'))];
      if ($location == 'Asia/Bangalore' || $location == 'Asia/Beijing' || $location == 'Europe/Munich' || $location == 'Asia/Penang') {
        $item->value = $location;
      }
      if ($item->value == 'Asia/Singapore') {
        $element[$delta] = ['#markup' => '+08:00 SGT'];
      }
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public static function defaultSettings() {
    return [
      'format' => 'T',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line Core has not yet documented this method properly.
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['format'] = [
      '#title' => $this->t('Date format string'),
      '#description' => $this->t('Enter a <a rel="noreferrer" href="https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters">PHP date format string</a>, e.g. <em>T</em> to display the current time zone abbreviation.'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('format'),
    ];
    return $form;
  }

}
