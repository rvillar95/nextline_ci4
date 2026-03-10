<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Asegura que agenda.id tenga AUTO_INCREMENT.
 * Al importar un dump en otro servidor a veces se pierde el AUTO_INCREMENT;
 * esta migración lo restaura para que el esquema coincida con el origen.
 */
class EnsureAgendaIdAutoIncrement extends Migration
{
    public function up(): void
    {
        // MySQL exige que la columna AUTO_INCREMENT sea una clave (PK o unique).
        // Si el import dejó id sin PK, añadirlo primero; luego poner AUTO_INCREMENT.
        try {
            $this->db->query("ALTER TABLE `agenda` ADD PRIMARY KEY (`id`)");
        } catch (\Throwable $e) {
            // Ya tiene PK o id ya es clave → ignorar
        }
        $this->db->query("ALTER TABLE `agenda` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT");
    }

    public function down(): void
    {
        $this->db->query("ALTER TABLE `agenda` MODIFY COLUMN `id` INT(11) NOT NULL");
    }
}
