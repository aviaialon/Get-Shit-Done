<?php
/**
 * Base Repo class
 *
 * @namespace    Core
 * @package      Domain
 * @subpackage   Repositories
 * @author       Avi Aialon <aviaialon@gmail.com>
 *
 */
namespace Core\Domain\Repositories;

use Cache;

/**
 * Base Repo class
 *
 * @namespace    Core
 * @package      Domain
 * @subpackage   Repositories
 * @author     Avi Aialon <aviaialon@gmail.com>
 */
abstract class Repository
{
    /**
     * The associated model instance
     *
     * @var \Core\Domain\Models\ModelBase
     */
    protected $_dataAccessInterface;

    /**
     * The associated model class
     *
     * @var string
     */
    protected $_domainModelClass;

    /**
     * Repository constructor.
     *
     * @param \Core\Domain\Models\ModelBase $_dataAccessInterface
     */
    public function __construct(\Core\Domain\Models\ModelBase $_dataAccessInterface = null)
    {
        $this->_dataAccessInterface = $_dataAccessInterface;

        if (empty($this->_dataAccessInterface) === true) {
            if (empty($this->_domainModelClass)) {
                $classShortname          = strtolower((new \ReflectionClass($this))->getShortName());
                $this->_domainModelClass = sprintf('\\Core\\Domain\\Models\\%s\\%s',
                                           ucwords(str_plural($classShortname)), ucwords($classShortname));
            }

            if (class_exists($this->_domainModelClass) === false) {
                throw new \UnexpectedValueException(sprintf(
                    'The model class %s could not be loaded via %s',
                    $this->_domainModelClass,
                    __CLASS__
                ));
            }

            $this->_dataAccessInterface = new $this->_domainModelClass();
        }
    }

    /**
     * Creates a new model instance
     *
     * @return \Core\Domain\Models\ModelBase
     */
    public function newInstance()
    {
        $instanceClass = $this->_domainModelClass;

        return new $instanceClass();
    }

    /**
     * Removes relations
     *
     * @return ModelBase
     */
    public function noRelation()
    {
        $this->_dataAccessInterface->relate([]);

        return $this;
    }


    /**
     * sets relations
     *
     * @param array $relation
     * @return ModelBase
     */
    public function relate(array $relation = [])
    {
        $this->_dataAccessInterface->relate($relation);

        return $this;
    }


    /**
     * Search model library
     *
     * @param  string $term
     * @return \Core\Domain\Models\ModelBase[]
     * @see https://github.com/jarektkaczyk/eloquence/wiki
     */
    public function search($term = null)
    {
        $columns = [];
        $search  = [];

        if (isset($this->_dataAccessInterface->searchable)) {
            $columns = $this->_dataAccessInterface->searchable;
        }

        if (empty($columns)) {
            return null;
        }

        foreach (explode(' ', $term) as $word) {
            $search[] = sprintf('*%s*', strtolower($word));
            $search[] = sprintf('*%s*', ucfirst($word));
            $search[] = sprintf('*%s*', $word);
        }

        $search[] = $term;

        return $this->_dataAccessInterface->search($search, $columns, true);
    }

    /**
     * Returns records by binding
     *
     * @param  array $params
     * @return \Core\Domain\Models\ModelBase[]
     * @codeCoverageIgnore
     */
    public function find(array $params = [])
    {
        return $this->_dataAccessInterface->where($params)->get();
    }

    /**
     * Returns records by binding with pagination
     *
     * @param  array $params
     * @param  int $amtPerPage
     * @param  int $currentPage
     * @return \Core\Domain\Models\ModelBase[]
     * @codeCoverageIgnore
     */
    public function paginate(array $params = [], $amtPerPage= 10, $currentPage = 0)
    {
        if ($currentPage > 0) {
            request()->replace(['page' => $currentPage]);
        }

        return $this->_dataAccessInterface->where($params)->paginate($amtPerPage);
    }

    /**
     * Returns the record by id
     *
     * @param  integer $id
     * @return \Core\Domain\Models\ModelBase
     * @codeCoverageIgnore
     */
    public function getById($id)
    {
        return $this->_dataAccessInterface->where('id', (int) $id)->first();
    }

    /**
     * Gets all the available models
     *
     * @return \Core\Domain\Models\ModelBase[]
     */
    public function getAll()
    {
        $_modelClass = $this->_dataAccessInterface;

        return Cache::remember(sprintf('%s::%s', static::class, __FUNCTION__), 2, function() use ($_modelClass) {
            return $_modelClass::orderBy('id', 'ASC')->get(array('*'));
        });
    }

    /**
     * Gets all the available models
     *
     * @return \Core\Domain\Models\ModelBase[]
     */
    public function all()
    {
        return $this->_dataAccessInterface->orderBy('id', 'ASC');
    }


    /**
     * Returns a recordcount
     *
     * @param  array $where
     * @return interger
     */
    public function count($where = [])
    {
        $builder = $this->_dataAccessInterface;

        if (empty($where) === false) {
            $builder->where($where);
        }

        return $builder->count();
    }

    /**
     * Execute an action if the model (by id) exists
     * TODO: Put this in a trait..
     *
     * @param  mixed    $id          The object id
     * @param  \Closure $_fnCallback The callback function to execute when valid
     * @return \Core\Domain\Models\ModelBase
     * @codeCoverageIgnore
     */
    public function whenValid($id, \Closure $_fnCallback)
    {
        return $this->_dataAccessInterface->whenValid($id, $_fnCallback);
    }

    /**
     * Creates a new model instance
     *
     * @param array $attributes
     * @return \Core\Domain\Models\ModelBase
     * @codeCoverageIgnore
     */
    public function create(array $attributes = [])
    {
        $_model = new $this->_domainModelClass();
        $_model->create($attributes);

        return $_model;
    }

    /**
     * Proxy save method
     *
     * @param \Core\Domain\Models\ModelBase $model
     * @return \Core\Domain\Models\ModelBase
     * @codeCoverageIgnore
     */
    public function save(\Core\Domain\Models\ModelBase $model)
    {
        $model->save();

        return $model;
    }

    /**
     * Executes a call from teh repository to the model
     *
     * @param  $name
     * @param  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->_dataAccessInterface, $name], $arguments);
    }
}
