<?php if (session('validation')) : ?>
    <div>
        <?= session('validation')->listErrors() ?>
    </div>
    <br>
<?php endif ?>