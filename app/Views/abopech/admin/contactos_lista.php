<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech">Contactos</h1>
<table class="table table-sm bg-white shadow-sm">
    <thead><tr><th>Fecha</th><th>Abogado ID</th><th>Visitante</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($contactos as $c): ?>
        <tr>
            <td><?= esc($c['fcreacion']) ?></td>
            <td><?= (int) $c['abogado_id'] ?></td>
            <td><?= esc($c['nombre'] . ' ' . $c['apellido']) ?><br><small><?= esc($c['correo']) ?> / <?= esc($c['telefono']) ?></small></td>
            <td><?= esc($c['estado_seguimiento']) ?></td>
            <td>
                <form method="post" action="<?= base_url('abopech/admin/contactos/estado/' . (int) $c['id']) ?>" class="d-flex gap-1">
                    <?= csrf_field() ?>
                    <select name="estado_seguimiento" class="form-select form-select-sm">
                        <?php foreach (['nuevo','visto','en_gestion','cerrado'] as $st): ?>
                            <option value="<?= $st ?>" <?= $c['estado_seguimiento'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input name="notas_internas" class="form-control form-control-sm" placeholder="Notas" value="<?= esc($c['notas_internas'] ?? '') ?>">
                    <button class="btn btn-sm btn-abopech">OK</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
