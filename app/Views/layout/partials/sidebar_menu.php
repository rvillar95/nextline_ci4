<?php
/**
 * Menú lateral normalizado (requiere $sidebarMenu desde DashboardMenuBuilder).
 *
 * @var list<array> $sidebarMenu
 */

use App\Libraries\DashboardMenuBuilder;

$contador = 1;
$currentPath = '/' . trim(uri_string(), '/');
?>
<ul class="list-unstyled menu-categories nutrinext-sidebar-menu" id="accordionExample">
<?php foreach ($sidebarMenu as $item) : ?>
    <?php if (($item['type'] ?? '') === 'heading') : ?>
        <li class="menu menu-section-heading-item">
            <div class="menu-section-heading"><?= esc($item['label'] ?? '') ?></div>
        </li>
    <?php elseif (($item['type'] ?? '') === 'link') : ?>
        <?php
        $href = (string) ($item['href'] ?? '#');
        $path = parse_url($href, PHP_URL_PATH) ?: '';
        $isActive = $path !== '' && str_starts_with($currentPath, rtrim($path, '/'));
        ?>
        <li class="menu<?= $isActive ? ' active' : '' ?>">
            <a href="<?= esc($href) ?>" class="dropdown-toggle sidebar-menu-link<?= $isActive ? ' active' : '' ?>" aria-expanded="false">
                <div class="sidebar-menu-link-inner">
                    <?= DashboardMenuBuilder::featherSvg((string) ($item['icon'] ?? 'circle')) ?>
                    <span><?= esc($item['label'] ?? '') ?></span>
                </div>
            </a>
        </li>
    <?php elseif (($item['type'] ?? '') === 'group') : ?>
        <?php
        $groupId = 'modulo_' . $contador;
        $contador++;
        $icon = (string) ($item['icon'] ?? 'circle');
        ?>
        <li class="menu active">
            <a href="#<?= esc($groupId) ?>" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed sidebar-menu-link">
                <div class="sidebar-menu-link-inner">
                    <?= DashboardMenuBuilder::featherSvg($icon) ?>
                    <span><?= esc($item['label'] ?? '') ?></span>
                </div>
            </a>
            <ul class="submenu list-unstyled collapse" id="<?= esc($groupId) ?>" data-bs-parent="#accordionExample">
                <?php foreach ($item['children'] ?? [] as $child) : ?>
                    <?php
                    $childHref = (string) ($child['href'] ?? '#');
                    $childPath = parse_url($childHref, PHP_URL_PATH) ?: '';
                    $childActive = $childPath !== '' && str_starts_with($currentPath, rtrim($childPath, '/'));
                    ?>
                    <li>
                        <a href="<?= esc($childHref) ?>" class="<?= $childActive ? 'active' : '' ?>"><?= esc($child['label'] ?? '') ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endif; ?>
<?php endforeach; ?>
</ul>
