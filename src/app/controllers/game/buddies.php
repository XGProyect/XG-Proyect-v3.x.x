<?php
/**
 * Buddies Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace App\controllers\game;

use App\core\BaseController;
use App\core\entities\BuddyEntity;
use App\libraries\buddies\Buddy;
use App\core\enumerators\BuddiesStatusEnumerator as BuddiesStatus;
use App\libraries\Functions;
use App\libraries\TimingLibrary as Timing;
use App\libraries\Users;
use Exception;

/**
 * Buddies Class
 */
class Buddies extends BaseController
{
    /**
     * The module ID
     *
     * @var int
     */
    public const MODULE_ID = 20;

    /**
     * Contains a Buddy object
     *
     * @var \Buddy
     */
    private $buddy = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Model
        parent::loadModel('game/buddies');

        // load Language
        parent::loadLang(['game/buddies']);

        // init a new buddy object
        $this->setUpBudies();
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new buddy object that will handle all the buddies
     * creation methods and actions
     *
     * @return void
     */
    private function setUpBudies()
    {
        $this->buddy = new Buddy(
            $this->Buddies_Model->getBuddiesByUserId($this->user['user_id']),
            $this->user['user_id']
        );
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction()
    {
        $mode = filter_input(INPUT_GET, 'mode', FILTER_VALIDATE_INT);
        $sm = filter_input(INPUT_GET, 'sm', FILTER_VALIDATE_INT);

        $allowed_modes = [
            1 => 'execAction', // exec one of the allowed actions
            2 => 'buildRequestForm', // show the send request form
        ];

        $allowed_actions = [
            1 => 'removeRequest', // applies for reject or cancel
            2 => 'acceptRequest', // accept an incoming request
            3 => 'sendRequest', // send the request
        ];

        if (isset($allowed_modes[$mode])) {
            if (isset($allowed_actions[$sm])) {
                $this->{$allowed_modes[$mode]}($allowed_actions[$sm]);
            } else {
                if ($allowed_modes[$mode] == 'buildRequestForm') {
                    $this->{$allowed_modes[$mode]}();
                }
            }
        }
    }

    /**
     * Exec provided action
     *
     * @param string $action Action
     *
     * @throws Exception
     */
    private function execAction($action)
    {
        try {
            if (empty($action)) {
                throw new Exception('Action cannot be empty');
            }

            $this->{$action}();
            Functions::redirect('game.php?page=buddies');
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Reject, Cancel, Delete or Remove a buddy request
     *
     * @return void
     */
    private function removeRequest()
    {
        $bid = filter_input(INPUT_GET, 'bid', FILTER_VALIDATE_INT);

        $buddy = new BuddyEntity(
            $this->Buddies_Model->getBuddyDataByBuddyId($bid)
        );

        if ($buddy->getBuddyStatus() == BuddiesStatus::isNotBuddy) {
            if ($buddy->getBuddySender() != $this->user['user_id']) {
                $this->sendMessage($buddy->getBuddySender(), 1);
            } elseif ($buddy->getBuddySender() == $this->user['user_id']) {
                $this->sendMessage($buddy->getBuddyReceiver(), 1);
            }
        } else {
            if ($buddy->getBuddySender() != $this->user['user_id']) {
                $this->sendMessage($buddy->getBuddySender(), 2);
            } elseif ($buddy->getBuddySender() == $this->user['user_id']) {
                $this->sendMessage($buddy->getBuddyReceiver(), 2);
            }
        }

        $this->Buddies_Model->removeBuddyById($bid, $this->user['user_id']);
    }

    /**
     * Accept a buddy request
     *
     * @return void
     */
    private function acceptRequest()
    {
        $bid = filter_input(INPUT_GET, 'bid', FILTER_VALIDATE_INT);

        $buddy = new BuddyEntity(
            $this->Buddies_Model->getBuddyDataByBuddyId($bid)
        );

        $this->sendMessage($buddy->getBuddySender(), 3);

        $this->Buddies_Model->setBuddyStatusById($bid, $this->user['user_id']);
    }

    /**
     * Send a buddy request
     *
     * @return void
     */
    private function sendRequest()
    {
        $user = filter_input(INPUT_POST, 'user', FILTER_VALIDATE_INT);
        $text = filter_input(INPUT_POST, 'text');

        $buddy = null;

        if ($buddy_data = $this->Buddies_Model->getBuddyIdByReceiverAndSender($user, $this->user['user_id'])) {
            $buddy = new BuddyEntity($buddy_data);
        }

        if (!is_null($buddy) && $buddy->getBuddyId() != 0) {
            Functions::message($this->langs->line('bu_request_exists'), 'game.php?page=buddies', 3, true);
        }

        $this->sendMessage($user, 4);

        $this->Buddies_Model->insertNewBuddyRequest(
            $user,
            $this->user['user_id'],
            $text
        );
    }

    /**
     * Send message
     *
     * @param int $to   To
     * @param int $type Type
     *
     * @return void
     */
    private function sendMessage($to, $type)
    {
        $types = [
            1 => [
                'title' => 'bu_rejected_title',
                'text' => 'bu_rejected_text',
            ],
            2 => [
                'title' => 'bu_deleted_title',
                'text' => 'bu_deleted_text',
            ],
            3 => [
                'title' => 'bu_accepted_title',
                'text' => 'bu_accepted_text',
            ],
            4 => [
                'title' => 'bu_to_accept_title',
                'text' => 'bu_to_accept_text',
            ],
        ];

        Functions::sendMessage(
            $to,
            $this->user['user_id'],
            '',
            5,
            $this->user['user_name'],
            $this->langs->line($types[$type]['title']),
            str_replace(
                '%u',
                $this->user['user_name'],
                $this->langs->line($types[$type]['text'])
            )
        );
    }

    /**
     * Build the buddy request form page
     *
     * @return void
     */
    private function buildRequestForm()
    {
        $user = filter_input(INPUT_GET, 'u', FILTER_VALIDATE_INT);

        if ($user == $this->user['user_id']) {
            Functions::message($this->langs->line('bu_cannot_request_yourself'), 'game.php?page=buddies', 2, true);
        }

        $user = $this->Buddies_Model->checkIfBuddyExists($user);

        if (!$user) {
            Functions::redirect('game.php?page=buddies');
        }

        $this->page->display(
            $this->template->set(
                'game/buddies_request',
                array_merge(
                    ['js_path' => JS_PATH],
                    $user,
                    $this->langs->language
                )
            )
        );
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage()
    {
        /**
         * Parse the items
         */
        $page = [];
        $page['list_of_requests_received'] = $this->buildListOfRequestsReceived();
        $page['list_of_requests_sent'] = $this->buildListOfRequestsSent();
        $page['list_of_buddies'] = $this->buildListOfBuddies();

        // display the page
        $this->page->display(
            $this->template->set(
                'game/buddies_view',
                array_merge($page, $this->langs->language)
            )
        );
    }

    /**
     * Build the list of requests received
     *
     * @return string
     */
    private function buildListOfRequestsReceived()
    {
        $received_requests = $this->buddy->getReceivedRequests();
        $rows = [];

        if ($this->hasAny($received_requests)) {
            foreach ($received_requests as $received) {
                $rows[] = $this->extractPlayerData($received);
            }
        }

        return $rows;
    }

    /**
     * Build the list of requests sent
     *
     * @return string
     */
    private function buildListOfRequestsSent()
    {
        $requests_sent = $this->buddy->getSentRequests();
        $rows = [];

        if ($this->hasAny($requests_sent)) {
            foreach ($requests_sent as $sent) {
                $rows[] = $this->extractPlayerData($sent);
            }
        }

        return $rows;
    }

    /**
     * Build the list of buddies
     *
     * @return array
     */
    private function buildListOfBuddies()
    {
        $buddies = $this->buddy->getBuddies();
        $rows = [];

        if ($this->hasAny($buddies)) {
            foreach ($buddies as $buddy) {
                $rows[] = $this->extractPlayerData($buddy);
            }
        }

        return $rows;
    }

    /**
     * Extract player data based on provided object
     *
     * @param BuddyEntity $buddy Buddy Entity Object
     *
     * @return arrau
     */
    private function extractPlayerData(BuddyEntity $buddy)
    {
        if ($buddy->getBuddySender() == $this->user['user_id']) {
            $id_to_get = $buddy->getBuddyReceiver();
        } else {
            $id_to_get = $buddy->getBuddySender();
        }

        // get user data
        $user_data = $this->Buddies_Model->getBuddyDataById($id_to_get);

        return [
            'id' => $user_data['user_id'],
            'username' => $user_data['user_name'],
            'ally_id' => $user_data['alliance_id'],
            'alliance_name' => $user_data['alliance_name'],
            'galaxy' => $user_data['user_galaxy'],
            'system' => $user_data['user_system'],
            'planet' => $user_data['user_planet'],
            'text' => $this->setText($buddy, $user_data['user_onlinetime']),
            'action' => $this->setAction($buddy),
        ];
    }

    /**
     * Set the text
     *
     * @param BuddyEntity $buddy Buddy
     *
     * @return string
     */
    private function setText(BuddyEntity $buddy, $online_time)
    {
        if ($buddy->getBuddyStatus() == BuddiesStatus::isBuddy) {
            return Timing::setOnlineStatus($online_time);
        } else {
            return $buddy->getRequestText();
        }
    }

    /**
     * Set action button based on the request status
     *
     * @param BuddyEntity $buddy Buddy
     *
     * @return string
     */
    private function setAction(BuddyEntity $buddy)
    {
        $bid = $buddy->getBuddyId();

        if ($buddy->getBuddyStatus() == BuddiesStatus::isBuddy) {
            $url = $this->generateUrl($bid, 1, $this->langs->line('bu_delete'));
        } else {
            if ($buddy->getBuddySender() == $this->user['user_id']) {
                $url = $this->generateUrl($bid, 1, $this->langs->line('bu_cancel_request'));
            } else {
                $url = $this->generateUrl($bid, 2, $this->langs->line('bu_accept'));
                $url .= '<br/>';
                $url .= $this->generateUrl($bid, 1, $this->langs->line('bu_decline'));
            }
        }

        return $url;
    }

    /**
     * Generate the URL
     *
     * @param int    $buddy_id  Buddy ID
     * @param int    $sm        Action
     * @param string $lang_line Lang Line
     *
     * @return string
     */
    private function generateUrl($buddy_id, $sm, $lang_line)
    {
        return '<a href="game.php?page=buddies&mode=1&sm=' . $sm . '&bid=' . $buddy_id . '">' . $lang_line . '</a>';
    }

    /**
     * Check if there's anything
     *
     * @param array $array Array
     *
     * @return boolean
     */
    private function hasAny($array)
    {
        return (count($array) > 0);
    }
}
