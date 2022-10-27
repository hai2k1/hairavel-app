<?php

namespace Hairavel\Core\UI;

use Hairavel\Core\Exceptions\ErrorException;
use Hairavel\Core\UI\Form\Node;
use Hairavel\Core\UI\Widget\Icon;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Hairavel\Core\Model\ModelAgent;
use Hairavel\Core\Util\View;

/**
 * Form UI
 *Class Form
 * @package Hairavel\Core\UI
 *
 * @method Form\Area area(string $name, array $map = [], string $has = '')
 * @method Form\Cascader cascader(string $name, string $field, $data = null, string $has = '')
 * @method Form\CheckBox checkbox(string $name, string $field, $data = null, string $has = '')
 * @method Form\Choice choice(string $name, string $field, string $has = '')
 * @method Form\Color color(string $name, string $field, string $has = '')
 * @method Form\Data data(string $name, string $field, string $has = '')
 * @method Form\Date date(string $name, string $field, string $has = '')
 * @method Form\Daterange daterange(string $name, string $field, string $has = '')
 * @method Form\Datetime datetime(string $name, string $field, string $has = '')
 * @method Form\Editor editor(string $name, string $field, string $has = '')
 * @method Form\Email email(string $name, string $field, string $has = '')
 * @method Form\File file(string $name, string $field, string $has = '')
 * @method Form\Files files(string $name, string $field, string $has = '')
 * @method Form\Image image(string $name, string $field, string $has = '')
 * @method Form\Images images(string $name, string $field, string $has = '')
 * @method Form\Ip ip(string $name, string $field, string $has = '')
 * @method Form\Location location(string $name, string $field, string $has = '')
 * @method Form\Password password(string $name, string $field, string $has = '')
 * @method Form\Radio radio(string $name, string $field, $data = null, string $has = '')
 * @method Form\Select select(string $name, string $field, $data = null, string $has = '')
 * @method Form\Tree tree(string $name, string $field, $data = null, string $has = '')
 * @method Form\TreeSelect treeSelect(string $name, string $field, $data = null, string $has = '')
 * @method Form\Toggle toggle(string $name, string $field, string $has = '')
 * @method Form\Tags tags(string $name, string $field, string $has = '')
 * @method Form\Tel tel(string $name, string $field, string $has = '')
 * @method Form\Text text(string $name, string $field, string $has = '')
 * @method Form\Textarea textarea(string $name, string $field, string $has = '')
 * @method Form\Number number(string $name, string $field, string $has = '')
 * @method Form\Time time(string $name, string $field, string $has = '')
 * @method Form\Url url(string $name, string $field, string $has = '')
 */
class Form
{
    public $model;
    public $modelElo;
    public $info;
    protected string $title = '';
    protected bool $back = true;
    protected array $attr = [];
    protected array $extend = [];
    protected string $method = 'post';
    protected string $action = '';
    protected array $keys = [];
    protected array $row = [];
    protected array $flow = [];
    protected array $assign = [];
    protected array $script = [];
    protected array $scriptReturn = [];
    protected array $sideNode = [];
    protected bool $dialog = false;
    protected bool $vertical = true;
    protected array $map = [];
    public Collection $element;
    private ?array $bottom = null;
    protected array $statics = [];
    protected bool $debug = false;

    /**
     * Form constructor.
     * @param $data
     * @param bool $model
     */
    public function __construct($data = null, bool $model = true)
    {

        if (!$model) {
            // dummy data
            $this->info = $data;
        } else {
            // data model
            if ($data instanceof Eloquent) {
                $this->model = new ModelAgent($data);
                $this->modelElo = $data;
            } else {
                $this->info = $data;
            }
        }
        $this->element = Collection::make();

        if (request()->header('x-dialog')) {
            $this->dialog = true;
        }
    }

    /**
     * Set conditional primary key
     * @param $key
     * @param $value
     */
    public function setKey($key, $value): void
    {
        if ($key && $value) {
            $this->keys[$key] = $value;
        }
        if (!$this->model) {
            return;
        }
        $this->setInfo();
    }

