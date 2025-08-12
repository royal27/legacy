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
}
