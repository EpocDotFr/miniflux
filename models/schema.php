<?php

namespace Schema;

use PDO;
use Model\Config;

const VERSION = 40;

function version_40($pdo)
{
    $pdo->exec('UPDATE settings SET "value"="https://github.com/miniflux/miniflux/archive/master.zip" WHERE "key"="auto_update_url"');
}

function version_39($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN cloak_referrer INTEGER DEFAULT 0');
}

function version_38($pdo)
{
    $pdo->exec('INSERT INTO settings ("key", "value") VALUES ("original_marks_read", 1)');
}

function version_37($pdo)
{
    $pdo->exec('INSERT INTO settings ("key", "value") VALUES ("debug_mode", 0)');
}

function version_36($pdo)
{
    $pdo->exec('INSERT INTO settings ("key", "value") VALUES ("frontend_updatecheck_interval", 10)');
}

function version_35($pdo)
{
    $pdo->exec('DELETE FROM favicons WHERE icon = ""');

    $pdo->exec('
        CREATE TABLE settings (
            "key" TEXT NOT NULL UNIQUE,
            "value" TEXT Default NULL,
            PRIMARY KEY(key)
        )
    ');

    $pdo->exec("
        INSERT INTO settings (key,value)
            SELECT 'username', username FROM config UNION
            SELECT 'password', password FROM config UNION
            SELECT 'language', language FROM config UNION
            SELECT 'autoflush', autoflush FROM config UNION
            SELECT 'nocontent', nocontent FROM config UNION
            SELECT 'items_per_page', items_per_page FROM config UNION
            SELECT 'theme', theme FROM config UNION
            SELECT 'api_token', api_token FROM config UNION
            SELECT 'feed_token', feed_token FROM config UNION
            SELECT 'items_sorting_direction', items_sorting_direction FROM config UNION
            SELECT 'redirect_nothing_to_read', redirect_nothing_to_read FROM config UNION
            SELECT 'timezone', timezone FROM config UNION
            SELECT 'auto_update_url', auto_update_url FROM config UNION
            SELECT 'bookmarklet_token', bookmarklet_token FROM config UNION
            SELECT 'items_display_mode', items_display_mode FROM config UNION
            SELECT 'fever_token', fever_token FROM config UNION
            SELECT 'autoflush_unread', autoflush_unread FROM config UNION
            SELECT 'pinboard_enabled', pinboard_enabled FROM config UNION
            SELECT 'pinboard_token', pinboard_token FROM config UNION
            SELECT 'pinboard_tags', pinboard_tags FROM config UNION
            SELECT 'instapaper_enabled', instapaper_enabled FROM config UNION
            SELECT 'instapaper_username', instapaper_username FROM config UNION
            SELECT 'instapaper_password', instapaper_password FROM config UNION
            SELECT 'image_proxy', image_proxy FROM config UNION
            SELECT 'favicons', favicons FROM config
    ");

    $pdo->exec('DROP TABLE config');
}

function version_34($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN favicons INTEGER DEFAULT 0');

    $pdo->exec(
        'CREATE TABLE favicons (
            feed_id INTEGER UNIQUE,
            link TEXT,
            icon TEXT,
            FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
        )'
    );
}

function version_33($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN image_proxy INTEGER DEFAULT 0');
}

function version_32($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN instapaper_enabled INTEGER DEFAULT 0');
    $pdo->exec('ALTER TABLE config ADD COLUMN instapaper_username TEXT');
    $pdo->exec('ALTER TABLE config ADD COLUMN instapaper_password TEXT');
}

function version_31($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN pinboard_enabled INTEGER DEFAULT 0');
    $pdo->exec('ALTER TABLE config ADD COLUMN pinboard_token TEXT');
    $pdo->exec('ALTER TABLE config ADD COLUMN pinboard_tags TEXT DEFAULT "miniflux"');
}

function version_30($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN autoflush_unread INTEGER DEFAULT 45');
}

function version_29($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN fever_token INTEGER DEFAULT "'.substr(Config\generate_token(), 0, 8).'"');
}

function version_28($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN rtl INTEGER DEFAULT 0');
}

function version_27($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN items_display_mode TEXT DEFAULT "summaries"');
}

function version_26($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN bookmarklet_token TEXT DEFAULT "'.Config\generate_token().'"');
}

function version_25($pdo)
{
    $pdo->exec(
        'CREATE TABLE remember_me (
            id INTEGER PRIMARY KEY,
            username TEXT,
            ip TEXT,
            user_agent TEXT,
            token TEXT,
            sequence TEXT,
            expiration INTEGER,
            date_creation INTEGER
        )'
    );
}

