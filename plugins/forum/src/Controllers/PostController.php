<?php

namespace Plugins\Forum\Controllers;

use App\Core\Controller;
use App\Core\View;
use Plugins\Forum\Models\Topic;
use Plugins\Forum\Models\Post;

class PostController extends Controller
{
    /**
     * Show all posts in a specific topic.
     */
    public function show()
    {
        $topic_id = $this->route_params['id'];

        $topic = Topic::findById($topic_id);
        $posts = Post::findAllByTopicId($topic_id);

        if (!$topic) {
            die('Topic not found.');
        }

use App\Core\Auth;
use App\Core\Session;

        View::render('@forum_frontend/post/index.php', [
            'title' => $topic['title'],
            'topic' => $topic,
            'posts' => $posts
        ]);
    }

    /**
     * Handle the submission of a new reply.
     */
    public function reply()
    {
        $topic_id = $this->route_params['id'];

        if (!Auth::check()) {
            Session::flash('error', 'You must be logged in to reply.');
            header("Location: " . url("topic/$topic_id"));
            exit;
        }

        $data = [
            'topic_id' => $topic_id,
            'user_id' => Auth::id(),
            'content' => $_POST['content'] ?? ''
        ];

        if (empty($data['content'])) {
            Session::flash('error', 'Your reply cannot be empty.');
            header("Location: " . url("topic/$topic_id"));
            exit;
        }

        if (Post::create($data)) {
            Topic::updateLastPost($topic_id, Auth::id());
            Session::flash('success', 'Reply posted successfully.');
        } else {
            Session::flash('error', 'Failed to post reply.');
        }

        header("Location: " . url("topic/$topic_id"));
        exit;
    }
}
