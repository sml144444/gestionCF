<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReportationMessage extends Model {
    protected $fillable = [
        'reportation_id', 'user_id', 'message',
        'attachment_path', 'attachment_name', 'attachment_type', 'seen_at',
    ];
        protected $casts = [
        'seen_at' => 'datetime', // ← ajouter
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function reportation() { return $this->belongsTo(Reportation::class); }
}