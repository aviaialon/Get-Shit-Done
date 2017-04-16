<?php
/**
 * Base respository interface
 *
 * @namespace  Core
 * @package    Domain
 * @subpackage Repositories
 * @author     Avi Aialon <avi@mventures.ca>
 */
namespace Core\Domain\Repositories;

/**
 * Base respository interface
 *
 * @namespace  Core
 * @package    Domain  
 * @subpackage Repositories
 * @author     Avi Aialon <avi@mventures.ca>
 */
interface RepositoryInterface
{
    /**
     * Gets all the record by id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getById($id);
 
    /**
     * Gets all the available record
     *
     * @return \App\Models\ModelInterface[]
     */
    public function getAll();

    /**
     * Creates a new model instance
     *
     * @return \Core\Domain\Models\ModelBase
     */
    public function newInstance();
}