    /**
     * Get current data
     * @return array|Eloquent
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * model object
     * @return ModelAgent
     */
    public function model(): \Hairavel\Core\Model\ModelAgent
    {
        return $this->model;
    }

    /**
     * model object
     * @return Eloquent
     */
    public function modelElo(): ?Eloquent
    {
        return $this->modelElo;
    }

    /**
     * Get the set of elements
     * @param null $class
     */
    public function getElement($class = null, $num = 0): Collection
    {
        if ($class) {
            $i = 0;
            foreach ($this->element as $vo) {
                if ($vo instanceof $class) {
                    if ($i === $num) {
                        return $vo;
                    }
                    $i++;
                }
            }
        }
        return $this->element;
    }

    /**
     * form title
     * @param string $title
     * @param bool $back
     * @return $this
     */
    public function title(string $title, bool $back = true): self
    {
        $this->title = $title;
        $this->back = $back;
        return $this;
    }

    /**
     * Additional script
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
     * Additional properties
     * @param $name
     * @param $value
     * @return $this
     */
    public function attr($name, $value): Form
    {
        $this->attr[] = $name . '="' . $value . '"';
        return $this;
    }

    /**
     * Multi-line components
     * @return Form\Row
     */
    public function row(): Form\Row
    {
        $data = new Form\Row();
        $data->dialog($this->dialog);
        $data->vertical($this->vertical);
        $this->element->push($data);
        return $data;
    }

    /**
     * switch components
     * @return Form\Tab
     */
    public function tab(): Form\Tab
    {
        $data = new Form\Tab();
        $data->dialog($this->dialog);
        $data->vertical($this->vertical);
        $this->element->push($data);
        return $data;
    }

    /**
     * Card components
     * @param $callback
     * @return Form\Card
     */
    public function card($callback): Form\Card
    {
        $data = new Form\Card($callback);
        $data->dialog($this->dialog);
        $data->vertical($this->vertical);
        $this->element->push($data);
        return $data;
    }

    /**
     * Html content
     * @param $name
     * @param $callback
     * @return Form\Html
     */
    public function html($name, $callback): Form\Html
    {
        $data = new Form\Html($name, $callback);
        $data->dialog($this->dialog);
        $data->vertical($this->vertical);
        $this->element->push($data);
        return $data;
    }

    /**
     * layout components
     * @param $callback
     * @return Form\Layout
     */
    public function layout($callback): Form\Layout
    {
        $data = new Form\Layout($callback);
        $data->dialog($this->dialog);
        $this->element->push($data);
        return $data;
    }

