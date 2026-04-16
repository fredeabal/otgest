<?php
// =================================================================================
// Migración: Tabla documents
// =================================================================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Documents extends Migration
{
    // Crea la tabla documents con los campos necesarios para el envío y recepción de documentos
    public function up()
    {
        // Definición de la estructura de la tabla documents
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sender_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                // ID del usuario que envía el documento
            ],
            'receiver_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                // ID del usuario que recibe el documento
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                // Título del documento
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                // Descripción opcional del documento
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                // Ruta del archivo subido
            ],
            'sent_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                // Fecha y hora de envío
            ],
            'read_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                // Fecha y hora de lectura, null si no se ha leído
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        // Claves foráneas
        $this->forge->addForeignKey('sender_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('documents');
    }

    // Elimina la tabla documents si existe
    public function down()
    {
        $this->forge->dropTable('documents');
    }
}