<?php

abstract class dmModelTreeView extends dmConfigurable
{
  protected
  $model,
  $fields,
  $helper,
  $culture,
  $tree,
  $html,
  $level,
  $lastLevel;

  public function __construct(dmHelper $helper, $culture, array $options)
  {
    $this->helper   = $helper;
    $this->culture  = $culture;
    $this->tree     = $this->getRecordTree();
    $this->fields   = array('model' => array('id', 'name'));
    $this->model    = 'TeamCategory';
    //die($this->model);
    
    $this->initialize($options);
  }

  public function setModel($model) {
    if (dmDb::table($model) instanceof dmDoctrineTable && dmDb::table($model)->isNestedSet()) {
      $this->model = $model;
    }
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  abstract protected function renderModelLink(array $page);

  protected function getRecordTree()
  {
    $this->model = 'TeamCategory';
    $this->fields   = array('model' => array('id', 'name'));
    $modelTableTree = dmDb::table($this->model)->getTree();

    $modelTableTree->setBaseQuery($this->getRecordTreeQuery());

    $tree = $modelTableTree->fetchTree(array(), Doctrine_Core::HYDRATE_NONE);
    
    $modelTableTree->resetBaseQuery();

    return $tree;
  }

  protected function getRecordTreeQuery()
  {
    $select = 'model.' . implode(', model.', $this->fields['model']);
    if (isset($this->fields['i18n']) && is_array($this->fields['i18n'])) {
      $select .= 'modelTranslation.' . implode(', modelTranslation.', $this->fields['i18n']);
    }
    //die(var_dump($select));

    $query = dmDb::table($this->model)->createQuery('model');
    if (dmDb::table($this->model)->hasI18n()) {
      $query->withI18n($this->culture, null, 'model');
    }
    return $query->select($select);
  }

  public function render($options = array())
  {
    $this->options = array_merge(dmString::toArray($options, true), $this->options);

    $this->html = $this->helper->open('ul', $this->options);

    $this->lastLevel = false;
    foreach($this->tree as $node)
    {
      $this->level = $node[4];
      $this->html .= $this->renderNode($node);
      $this->lastLevel = $this->level;
    }

    $this->html .= str_repeat('</li></ul>', $this->lastLevel+1);

    return $this->html;
  }

  protected function renderNode(array $model)
  {
    /*
     * First time, don't insert nothing
     */
    if ($this->lastLevel === false)
    {
      $html = '';
    }
    elseif ($this->level === $this->lastLevel)
    {
      $html = '</li>';
    }
    elseif ($this->level > $this->lastLevel)
    {
      $html = '<ul>';
    }
    else // $this->level < $this->lastLevel
    {
      $html = str_repeat('</li></ul>', $this->lastLevel - $this->level).'</li>';
    }

    $html .= $this->renderOpenLi($model);

    $html .= $this->renderModelLink($model);

    return $html;
  }

  protected function renderOpenLi(array $model)
  {
    return '<li id="dmm'.$model[0].'" rel="manual">';
  }

}