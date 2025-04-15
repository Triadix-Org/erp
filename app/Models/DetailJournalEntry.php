<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailJournalEntry extends Model
{
    /** @use HasFactory<\Database\Factories\DetailJournalEntryFactory> */
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];
}
