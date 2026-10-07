<?php
require_once __DIR__ . '/includes/auth.php';
admin_require_login();

$adminTitle = 'Menu';
$adminActive = 'menu';

/* -------------------------------- actions ------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        flash_set('error', 'Your session expired. Please try that again.');
        redirect('menu.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'add') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $tags = implode(',', array_filter(array_map('trim', explode(',', (string) ($_POST['tags'] ?? '')))));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $price = (int) ($_POST['price'] ?? -1);

        $validCategory = (int) db_one('SELECT COUNT(*) AS total FROM menu_categories WHERE id = ?', [$categoryId])['total'] > 0;

        if (mb_strlen($name) < 2 || mb_strlen($description) < 3) {
            flash_set('error', 'Please give the dish a name and a description.');
        } elseif (!$validCategory) {
            flash_set('error', 'Please choose a valid section.');
        } elseif ($price < 0 || $price > 100000) {
            flash_set('error', 'Please enter a valid price.');
        } else {
            $maxOrder = (int) db_one('SELECT COALESCE(MAX(sort_order), 0) AS total FROM menu_items')['total'];
            db_insert(
                'INSERT INTO menu_items
                    (category_id, name, description, price, tags, is_vegetarian, is_featured, is_available, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)',
                [
                    $categoryId,
                    $name,
                    $description,
                    $price,
                    $tags,
                    isset($_POST['is_vegetarian']) ? 1 : 0,
                    isset($_POST['is_featured']) ? 1 : 0,
                    $maxOrder + 1,
                ]
            );
            flash_set('success', 'Dish added to the menu.');
        }
    } elseif ($id > 0 && $action === 'availability') {
        db_run('UPDATE menu_items SET is_available = 1 - is_available WHERE id = ?', [$id]);
        flash_set('success', 'Availability updated.');
    } elseif ($id > 0 && $action === 'featured') {
        db_run('UPDATE menu_items SET is_featured = 1 - is_featured WHERE id = ?', [$id]);
        flash_set('success', 'Featured updated.');
    } elseif ($id > 0 && $action === 'delete') {
        db_run('DELETE FROM menu_items WHERE id = ?', [$id]);
        flash_set('success', 'Dish deleted.');
    }

    redirect('menu.php');
}

/* --------------------------------- data -------------------------------- */
$categories = db_all(
    'SELECT c.*, COUNT(m.id) AS item_count
       FROM menu_categories c
       LEFT JOIN menu_items m ON m.category_id = c.id
      GROUP BY c.id
      ORDER BY c.sort_order'
);
$items = db_all(
    'SELECT m.*, c.name AS category_name
       FROM menu_items m
       JOIN menu_categories c ON c.id = m.category_id
      ORDER BY c.sort_order, m.sort_order'
);

require __DIR__ . '/includes/header.php';
?>

<div class="admin-intro">
    <p>Turn dishes off when they run out, feature them on the home page, or add something new.</p>
</div>

<details class="add-dish">
    <summary>+ Add a new dish</summary>
    <form method="post" action="menu.php" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">

        <label class="field">
            <span>Dish name</span>
            <input type="text" name="name" required maxlength="160">
        </label>
        <label class="field">
            <span>Price (BDT)</span>
            <input type="number" name="price" required min="0" max="100000" step="1">
        </label>
        <label class="field">
            <span>Section</span>
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">
            <span>Tags (comma separated)</span>
            <input type="text" name="tags" placeholder="bestseller, spicy, gluten free">
        </label>
        <label class="field field--full">
            <span>Description</span>
            <textarea name="description" rows="3" required maxlength="1000"></textarea>
        </label>
        <div class="field--full" style="display:flex;flex-wrap:wrap;gap:32px">
            <label class="field--check" style="display:flex;align-items:center;gap:10px">
                <input type="checkbox" name="is_vegetarian"> Vegetarian
            </label>
            <label class="field--check" style="display:flex;align-items:center;gap:10px">
                <input type="checkbox" name="is_featured"> Feature on the home page
            </label>
        </div>
        <div class="field--full">
            <button class="btn btn--ink" type="submit">Add dish</button>
        </div>
    </form>
</details>

<?php foreach ($categories as $category): ?>
    <?php $categoryItems = array_values(array_filter($items, function ($item) use ($category) {
        return (int) $item['category_id'] === (int) $category['id'];
    })); ?>
    <?php if (!$categoryItems) { continue; } ?>

    <section class="menu-admin-group">
        <div class="menu-admin-group__head">
            <h2><?= e($category['name']) ?></h2>
            <p><?= count($categoryItems) ?> dishes</p>
        </div>

        <div class="menu-admin-list">
            <?php foreach ($categoryItems as $item): ?>
                <div class="menu-admin-row">
                    <div class="menu-admin-row__main">
                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px">
                            <h3 class="menu-admin-row__name <?= (int) $item['is_available'] === 1 ? '' : 'is-off' ?>">
                                <?= e($item['name']) ?>
                            </h3>
                            <?php if ((int) $item['is_featured'] === 1): ?><span class="badge badge--featured">featured</span><?php endif; ?>
                            <?php if ((int) $item['is_vegetarian'] === 1): ?><span class="badge badge--veg">veg</span><?php endif; ?>
                            <?php if ((int) $item['is_available'] === 0): ?><span class="badge badge--off">off</span><?php endif; ?>
                        </div>
                        <p class="menu-admin-row__desc"><?= e($item['description']) ?></p>
                        <?php if ((string) $item['tags'] !== ''): ?>
                            <p class="menu-admin-row__desc" style="margin-top:2px">Tags: <?= e($item['tags']) ?></p>
                        <?php endif; ?>
                    </div>

                    <span class="menu-admin-row__price"><?= e(format_bdt($item['price'])) ?></span>

                    <div class="menu-admin-row__actions">
                        <form method="post" action="menu.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="availability">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button class="btn btn--sm btn--ink" type="submit">
                                <?= (int) $item['is_available'] === 1 ? 'Mark off' : 'Back on' ?>
                            </button>
                        </form>
                        <form method="post" action="menu.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="featured">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button class="btn btn--sm btn--quiet" type="submit">
                                <?= (int) $item['is_featured'] === 1 ? 'Unfeature' : 'Feature' ?>
                            </button>
                        </form>
                        <form method="post" action="menu.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button class="btn btn--sm btn--quiet btn--danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
