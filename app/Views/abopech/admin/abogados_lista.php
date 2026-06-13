<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech">Abogados — <?= esc($estado) ?></h1>
<form class="mb-3" method="get"><select name="estado" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
    <?php foreach (['pendiente','aprobado','rechazado','borrador','suspendido'] as $e): ?>
        <option value="<?= $e ?>" <?= $estado === $e ? 'selected' : '' ?>><?= $e ?></option>
    <?php endforeach; ?>
</select></form>
<table class="table table-sm bg-white shadow-sm">
    <thead><tr><th>Nombre</th><th>RUT</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($abogados as $a): ?>
        <tr>
            <td><?= esc($a['nombres'] . ' ' . $a['apellidos']) ?></td>
            <td><?= esc($a['rut']) ?></td>
            <td><?= esc($a['estado_perfil']) ?></td>
            <td>
                <a href="<?= base_url('abopech/admin/abogados/' . (int) $a['id']) ?>">Ver</a>
                <?php if ($a['estado_perfil'] === 'pendiente'): ?>
                    <form method="post" action="<?= base_url('abopech/admin/abogados/aprobar/' . (int) $a['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-success">Aprobar</button></form>
                    <form method="post" action="<?= base_url('abopech/admin/abogados/rechazar/' . (int) $a['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-danger">Rechazar</button></form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
