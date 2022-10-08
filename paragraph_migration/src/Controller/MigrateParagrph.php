<?php

namespace Drupal\paragraph_migration\Controller;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;

class MigrateParagrph {

  public static function migrateParagraph($paragraphs_array, &$context){
    $message = 'Migrating Paragraphs...';
    $results = array();
    $i=0;
    $temp = "";
    foreach ($paragraphs_array as $key => $value) {
     if(is_numeric($value[0])){
      $field_max = !empty($value[1]) ? $value[1] : 0;
      $field_min = !empty($value[2]) ? $value[2] : 0;
      $field_profit = !empty($value[3]) ? $value[3] : 0;
      $textparagraph = Paragraph::create([
          'type' => 'calculator_attributes_for_online',
          'field_max' => array(
          "value"  =>  $field_max
          ),
          'field_min' => array(
          "value"  =>  $field_min
          ),
          'field_profit' => array(
          "value"  =>  $field_profit
          ),
        ]);
        $textparagraph->save();
        $nid = ($temp == $value[0]) ? $temp : $value[0];
        
        $node = Node::load($nid);
        $node->field_calculator_atr_onlin[] = array(
        'target_id' => $textparagraph->id(),
        'target_revision_id' => $textparagraph->getRevisionId(),
        );
        
        $results[] = $node->save();

        $temp = $value[0];
      }
      $i++;
    }
    $context['message'] = $message;
    $context['results'] = $results;
    //exit();
  }

  function deleteNodeExampleFinishedCallback($success, $results, $operations) {
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