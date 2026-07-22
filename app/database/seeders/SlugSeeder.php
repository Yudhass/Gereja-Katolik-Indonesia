<?php
/**
 * One-time seeder to generate slugs for existing gereja records.
 * Run: php -f app/database/seeders/SlugSeeder.php
 */

// Load config
require_once dirname(dirname(dirname(__FILE__))) . '/core/Config.php';

// Load model
require_once dirname(dirname(dirname(__FILE__))) . '/models/ModelGereja.php';

$model = new ModelGereja();
$all = $model->all();

$count = 0;
foreach ($all as $g) {
    if (!empty($g->slug)) continue;

    $slug = generateSlug($g->nama_gereja);

    // Handle duplicate slugs by appending a number
    $baseSlug = $slug;
    $i = 1;
    while (true) {
        $existing = $model->findBySlug($slug);
        if (!$existing || $existing->id == $g->id) break;
        $slug = $baseSlug . '-' . $i;
        $i++;
    }

    $model->rawQuery("UPDATE gereja SET slug = ? WHERE id = ?", array($slug, $g->id));
    echo "Updated: {$g->nama_gereja} -> {$slug}\n";
    $count++;
}

echo "Done. {$count} slugs generated.\n";
