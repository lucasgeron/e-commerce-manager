<?php
class ReportsController extends ApplicationController {
  protected $beforeAction = array('authenticated' => 'all');

  public function _new() {
    $this->report = new Report();
    $this->report->setFirstDate($this->params['first_date']);
    $this->report->setLastDate($this->params['last_date']);

    $url = $_SERVER['REDIRECT_URL'];
    $url = str_replace("/PI/relatorios/","", "$url");
    if($url== "ranking-de-vendas") {
      $this->title = "<i class='fa fa-trophy fa-2x'> </i> Ranking de Vendas <small> Pedidos Fechados </small>";
      $this->action = '/relatorios/ranking-de-vendas/:por-data';
    }else {
      $this->title = "<i class='fa fa-area-chart fa-2x'> </i> Balanço de Vendas <small> Pedidos Fechados </small>";
      $this->action = '/relatorios/balanco-de-vendas/:por-data';
    }
  }
  public function SellingRanking(){
    $this->report = new Report();
    $this->report->setFirstDate($this->params['report']['first_date']);
    $this->report->setLastDate($this->params['report']['last_date']);

    if($this->params['report']['first_date'] >= $this->params['report']['last_date']){
      Flash::message('danger', "Atenção, as datas não são validas! Informe os campos corretamente.");
      $this->redirectTo('/relatorios/ranking-de-vendas');
    }
    $this->SellingRanking = Report::SellingRanking($this->params['report']['first_date'], $this->params['report']['last_date']);
    $this->title = "<i class='fa fa-trophy fa-2x'> </i> Ranking de Vendas <small> Pedidos Fechados </small>";
    $this->action = '/relatorios/ranking-de-vendas/:por-data';
    $this->render('new');
  }

  public function SellingBalance(){
    $this->report = new Report();
    $this->report->setFirstDate($this->params['report']['first_date']);
    $this->report->setLastDate($this->params['report']['last_date']);

    if($this->params['report']['first_date'] >= $this->params['report']['last_date']){
      Flash::message('danger', "Atenção, as datas não são validas! Informe os campos corretamente.");
      $this->redirectTo('/relatorios/ranking-de-vendas');
    }
    $this->SellingBalance= Report::SellingBalance($this->params['report']['first_date'], $this->params['report']['last_date']);
    $this->title = "<i class='fa fa-area-chart fa-2x'> </i> Balanço de Vendas <small> Pedidos Fechados </small>";
    $this->action = '/relatorios/balanco-de-vendas/:por-data';
    $this->render('new');
  }
} ?>
