<?php

namespace Plugins\PhotoGallery\Controllers;

use App\Core\Controller;
use App\Core\View;
use Plugins\PhotoGallery\Models\GalleryAlbum;
use Plugins\PhotoGallery\Models\GalleryPhoto;

class GalleryController extends Controller
{
    /**
     * Display the main gallery page (list of public albums).
     */
    public function index()
    {
        $albums = GalleryAlbum::getPublicAlbums();
        View::render('@photo_gallery_frontend/index.php', [
            'title' => 'Photo Gallery',
            'albums' => $albums
        ]);
    }

    /**
     * Display photos in a specific album.
     */
    public function album()
    {
        $album_id = $this->route_params['id'];
        // We need Album::findById()
        // $album = GalleryAlbum::findById($album_id);
        $photos = GalleryPhoto::findAllByAlbum($album_id);

        View::render('@photo_gallery_frontend/album.php', [
            'title' => 'Album', // $album['name'],
            // 'album' => $album,
            'photos' => $photos
        ]);
    }

    /**
     * Display a single photo.
     */
    public function photo()
    {
        // Logic for viewing a single photo will go here
        echo "Viewing photo #" . $this->route_params['id'];
    }
}
