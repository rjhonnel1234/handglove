<?php
    $adminMenu = config('AdminMenu');
    $roleId = session()->get('admin_roleId');
    $isAdmin = session()->get('isAdmin') == 1;
    
    $allowedMenus = $adminMenu->menus;
    
    if (!$isAdmin) {
        $accessModel = new \App\Models\AccessMatrixModel();
        $accessData = $accessModel->where('roleId', $roleId)->where('isDeleted', 0)->first();
        $allowedMenus = $accessData ? json_decode($accessData['access'], true) : [];
    }
?>

<?php foreach ($allowedMenus as $section => $links): ?>
    <?php 
        $hasVisibleLink = false;
        if (!empty($links)) {
            foreach ($links as $link) {
                if (isset($link['url'])) {
                    $hasVisibleLink = true;
                    break;
                }
            }
        }
    ?>
    <?php if ($hasVisibleLink): ?>
        <ul class="admin_nav">
            <li class="section-nav"><?= esc($section) ?></li>
            <?php foreach ($links as $link): ?>
                <?php if(isset($link['url'])): ?>
                    <li>
                        <a href="<?= base_url($link['url']) ?>">
                            <i class="<?= esc($link['icon']) ?>"></i><?= esc($link['title']) ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endforeach; ?>

<?php if($isAdmin): ?>
<ul class="admin_nav">
    <li class="section-nav">Admin</li>
    <li><a href="<?= base_url('admin/donors') ?>"><i class="fa fa-hospital-user"></i>Facility Personnel</a></li>
    <li><a href="<?= base_url('admin/roles') ?>"><i class="fa fa-user-shield"></i>Roles</a></li>
    <li><a href="<?= base_url('admin/users') ?>"><i class="fa fa-user"></i>Users</a></li>
</ul>
<?php endif; ?>