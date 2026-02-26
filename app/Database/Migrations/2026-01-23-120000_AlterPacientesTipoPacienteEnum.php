<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Amplía el ENUM tipo_paciente en pacientes para incluir: fonasa, isapre, otro.
 */
class AlterPacientesTipoPacienteEnum extends Migration
{
    public function up(): void
    {
        $sql = "ALTER TABLE `pacientes` MODIFY COLUMN `tipo_paciente` " .
               "ENUM('particular','convenio','seguro','fonasa','isapre','otro') NOT NULL DEFAULT 'particular'";
        $this->db->query($sql);
    }

    public function down(): void
    {
        // Opcional: revertir a los 3 valores originales (solo si no hay registros con fonasa/isapre/otro)
        $sql = "ALTER TABLE `pacientes` MODIFY COLUMN `tipo_paciente` " .
               "ENUM('particular','convenio','seguro') NOT NULL DEFAULT 'particular'";
        $this->db->query($sql);
    }
}
