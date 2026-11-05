<?php
$workshops = require __DIR__ . '/data/workshops.php';

$query = trim((string)($_GET['q'] ?? ''));
$category = (string)($_GET['category'] ?? 'all');
$allowedCategories = ['all','Web','AI','Design'];
if (!in_array($category, $allowedCategories, true)) $category = 'all';
$results = array_filter($workshops, static function(array $w) use ($query, $category): bool {
  return ($query === '' || stripos($w['title'], $query) !== false)
    && ($category === 'all' || $w['category'] === $category);
});
?>

<form action="index.php" method="get">
    <input name="q" placeholder="Search title">

    <select name="category">
        <option value="all">All</option>
        <option value="Web">Web</option>
        <option value="AI">AI</option>
        <option value="Design">Design</option>
    </select>

    <button>Search</button>
</form>

<?php if (!$results): ?>
    <p>No workshops matched your search.</p>
<?php else: ?>
    <?php foreach ($results as $item): ?>
        <article>
            <h2><?= htmlspecialchars($item['title']) ?></h2>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
