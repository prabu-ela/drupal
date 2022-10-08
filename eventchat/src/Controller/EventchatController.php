<?php

namespace Drupal\eventchat\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

/**
 * Event Chat.
 */
class EventchatController extends ControllerBase {

  /**
   * Function to render drupalchat app settings iframe.
   */
  public function eventchatappsettings() {

    // Load the current user.
    $user = User::load(\Drupal::currentUser()->id());

    // $user_name = $user->getUsername();
    $user_name = $user->name->value;

    if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {
      $token = $_SESSION['token'];
    }
    else {
      $json = drupalchatController::_drupalchat_get_auth([]);
      $token = $json->key;
    }

    $drupalchat_host = DRUPALCHAT_EXTERNAL_A_HOST;
    $host = explode("/", $drupalchat_host);
    $host_name = $host[2];

    $dashboardUrl = "//" . DRUPALCHAT_EXTERNAL_CDN_HOST . "/apps/dashboard/#/app-settings?sessid=" . $token . "&hostName=" . $host_name . "&hostPort=" . DRUPALCHAT_EXTERNAL_A_PORT;

    $form = [];
    $form['eventchat_app_dashboard'] = [
      '#type' => 'button',
      '#attributes' => ['onClick' => 'window.open("' . $dashboardUrl . '","_blank")'],
      '#value' => t('Click here to open App Dashboard'),
    ];

    return $form;

  }

  /**
   * Assign repo user.
   */
  public function assignuser() {
    $id = \Drupal::request()->request->get('user_id');
    $qid = \Drupal::request()->request->get('queue_id');
    $database = \Drupal::database();
    $result = $database->update('eventchat_queue')
      ->fields([
        'handoff' => $id,
        'status' => 1,
      ])
      ->condition('qid', $qid, '=')->execute();
    echo ($result == 1) ? 200 : 400;
    die;

  }

  /**
   * Queue save functionality.
   */
  public function saveqdata() {
    $queue_data = \Drupal::request()->request->get('queue_data');
    $queue_type = \Drupal::request()->request->get('queue_type');
    $uid = \Drupal::currentUser()->id();
    $database = \Drupal::database();
    $result = $database->insert('eventchat_queue')
      ->fields([
        'type' => $queue_type,
        'uid' => $uid,
        'details' => $queue_data,
        'status' => 0,
        'created' => time(),
      ])
      ->execute();
    echo !empty($result) ? $result : NULL;
    die;
  }

  /**
   * Assigned data get.
   */
  public function getdata() {

    // Getting query params.
    $queue_data = \Drupal::request()->request->get('queue_type');

    // Fetching user id.
    $user_repo_id = \Drupal::currentUser()->id();

    // Database Connectivity.
    $database = \Drupal::database();

    if (!empty($queue_data)) {
      $query = $database->query("SELECT *  FROM eventchat_queue WHERE type = $queue_data and handoff = $user_repo_id and status=1 ");
    }
    else {
      $query = $database->query("SELECT *  FROM eventchat_queue WHERE status=1 and handoff = $user_repo_id");
    }
    $result = $query->fetchAll();

    $html = '<a id="assign">Assigned to Me <span>(' . count($result) . ')</span></a><div id="assignsub">';

    foreach ($result as $key => $rl) {
      $user = User::load($rl->uid);
      if ($rl->status == 1) {
        $difference = date_diff(date("Y-m-d H:i:s", $rl->created), date('Y-m-d H:i:s'));
        $minutes = $difference->days * 24 * 60;
        $minutes += $difference->h * 60;
        $minutes += $difference->i;
        $html .= '<div class="chat_profile">
       <div class="visible_chater_assign">
          <div class="user_letter">' . ucfirst(substr($user->name->value, 0, 1)) . '</div>
          <div class="dtext">
             <p class="user_clickchat" data-queue="' . $rl->qid . '" data="' . $user->name->value . '" data-id="' . $rl->uid . '">' . $user->name->value . '</p>
             <p>' . $rl->details . '</p>
          </div>
          <div class="time">' . \Drupal::service('date.formatter')->formatInterval(REQUEST_TIME - $rl->created) . ' ago</div>    
       </div>
      <div class="sub_btn">
        <a id="assign_process" class="assign_process" data-user="' . $user->name->value . '" data="' . $rl->qid . '">Assign</a>
        <a id="unassign_process" class="unassign_process" data="' . $rl->qid . '">Mark Complete</a>
        <a id="chat_history" class="view_chat_history" data="' . $rl->qid . '">View Chat History</a>
      </div>
    </div>';
      }

    }
    $html .= '</div>';
    echo $html;
    die;

  }

