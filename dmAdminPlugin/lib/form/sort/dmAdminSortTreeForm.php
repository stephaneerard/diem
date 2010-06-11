<?php

class dmAdminSortTreeForm extends dmAdminSortForm
{
  protected
  $records;
  
  public function configure()
  {
    $this->configureRecordFields();
  }
}