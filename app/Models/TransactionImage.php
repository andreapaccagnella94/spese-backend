<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'original_name',
        'mime_type',
        'size',
        'transaction_type',
        'account_id',
        'extracted_text',
        'extracted_amount',
        'category_id',
        'extracted_date',
        'extracted_description',
        'is_processed',
    ];

    protected $casts = [
        'extracted_amount' => 'decimal:2',
        'extracted_date' => 'date',
        'is_processed' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
