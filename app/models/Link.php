<?php

class Link extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all links for the menu, translated for the current language.
     * @param string $language_code The language code (e.g., 'en', 'ro')
     * @return array
     */
    public function get_menu_links($language_code) {
        $prefix = $this->db->getPrefix();

        $sql = "
            SELECT
                l.id, l.parent_id, l.url,
                lt.title
            FROM
                {$prefix}links l
            JOIN
                {$prefix}link_translations lt ON l.id = lt.link_id
            WHERE
                lt.language_code = ?
            ORDER BY
                l.display_order ASC, lt.title ASC
        ";

        $this->db->query($sql);
        $links = $this->db->resultSet([$language_code]);

        // In a more complex scenario, you would build a hierarchical tree here.
        // For now, a flat list is sufficient.
        return $links;
    }

    public function getAllLinksForAdmin() {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}links ORDER BY display_order ASC");
        $links = $this->db->resultSet();

        // Get all translations and map them
        $this->db->query("SELECT * FROM {$prefix}link_translations");
        $translations_raw = $this->db->resultSet();
        $translations = [];
        foreach ($translations_raw as $trans) {
            $translations[$trans['link_id']][$trans['language_code']] = $trans['title'];
        }

        foreach ($links as &$link) {
            $link['translations'] = $translations[$link['id']] ?? [];
        }

        return $this->buildLinkTree($links);
    }

    private function buildLinkTree(array &$elements, $parentId = 0) {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildLinkTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function getLinkById($id) {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}links WHERE id = ?");
        $link = $this->db->single([$id]);

        if ($link) {
            $this->db->query("SELECT * FROM {$prefix}link_translations WHERE link_id = ?");
            $translations_raw = $this->db->resultSet([$id]);
            $translations = [];
            foreach ($translations_raw as $trans) {
                $translations[$trans['language_code']] = $trans['title'];
            }
            $link['translations'] = $translations;
        }
        return $link;
    }

    public function createLink($data) {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}links (parent_id, url, display_order) VALUES (?, ?, ?)");
        if (!$this->db->execute([$data['parent_id'], $data['url'], $data['display_order']])) {
            return false;
        }
        $link_id = $this->db->lastInsertId();

        $this->db->query("INSERT INTO {$prefix}link_translations (link_id, language_code, title) VALUES (?, ?, ?)");
        foreach ($data['translations'] as $lang_code => $title) {
            if (!empty($title)) {
                $this->db->execute([$link_id, $lang_code, $title]);
            }
        }
        return true;
    }

    public function updateLink($id, $data) {
        $prefix = $this->db->getPrefix();
        $this->db->query("UPDATE {$prefix}links SET parent_id = ?, url = ?, display_order = ? WHERE id = ?");
        if (!$this->db->execute([$data['parent_id'], $data['url'], $data['display_order'], $id])) {
            return false;
        }

        // Delete old translations and insert new ones
        $this->db->query("DELETE FROM {$prefix}link_translations WHERE link_id = ?");
        $this->db->execute([$id]);

        $this->db->query("INSERT INTO {$prefix}link_translations (link_id, language_code, title) VALUES (?, ?, ?)");
        foreach ($data['translations'] as $lang_code => $title) {
            if (!empty($title)) {
                $this->db->execute([$id, $lang_code, $title]);
            }
        }
        return true;
    }

    public function deleteLink($id) {
        $prefix = $this->db->getPrefix();
        // First delete translations
        $this->db->query("DELETE FROM {$prefix}link_translations WHERE link_id = ?");
        $this->db->execute([$id]);

        // Then delete the link itself
        $this->db->query("DELETE FROM {$prefix}links WHERE id = ?");
        return $this->db->execute([$id]);
    }
}
