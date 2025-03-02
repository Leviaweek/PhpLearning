<?php

function fetch_config_data() {
    $configFile = 'api_settings.json';
    $data = file_get_contents($configFile);
    return json_decode($data, true);
}

function process_web_request($targetUrl) {
    $reqHandler = curl_init();
    curl_setopt($reqHandler, CURLOPT_URL, $targetUrl);
    curl_setopt($reqHandler, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CustomBot/1.1)');
    curl_setopt($reqHandler, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($reqHandler, CURLOPT_TIMEOUT, 15);
    $resultData = curl_exec($reqHandler);
    curl_close($reqHandler);
    return json_decode($resultData, true);
}

function display_results($resultItems): void
{
    foreach ($resultItems as $entry) {
        $cleanURL = preg_replace('/^https?:\/\//', '', $entry['link']);
        echo <<<OUTPUT
        <article class="result-card">
            <div class="result-header">
                <span class="source-url">{$cleanURL}</span>
                <a class="result-link" href="{$entry['link']}" target="_blank">↗</a>
            </div>
            <h2 class="result-title"><a href="{$entry['link']}">{$entry['htmlTitle']}</a></h2>
            <div class="result-excerpt">{$entry['htmlSnippet']}</div>
        </article>
OUTPUT;
    }
}

function perform_search_operation($searchTerm) {
    $config = fetch_config_data();
    $requestURL = 'https://www.googleapis.com/customsearch/v1?' . http_build_query([
        'key' => $config['searchAPIKey'],
        'cx' => $config['engineID'],
        'q' => $searchTerm,
        'num' => 8
    ]);
    $response = process_web_request($requestURL);
    return $response['items'] ?? [];
}

$searchQuery = $_GET['q'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebSearch Interface</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .search-container { margin: 40px 0; }
        .search-box { width: 100%; padding: 12px; font-size: 16px; border: 2px solid #ddd; border-radius: 24px; }
        .result-card { margin: 30px 0; padding: 15px; border-left: 3px solid #4CAF50; }
        .source-url { color: #666; font-size: 0.9em; }
        .result-link { float: right; text-decoration: none; color: #1a73e8; }
        .result-title { margin: 8px 0; font-size: 1.1em; }
        .result-title a { color: #1a0dab; text-decoration: none; }
        .result-excerpt { color: #444; line-height: 1.5; }
    </style>
</head>
<body>
    <header>
        <h1>WebSearch</h1>
        <form class="search-container" method="get">
            <input type="search" name="q" class="search-box" 
                   placeholder="Enter search terms..." 
                   value="<?= htmlspecialchars($searchQuery) ?>">
        </form>
    </header>

    <?php if(!empty($searchQuery)): ?>
    <section class="results-section">
        <?php 
            $results = perform_search_operation($searchQuery);
            if(!empty($results)) {
                display_results($results);
            } else {
                echo '<p class="no-results">No matches found. Try different keywords.</p>';
            }
        ?>
    </section>
    <?php endif; ?>
</body>
</html>