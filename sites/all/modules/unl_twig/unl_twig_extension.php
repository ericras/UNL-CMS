<?php

class Unl_Twig_Extension extends Twig_Extension
{
  public function getName() {
    return 'unl_twig';
  }

  public function getFunctions() {
    return array(
      new Twig_SimpleFunction('render', function($a) {return drupal_render($a);}),
    );
  }
}
