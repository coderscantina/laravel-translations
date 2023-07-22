<?php namespace CodersCantina\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * @property int $id
 * @property string|null $namespace
 * @property string $key
 * @property string $value
 * @property string $language_iso
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereLanguageIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereNamespace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation withoutTrashed()
 * @mixin \Eloquent
 */
class Translation extends Model
{
    use SoftDeletes;

    protected $table = 'translations';

    protected $fillable = [
        'namespace',
        'key',
        'value',
        'language_iso'
    ];
}
