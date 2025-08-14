<?php

namespace Plugins\Forum\Controllers;

use App\Core\Controller;
use App\Core\View;
use Plugins\Forum\Models\Forum;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::getHierarchy();

        View::render('@forum_frontend/index.php', [
            'title' => 'Forum',
            'forums' => $forums
        ]);
    }
}
