<?php
// =================================================================================
// Modelo: DocumentsModel
// =================================================================================

namespace App\Models;

use CodeIgniter\Model;

class DocumentsModel extends Model
{
    // Nombre de la tabla asociada
    protected $table = 'documents';
    // Llave primaria
    protected $primaryKey = 'id';
    // Campos permitidos para inserción/actualización masiva
    protected $allowedFields = [
        'sender_id', 'receiver_id', 'title', 'description', 'file_path', 'sent_at', 'read_at'
    ];
    // Activar timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // =================================================================================
    // Métodos personalizados para documentos
    // =================================================================================


    // Obtener query de documentos enviados por un usuario (sin paginar)
    public function getSentDocumentsQuery($userId)
    {
        return $this->select('documents.*, users.name as receiver_name, users.email as receiver_email, users.identification as receiver_identification')
                    ->join('users', 'users.id = documents.receiver_id')
                    ->where('documents.sender_id', $userId)
                    ->orderBy('documents.created_at', 'DESC');
    }


    // Obtener query de documentos recibidos por un usuario (sin paginar)
    public function getReceivedDocumentsQuery($userId)
    {
        return $this->select('documents.*, users.name as sender_name, users.email as sender_email, users.identification as sender_identification')
                    ->join('users', 'users.id = documents.sender_id')
                    ->where('documents.receiver_id', $userId)
                    ->orderBy('documents.created_at', 'DESC');
    }

    // Marcar documento como leído
    public function markAsRead($documentId)
    {
        return $this->update($documentId, ['read_at' => date('Y-m-d H:i:s')]);
    }

    // Obtener documento con información del remitente y destinatario
    public function getDocumentWithUsers($documentId)
    {
        return $this->select('documents.*, sender.name as sender_name, sender.email as sender_email, receiver.name as receiver_name, receiver.email as receiver_email')
                    ->join('users as sender', 'sender.id = documents.sender_id')
                    ->join('users as receiver', 'receiver.id = documents.receiver_id')
                    ->where('documents.id', $documentId)
                    ->first();
    }
}