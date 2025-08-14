<?php

namespace Plugins\Forum\Models;

use App\Core\Database;

class Topic
{
    /**
     * Find all topics in a given forum.
     *
     * @param int $forum_id
     * @return array
     */
    public static function findAllByForumId($forum_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "SELECT t.*, u.username as author_name
                FROM {$prefix}topics t
                JOIN {$prefix}users u ON t.user_id = u.id
                WHERE t.forum_id = ?
                ORDER BY t.is_sticky DESC, t.last_post_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $forum_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a topic by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("SELECT * FROM {$prefix}topics WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create a new topic and its first post in a transaction.
     *
     * @param array $topic_data
     * @param array $post_data
     * @return int|false The new topic ID on success, false on failure.
     */
    public static function create($topic_data, $post_data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $db->begin_transaction();

        try {
            // Create the topic
            $sql_topic = "INSERT INTO {$prefix}topics (forum_id, user_id, title, created_at) VALUES (?, ?, ?, NOW())";
            $stmt_topic = $db->prepare($sql_topic);
            $stmt_topic->bind_param('iis', $topic_data['forum_id'], $topic_data['user_id'], $topic_data['title']);
            $stmt_topic->execute();

            $new_topic_id = $db->insert_id;
            $stmt_topic->close();

            // Create the first post
            $sql_post = "INSERT INTO {$prefix}posts (topic_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";
            $stmt_post = $db->prepare($sql_post);
            $stmt_post->bind_param('iis', $new_topic_id, $post_data['user_id'], $post_data['content']);
            $stmt_post->execute();
            $stmt_post->close();

            // Update the topic's last post info
            self::updateLastPost($new_topic_id, $topic_data['user_id']);

            $db->commit();

            // Award points
            \App\Core\Hooks::trigger('user_created_content', $topic_data['user_id'], 'new_topic');
            \App\Core\Hooks::trigger('user_created_content', $post_data['user_id'], 'new_post');
            return $new_topic_id;

        } catch (\Exception $e) {
            $db->rollback();
            return false;
        }
    }

    /**
     * Update the last post information for a topic.
     *
     * @param int $topic_id
     * @param int $user_id
     * @return bool
     */
    public static function updateLastPost($topic_id, $user_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "UPDATE {$prefix}topics
                SET last_post_at = NOW(), last_post_user_id = ?
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('ii', $user_id, $topic_id);

        return $stmt->execute();
    }
}