function version_24($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN auto_update_url TEXT DEFAULT 'https://github.com/fguillot/miniflux/archive/master.zip'");
}

function version_23($pdo)
{
    $pdo->exec('ALTER TABLE items ADD COLUMN language TEXT');
}

function version_22($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN timezone TEXT DEFAULT 'UTC'");
}

function version_21($pdo)
{
    $pdo->exec('ALTER TABLE items ADD COLUMN enclosure TEXT');
    $pdo->exec('ALTER TABLE items ADD COLUMN enclosure_type TEXT');
}

function version_20($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN redirect_nothing_to_read TEXT DEFAULT "feeds"');
}

function version_19($pdo)
{
    $rq = $pdo->prepare('SELECT autoflush FROM config');
    $rq->execute();
    $value = (int) $rq->fetchColumn();

    // Change default value of autoflush to 15 days to avoid very large database
    if ($value <= 0) {
        $rq = $pdo->prepare('UPDATE config SET autoflush=?');
        $rq->execute(array(15));
    }
}

function version_18($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN parsing_error INTEGER DEFAULT 0');
}

function version_17($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN items_sorting_direction TEXT DEFAULT "desc"');
}

function version_16($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN auth_google_token TEXT DEFAULT ""');
    $pdo->exec('ALTER TABLE config ADD COLUMN auth_mozilla_token TEXT DEFAULT ""');
}

function version_15($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN download_content INTEGER DEFAULT 0');
}

function version_14($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN feed_token TEXT DEFAULT "'.Config\generate_token().'"');
}

function version_13($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN enabled INTEGER DEFAULT 1');
}

function version_12($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN api_token TEXT DEFAULT "'.Config\generate_token().'"');
}

function version_11($pdo)
{
    $rq = $pdo->prepare('
        SELECT
        items.id, items.url AS item_url, feeds.site_url
        FROM items
        LEFT JOIN feeds ON feeds.id=items.feed_id
    ');

    $rq->execute();

    $items = $rq->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {

        if ($item['id'] !== $item['item_url']) {

            $id = hash('crc32b', $item['id'].$item['site_url']);
        }
        else {

            $id = hash('crc32b', $item['item_url'].$item['site_url']);
        }

        $rq = $pdo->prepare('UPDATE items SET id=? WHERE id=?');
        $rq->execute(array($id, $item['id']));
    }
}

function version_10($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN theme TEXT DEFAULT "original"');
}

function version_9($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN items_per_page INTEGER DEFAULT 100');
}

function version_8($pdo)
{
    $pdo->exec('ALTER TABLE items ADD COLUMN bookmark INTEGER DEFAULT 0');
}

function version_7($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN nocontent INTEGER DEFAULT 0');
}

function version_6($pdo)
{
    $pdo->exec('ALTER TABLE config ADD COLUMN autoflush INTEGER DEFAULT 0');
}

function version_5($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN last_checked INTEGER');
}

function version_4($pdo)
{
    $pdo->exec('CREATE INDEX idx_status ON items(status)');
}

function version_3($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN language TEXT DEFAULT 'en_US'");
}

function version_2($pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN last_modified TEXT');
    $pdo->exec('ALTER TABLE feeds ADD COLUMN etag TEXT');
}

function version_1($pdo)
{
    $pdo->exec("
        CREATE TABLE config (
            username TEXT DEFAULT 'admin',
            password TEXT
        )
    ");

    $pdo->exec("
        INSERT INTO config
        (password)
        VALUES ('".\password_hash('admin', PASSWORD_BCRYPT)."')
    ");

    $pdo->exec('
        CREATE TABLE feeds (
            id INTEGER PRIMARY KEY,
            site_url TEXT,
            feed_url TEXT UNIQUE,
            title TEXT COLLATE NOCASE
        )
    ');

    $pdo->exec('
        CREATE TABLE items (
            id TEXT PRIMARY KEY,
            url TEXT,
            title TEXT,
            author TEXT,
            content TEXT,
            updated INTEGER,
            status TEXT,
            feed_id INTEGER,
            FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
        )
    ');
}
