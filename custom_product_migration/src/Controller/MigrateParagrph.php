<?php

namespace Drupal\custom_product_migration\Controller;

use Drupal\commerce_product\Entity\Product;
use Drupal\paragraphs\Entity\Paragraph;

/**
 *
 */
class MigrateParagrph {

  /**
   *
   */
  public static function migrateParagraph($paragraphs_array, &$context) {
    $message = 'Migrating Paragraphs...';
    $results = [];

    $i = 0;
    $temp = "";

    foreach ($paragraphs_array as $key => $value) {
      if (is_numeric($value[0])) {
        $field_max = !empty($value[1]) ? $value[1] : 0;
        $field_min = !empty($value[2]) ? $value[2] : 0;
        $field_profit = !empty($value[3]) ? $value[3] : 0;

        $textparagraph = Paragraph::create([
          'type' => 'calculator_attributes_for_online',
          'field_max' => [
            "value"  => $field_max,
          ],
          'field_min' => [
            "value"  => $field_min,
          ],
          'field_profit' => [
            "value"  => $field_profit,
          ],
        ]);

        $textparagraph->save();
        $pid = ($temp == $value[0]) ? $temp : $value[0];
        $product = Product::load((int) $pid);
        if (!empty($product)) {
          $product->field_calculator_attributes_para[] = [
            'target_id' => $textparagraph->id(),
            'target_revision_id' => $textparagraph->getRevisionId(),
          ];

          // \Drupal::logger('some_channel_name')->warning('<pre><code>' . print_r($product, TRUE) . '</code></pre>');
          $results[] = $product->save();
          $temp = $value[0];
        }
      }
      $i++;
    }
    $context['message'] = $message;
    $context['results'] = $results;
    // exit();
  }

  /**
   *
   */
  public function deleteNodeExampleFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }

}