  /**
   * Unassigned data get.
   */
  public function getdataunassign() {
    $queue_data = \Drupal::request()->request->get('queue_type');
    $database = \Drupal::database();
    if (!empty($queue_data)) {
      $query = $database->query("SELECT *  FROM eventchat_queue WHERE type = $queue_data and status=0 ");
    }
    else {
      $query = $database->query("SELECT *  FROM eventchat_queue WHERE status=0 ");
    }
    $result = $query->fetchAll();

    $user_repo_id = \Drupal::currentUser()->id();

    $html = '<a id="unassign">Unassigned <span>(' . count($result) . ')</span></a><div id="unassignsub">';
    foreach ($result as $key => $rl) {
      $user = User::load($rl->uid);
      if ($rl->status == 0) {
        $difference = date_diff(date("Y-m-d H:i:s", $rl->created), date('Y-m-d H:i:s'));
        $minutes = $difference->days * 24 * 60;
        $minutes += $difference->h * 60;
        $minutes += $difference->i;
        $html .= '<div class="chat_profile" data-chatqid ="' . $rl->qid . '" >
       <div class="visible_chater_assign">
       <div class="user_letter">' . ucfirst(substr($user->name->value, 0, 1)) . '</div>
          <div class="dtext">
             <p class="user_clickchat unassigned_terms" data-queue="' . $rl->qid . '" data="' . $user->name->value . '" data-id="' . $rl->uid . '" data-repo="' . $user_repo_id . '">' . $user->name->value . '</p>
             <p>' . $rl->details . '</p>
          </div>
          <div class="time">' . \Drupal::service('date.formatter')->formatInterval(REQUEST_TIME - $rl->created) . ' ago</div>
       </div>
    
       <div class="sub_btn">
          <a id="assign_process" class="assign_process" data-user="' . $user->name->value . '" data="' . $rl->qid . '">Assign</a>
          <a id="assign_process_me" class="assign_process_me" data-user="' . $user->name->value . '" data-id="' . $user_repo_id . '" data-userid="'. $rl->uid .'" data="' . $rl->qid . '">Assign to me</a>
          <a id="unassign_process" class="unassign_process" data="' . $rl->qid . '">Mark Complete</a>
          <a id="chat_history" class="view_chat_history" data="' . $rl->qid . '">View Chat History</a>
       </div>
    </div>';

      }

    }
    $html .= '</div>';
    echo $html;
    die;
  }

  /**
   * Assign task in the representative side.
   */
  public function assignedtaskrepo() {
    $queue_data = \Drupal::request()->request->get('queue_type');
    $user_repo_id = \Drupal::currentUser()->id();
    $database = \Drupal::database();
    $query = $database->query("SELECT *  FROM eventchat_queue WHERE type = $queue_data and handoff = $user_repo_id and status=1 ");
    $result = $query->fetchAll();

    // Here you can use drupal's format_date(),or some custom php date formate.
    $html = '<a id="assign">Assigned to Me <span>(' . count($result) . ')</span></a><div id="assignsub">';
    foreach ($result as $key => $rl) {
      $user = User::load($rl->uid);
      if ($rl->status == 1) {
        $difference = date_diff(date("Y-m-d H:i:s", $rl->created), date('Y-m-d H:i:s'));
        $minutes = $difference->days * 24 * 60;
        $minutes += $difference->h * 60;
        $minutes += $difference->i;
        $html .= '<div class="chat_profile">
       <div class="visible_chater_assign">
       <div class="user_letter">' . ucfirst(substr($user->name->value, 0, 1)) . '</div>
          <div class="dtext">
             <p class="user_clickchat" data-queue="' . $rl->qid . '" data="' . $user->name->value . '" data-id="' . $rl->uid . '">' . $user->name->value . '</p>
             <p>' . $rl->details . '</p>
          </div>
          <div class="time">' . \Drupal::service('date.formatter')->formatInterval(REQUEST_TIME - $rl->created) . ' ago</div>
       </div>
      </div>
       <div class="sub_btn">
          <a id="assign_process" class="assign_process" data-user="' . $user->name->value . '" data="' . $rl->qid . '">Assign</a>
          <a id="unassign_process" class="unassign_process" data="' . $rl->qid . '">Mark Complete</a>
          <a id="chat_history" class="view_chat_history" data="' . $rl->qid . '">View Chat History</a>
          <a id="chat_process" data-id="' . $rl->uid . '" data="' . $user->name->value . '">Chat with user</a>
       </div>
    </div>';

      }

    }
    $html .= '</div>';
    echo $html;
    die;

  }

