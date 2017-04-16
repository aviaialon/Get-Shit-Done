<?php
/**
 * Pagination built from a eloquent query builder
 *
 * @namespace    Core
 * @package      Utils
 * @subpackage   Pagination
 * @author       Avi Aialon <aviaialon@gmail.com>
 *
 */
namespace Core\Utils\Pagination;

/**
 * Pagination built from a eloquent query builder
 *
 * @namespace    Core
 * @package      Utils
 * @subpackage   Pagination
 * @author       Avi Aialon <aviaialon@gmail.com>
 *
 */
class QueryBuilderPagination
{
    /**
     * The builder interface. Cant be typed cause neither sofa or eloquent
     * interfaced their builders ... YAY
     *
     * @var mixed
     */
    protected $_builder;

    /**
     * QueryBuilderPagination constructor.
     *
     * @param $builder a query builder
     */
    public function __construct($builder)
    {
        $this->_builder = $builder;
    }

    /**
     * Builds the paginator
     *
     * @param  int $amountPerPage
     * @param  int $pagesToDisplay Amount of page numbers to display
     * @return \Illuminate\Pagination\LengthAwarePaginator | array
     * @throws \InvalidArgumentException
     */
    public function paginate($amountPerPage = 10, $pagesToDisplay = 5)
    {
        if (method_exists($this->_builder, 'paginate') === false) {
            throw new \InvalidArgumentException('Invalid builder provided. Method build does not exists');
        }

        $pagination = $this->_builder->paginate((int) $amountPerPage)->toArray();

        if ((int) $pagination['last_page'] > 0) {
            $midRange            = floor($pagesToDisplay / 2);
            $pagination['range'] = [];
            $pagination['range']['start_range'] = ($pagination['current_page'] >= $pagesToDisplay) ? $pagination['current_page'] - $midRange : 1;
            $pagination['range']['end_range'] 	= ($pagination['current_page'] >= $pagesToDisplay) ? $pagination['current_page'] + $midRange : $pagesToDisplay;

            if($pagination['range']['start_range'] <= 0) {
                $pagination['range']['end_range']  += abs($pagination['range']['start_range']) + 1;
            }

            if($pagination['range']['end_range'] > $pagination['last_page']) {
                $pagination['range']['end_range'] = $pagination['last_page'];
            }

            $pagination['range']['pages']     = range($pagination['range']['start_range'], $pagination['range']['end_range']);
            $pagination['range']['start_dot'] = 0;
            $pagination['range']['end_dot']   = 0;

            $pagination['range']['before_dot'] = [];
            $pagination['range']['after_dot']  = [];

            if ($pagination['current_page'] >= $pagesToDisplay) {
                $pagination['range']['start_dot'] = ($pagination['range']['start_range'] - 1);
            }

            if ($pagination['range']['start_dot'] > 2) {
                $pagination['range']['before_dot'] = range(1, 2);
            }

            if (($pagination['last_page'] - $pagesToDisplay) > $pagination['current_page']) {
                $pagination['range']['after_dot'] = range($pagination['last_page'] - 1, $pagination['last_page']);
                $pagination['range']['end_dot']   = $pagination['range']['end_range'] + 1;
            }

            /*
            if ($pagination['current_page'] >= $amountPerPage) {
                $pagination['range']['start_dot'] = $pagination['range']['start_range'] - 1;

                if ($pagination['range']['start_dot'] > 1) {
                    $i = 1;
                    $check = true;
                    while ($i < 4 && $check) {
                        array_unshift($pagination['range']['before_dot'], $pagination['range']['start_dot'] - $i);
                        $check = ($pagination['range']['start_dot'] - ($i + 1)) > 1;
                        $i++;
                    }
                }
            }

            if ($pagination['range']['end_range'] < $pagination['last_page']) {
                $pagination['range']['end_dot'] = $pagination['range']['end_range'] + 1;

                if ($pagination['range']['end_dot'] < $pagination['last_page']) {
                    $i = 1;
                    $check = true;
                    while ($i < 4 && $check) {
                        array_push ($pagination['range']['after_dot'], $pagination['range']['end_dot'] + $i);
                        $check = ($pagination['range']['end_dot'] + ($i + 1)) < (int) $pagination['last_page'];
                        $i++;
                    }
                }
            }
            */
        }

        return $pagination;
    }
}
