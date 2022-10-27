<?php

namespace Hairavel\Core\UI;

use Doctrine\DBAL\Schema\View;
use Hairavel\Core\UI\Table\Node;
use Hairavel\Core\UI\Widget\Icon;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Hairavel\Core\Model\ModelAgent;
use Hairavel\Core\Util\Tree;
use Hairavel\Core\UI\Table\Action;
use Hairavel\Core\UI\Table\Batch;
use Hairavel\Core\UI\Table\Column;
use Hairavel\Core\UI\Table\Filter;
use Hairavel\Core\UI\Table\FilterType;

/**
 * Form UI
 * Class Table
 * @package Hairavel\Core\UI
 */
class Table
{
    public ?Eloquent $model = null;
    public ?ModelAgent $query = null;
    public array $fields = [];
    protected ?Collection $columns = null;
    protected ?Collection $filters = null;
    protected ?Collection $filtersType = null;
    protected ?Action $action = null;
    protected ?Batch $batch = null;
    protected array $expand = [];
    protected array $class = [];
    protected array $rows = [];
    protected array $map = [];
    protected array $filterParams = [];
    protected string $url = '';
    protected bool $urlBind = true;
    protected string $key = '';
    protected ?bool $dialog = null;
    protected string $title = '';
    private ?string $eventName = null;
    protected array $headerNode = [];
    protected array $footerNode = [];
    protected array $sideNode = [];
    protected array $pageNode = [];
    protected bool $tree = false;
    protected bool $back = false;
    protected int $limit = 20;
    protected array $attr = [];
    protected $script = [];
    protected $scriptReturn = [];
    protected $scriptData = [];
    protected ?\Closure $dataCallback = null;
    protected $data;
    protected array $bindFilter = [];
    protected array $columnsData = [];
    protected array $statics = [];
    protected bool $debug = false;

    /**
     * Table constructor.
     * @param $data
     */
    public function __construct($data)
    {
        if ($data instanceof Eloquent) {
            $this->model = $data;
            $this->query = new ModelAgent($data);
            $this->fields = \Schema::getColumnListing($data->getTable());
        } else {
            $this->data = $data;
        }
        $this->columns = Collection::make();
        $this->filters = Collection::make();
        $this->filtersType = Collection::make();

        if (request()->header('x-dialog')) {
            $this->dialog = true;
        }
    }

    /**
     * set column
     * @param string $name
     * @param string $label
     * @param null $callback
     * @return Column
     */
    public function column(string $name = '', string $label = '', $callback = null): Column
    {
        //Associative model
        if ($this->model && \Str::contains($label, '.')) {
            return $this->joinColumn($name, $label, $callback);
        }

        //array object
        if (\Str::contains($label, '->')) {
            $label = str_replace('->', '.', $label);
            return $this->addColumn($name, $label, $callback);
        }

        //whether to associate the model
        if ($this->model && $this->hasRelationColumn($label)) {
            $this->query->with($label);
            return $this->addColumn($name, $label, $callback)->setRelation($label);
        }
        return $this->addColumn($name, $label, $callback);
    }

    /**
     * expand row
     * @param string $title
     * @param array $node
     * @param int $width
     * @return $this
     */
    public function expand(string $title = '', array $node = [], int $width = 100): self
    {
        $this->expand = [
            'title' => $title,
            'width' => $width,
            'vRender:expandedRowRender:rowData' => $node
        ];
        return $this;
    }

    /**
     * Add column parameter
     * @param $name
     * @param $label
     * @param $callback
     * @return Column
     */
    protected function addColumn($name, $label, $callback): Column
    {
        $column = new Column($name, $label, $callback);
        $column->setLayout($this);
        return tap($column, function ($value) {
            $this->columns->push($value);
        });
    }

    /**
     * Judgment association model
     * @param $relation
     * @return bool
     */
    protected function hasRelationColumn($relation): bool
    {
        if (!method_exists($this->model, $relation)) {
            return false;
        }
        if (!$this->model->{$relation}() instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            return false;
        }
        return true;
    }

    /**
     * Associative model
     * @param $name
     * @param $label
     * @param $callback
     * @return Column
     */
    protected function joinColumn($name, $label, $callback): Column
    {
        [$relation, $field] = explode('.', $label, 2);
        $this->query->with($relation);
        return $this->addColumn($name, str_replace('->', '.', $field), $callback)->setRelation($relation);
    }

    /**
     * Get the column set
     * @return Collection
     */
    protected function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * set row data
     * @param \Closure $callback
     * @return $this
     */
    public function row(\Closure $callback): self
    {
        $this->rows[] = $callback;
        return $this;
    }

    /**
     * Set field mapping
     * @param array $map
     * @return $this
     */
    public function map(array $map): self
    {
        $this->map = array_merge($this->map, $map);
        return $this;
    }

