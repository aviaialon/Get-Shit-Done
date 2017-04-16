<?php
/**
 * Base Model
 *
 * @namespace  Core
 * @package    Domain
 * @subpackage Repositories
 * @author     Avi Aialon <aviaialon@gmail.com>
 *
 */
 
namespace Core\Domain\Models;

use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Core\Domain\Models\DtoModelTrait;

/**
 * Base Model
 *
 * @namespace  Core
 * @package    Domain
 * @subpackage Repositories
 * @author     Avi Aialon <aviaialon@gmail.com>
 */
abstract class ModelBase extends Model
{
    use DtoModelTrait;

    /**
     * Eager loaded relationships
     *
     * @var array
     */
    protected $with = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Append custom properties
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     * @see https://mattstauffer.co/blog/laravel-5.0-eloquent-attribute-casting
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Searchable fields
     *
     * @var array
     */
    protected $searchableColumns = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (empty($this->table) === true) {
            $this->table = str_plural(strtolower((new \ReflectionClass($this))->getShortName()));
        }
    }

    /**
     * Returns a hash representation of the value
     *
     * @param  string $value
     * @return string
     */
    public final function hashValue($value = null)
    {
        if (empty($value)) {
            return $value;
        }

        $accentedCharacters = [
            'Å '=>'S', 'Å¡'=>'s', 'Å½'=>'Z', 'Å¾'=>'z', 'Ã€'=>'A', 'Ã'=>'A', 'Ã‚'=>'A', 'Ãƒ'=>'A', 'Ã„'=>'A', 'Ã…'=>'A', 'Ã†'=>'A', 'Ã‡'=>'C', 'Ãˆ'=>'E', 'Ã‰'=>'E',
            'ÃŠ'=>'E', 'Ã‹'=>'E', 'ÃŒ'=>'I', 'Ã'=>'I', 'ÃŽ'=>'I', 'Ã'=>'I', 'Ã‘'=>'N', 'Ã’'=>'O', 'Ã“'=>'O', 'Ã”'=>'O', 'Ã•'=>'O', 'Ã–'=>'O', 'Ã˜'=>'O', 'Ã™'=>'U',
            'Ãš'=>'U', 'Ã›'=>'U', 'Ãœ'=>'U', 'Ã'=>'Y', 'Ãž'=>'B', 'ÃŸ'=>'Ss', 'Ã '=>'a', 'Ã¡'=>'a', 'Ã¢'=>'a', 'Ã£'=>'a', 'Ã¤'=>'a', 'Ã¥'=>'a', 'Ã¦'=>'a', 'Ã§'=>'c',
            'Ã¨'=>'e', 'Ã©'=>'e', 'Ãª'=>'e', 'Ã«'=>'e', 'Ã¬'=>'i', 'Ã­'=>'i', 'Ã®'=>'i', 'Ã¯'=>'i', 'Ã°'=>'o', 'Ã±'=>'n', 'Ã²'=>'o', 'Ã³'=>'o', 'Ã´'=>'o', 'Ãµ'=>'o',
            'Ã¶'=>'o', 'Ã¸'=>'o', 'Ã¹'=>'u', 'Ãº'=>'u', 'Ã»'=>'u', 'Ã½'=>'y', 'Ã¾'=>'b', 'Ã¿'=>'y'
        ];

        $value = trim($value);
        $value = strtr($value, $accentedCharacters);
        $value = preg_replace('/\s/', '', $value);

        return md5(strtolower($value));
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array $attributes
     * @param  null $connection
     * @return ModelBase
     */
    public function newFromBuilder($attributes = array(), $connection = null)
    {
        $instance = parent::newFromBuilder($attributes, $connection);

        if (method_exists($instance, 'onloadEvent') === true) {
            $instance->onloadEvent();
        }

        return $instance;
    }

    /**
     * Removes relations
     *
     * @param  array $relation
     * @return ModelBase
     */
    public function relate(array $relation = [])
    {
        $this->with = $relation;

        return $this;
    }

    /**
     * Execute an action if the model (by id) exists
     *
     * @param  mixed    $id          The object id
     * @param  \Closure $_fnCallback The callback function to execute when valid
     * @return void
     * @codeCoverageIgnore
     */
    public static function whenValid($id, \Closure $_fnCallback)
    {
        $_model = static::find($id);

        if (empty($_model) === false) {
            call_user_func($_fnCallback, $_model);
        }
    }
}
