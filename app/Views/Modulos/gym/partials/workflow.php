<?php
/** @var string $active ejercicio|rutina|programa|alumno|asignacion */
$active = $active ?? 'ejercicio';
$steps = [
    ['key' => 'ejercicio', 'num' => 1, 'label' => 'Ejercicios', 'url' => base_url('dashboard/gym/ejercicio/lista')],
    ['key' => 'rutina', 'num' => 2, 'label' => 'Rutinas', 'url' => base_url('dashboard/gym/rutina/lista')],
    ['key' => 'programa', 'num' => 3, 'label' => 'Programas', 'url' => base_url('dashboard/gym/programa/lista')],
    ['key' => 'alumno', 'num' => 4, 'label' => 'Alumnos', 'url' => base_url('dashboard/gym/alumno/lista')],
    ['key' => 'asignacion', 'num' => 5, 'label' => 'Asignar', 'url' => base_url('dashboard/gym/asignacion/lista')],
];
?>
<nav class="gym-workflow" aria-label="Flujo del gimnasio">
    <span class="gym-workflow__label">Tu flujo de trabajo</span>
    <?php foreach ($steps as $step) : ?>
        <?php
        $isActive = $step['key'] === $active;
        $class = 'gym-workflow__step' . ($isActive ? ' gym-workflow__step--active' : '');
        ?>
        <a href="<?= esc($step['url']) ?>" class="<?= $class ?>">
            <span class="gym-workflow__num"><?= (int) $step['num'] ?></span>
            <?= esc($step['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>
