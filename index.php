<?php
if (isset($_POST['repo_url'])) {
    $repo_url = trim($_POST['repo_url']);
    // Creator by kariexs   
    if (!filter_var($repo_url, FILTER_VALIDATE_URL)) {
        $error_message = "Invalid URL format. Please enter a valid GitHub repository URL (e.g. https://github.com/username/repo).";
    } else {
        $parsed_url = parse_url($repo_url);
        if (!isset($parsed_url['host']) || $parsed_url['host'] != 'github.com') {
            $error_message = "The URL must point to a valid GitHub repository.";
        } else {
            $repo_name = trim($parsed_url['path'], '/');
            $api_url = "https://api.github.com/repos/{$repo_name}";
            $context = stream_context_create([
                "http" => [
                    "header" => "User-Agent: GitHubRepoInfoApp"
                ]
            ]);
                // Developer by kariexs
            $repo_data = @file_get_contents($api_url, false, $context);

            if ($repo_data === FALSE) {
                $error_message = "Unable to fetch repository data. Please check the URL.";
            } else {
                $repo_info = json_decode($repo_data, true);

                if (isset($repo_info['message']) && $repo_info['message'] == "Not Found") {
                    $error_message = "Repository not found. Please check the URL.";
                } else {
                    $updated_at = $repo_info['updated_at'];
                    $sponsor = isset($repo_info['sponsors']) ? $repo_info['sponsors'] : 'No Sponsors';
                    $stars = $repo_info['stargazers_count'];
                    $forks = $repo_info['forks_count'];
                    $watchers = $repo_info['watchers_count'];
                    $name = $repo_info['name'];
                    $owner = $repo_info['owner']['login'];
                    $description = $repo_info['description'];
                    $language = $repo_info['language'];
                    $open_issues = $repo_info['open_issues_count'];
                    $contributors_url = $repo_info['contributors_url'];
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub Repo Info</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</head>
<body>
    <div id="particles-js"></div>
    <div class="container">
        <h1>GitHub Repository Information</h1>
        <form method="POST" action="">
            <label for="repo_url">Enter GitHub Repo URL:</label>
            <input type="text" id="repo_url" name="repo_url" required placeholder="https://github.com/username/repository">
            <button type="submit">Get Info</button>
        </form>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($repo_info)): ?>
            <div class="repo-info">
                <h2><?php echo $name; ?></h2>
                <p><strong>Owner:</strong> <?php echo $owner; ?></p>
                <p><strong>Description:</strong> <?php echo $description ?: 'No description available.'; ?></p>
                <p><strong>Last Updated:</strong> <?php echo date('d M Y', strtotime($updated_at)); ?></p>
                <p><strong>Sponsors:</strong> <?php echo $sponsor; ?></p>
                <p><strong>Stars:</strong> <?php echo $stars; ?></p>
                <p><strong>Forks:</strong> <?php echo $forks; ?></p>
                <p><strong>Watchers:</strong> <?php echo $watchers; ?></p>
                <p><strong>Open Issues:</strong> <?php echo $open_issues; ?></p>
                <p><strong>Language:</strong> <?php echo $language; ?></p>
                <p><strong>Contributors:</strong> <a href="<?php echo $contributors_url; ?>" target="_blank">View Contributors</a></p>
            </div>
        <?php endif; ?>
    </div>

    <script src="particles.js"></script>
</body>
</html>
