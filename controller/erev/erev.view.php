<?php

/*
 * Klasa: erev_view
 *
 * Email: ergomicomseosem@gmail.com
 */

  class erev_view extends EREV{

    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    /**
    * Funkcja
    */
    public function GET_view($erid){
      if($this->single($erid)){
        $template = $this->view_layout();
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }else{
        throw new Exception("EREV record cannot be loaded.");
      }
    }

    private function view_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      $layout->ercr = $this->ercr;
      $layout->eid_rel = $this->eid_rel;

      $layout->etype = $this->etype;

      $layout->erv = $this->erv ? 1:0;
      $layout->erc = $this->erc ? 1:0;
      $layout->eru = $this->eru ? 1:0;

      $layout->cp = $this->count_parents;
      $layout->cc = $this->count_childs;

      $layout->map = $this->map;
      $layout->mac = $this->mac;

      //$layout->AddSugestion('erid_'.$this->erid);
      if($this->erv){
        if($this->erc){
          if($this->eru){
            $suggestion_v = 'used';
            $layout->AddSugestion($suggestion_v);
          }else{
            $suggestion_v = 'confirm';
            $layout->AddSugestion($suggestion_v);
          }
        }else{
          $suggestion_v = 'valid';
          $layout->AddSugestion($suggestion_v);
        }
      }else{
        $suggestion_v = 'invalid';
        $sugestion_t = '';
        switch($this->etype){
            case 1: $sugestion_t='/container';break;
            case 2: $sugestion_t='/fieldgroup';break;
            case 3: $sugestion_t='/field';break;
            case 4: $sugestion_t='/datatype';break;
            default: $sugestion_t='/error';break;
        }

          if($this->map){ $layout->AddSugestion($suggestion_v.$sugestion_t.'/map'); }
          if($this->mac){ $layout->AddSugestion($suggestion_v.$sugestion_t.'/mac'); }

          $layout->AddSugestion($suggestion_v.$sugestion_t);
          $layout->AddSugestion($suggestion_v);
      }

      $layout->SetStorageFileName('erev_'.$this->erid);
      $layout->SetStoraged(true);
      return $layout->Render();
    }

  }
