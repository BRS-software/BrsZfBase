<?php

namespace BrsZfBase\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use BrsZfSloth\Repository\Repository;

class JqGrid extends AbstractPlugin
{
    protected $params = [];
    protected $repository;
    protected $setupConditions;

    public function __invoke()
    {
        $this->repository = null;
        $this->updateSelect = null;
        return $this;
        // dbg(1);
        // dbgd($this->controller->params());
        // if (0 === func_num_args()) {
        //     return $this->getAcl();
        // }
        // // $this->getAcl()->assert($)
        // call_user_func_array([$this->getAcl(), 'assert'], func_get_args());
    }

    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    public function getRepository()
    {
        if (null === $this->repository) {
            throw new \LogicException('repository must be set in JqGrid plugin');
        }
        return $this->repository;
    }

    public function setupConditions(\Closure $setupConditions)
    {
        $this->setupConditions = $setupConditions;
        return $this;
    }

    public function getGridRequestParams()
    {
        $p = $this->controller->params();
        $defaultOrder = $this->getRepository()->getDefinition()->getDefaultOrder()->getField();
        return [
            'page' => (int) $p->fromQuery('page', 1), // get the requested page
            'limit' => (int) $p->fromQuery('rows', 100), // get how many rows we want to have into the grid
            'sidx' => $p->fromQuery('sidx', $defaultOrder) ?: $defaultOrder, // get index row - i.e. user click to sort
            'sord' => $p->fromQuery('sord', $this->getRepository()->getDefinition()->getDefaultOrder()->getDirect()), // get the direction
        ];
    }
    public function getGridData()
    {
        // test repository model interface
        if (! $this->getRepository()->createEntity() instanceof GridModelInterface) {
            throw new \LogicException(
                sprintf('Model entity class %s must implements interface BrsZfBase\Controller\Plugin\GridModelInterface', get_class($this->getRepository()->createEntity()))
            );
        }

        $rqParams = $this->getGridRequestParams();
        $setupConditions = function ($select) {
            if ($this->setupConditions) {
                $sc = $this->setupConditions;
                $sc($select);
            }
            return $select;
        };

        $collection = $this->getRepository()->fetch(function ($select) use ($rqParams, $setupConditions) {
            $select
                ->reset('order')
                // xxx daje możliwość sortowania po dowolnym polu, a nie tylko po tym zwracamym z modelu ::toJsModel()
                ->order(new \BrsZfSloth\Sql\Order($rqParams['sidx'], $rqParams['sord']))
                ->limit($rqParams['limit'])
                ->offset($rqParams['limit'] * $rqParams['page'] - $rqParams['limit'])
            ;
            return $setupConditions($select);
        });

        $totalRecords = $this->getRepository()->select(function ($select) use ($rqParams, $setupConditions) {
            $select
                ->reset('order')
                ->reset('columns')
                ->columns(['num' => new \Zend\Db\Sql\Expression('COUNT(1)')])
            ;
            if ($this->setupConditions) {
                $sc = $this->setupConditions;
                $sc($select);
            }
            return $setupConditions($select);
        })[0]['num'];

        $total = $totalRecords > 0 ? (int) ceil($totalRecords / $rqParams['limit']) : 0;
        $page = $rqParams['page'] <= $total ? $rqParams['page'] : $total;

        return [
            'rows' => array_map(function($m) { return $m->getGridData(); }, $collection->toArray()),
            'records' => $totalRecords,
            'total' => $total,
            'page' => $page,
        ];
    }
}