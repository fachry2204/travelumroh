<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\RecordsAuditLog;

class PilgrimDocument extends Model
{
    use HasFactory, RecordsAuditLog;

    protected $fillable = [
        'pilgrim_id', 'document_type', 'file_path', 'original_filename',
        'status', 'note', 'validated_by', 'validated_at',
    ];

    protected $casts = ['validated_at' => 'datetime'];

    public function pilgrim() { return $this->belongsTo(Pilgrim::class); }
    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }
}
