<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlanInteresLeadContacto extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('lead_contacto')) {
            return;
        }
        if ($this->db->fieldExists('plan_interes', 'lead_contacto')) {
            return;
        }

        $this->forge->addColumn('lead_contacto', [
            'plan_interes' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'mensaje',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('lead_contacto') && $this->db->fieldExists('plan_interes', 'lead_contacto')) {
            $this->forge->dropColumn('lead_contacto', 'plan_interes');
        }
    }
}
