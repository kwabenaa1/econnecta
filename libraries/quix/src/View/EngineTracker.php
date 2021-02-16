<?php

namespace ThemeXpert\View;

class EngineTracker
{
  public $id;
  public $type;
  public $builder;

  public function set($id, $type, $builder) 
  {
    $this->id = $id;
    $this->type = $type;
    $this->builder = $builder;
  }
}