<?php
/**
 * DTO (data access object) That will always be joined by a user
 *
 * @namespace  Core
 * @package    Domain
 * @subpackage Repositories
 * @author     Avi Aialon <aviaialon@gmail.com>
 *
 */
namespace Core\Domain\Models\Traits;

trait OnlyForUserTrait
{
    /**
     * Force append user id on queries
     *
     * @param  bool $company_id
     * @return mixed
     */
    public function newQuery()
    {
        $query = parent::newQuery();
        $query->where('user_id', '=', Auth::user()->id);

        return $query;
    }
}