    // sidebar element
    public function side($callback, string $direction = 'left'): self
    {
        $this->sideNode[] = [
            'callback' => $callback,
            'direction' => $direction
        ];
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
     * Bullet width
     * @param string $width
     * @return $this
     */
    public function width(string $width): self
    {
        $this->statics(".page-dialog{width: {$width};max-width:none;}");
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
     * Get form data
     */
    public function renderData($info)
    {
        $collection = Collection::make();
        $this->element->map(function ($item) use ($collection, $info) {
            $data = $item->getData($info);
            foreach ($data as $key => $vo) {
                $collection->put($key, $vo);
            }
        });
        if ($this->map) {
            foreach ($this->map as $k => $v) {
                $key = is_int($k) ? str_replace(['.', '->'], '_', $v) : $k;
                $vo = is_callable($v) ? call_user_func($v, $info) : Tools::parsingArrData($info,$v);
                $collection->put($key, $vo);
            }
        }
        return $collection->toArray();
    }

    /**
     * @return array
     */
    public function renderForm(): array
    {
        return $this->element->map(function ($vo, $key) {
            $sort = $vo->getSort();
            $sort = $sort ?? $key;

            $groupRule = $vo->getGroup();
            $group = [];
            foreach ($groupRule as $rule) {
                if (is_array($rule['value'])) {
                    $value = json_encode($rule['value']);
                    $group[] = "{$value}.indexOf(data.{$rule['name']}) !== -1";
                }else {
                    $group[] = "data.{$rule['name']} == '{$rule['value']}'";
                }

            }
            $group = $group ? implode(' || ', $group) : null;

            if ($vo instanceof Form\Composite) {
                $node = [
                    'nodeName' => 'div',
                    'child' => $vo->getRender(),
                    'sort' => $sort,
                ];
                if ($group) {
                    $node['vIf'] = $group;
                }

                return array_merge($node, $vo->getLayoutAttr());
            }

            $helpNode = [];
            $prompt = $vo->getPrompt();
            $help = $vo->getHelp();
            if ($prompt) {
                $helpNode = [
                    'nodeName' => 'a-tooltip',
                    'class' => 'ml-3',
                    'position' => 'top',
                    'content' => $vo->getPrompt(),
                    'child' => [
                        'nodeName' => 'span',
                        'child' => [
                            'nodeName' => 'icon-question-circle'
                        ]
                    ],
                ];
            }
            if ($help) {
                $helpNode = [
                    'nodeName' => 'div',
                    'class' => 'text-gray-300 pt-2 pb-2 ml-3',
                    'child' => $help
                ];
            }

            $helpLine = $vo->getHelpLine();
            $must = $vo->getMust();

            $item = [
                'nodeName' => 'a-form-item',
                'label' => $vo->getName(),
                'field' => $vo->getField(),
                'vIf' => $group,
                'sort' => $sort,
                'child' => [
                    $vo->getRender(),
                    $helpLine ? [
                        'vSlot:help' => '',
                        'nodeName' => 'div',
                        'child' => $helpLine
                    ] : [],
                    $helpNode ? [
                        'nodeName' => 'div',
                        'class' => 'ml-2',
                        'child' => $helpNode
                    ] : []
                ]
            ];

            if ($must) {
                $item['rules'] = [
                    [
                        'required' => true,
                        'message' => 'Please fill in' . $vo->getName()
                    ]
                ];
            }
            return $item;
        })->filter()->sortBy('sort')->values()->toArray();
    }

    /**
     * @return mixed|void|null
     * @throws ErrorException
     */
    public function setInfo()
    {
        if ($this->info) {
            return $this->info;
        }
        if ($this->keys) {
            $model = $this->model();
            foreach ($this->keys as $key => $value) {
                $model->where($key, $value);
            }
            $info = $model->eloquent()->first();
            if (empty($info)) {
                app_error('Content does not exist');
            }
        } else {
            $info = [];
        }
        $this->info = $info;
    }

    /**
     * Submission type
     * @param string $name
     * @return $this
     */
    public function method(string $name = 'post'): self
    {
        $this->method = $name;
        return $this;
    }

    /**
     * Specify template variables
     * @param string $name
     * @param null $value
     * @return $this
     */
    public function assign(string $name, $value = null): self
    {
        $this->assign[$name] = $value;
        return $this;
    }

    /**
     * Whether to pop up the window
     * @param bool $status
     * @return $this
     */
    public function dialog(bool $status): self
    {
        $this->dialog = $status;
        $this->vertical = true;
        return $this;
    }

    /**
     * Vertical form
     * @param bool $status
     * @return $this
     */
    public function vertical(bool $status): self
    {
        $this->vertical = $status;
        return $this;
    }

    /**
     * Get popup status
     * @return bool
     */
    public function getDialog(): bool
    {
        return $this->dialog;
    }

    /**
     * save link
     * @param $uri
     * @return $this
     */
    public function action($uri): self
    {
        $this->action = $uri;
        return $this;
    }

    /**
     * Bottom component
     * @param array|null $bottom
     * @return $this
     */
    public function bottom(?array $bottom): self
    {
        $this->bottom = $bottom;
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
     * render the form
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function render()
    {
        return app_success('ok', $this->renderArray());
    }

    /**
     * render form (array)
     * @return array
     */
    public function renderArray()
    {
        $node = $this->renderNode();
        return $node->render();
    }

    /**
     * only render the form
     * @return array
     */
    public function renderFormCore()
    {
        $node = $this->renderNode();
        return $node->renderFormCore();
    }

    /**
     * render component
     * @return Node
     */
    public function renderNode()
    {
        // submit address
        if ($this->action) {
            $action = $this->action;
        } else {
            $params = request()->all();
            $routeParams = request()->route()->parameters();
            $params = array_merge($params, $routeParams);
            if ($this->modelElo) {
                $key = $this->modelElo->getKeyName();
                $id = $this->info->$key;
                $params['id'] = $id;
            }else {
                foreach ($this->keys as $name => $value) {
                    $params[$name] = $value;
                }
            }
            $action = route(\Str::beforeLast(request()->route()->getName(), '.') . '.' . 'save', $params);
        }

        $node = new Node($action, $this->method, $this->title);
        $node->dialog($this->dialog);
        $node->debug($this->debug);
        $node->vertical($this->vertical);
        $node->back($this->back);

        // form element
        $node->element($this->renderForm());

        $node->bottom($this->bottom);
        $node->statics($this->statics);

        // form data
        $node->data($this->renderData($this->info));

        // sidebar element
        foreach ($this->sideNode as $vo) {
            $node->side($vo['callback'], $vo['direction']);
        }

        // handle additional js
        foreach ($this->script as $key => $value) {
            $node->script($value, $this->scriptReturn[$key]);
        }

        return $node;
    }

    /**
     * Get submission data
     * @param $time
     * @param array|null $data
     * @return Collection
     */
    public function getInput($time,?array $data = null): Collection
    {
        // get submitted data
        if(is_null($data)){
            $data = request()->input();
        }

        // submit data processing
        if ($this->flow['submit']) {
            foreach ($this->flow['submit'] as $item) {
                $data = $item($data, $time);
            }
        }
        // filter data
        $collection = Collection::make();
        $this->element->map(function ($item) use ($collection, $time) {
            $inputs = $item->getInput($time);

            foreach ($inputs as $key => $vo) {
                $collection->put($key, $vo);
            }
        });

        //verify the data
        $rules = [];
        $msgs = [];
        $collection->map(function ($item) use (&$rules, &$msgs) {
            if ($item['verify']['rule']) {
                $rules = $rules + $item['verify']['rule'];
            }
            if ($item['verify']['msg']) {
                $msgs = $msgs + $item['verify']['msg'];
            }
        });
        $validator = \Validator::make($data, $rules, $msgs);

        if ($this->flow['validator']) {
            foreach ($this->flow['validator'] as $vo) {
                $vo($validator);
            }
        }
        $validator->validate();

        // format data
        return $collection->map(function ($item) {
            $value = $item['value'];
            if ($item['format']) {
                foreach ($item['format'] as $vo) {
                    $value = call_user_func($vo, $item['value']);
                }
            }
            return ['value' => $value, 'has' => $item['has'], 'pivot' => $item['pivot']];
        });
    }

    /**
     * Process time
     * @var array
     */
    protected array $prepared = [];

    /**
     * primary key value
     * @var null
     */
    public $modelId = null;

    /**
     * save data
     * @return null $modelId
     */
    public function save()
    {
        // Get primary key data
        $id = 0;
        if ($this->modelElo) {
            $id = $this->keys[$this->modelElo->getKeyName()];
        }

        // save type
        $type = $id ? 'edit' : 'add';

        // get submitted data
        $data = $this->getInput($type);

        // extract submission data
        $formatData = [];
        foreach ($data as $key => $vo) {
            $formatData[$key] = $vo['value'];
        }
        $formatData = collect($formatData);

        // non-model return collection
        if (!$this->modelElo) {
            return $formatData;
        }

        // get the model object
        if ($type === 'add') {
            $model = $this->modelElo;
        } else {
            $model = $this->modelElo->find($id);
        }

        // save the database
        DB::transaction(function () use ($model, $data, $type, $formatData) {

            // save the previous callback
            if ($this->flow['front']) {
                foreach ($this->flow['front'] as $item) {
                    $ret = $item($formatData, $type, $model);
                    if ($ret instanceof Eloquent) {
                        $model = $ret;
                    }
                }
            }

            // Tree processing has been abandoned first set scoped data and then set parent data
            /*if ($model->parent_id) {
                if (method_exists($model, 'appendToNode')) {
                    $model = $model->appendToNode($this->modelElo->find($formatData['parent_id']));
                }
            }*/

            $data->map(function ($item, $key) use ($model) {
                $has = $item['has'];
                // Query the associated object
                if (method_exists($model, $has) && !is_null($item['value'])) {
                    $relation = $model->$has();
                    // many to many
                    if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                        $this->prepared[] = static function ($model) use ($item) {
                            $sync = is_array($item['value']) ? $item['value'] : [$item['value']];
                            $syncFormat = [];
                            if ($item['pivot']) {
                                foreach ($sync as $vo) {
                                    $syncFormat[$vo] = $item['pivot'];
                                }
                                $sync = $syncFormat;
                            }
                            $model->{$item['has']}()->sync($sync);
                        };
                    }
                } else if (\Schema::hasColumn($model->getTable(), $key)) {
                    // filter useless fields
                    $model->$key = $item['value'];
                }
            });

            // save the previous callback
            if ($this->flow['before']) {
                foreach ($this->flow['before'] as $item) {
                    $ret = $item($formatData, $type, $model);
                    if ($ret instanceof Eloquent) {
                        $model = $ret;
                    }
                }
            }

            $model->save();

            // synchronize associated data
            foreach ($this->prepared as $callback) {
                $callback($model);
            }
            // save callback
            if ($this->flow['after']) {
                foreach ($this->flow['after'] as $item) {
                    $item($formatData, $type, $model);
                }
            }
        });
        $this->modelId = $model->getKey();
        return $this->modelId;
    }

    /**
     * Submit before processing data
     * @param $callback
     * @return $this
     */
    public function front($callback): Form
    {
        $this->flow['front'][] = $callback;
        return $this;
    }

    /**
     * Validation form extension
     * @param $callback
     * @return $this
     */
    public function validator($callback): Form
    {
        $this->flow['validator'][] = $callback;
        return $this;
    }

    /**
     * Callback before submitting
     * @param $callback
     * @return $this
     */
    public function submit($callback): Form
    {
        $this->flow['submit'][] = $callback;
        return $this;
    }

    /**
     * Callback before saving
     * @param $callback
     * @return $this
     */
    public function before($callback): Form
    {
        $this->flow['before'][] = $callback;
        return $this;
    }

    /**
     * Callback after saving
     * @param $callback
     * @return $this
     */
    public function after($callback): Form
    {
        $this->flow['after'][] = $callback;
        return $this;
    }

    /**
     * Extended element
     * @param $method
     * @param $className
     */
    public function extend($method, $className): void
    {
        $this->extend[$method] = $className;
    }


    /**
     * Front-end events
     * @param $table
     * @param $name
     * @param $type
     * @return array|false
     */
    public function callbackEvent($table, $name, $type, $data = null)
    {
        if (!$this->modelId) {
            return false;
        }
        $rowsData = $data ?: $this->modelElo->where($this->modelElo->getKeyName(), $this->modelId)->get();
        $list = $table->renderRowData($rowsData, false);

        $parentKey = null;
        if ($table->getTree()) {
            $parentKey = $this->modelElo->find($this->modelId)->parent_id;
        }

        $event = new Event($name);
        foreach ($list as $item) {
            $event->add($type, $this->modelId, $item, $parentKey !== false ? ['parentKey' => $parentKey] : []);
        }
        return $event->render();

    }

    /**
     * Callback class library
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        $class = '\\Hairavel\\Core\\UI\\Form\\' .ucfirst($method);
        if (!class_exists($class)) {
            if (!$this->extend[$method]) {
                throw new \Exception('There is no form method "' . $method . '"');
            }
            $class = $this->extend[$method];
        }
        $object = new $class(...$arguments);
        $object->dialog($this->dialog);
        $this->element->push($object);
        return $object;
    }

}