    /**
     * filter parameters
     * @param $key
     * @param $value
     * @return $this
     */
    public function filterParams($key, $value): self
    {
        $this->filterParams[$key] = $value;
        return $this;
    }

    /**
     * custom header
     * @param string|callable|object $callback
     * @return $this
     */
    public function header($callback): self
    {
        $this->headerNode[] = $callback;
        return $this;
    }

    /**
     * custom bottom
     * @param string|callable|object $callback
     * @return $this
     */
    public function footer($callback): self
    {
        $this->footerNode[] = $callback;
        return $this;
    }

    /**
     * custom side
     * @param $callback
     * @param string $direction
     * @param bool $resize
     * @param string $width
     * @return $this
     */
    public function side($callback, string $direction = 'left', bool $resize = false, string $width = '100px'): self
    {
        $this->sideNode[] = [
            'callback' => $callback,
            'direction' => $direction,
            'resize' => $resize,
            'width' => $width
        ];
        return $this;
    }

    /**
     * Customize page content
     * @param $callback
     * @param string $direction
     * @return $this
     */
    public function page($callback, string $direction = 'left'): self
    {
        $this->pageNode[] = [
            'callback' => $callback,
            'direction' => $direction
        ];
        return $this;
    }

    /**
     * Set style class
     * @param string $class
     * @return $this
     */
    public function class(string $class): self
    {
        $this->class[] = $class;
        return $this;
    }

    /**
     * Set the binding method
     * @param $urlBind true use url to bind the control
     * @return $this
     */
    public function urlBind($urlBind): self
    {
        $this->urlBind = $urlBind;
        return $this;
    }

    /**
     * Set the request event binding name
     * @param string|null $eventName
     * @return $this
     */
    public function eventName(?string $eventName): self
    {
        $this->eventName = $eventName;
        return $this;
    }

    /**
     * set filter conditions
     * @param string $name
     * @param string $field
     * @param bool $where
     * @param null $default
     * @return Filter
     */
    public function filter(string $name, string $field, $where = true, $default = null): Filter
    {
        $filter = new \Hairavel\Core\UI\Table\Filter($name, $field, $where, $default);
        $filter->setLayout($this);
        return tap($filter, function ($value) {
            $this->filters->push($value);
        });
    }


    /**
     * filter type
     * @param string $name
     * @param callable|null $where
     * @return FilterType
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function filterType(string $name, callable $where = null): FilterType
    {
        if (!isset($this->filterParams['type'])) {
            $this->filterParams('type', request()->get('type', 0));
        }
        $filterType = new \Hairavel\Core\UI\Table\FilterType($name, $where, $this->filterParams['type']);
        $filterType->setLayout($this);
        return tap($filterType, function ($value) {
            $this->filtersType->push($value);
        });
    }

    /**
     * Bind other filter data
     * @param string $filterName
     * @param string $name
     * @return $this
     */
    public function bindFilter(string $filterName,string $name = 'filter'): self
    {
        $this->bindFilter[] = "{$filterName}.{$name}";
        return $this;
    }

    /**
     * bind data
     * @param string $dataName
     * @return $this
     */
    public function columnsData(string $dataName): self
    {
        $this->columnsData[] = $dataName;
        return $this;
    }

    /**
     * set action
     * @return Action
     */
    public function action(): Action
    {
        if (!$this->action) {
            $this->action = new Action();
        }
        return $this->action;
    }

    /**
     * Batch operations
     * @return Batch
     */
    public function batch(): Batch
    {
        if (!$this->batch) {
            $this->batch = new Batch();
        }
        return $this->batch;
    }

    /**
     * tree form
     * @return $this
     */
    public function tree(): self
    {
        $this->tree = true;
        return $this;
    }

    /**
     * tree state
     * @return bool
     */
    public function getTree(): bool
    {
        return $this->tree;
    }

    /**
     * table title
     * @param string $title
     * @return $this
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Number of pagination
     * @param int $num
     * @return $this
     */
    public function limit(int $num): self
    {
        $this->limit = $num;
        return $this;
    }

    /**
     * model object
     */
    public function model(): ModelAgent
    {
        return $this->query;
    }

    /**
     * model object
     * @return Eloquent
     */
    public function modelElo(): ?Eloquent
    {
        return $this->model;
    }

    /**
     * Set additional properties
     * @param $name
     * @param $value
     * @return $this
     */
    public function attr($name, $value): self
    {
        $this->attr[$name] = $value;
        return $this;
    }

    /**
     * Window scroll parameters
     * @param $x
     * @param $y
     * @return $this
     */
    public function scroll($x,$y){
        return $this->attr('scroll',['x' => $x,'y' => $y]);
    }

