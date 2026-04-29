<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReportationMessage extends Model {
    protected $fillable = ['reportation_id', 'user_id', 'message'];
    public function user() { return $this->belongsTo(User::class); }
    public function reportation() { return $this->belongsTo(Reportation::class); }
}