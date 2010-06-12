<?php

class dmAdminModelTreeView extends dmModelTreeView
{

  protected function renderModelLink(array $page)
  {
    return '<a data-page-id="'.$page[0].'"><ins></ins>'.$page[1].'</a>';
  }

}