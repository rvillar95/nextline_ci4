<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MenuSidebarConfigurable extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('menu_grupo')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'slug' => ['type' => 'VARCHAR', 'constraint' => 50],
                'etiqueta' => ['type' => 'VARCHAR', 'constraint' => 100],
                'orden' => ['type' => 'INT', 'default' => 0],
                'estado' => ['type' => 'ENUM', 'constraint' => ['A', 'I'], 'default' => 'A'],
                'fcreacion' => ['type' => 'DATETIME', 'null' => true],
                'factualizacion' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable('menu_grupo', true);
        }

        $now = date('Y-m-d H:i:s');
        $grupos = [
            ['slug' => 'operacion', 'etiqueta' => 'Día a día', 'orden' => 10],
            ['slug' => 'pacientes', 'etiqueta' => 'Pacientes', 'orden' => 20],
            ['slug' => 'finanzas', 'etiqueta' => 'Cobros', 'orden' => 30],
            ['slug' => 'equipo', 'etiqueta' => 'Mi equipo', 'orden' => 35],
            ['slug' => 'cuenta', 'etiqueta' => 'Cuenta', 'orden' => 40],
            ['slug' => 'admin', 'etiqueta' => 'Administración', 'orden' => 50],
            ['slug' => 'otros', 'etiqueta' => 'Más', 'orden' => 60],
        ];

        foreach ($grupos as $g) {
            $exists = $this->db->table('menu_grupo')->where('slug', $g['slug'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('menu_grupo')->insert([
                    'slug' => $g['slug'],
                    'etiqueta' => $g['etiqueta'],
                    'orden' => $g['orden'],
                    'estado' => 'A',
                    'fcreacion' => $now,
                    'factualizacion' => $now,
                ]);
            }
        }

        if ($this->db->tableExists('modulo')) {
            $moduloCols = [
                'menu_grupo_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'mostrar'],
                'menu_icono' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'circle', 'after' => 'menu_grupo_id'],
                'menu_etiqueta' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'menu_icono'],
                'menu_aplanar' => ['type' => 'ENUM', 'constraint' => ['S', 'N'], 'default' => 'S', 'after' => 'menu_etiqueta'],
                'menu_ruta_alterna' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'menu_aplanar'],
                'menu_etiqueta_alterna' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'menu_ruta_alterna'],
                'menu_solo_sa' => ['type' => 'ENUM', 'constraint' => ['S', 'N'], 'default' => 'N', 'after' => 'menu_etiqueta_alterna'],
            ];
            foreach ($moduloCols as $name => $def) {
                if (! $this->db->fieldExists($name, 'modulo')) {
                    $this->forge->addColumn('modulo', [$name => $def]);
                }
            }
        }

        if ($this->db->tableExists('modulo_detalle') && ! $this->db->fieldExists('menu_etiqueta', 'modulo_detalle')) {
            $this->forge->addColumn('modulo_detalle', [
                'menu_etiqueta' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'descripcion'],
            ]);
        }

        $this->asignarModulosAGrupos();
    }

    private function asignarModulosAGrupos(): void
    {
        if (! $this->db->tableExists('modulo') || ! $this->db->tableExists('menu_grupo')) {
            return;
        }

        $map = [];
        foreach ($this->db->table('menu_grupo')->get()->getResultArray() as $row) {
            $map[$row['slug']] = (int) $row['id'];
        }

        $gid = fn (string $slug) => $map[$slug] ?? 0;

        if ($gid('operacion')) {
            $this->db->query('UPDATE modulo SET menu_grupo_id = ? WHERE menu_grupo_id IS NULL AND (ruta LIKE ? OR ruta LIKE ? OR ruta LIKE ? OR nombre LIKE ? OR nombre LIKE ?)', [
                $gid('operacion'), '%menu%', '%mensaje%', '%agenda%', '%inicio%', '%mensaje%',
            ]);
        }
        if ($gid('pacientes')) {
            $this->db->query('UPDATE modulo SET menu_grupo_id = ? WHERE menu_grupo_id IS NULL AND (ruta LIKE ? OR ruta LIKE ? OR ruta LIKE ? OR ruta LIKE ?)', [
                $gid('pacientes'), '%paciente%', '%historial%', '%documento%', '%plan-alimentario%',
            ]);
        }
        if ($gid('finanzas')) {
            $this->db->query('UPDATE modulo SET menu_grupo_id = ? WHERE menu_grupo_id IS NULL AND (ruta LIKE ? OR (ruta LIKE ? AND ruta NOT LIKE ?))', [
                $gid('finanzas'), '%boton-pago%', '%pago%', '%boton-pago%',
            ]);
        }
        if ($gid('equipo')) {
            $this->db->query("UPDATE modulo SET menu_grupo_id = ? WHERE menu_grupo_id IS NULL AND (ruta LIKE '%perfil%' OR ruta LIKE '%perfil-detalle%') AND ruta NOT LIKE '%mi-perfil%'", [$gid('equipo')]);
        }
        if ($gid('cuenta')) {
            $this->db->query('UPDATE modulo SET menu_grupo_id = ? WHERE menu_grupo_id IS NULL AND (ruta LIKE ? OR ruta LIKE ?)', [
                $gid('cuenta'), '%mi-perfil%', '%configuracion%',
            ]);
        }
        if ($gid('admin')) {
            $this->db->query("UPDATE modulo SET menu_grupo_id = ? WHERE menu_grupo_id IS NULL AND (ruta LIKE '%paquete%' OR ruta LIKE '%/modulo%' OR (ruta LIKE '%/empresa%' AND ruta NOT LIKE '%perfil%'))", [$gid('admin')]);
        }
        if ($gid('otros')) {
            $this->db->table('modulo')->where('menu_grupo_id', null)->update(['menu_grupo_id' => $gid('otros')]);
        }

        $this->db->query("
            UPDATE modulo SET
                menu_ruta_alterna = 'dashboard/pago/cobros',
                menu_etiqueta_alterna = 'Cobros',
                menu_icono = IF(menu_icono IS NULL OR menu_icono = '', 'dollar-sign', menu_icono)
            WHERE ruta LIKE '%/pago%' AND ruta NOT LIKE '%boton-pago%'
        ");

        $this->db->query("
            UPDATE modulo SET menu_solo_sa = 'S'
            WHERE ruta LIKE '%paquete%' OR ruta LIKE '%/modulo%' OR (ruta LIKE '%/empresa%' AND ruta NOT LIKE '%perfil%')
        ");
    }

    public function down(): void
    {
        if ($this->db->tableExists('modulo_detalle') && $this->db->fieldExists('menu_etiqueta', 'modulo_detalle')) {
            $this->forge->dropColumn('modulo_detalle', 'menu_etiqueta');
        }

        if ($this->db->tableExists('modulo')) {
            foreach (['menu_solo_sa', 'menu_etiqueta_alterna', 'menu_ruta_alterna', 'menu_aplanar', 'menu_etiqueta', 'menu_icono', 'menu_grupo_id'] as $col) {
                if ($this->db->fieldExists($col, 'modulo')) {
                    $this->forge->dropColumn('modulo', $col);
                }
            }
        }

        if ($this->db->tableExists('menu_grupo')) {
            $this->forge->dropTable('menu_grupo', true);
        }
    }
}
