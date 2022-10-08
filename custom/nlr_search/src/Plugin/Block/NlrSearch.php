<?php

namespace Drupal\nlr_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Block for NLR Google CSE without branding
 *
 * @Block(
 *   id = "nlr_search_block",
 *   admin_label = @Translation("NLR Search"),
 *   category = @Translation("NLR Search"),
 * )
 */
class NlrSearch extends BlockBase {

  protected $markup = '
  <div class="searchBar">
    <div id="cse">
      <gcse:searchbox-only linktarget="/nlr-legal-analysis-and-news-database-search" queryparametername"qnlr"></gcse:searchbox-only>
    </div>
  </div>';

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t($this->markup),
    ];
  }
}
