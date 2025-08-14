<?php

namespace Plugins\Forum\Models;

use App\Core\Database;

class Post
{
    /**
     * Find all posts in a given topic.
     *
     * @param int $topic_id
     * @return array
     */
    public static function findAllByTopicId($topic_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "SELECT p.*, u.username as author_name
                FROM {$prefix}posts p
                JOIN {$prefix}users u ON p.user_id = u.id
                WHERE p.topic_id = ?
                ORDER BY p.created_at ASC";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return bool
     */
    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "INSERT INTO {$prefix}posts (topic_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('iis', $data['topic_id'], $data['user_id'], $data['content']);
        $success = $stmt->execute();

        if ($success) {
            \App\Core\Hooks::trigger('user_created_content', $data['user_id'], 'new_post');
        }

        return $success;
    }
}
