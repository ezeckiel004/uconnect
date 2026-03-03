<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$controller = new \App\Http\Controllers\Api\AssociationController();
$response = $controller->getAssociationsByCountry();
$data = json_decode($response->content(), true);

echo "=== API Response Debug ===\n";
echo 'Success: ' . ($data['success'] ? 'YES' : 'NO') . "\n";
echo 'Countries: ' . count($data['data']) . "\n\n";

foreach ($data['data'] as $country => $assocs) {
    echo "Country: $country (" . count($assocs) . " associations)\n";
    foreach ($assocs as $idx => $assoc) {
        echo "  [$idx] Name: " . ($assoc['name'] ?? 'NULL') . "\n";
        echo "      Category: " . ($assoc['category'] ?? 'NULL') . "\n";
        echo "      Logo: " . ($assoc['logo_path'] ?? 'none') . "\n";
        echo "      Campaigns: " . count($assoc['campaigns']) . "\n";
        if (!empty($assoc['campaigns'])) {
            foreach ($assoc['campaigns'] as $c) {
                echo "        - " . ($c['title'] ?? 'NULL') . " (" . ($c['goal'] ?? '?') . "€)\n";
            }
        }
    }
    echo "\n";
}
?>
