<?php

namespace Drupal\bfri_dynamic_review\Plugin\WebformHandler;

use Drupal\node\Entity\Node;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Create a review entity from a webform submission.
 *
 *  @WebformHandler(
 *   id = "Create a Review",
 *   label = @Translation("Create a Review"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new review from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 *  )
 */
class CreateReviewWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = FALSE) {

    // Get an array of the values from the submission.
    $values = $webform_submission->getData();

    // Getting Current user id.
    $user = \Drupal::currentUser();
    $id = $user->id();
    $user_id = empty($id) ? 8 : $id;

    $productType = '';

    if (empty($values['produtcs'])) {
      $productType = $values['brouchers'];
    }
    else {
      $productType = $values['produtcs'];
    }

    // Node insert.
    $node_args['product'] = [
      'type' => 'review',
      'langcode' => 'en',
      'created' => time(),
      'changed' => time(),
      'uid' => $user_id,
      'status' => FALSE,
      'title' => $values['title'],
      'field_star_rating' => $values['rate_our_product'] * 20,
      'field_vocabulary_1' => 183,
      'field_end_page' => $productType,
      'field_testimonial_person' => $values['your_name'],
      'field_testimonial_venue' => $values['school_organization_name'],
      'body' => [
        'value' => $values['briefly_describe_how_well_our_fundraising_product_worked_for_you'],
        'format' => 'full_html',
      ],
    ];

    $node_args['company'] = [
      'type' => 'review',
      'langcode' => 'en',
      'created' => time(),
      'changed' => time(),
      'uid' => $user_id,
      'status' => FALSE,
      'title' => $values['title'],
      'field_star_rating' => $values['rate_our_service'] * 20,
      'field_vocabulary_1' => 1,
      'field_testimonial_person' => $values['your_name'],
      'field_testimonial_venue' => $values['school_organization_name'],
      'body' => [
        'value' => $values['please_summarize_your_overall_experience_with_us'],
        'format' => 'full_html',
      ],
    ];

    // Node insert.
    foreach ($node_args as $val) {
      $node = Node::create($val);
      $node->save();
    }

    $data = [
      [
        'order_id' => $values['order_id'],
        'product_variation' => $values['id'],
        'status' => 1,
      ],
    ];

    $database = \Drupal::database();
    $query = $database->insert('bfri_order_review')
      ->fields(['order_id', 'product_variation', 'status']);
    foreach ($data as $developer) {
      $query->values($developer);
    }
    $query->execute();
  }

}
