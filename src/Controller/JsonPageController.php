<?php

namespace Drupal\site_info\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Controller routines for site_info routes.
 */
class JsonPageController extends ControllerBase {
  /**
   * Returns a Json of page data.
   */
  public function JsonPage() {
    $node_data = \Drupal::service('current_route_match');
    $nid = $node_data->getParameter('node');
    $siteapikey_url_param = $node_data->getParameter('siteapikey');
    $siteapikey = \Drupal::config('site_info.settings')->get('siteapikey');

    $node_details = Node::load($nid);
    if(($siteapikey_url_param != $siteapikey) || (empty($node_details))||($node_details->bundle() != 'page')){
      //Thorw excemption for content type other than page, node id doesnot exist and siteapikey is incorrect.
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    } else {
      $bundle = $node_details->bundle();
      $id = $node_details->id();
      $title = $node_details->title->value;
      $full_body = $node_details->body->view('full');
      $element = array(
        '#siteapikey' => $siteapikey,
        '#id' => $id,
        '#title' => $title,
        '#bundle' => $bundle,
        '#bodycontent' => $full_body,
      );
      return new JsonResponse($element);      
    }
  }

}
