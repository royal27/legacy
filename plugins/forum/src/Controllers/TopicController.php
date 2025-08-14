<?php

namespace Plugins\Forum\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use Plugins\Forum\Models\Forum;
use Plugins\Forum\Models\Topic;
use Plugins\Forum\Models\Post;

class TopicController extends Controller
{
    /**
     * Show the form for creating a new topic.
     */
    public function new()
    {
        $forum_id = $this->route_params['id'];
        $forum = Forum::findById($forum_id);

        if (!Auth::check()) {
            Session::flash('error', 'You must be logged in to create a topic.');
            header("Location: /forum/$forum_id");
            exit;
        }

        View::render('@forum_frontend/topic/new.php', [
            'title' => 'New Topic',
            'forum' => $forum
        ]);
    }

    /**
     * Handle the submission of a new topic.
     */
    public function create()
    {
        $forum_id = $_POST['forum_id'];

        if (!Auth::check()) {
            Session::flash('error', 'You must be logged in to create a topic.');
            header("Location: /forum/$forum_id");
            exit;
        }

        $topic_data = [
            'forum_id' => $forum_id,
            'user_id' => Auth::id(),
            'title' => $_POST['title'] ?? ''
        ];

        $post_data = [
            'user_id' => Auth::id(),
            'content' => $_POST['content'] ?? ''
        ];

        if (empty($topic_data['title']) || empty($post_data['content'])) {
            Session::flash('error', 'Title and content are required.');
            header("Location: /forum/topic/new/$forum_id");
            exit;
        }

        $new_topic_id = Topic::create($topic_data, $post_data);

        if ($new_topic_id) {
            Session::flash('success', 'Topic created successfully.');
            header("Location: /topic/$new_topic_id");
            exit;
        } else {
            Session::flash('error', 'Failed to create topic.');
            header("Location: /forum/$forum_id");
            exit;
        }
    }
}
