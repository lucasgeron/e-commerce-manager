<?php
require 'application.php';
$router = new Router($_SERVER['REQUEST_URI']);

$router->get('/', array('controller' => 'HomeController', 'action' => 'index'));

/* Rotas para as clientes
--------------------------------- */
$router->get('/clientes/novo', array('controller' => 'ClientsController', 'action' => '_new'));
$router->get('/clientes/pesquisa', array('controller' => 'ClientsController', 'action' => 'search'));
$router->post('/clientes', array('controller' => 'ClientsController', 'action' => 'create'));
$router->get('/clientes', array('controller' => 'ClientsController','action' => 'index'));
$router->get('/clientes/:id/editar', array('controller' => 'ClientsController','action' => 'edit'));
$router->post('/clientes/:id', array('controller' => 'ClientsController','action' => 'update'));
$router->get('/clientes/:id', array('controller' => 'ClientsController','action' => 'show'));
$router->get('/clientes/:id/deletar', array('controller' => 'ClientsController','action' => 'delete'));
/* Fim das rotas para as clientes
--------------------------------- */

/* Rotas para as produtos
--------------------------------- */
$router->get('/produtos/auto_complete_search', array('controller' => 'ProductsController','action' => 'autoCompleteSearch'));
$router->get('/produtos/novo', array('controller' => 'ProductsController', 'action' => '_new'));
$router->get('/produtos/pesquisa', array('controller' => 'ProductsController', 'action' => 'search'));
$router->post('/produtos', array('controller' => 'ProductsController', 'action' => 'create'));
$router->get('/produtos', array('controller' => 'ProductsController','action' => 'index'));
$router->get('/produtos/:id/editar', array('controller' => 'ProductsController','action' => 'edit'));
$router->post('/produtos/:id', array('controller' => 'ProductsController','action' => 'update'));
$router->get('/produtos/:id', array('controller' => 'ProductsController','action' => 'show'));
$router->get('/produtos/:id/deletar', array('controller' => 'ProductsController','action' => 'delete'));
/* Fim das rotas para as produtos
--------------------------------- */

/* Rotas para as categorias
--------------------------------- */
$router->get('/categorias/novo', array('controller' => 'CategoriesController', 'action' => '_new'));
$router->get('/categorias/pesquisa', array('controller' => 'CategoriesController', 'action' => 'search'));
$router->post('/categorias', array('controller' => 'CategoriesController', 'action' => 'create'));
$router->get('/categorias', array('controller' => 'CategoriesController','action' => 'index'));
$router->get('/categorias/:id/editar', array('controller' => 'CategoriesController','action' => 'edit'));
$router->post('/categorias/:id', array('controller' => 'CategoriesController','action' => 'update'));
$router->get('/categorias/:id', array('controller' => 'CategoriesController','action' => 'show'));
$router->get('/categorias/:id/deletar', array('controller' => 'CategoriesController','action' => 'delete'));
/* Fim das rotas para as categorias
--------------------------------- */

/* Rotas para as pedidos
--------------------------------- */
$router->get('/clientes/:client_id/pedidos/novo', array('controller' => 'OrdersController', 'action' => 'create'));
$router->post('/pedidos/adiciona-produto', array('controller' => 'OrdersController','action' => 'addProduct'));
$router->get('/pedidos/:id/:item/remove-produto', array('controller' => 'OrdersController','action' => 'rmvProduct'));
$router->get('/pedidos/:id/:item/adiciona-unidade', array('controller' => 'OrdersController','action' => 'addAmount'));
$router->get('/pedidos/:id/:item/remove-unidade', array('controller' => 'OrdersController','action' => 'rmvAmount'));
$router->get('/pedidos/pesquisa', array('controller' => 'OrdersController', 'action' => 'search'));
$router->get('/pedidos', array('controller' => 'OrdersController','action' => 'index'));
$router->get('/pedidos/:id/fechar-pedido', array('controller' => 'OrdersController','action' => 'closeOrder'));
$router->get('/pedidos/:id/detalhes-do-pedido', array('controller' => 'OrdersController','action' => 'showClosedOrder'));
$router->get('/pedidos/:id', array('controller' => 'OrdersController','action' => 'show'));
$router->get('/pedidos/:id/deletar', array('controller' => 'OrdersController','action' => 'delete'));
/* Fim das rotas para as pedidos
--------------------------------- */

/* Rotas para as Relatórios
--------------------------------- */
$router->get('/relatorios/ranking-de-vendas', array('controller' => 'ReportsController','action' => '_new'));
$router->get('/relatorios/ranking-de-vendas/:por-data', array('controller' => 'ReportsController','action' => 'SellingRanking'));
$router->get('/relatorios/balanco-de-vendas', array('controller' => 'ReportsController','action' => '_new'));
$router->get('/relatorios/balanco-de-vendas/:por-data', array('controller' => 'ReportsController','action' => 'SellingBalance'));

/* Fim das rotas para os Relatórios
--------------------------------- */

/* Rotas para os usuários
------------------------- */
$router->get('/usuarios', array('controller' => 'UsersController', 'action' => 'index'));
$router->get('/criar-conta', array('controller' => 'UsersController', 'action' => '_new'));
$router->post('/criar-conta', array('controller' => 'UsersController', 'action' => 'create'));
$router->get('/perfil', array('controller' => 'UsersController', 'action' => 'edit'));
$router->post('/perfil', array('controller' => 'UsersController', 'action' => 'update'));
/* Fim das rotas para os usuários
--------------------------------- */

/* Rotas para os sessões
------------------------- */
$router->get('/login', array('controller' => 'SessionsController', 'action' => '_new'));
$router->post('/login', array('controller' => 'SessionsController', 'action' => 'create'));
$router->get('/logout', array('controller' => 'SessionsController', 'action' => 'destroy'));
/* Fim das rotas para os sessões
--------------------------------- */



$router->load();
?>
