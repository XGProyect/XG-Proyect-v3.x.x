<?php
/**
 * Messages Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\adm;

use App\core\Model;

/**
 * Messages Class
 */
class Messages extends Model
{
    /**
     * Get all messages filtered
     *
     * @param array $to_query
     * @return array
     */
    public function getAllMessagesFiltered(array $to_query): array
    {
        $result = $this->db->queryFetchAll(
            $this->buildSearchQuery($to_query)
        );

        if ($result) {
            return $result;
        }

        return [];
    }

    /**
     * Delete all messages in the set of IDs
     *
     * @param array $ids
     * @return void
     */
    public function deleteAllMessagesByIds(array $ids): void
    {
        $this->db->query(
            "DELETE FROM `" . MESSAGES . "`
            WHERE `message_id` IN (" . join(',', $ids) . ")"
        );
    }

    /**
     * Build the search query
     *
     * @param array $to_query
     * @return string
     */
    private function buildSearchQuery(array $to_query): string
    {
        // search by username or user id
        if (isset($to_query['message_user']) && !empty($to_query['message_user'])) {
            $message_user = $to_query['message_user'];

            if (is_string($message_user)) {
                $query_search['message_user'] = "(`message_sender` = (SELECT `user_id` FROM `" . USERS . "` WHERE `user_name` = '" . $message_user . "' LIMIT 1) OR `message_receiver` = (SELECT `user_id` FROM `" . USERS . "` WHERE `user_name` = '" . $message_user . "' LIMIT 1))";
            }
        }

        // search by subject
        if (isset($to_query['message_subject']) && !empty($to_query['message_subject'])) {
            $query_search['message_subject'] = "(`message_subject` LIKE '%" . $to_query['message_subject'] . "%')";
        }

        // search by date
        if (isset($to_query['message_date'])) {
            if ((bool) strtotime($to_query['message_date'])) {
                $start_date = strtotime($to_query['message_date'] . ' 00:00:00');
                $end_date = strtotime($to_query['message_date'] . ' 23:59:59');

                $query_search['message_time'] = "(`message_time` >= '" . $start_date . "' AND `message_time` <= '" . $end_date . "')";
            }
        }

        // search by message type
        if (isset($to_query['message_type']) && !empty($to_query['message_type'])) {
            $message_type = (int) $to_query['message_type'];

            if ($message_type > 0) {
                $query_search['message_type'] = "(`message_type` = '" . $message_type . "')";
            }
        }

        // search by message text
        if (isset($to_query['message_text']) && !empty($to_query['message_text'])) {
            $message_text = (string) $to_query['message_text'];

            $query_search['message_text'] = "(`message_text` LIKE '%" . $message_text . "%')";
        }

        if (isset($query_search)) {
            $search_query_string = "SELECT m.*, u1.`user_name` AS `sender`, u2.`user_name` AS `receiver`
                FROM `" . MESSAGES . "` AS m
                LEFT JOIN `" . USERS . "` as u1 ON u1.`user_id` = m.`message_sender`
                LEFT JOIN `" . USERS . "` as u2 ON u2.`user_id` = m.`message_receiver`
                WHERE ";

            foreach ($query_search as $content) {
                $search_query_string .= $content . ' AND ';
            }

            $search_query_string = rtrim($search_query_string, ' AND ') . ';';

            return $search_query_string;
        }

        return '';
    }
}
