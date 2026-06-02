<?php if (session()->getFlashdata('errors') !== null) : ?>
    <div class="alert alert-danger gym-flash" role="alert">
        <ul class="mb-0">
            <?php if (! is_array(session()->getFlashdata('errors'))) : ?>
                <li><?= session()->getFlashdata('errors') ?></li>
            <?php else : ?>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success') !== null) : ?>
    <div class="alert alert-success gym-flash" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