  /**
   * View history show.
   */
  public function getthreads() {
    $queue_id = \Drupal::request()->request->get('queue_id');
    $database = \Drupal::database();
    $query = $database->query("SELECT *  FROM eventchat_queue WHERE qid= $queue_id");
    $result = $query->fetchAll();
    $message = [];
    if (count($result) > 0) {
      $handoff = $result[0]->handoff;
      $usersId = $result[0]->uid;
      $timestart = $result[0]->created;
      $apikey = 'xcisgjz8RojfLN-ew2k8Fk_Stb5jGcu02uci34Mm3hYW91663';
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://api.iflychat.com/api/1.1/threads/get");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $headers = ['Content-Type: application/json'];
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'api_key' => $apikey,
        'from_id' => $usersId,
        'to_id' => $handoff,
      ]
      ));
      $response = curl_exec($ch);
      $message = json_decode($response);

      curl_close($ch);

      $html = "";
      if (empty($message)) {
        $html .= "<div>There is no history</div>";
      }
      else {
        foreach ($message as $m_chat) {
          $html .= "<div class='chat_data'><div class='name_user_chat'><p class='chat_user_title'>" . $m_chat->from_name . "</p><p class='chat_created'>" . date('Y-m-d h:i A', $m_chat->time) . "</p><p class='message_chat'>" . $m_chat->message . "</div></div>";
        }
      }

      echo $html;
      die;

    }

  }

  /**
   * Mark Complete chat function.
   */
  public function markcomplete() {
    $qid = \Drupal::request()->request->get('queue_id');
    $database = \Drupal::database();
    $result = $database->update('eventchat_queue')->fields(['status' => 3])->condition('qid', $qid, '=')->execute();
    echo ($result == 1) ? 200 : 400;
    die;
  }

  /**
   * Check side part functionality.
   */
  public function checkrepoget() {
    $qid = \Drupal::request()->request->get('queue_id');
    $database = \Drupal::database();
    $query = $database->query("SELECT handoff  FROM eventchat_queue WHERE  qid = $qid AND handoff IS NOT NULL ");
    $result = $query->fetchAll();
    if (count($result) > 0) {
      echo 200;
      die;
    }
    else {
      echo 400;
      die;
    }
  }

  /**
   * User queue has been complete.
   */
  public function checkusersidemarkcomplte() {
    $qid = \Drupal::request()->request->get('queue_id');
    $database = \Drupal::database();
    $query = $database->query("SELECT *  FROM eventchat_queue WHERE  qid = $qid AND status = 3 ");
    $result = $query->fetchAll();
    if (count($result) > 0) {
      echo 200;
      die;
    }
    else {
      echo 400;
      die;
    }
  }

  /**
   * Chat pop up close function check.
   */
  public function getusertoclosechat() {
    $qid = \Drupal::request()->request->get('queue_id');
    $database = \Drupal::database();
    $query = $database->query("SELECT *  FROM eventchat_queue WHERE  qid = $qid");
    $result = $query->fetchAll();
    if (count($result) > 0) {
      $handoff = $result[0]->handoff;
      $user = User::load($handoff);
      $user_name = $user->name->value;
      $data = [
        'id' => $handoff,
        'name' => $user_name,
      ];
      echo json_encode($data);
      die;
    }
    else {
      echo 400;
      die;
    }
  }

  public function getnewunassigneddata() {
    $queue_data_id = \Drupal::request()->request->get('queue_id');
  //  print_r($queue_data_id);
    $queue_data_id_e = implode(',',$queue_data_id);
    $queue_data = \Drupal::request()->request->get('queue_type');
    $database = \Drupal::database();
   // echo "SELECT *  FROM eventchat_queue WHERE type = $queue_data and status=0 and qid NOT IN ($queue_data_id_e)";die;
    if (!empty($queue_data)) {
      $query = $database->query("SELECT *  FROM eventchat_queue WHERE type = $queue_data and status=0 and qid NOT IN ($queue_data_id_e)");
      $result = $query->fetchAll();
    }

    //print_r($result);die;

    if(count($result)  > 0){
      
  

      $user_repo_id = \Drupal::currentUser()->id();

      $html = '';
      foreach ($result as $key => $rl) {
        $user = User::load($rl->uid);
        if ($rl->status == 0) {
          $difference = date_diff(date("Y-m-d H:i:s", $rl->created), date('Y-m-d H:i:s'));
          $minutes = $difference->days * 24 * 60;
          $minutes += $difference->h * 60;
          $minutes += $difference->i;
          $html .= '<div class="chat_profile" data-chatqid ="' . $rl->qid . '" >
        <div class="visible_chater_assign">
        <div class="user_letter">' . ucfirst(substr($user->name->value, 0, 1)) . '</div>
            <div class="dtext">
              <p class="user_clickchat unassigned_terms" data-queue="' . $rl->qid . '" data="' . $user->name->value . '" data-id="' . $rl->uid . '" data-repo="' . $user_repo_id . '">' . $user->name->value . '</p>
              <p>' . $rl->details . '</p>
            </div>
            <div class="time">' . \Drupal::service('date.formatter')->formatInterval(REQUEST_TIME - $rl->created) . ' ago</div>
        </div>
      
        <div class="sub_btn">
            <a id="assign_process" class="assign_process" data-user="' . $user->name->value . '" data="' . $rl->qid . '">Assign</a>
            <a id="assign_process_me" class="assign_process_me" data-user="' . $user->name->value . '" data-id="' . $user_repo_id . '" data-userid="'. $rl->uid .'" data="' . $rl->qid . '">Assign to me</a>
            <a id="unassign_process" class="unassign_process" data="' . $rl->qid . '">Mark Complete</a>
            <a id="chat_history" class="view_chat_history" data="' . $rl->qid . '">View Chat History</a>
        </div>
      </div>';

        }

      }
      echo $html;
      die;
    }
    else{
      echo 400;
      die;
    }
  }


}
