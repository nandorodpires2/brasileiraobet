<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require APPLICATION_PATH . '/../library/vendor/autoload.php';

use Softr\Asaas\Adapter\BuzzAdapter;
//use Softr\Asaas\Adapter\GuzzleAdapter;
//use Softr\Asaas\Adapter\GuzzleHttpAdapter;
use Softr\Asaas\Asaas;

/**
 * Description of PagamentoController
 *
 * @author Fernando
 */
class Site_PagamentoController extends Zend_Controller_Action {
    
    protected $adpter;
    protected $asaas;
    protected $api_key;
    
    public function init() {
        if (!Plugin_Auth::check()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
        
        // api key
        $this->api_key = Zend_Registry::get('config')->boleto->api->key;
        
        // Instancie o adapter usando o token de acesso
        $this->adapter = new BuzzAdapter($this->api_key);
        //$adapter = new GuzzleAdapter($this->api_key);
        //$adapter = new GuzzleHttpAdapter($this->api_key);
                
        $this->asaas = new Asaas($this->adapter);
        
    }
    
    public function indexAction() {
     
        $deposito_id = Zend_Registry::get("session")->deposito_id;
        
        /**
         * Dados do deposito
         */
        $modelDeposito = new Model_DbTable_Deposito();
        $deposito = $modelDeposito->getById($deposito_id);
        
        if (!$deposito) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Falha na requisição!'
            ));
            $this->_redirect("/");
        }
        
        $this->view->deposito = $deposito;        
        
    }
    
    public function boletoAction() {
        
        if (APPLICATION_PATH == 'development') {
            $this->_redirect("pagamento/conclusao");            
        }
        
        $this->_helper->viewRenderer->setNoRender();
        
        $deposito_id = null;
        if (Zend_Registry::get("session")->deposito_id) {
            $deposito_id = Zend_Registry::get("session")->deposito_id;
        } elseif ($this->getRequest()->getParam("id")){
            $deposito_id = $this->getRequest()->getParam("id");
        }
        
        if (!$deposito_id){
            
        }
        
        
        /**
         * Dados do deposito
         */
        $modelDeposito = new Model_DbTable_Deposito();
        $deposito = $modelDeposito->getById($deposito_id);
        
        /**
         * Dados do usuario
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelUsuario = new Model_DbTable_Usuario();
        $usuario = $modelUsuario->getById($usuario_id);
        
        // verifica se ja existe cliente
        // caso nao exista cadastra o cliente
        $customer = $this->asaas->customer()->getByEmail($usuario->usuario_email);
        
        if (!$customer) {
            // cadastra o cliente
            $dadosCliente = array(
                'name' => $usuario->usuario_nome,
                'email' => $usuario->usuario_email,
                'company' => null,
                'phone' => null,
                'mobilePhone' => null,
                'address' => null,
                'adressNumber' => null,
                'complement' => null,
                'province' => null,
                'city' => null,
                'state' => null,
                'postalCode' => null,
                'cpfCnpj' => null,
            );
            
            try {
                $customer = $this->asaas->customer()->create($dadosCliente);
            } catch (Exception $ex) {
                $this->_helper->flashMessenger->addMessage(array(
                    "danger" => "Falha ao cadastrar o cliente - " . $ex->getCode()
                ));
                $this->_redirect("pagamento/");
            }            
        }    
        
        $nosso_numero = str_pad($deposito_id, 8, "0", STR_PAD_LEFT);
        
        // gera o boleto
        try {
            
            $zendDate = new Zend_Date($deposito->deposito_vencimento);            
                    
            // envia o link de 2ª via para o cliente
            $dataPayment = array(
                'customer' => $customer->id,
                'subscription' => null,
                'description' => 'AQUISIÇÃO DE CRÉDITO',
                'billingType' => 'BOLETO',
                'value' => (double)$deposito->deposito_valor,
                'dueDate' => $zendDate->get(Zend_Date::DATE_MEDIUM),                
                'nossoNumero' => $nosso_numero,
            );
            
            //Zend_Debug::dump($dadosCobranca); die();
            
            $payment = $this->asaas->payment()->create($dataPayment);
            
            if ($payment) {
                
                // atualiza a tabela
                $update = array(
                    'deposito_controle' => $payment->id,
                    'deposito_url' => $payment->boletoUrl
                );
                $modelDeposito->updateById($update, $deposito_id);
                
                /**
                 * Gera uma notificacao
                 */
                $zendCurrency = new Zend_Currency();
                $valor_deposito = $zendCurrency->toCurrency($deposito->deposito_valor);
                $modelNotificacao = new Model_DbTable_Notificacao();
                $conteudo = " 
                    Recebemos sua solicitação de deposito no valor de 
                    {$valor_deposito}. Assim que confirmarmos o pagamento do 
                    boleto seus créditos serão lançados na sua conta. 
                ";
                $notificacao = array(
                    'usuario_id' => $deposito->usuario_id,
                    'notificacao_conteudo' => $conteudo
                );
                $modelNotificacao->insert($notificacao);
                
                $this->_redirect("pagamento/conclusao");
                
            }            
            
        } catch (Exception $ex) {                                                                                                                                           
            $this->_helper->flashMessenger->addMessage(array(
                "danger" => $ex->getCode() . ' - ' . $ex->getMessage()
            ));
            $this->_redirect("pagamento/");            
        }
        
    }

    public function conclusaoAction() {
        
        $deposito_id = Zend_Registry::get("session")->deposito_id;
        
        /**
         * Dados do deposito
         */
        $modelDeposito = new Model_DbTable_Deposito();
        $deposito = $modelDeposito->getById($deposito_id);
        
        if (!$deposito) {
            
        }
        
        $this->view->deposito = $deposito;
        
    }

    private function checkPromocao($cupom) {
        
        if (!$cupom) {
            return;
        }
        
        if ($cupom === 'PROMO10') {
            $modelLancamento = new Model_DbTable_Lancamento();
            $modelLancamento->insert(array(
                'lancamento_valor' => 10,
                'lancamento_descricao' => "CRÉDITO CUPOM PROMOCIONAL (PROMO10)",
                'usuario_id' => Zend_Auth::getInstance()->getIdentity()->usuario_id
            ));     
        }
        
    }
    
}
