<?php
class ClientsController extends ApplicationController {
  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
    $this->clients = Client::all();
    $this->action = '/clientes/pesquisa';
    $this->subtitle = 'Todos';
  }

  public function show() {
    $this->client = Client::findById($this->params[':id']);
    $this->cities = City::all();

    if( !is_object($this->client)){
      Flash::message('danger', 'Este registro não existe.');
      $this->redirectTo('/clientes');
    }
  }

  public function search() {
    $this->action = '/clientes/pesquisa';
    $this->value = $this->params['search'];
    $this->subtitle = 'Pesquisa';
    $this->clients = Client::findByName($this->params['search']);
    $this->render ('index');
  }

  public function _new() {
    $this->client = new Client();
    $this->action = '/clientes';
    $this->submit = 'Salvar';
    $this->cities = City::all();
  }

  public function create(){
    $this->client = new Client($this->params['client']);

    if ($this->client->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->cities = City::all();
      $this->action = '/clientes';
      $this->submit = 'Salvar';
      $this->render('new');
    }
  }

  public function edit() {
    $this->client = Client::findById($this->params[':id']);
    $this->cities = City::all();
    $this->action = '/clientes/' . $this->client->getId();
    $this->submit = 'Atualizar';
  }

  public function update() {
    $this->client = Client::findById($this->params[':id']);
    $this->cities = City::all();

    if ($this->client->update($this->params['client'])) {
      Flash::message('success', "Registro ID: {$this->client->getId()} atualizado com sucesso!");
      $this->render('show');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/clientes/' . $this->client->getId();
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function delete() {
    $this->client = Client::findById($this->params[':id']);
    $this->client->delete();
    Flash::message('success', "Registro removido com sucesso!");
    $this->redirectTo('/clientes');
  }
} ?>
