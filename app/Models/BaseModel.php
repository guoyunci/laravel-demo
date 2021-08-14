<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    public const CREATED_AT = 'add_time';
    public const UPDATED_AT = 'update_time';

    public $defaultCasts = ['deleted' => 'boolean'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        parent::mergeCasts($this->defaultCasts);
    }

    /**
     * @return static|BaseModel
     * @noinspection PhpMissingReturnTypeInspection
     */
    public static function new()
    {
        return new static();
    }

    public function toArray()
    {
        $items = parent::toArray();
        // $items = array_filter($items, function ($item) {
        //     return !is_null($item);
        // });
        $keys = array_keys($items);
        $keys = array_map(function ($key) {
            return lcfirst(Str::studly($key));
        }, $keys);
        $values = array_values($items);
        return array_combine($keys, $values);
    }

    /**
     * @param  DateTimeInterface  $date
     * @return string
     */
    public function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::instance($date)->toDateTimeString();
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table ?? Str::snake(class_basename($this));
    }
}
