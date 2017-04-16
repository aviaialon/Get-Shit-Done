<?php
/**
 * Searchable model trait
 *
 * @namespace  Core
 * @package    Domain
 * @subpackage Repositories
 * @author     Avi Aialon <aviaialon@gmail.com>
 *
 */
namespace Core\Domain\Models\Traits;

trait SearchableModelTrait
{
   use Laravel\Scout\Searchable;
}
