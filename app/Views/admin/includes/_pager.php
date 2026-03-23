<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination pagination-rounded justify-content-center mb-0 mt-4">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
                    <i class="fas fa-angle-double-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>">
                    <i class="fas fa-angle-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<style>
    .pagination-rounded .page-link {
        border-radius: 8px !important;
        margin: 0 4px;
        border: 1px solid #e2e8f0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4a5568;
        font-weight: 600;
        transition: all 0.2s ease;
        background: #fff;
    }
    .pagination-rounded .page-item.active .page-link {
        background-color: #3182ce;
        border-color: #3182ce;
        color: #fff;
        box-shadow: 0 4px 6px -1px rgba(66, 153, 225, 0.4);
    }
    .pagination-rounded .page-link:hover {
        background-color: #f7fafc;
        color: #2b6cb0;
        border-color: #cbd5e1;
    }
</style>