    /**
     * Set the table primary key
     * @param $key
     * @return $this
     */
    public function key($key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Pop-ups
     * @param bool $status
     * @return $this
     */
    public function dialog(bool $status = true): self
    {
        $this->dialog = $status;
        return $this;
    }

    /**
     * url data
     * @param string $url
     * @return $this
     */
    public function url(string $url = ''): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get URL
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $content
     * @param string $return
     * @return $this
     */
    public function script(string $content = '', string $return = ''): self
    {
        $this->script[] = $content;
        $this->scriptReturn[] = $return;
        return $this;
    }

    /**
     * Front-end static coverage data
     * @param string|array $statics
     * @param string $key stype|css|scriptString|script
     * @return $this
     */
    public function statics($statics,string $key = 'style'): self
    {
        $this->statics[$key] = array_merge($this->statics[$key] ?? [],is_array($statics) ? $statics : [$statics]);
        return $this;
    }

    /**
     * debug
     * @param bool $debug
     * @return $this
     */
    public function debug(bool $debug = true): self
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function scriptData($data): self
    {
        $this->scriptData = array_merge($this->scriptData, $data);
        return $this;
    }

    /**
     * Data output
     * @param callable $callback
     */
    public function export(callable $callback): void
    {
        // filter data
        $this->filters->map(function ($filter) {
            $filter->execute($this->query);
        });
        $this->filtersType->map(function ($filter, $key) {
            $filter->execute($this->query, $key);
        });

        // Query export data
        $data = $this->query->eloquent()->get();
        // execute render output
        $export = new \Hairavel\Core\UI\Table\Export();
        $callback($export);
        $export->render($data);
    }

    /**
     * Data callback
     * @param callable $callback
     * @return $this
     */
    public function dataCallback(callable $callback): self
    {
        $this->dataCallback = $callback;
        return $this;
    }

    /**
     * render column node
     * @return array
     */
    public function renderColumn(): array
    {
        return $this->getColumns()->map(function ($column, $key) {
            $render = $column->getRender();
            if (!empty($render)) {
                $render['sort'] = $render['sort'] ?? $key;
                return $render;
            }
        })->filter()->sortBy('sort')->values()->toArray();
    }

    /**
     * render component
     * @return Node
     */
    public function renderNode()
    {
        // expand node
        $headerNode = [];
        foreach ($this->headerNode as $vo) {
            $headerNode[] = is_callable($vo) ? $vo() : $vo;
        }
        $footerNode = [];
        foreach ($this->footerNode as $vo) {
            $footerNode[] = is_callable($vo) ? $vo() : $vo;
        }
        // action node
        $actionNode = $this->action ? $this->action()->render() : [];
        // batch node
        $batchNode = $this->batch ? $this->batch()->render() : [];
        // type filter
        $typeNode = $this->filtersType->map(function ($filter, $key) {
            return $filter->render($key);
        })->toArray();
        // filter data
        $filters = $this->filters->map(function ($filter) {
            return $filter->render();
        })->toArray();
        $filterNode = [];
        $quickNode = [];
        foreach ($filters as $vo) {
            if ($vo['quick']) {
                $quickNode[] = $vo['render'];
            } else {
                $filterNode[] = $vo['render'];
            }
        }
        // table column node
        $columnNode = $this->getColumns()->map(function ($column, $key) {
            $render = $column->getRender();
            if (!empty($render)) {
                $render['sort'] = $render['sort'] ?? $key;
                return $render;
            }
        })->filter()->sortBy('sort')->values()->toArray();

        $keyName = $this->key ?: ($this->model ? $this->model->getKeyName() : '');
        $node = new Node($this->url ?: url(request()->path() . '/ajax'), $keyName, $this->title);
        $node->urlBind($this->urlBind);
        $node->class(implode(' ', $this->class));
        $node->params($this->attr);
        $node->debug($this->debug);
        $node->data($this->filterParams);
        if(isset($this->data)){
            $node->data($this->formatData($this->data, $this->columns ?? [],$this->tree),'data');
        }
        $node->bindFilter($this->bindFilter);
        $node->columnsData($this->columnsData);
        $node->columns($columnNode);
        $node->expand($this->expand);
        $node->eventName($this->eventName);

        foreach ($this->script as $key => $value) {
            $node->script($value, $this->scriptReturn[$key]);
        }
        if ($this->scriptData) {
            $node->scriptData($this->scriptData);
        }

        $node->limit($this->limit);
        $node->statics($this->statics);
        $node->type($typeNode);
        $node->quickFilter($quickNode);
        $node->filter($filterNode);

        foreach ($this->sideNode as $vo) {
            $node->side($vo['callback'], $vo['direction'], $vo['resize'], $vo['width']);
        }
        foreach ($this->pageNode as $vo) {
            $node->page($vo['callback'], $vo['direction']);
        }

        $node->header($headerNode);
        $node->footer($footerNode);

        if ($actionNode) {
            $node->action($actionNode);
        }
        if ($batchNode) {
            $node->bath($batchNode);
        }
        return $node;
    }

    /**
     * render table
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function render()
    {
        return app_success('ok',$this->renderArray());
    }

    /**
     * render table (array)
     * @return array
     */
    public function renderArray()
    {
        $node = $this->renderNode();
        return $node->render();
    }

    /**
     * only render table
     * @return array
     */
    public function renderTableCore()
    {
        $node = $this->renderNode();
        return $node->renderTableCore();
    }

    /**
     * Data rendering
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function renderAjax()
    {
        // filter data
        $this->filters->map(function ($filter) {
            $filter->execute($this->query);
        });
        $this->filtersType->map(function ($filter, $key) {
            $filter->execute($this->query, $key);
        });

        // column filter data
        if ($this->columns) {
            $this->columns->map(function ($column) {
                if (method_exists($column, 'execute')) {
                    $column->execute($this->query);
                }
            });
        }

        //primary key
        $key = $this->key ?: ($this->model ? $this->model->getKeyName() : '');

        $limit = request()->get('limit', $this->limit);

        // query list
        if ($this->query) {
            $data = $this->query;
            if ($this->tree) {
                $data = $data->paginate(99999)->eloquent();
                $data->setCollection($data->getCollection()->toTree());
            } else {
                $data = $data->paginate($limit)->eloquent();
            }
        } else {
            $data = $this->paginateCollection($this->data, $limit);
            if ($this->tree) {
                $data->setCollection(collect(Tree::arr2table($data->getCollection()->toArray(), $key, 'parent_id')));
            }
        }
        if ($this->dataCallback) {
            $dataCallback = call_user_func($this->dataCallback, $data->getCollection());
            $data->setCollection($dataCallback);
        }

        $totalPage = $data->lastPage();
        $page = $data->currentPage();
        $total = $data->total();


        $columns = [];
        if ($this->columns) {
            $columns = $this->columns->map(function ($column) {
                return $column;
            })->filter();
        }


        // set row data callback
        $this->map[] = $key;

        // sort automatically set the key
        if ($this->tree) {
            $this->map['key'] = $key;
        }

        $resetData = $this->formatData($data, $columns,$this->tree);

        return app_success('ok', [
            'data' => $resetData,
            'total' => $total,
            'pageSize' => $limit,
            'totalPage' => $totalPage,
        ]);
    }

    /**
     * render row data
     * @param Collection $data
     * @param bool $tree
     * @return array
     */
    public function renderRowData(Collection $data, bool $tree = true): array
    {
        if ($this->dataCallback) {
            $data = call_user_func($this->dataCallback, $data);
        }
        $key = $this->key ?: ($this->model ? $this->model->getKeyName() : '');
        $columns = [];
        if ($this->columns) {
            $columns = $this->columns->map(function ($column) {
                return $column;
            })->filter();
        }
        // set row data callback
        $this->map[] = $key;

        // sort automatically set the key
        if ($this->tree) {
            $this->map['key'] = $key;
        }
        return $this->formatData($data, $columns, $tree);

    }

    /**
     * @param $data
     * @param $columns
     * @param bool $tree
     * @return array
     */
    private function formatData($data, $columns, bool $tree = true): array
    {
        $resetData = [];
        foreach ($data as $vo) {
            $rowData = [];
            if ($this->rows) {
                foreach ($this->rows as $row) {
                    if ($call = call_user_func($row, $vo)) {
                        $rowData = $call;
                    }
                }
            }
            foreach ($columns as $column) {
                if ($colData = $column->getData($vo)) {
                    foreach ($colData as $k => $v) {
                        $rowData[$k] = $v;
                    }
                }
            }
            if ($this->map) {
                foreach ($this->map as $k => $v) {
                    $rowData[is_int($k) ? str_replace(['.', '->'], '_', $v) : $k] = is_callable($v) ? call_user_func($v, $vo) : Tools ::parsingArrData($vo, $v);
                }
            }
            if ($vo['children'] && $tree) {
                $rowData['children'] = $this->formatData($vo['children'], $columns, $tree);
            }
            $resetData[] = $rowData;
        }
        return $resetData;
    }

    /**
     * Collection pagination
     * @param $collection
     * @param $perPage
     * @param string $pageName
     * @param null $fragment
     * @return LengthAwarePaginator
     */
    protected function paginateCollection($collection, $perPage, $pageName = 'page', $fragment = null): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);
        return new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'pageName' => $pageName,
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
                'fragment' => $fragment
            ]
        );
    }
}
