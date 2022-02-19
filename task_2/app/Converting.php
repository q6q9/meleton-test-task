<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $currency_from
 * @property string $currency_to
 * @property float $value
 * @property float $converted_value
 * @property float $rate
 * @property string $created_at
 *
 * @method self create(array $attributes)
 */
class Converting extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        "currency_from",
        "currency_to",
        "value",
        "converted_value",
        "rate",
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        "id",
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
