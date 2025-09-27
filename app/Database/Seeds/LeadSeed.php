<?php
declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class LeadSeed extends Seeder
{
    public function run()
    {
        // Estados
        $this->db->table('lead_estado')->insertBatch([
            ['id' => 1, 'nombre' => 'Nuevo'],
            ['id' => 2, 'nombre' => 'En gestión'],
            ['id' => 3, 'nombre' => 'Contactado'],
            ['id' => 4, 'nombre' => 'Cerrado'],
        ]);

        // Módulo Leads (ajusta IDs según tu tabla modulo)
        $this->db->table('modulo')->insert([
            'nombre' => 'Leads', 'descripcion' => 'Consulta/Contacto web',
            'ruta' => '/dashboard/leads', 'estado' => 'A', 'mostrar' => 'S', 'sa' => 'N',
            'fcreacion' => date('Y-m-d H:i:s'), 'factualizacion' => date('Y-m-d H:i:s'), 'feliminacion' => '0000-00-00 00:00:00'
        ]);

        $moduloId = $this->db->insertID();

        $this->db->table('modulo_detalle')->insertBatch([
            ['modulo_id' => $moduloId, 'descripcion' => 'Lista de Leads',  'ruta' => '/lista', 'accion' => 'ver','estado' => 'A', 'mostrar' => 'S', 'orden' => 1, 'fcreacion' => date('Y-m-d H:i:s'), 'factualizacion' => date('Y-m-d H:i:s'), 'feliminacion' => '0000-00-00 00:00:00'],
            ['modulo_id' => $moduloId, 'descripcion' => 'Acción Obtener Leads',  'ruta' => '/getLeads','accion' => 'ver',  'estado' => 'A', 'mostrar' => 'N', 'orden' => 0, 'fcreacion' => date('Y-m-d H:i:s'), 'factualizacion' => date('Y-m-d H:i:s'), 'feliminacion' => '0000-00-00 00:00:00'],
            ['modulo_id' => $moduloId, 'descripcion' => 'Acción Cambiar Estado',  'ruta' => '/cambiarEstado','accion' => 'editar',  'estado' => 'A', 'mostrar' => 'N', 'orden' => 0, 'fcreacion' => date('Y-m-d H:i:s'), 'factualizacion' => date('Y-m-d H:i:s'), 'feliminacion' => '0000-00-00 00:00:00'],
            ['modulo_id' => $moduloId, 'descripcion' => 'Acción Eliminar Lead',  'ruta' => '/eliminar','accion' => 'eliminar',  'estado' => 'A', 'mostrar' => 'N', 'orden' => 0, 'fcreacion' => date('Y-m-d H:i:s'), 'factualizacion' => date('Y-m-d H:i:s'), 'feliminacion' => '0000-00-00 00:00:00'],
        ]);

        // Dar permisos iniciales al perfil “Administrador Web” (id=7 en tu dump) para ver Leads
        $this->db->table('perfil_modulo')->insert([
            'perfil_id' => 7, 'modulo_id' => $moduloId, 'ver' => 1, 'registrar' => 0, 'editar' => 0, 'eliminar' => 1,
            'estado' => 'A', 'orden' => 5, 'fcreacion' => date('Y-m-d H:i:s'), 'factualizacion' => date('Y-m-d H:i:s'), 'feliminacion' => '0000-00-00 00:00:00'
        ]);
    }
}
