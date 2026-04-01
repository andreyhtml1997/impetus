<?php
/**
 * SMKL review doctor ID fix package.
 *
 * IMPORTANT:
 * 1) This file is NOT auto-loaded in this theme.
 * 2) Copy blocks from this file into the target project where review logic lives.
 * 3) The goal is to stop doctor ID substitution forever:
 *    - new comments always keep current doctor IDs;
 *    - old comments are mapped once and then self-healed.
 */

/* ==========================================================================
 * 1) NEW HELPER FUNCTIONS (add once near your other smkl_* helpers)
 * ========================================================================== */

if (!defined('SMKL_REVIEW_IDS_SCHEMA_CURRENT')) {
    define('SMKL_REVIEW_IDS_SCHEMA_CURRENT', 2);
}

if (!function_exists('smkl_mark_comment_ids_as_current')) {
    function smkl_mark_comment_ids_as_current($comment_id): void
    {
        update_comment_meta((int) $comment_id, '_smkl_ids_schema', SMKL_REVIEW_IDS_SCHEMA_CURRENT);
    }
}

if (!function_exists('smkl_is_legacy_comment_ids')) {
    function smkl_is_legacy_comment_ids($comment_id): bool
    {
        return (int) get_comment_meta((int) $comment_id, '_smkl_ids_schema', true) < SMKL_REVIEW_IDS_SCHEMA_CURRENT;
    }
}

if (!function_exists('smkl_sanitize_doctor_ids')) {
    function smkl_sanitize_doctor_ids($raw): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $raw))));
        $out = array();

        foreach ($ids as $id) {
            if (get_post_type($id) === 'likari') {
                $out[] = $id;
            }
        }

        return array_values(array_unique($out));
    }
}

if (!function_exists('smkl_get_comment_doctor_ids_safe')) {
    function smkl_get_comment_doctor_ids_safe($comment_id): array
    {
        $comment_id = (int) $comment_id;

        if (!$comment_id) {
            return array();
        }

        $raw = get_comment_meta($comment_id, 'doctor', true);
        $raw_ids = is_array($raw) ? $raw : array($raw);
        $raw_ids = array_values(array_unique(array_filter(array_map('intval', $raw_ids))));

        if (!$raw_ids) {
            return array();
        }

        // Current schema: these are already "new" IDs, no map needed.
        if (!smkl_is_legacy_comment_ids($comment_id)) {
            return smkl_sanitize_doctor_ids($raw_ids);
        }

        // Legacy schema: map old IDs to new IDs once.
        $mapped = array();

        foreach ($raw_ids as $old_id) {
            $new_id = function_exists('smkl_map_doctor_id') ? (int) smkl_map_doctor_id($old_id) : 0;

            if ($new_id > 0) {
                $mapped[] = $new_id;
            }
        }

        $doctor_ids = smkl_sanitize_doctor_ids($mapped);

        // Self-heal old data and lock comment as "current schema".
        if ($doctor_ids) {
            update_comment_meta($comment_id, 'doctor', $doctor_ids);
            smkl_mark_comment_ids_as_current($comment_id);
        }

        return $doctor_ids;
    }
}

// Auto-mark comment schema when doctor meta is saved in any handler.
if (!function_exists('smkl_mark_schema_when_doctor_meta_changed')) {
    function smkl_mark_schema_when_doctor_meta_changed($meta_id, $comment_id, $meta_key, $meta_value): void
    {
        if ($meta_key !== 'doctor') {
            return;
        }

        smkl_mark_comment_ids_as_current($comment_id);
    }

    add_action('added_comment_meta', 'smkl_mark_schema_when_doctor_meta_changed', 10, 4);
    add_action('updated_comment_meta', 'smkl_mark_schema_when_doctor_meta_changed', 10, 4);
}

/* ==========================================================================
 * 2) REPLACE EXISTING smkl_map_doctor_id() WITH THIS VERSION
 * ========================================================================== */

/*
function smkl_map_doctor_id($old_id): int {
    $old_id = (int) $old_id;

    if (!$old_id) {
        return 0;
    }

    $map = smkl_doctor_id_map();

    if (!empty($map[$old_id])) {
        $mapped = (int) $map[$old_id];

        // Protect against broken map records.
        if ($mapped > 0 && get_post_type($mapped) === 'likari') {
            return $mapped;
        }

        return 0;
    }

    if (get_post_type($old_id) === 'likari') {
        return $old_id;
    }

    return 0;
}
*/

/* ==========================================================================
 * 3) REPLACE DOCTOR LOOP IN kwark_load_reviews_ajax()
 * ========================================================================== */

/*
$doctor_ids = smkl_get_comment_doctor_ids_safe($cid);

foreach ($doctor_ids as $doctor_id) {
    ?>
    <a href="<?php echo get_permalink($doctor_id); ?>" class="card__source-name">
        <?php echo get_the_title($doctor_id); ?>
    </a>
    <?php
}
*/

/* ==========================================================================
 * 4) OPTIONAL EXPLICIT MARK IN SAVE HANDLERS
 * ========================================================================== */

/*
After each successful:
update_comment_meta($comment_id, 'doctor', $doctor_ids);

you can also add:
smkl_mark_comment_ids_as_current($comment_id);
*/

