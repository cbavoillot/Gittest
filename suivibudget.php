<?php

if (!defined('_PS_VERSION_')) {
   exit;
}

class Suivibudget extends Module
{		

	//ICI def du module dans la liste des modules 
   public function __construct()
   {
       $this->name = 'suivibudget';
       $this->tab = 'front_office_features';
       $this->version = '2.0.0';
       $this->author = 'Bavoillot';
       $this->need_instance = 0;
       $this->context = Context::getContext();
       $this->bootstrap = true;
       parent::__construct();
       $this->displayName = $this->l('SuiviBudget');
       $this->description = $this->l('Module de suivi budgetaire');
       $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
   }
   
   
   
   //ICI installation du module def des variable configuration et association des hook 
 
public function install()
{
   
   return parent::install() &&
	   $this->registerHook('SuiviBudgetHook')&&
       $this->registerHook('displayMyAccountBlock')&&
	   $this->registerHook('displayCustomerAccount')&&
	   $this->registerHook('actionCartListOverride')&&
	   $this->registerHook('displayShoppingCart')&&
	   $this->registerHook('header') &&
       Configuration::updateValue('Budget1', '');
	   
	   
		
		if (! $this->installDb())
			return false;
		return true;
		
	   
	   	 

	   
}
	
	
	public  function installDb()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->name.'` (
		`id` 				INT(11) unsigned NOT NULL AUTO_INCREMENT,
		`id_shop` 			INT(11) unsigned NOT NULL DEFAULT 1,
		`id_customer`		INT(11) unsigned NOT NULL,
		`budget` 				VARCHAR(40),
		`depenses` 				VARCHAR(40),
		`balance` 				VARCHAR(40),
		`start`				DATETIME NOT NULL,
		`end`				DATETIME NOT NULL,
		PRIMARY KEY(`id`)) ENGINE = MyISAM default CHARSET = utf8';
		return Db::getInstance()->execute($sql);
	}
	
	
	//Ici Desinstallation 
	public function uninstall()
		{
			return parent::uninstall() &&
			Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.$this->name.'`')&&
			Configuration::deleteByName('Budget1');
		}
		
		
		
	//page de configuration du module 
	public function getContent()
	{
	$output = null;
	if (Tools::isSubmit('submit' . $this->name)) {
       $budget1 = Tools::getValue('Budget1');
       
       if (!$budget1 || empty($budget1) || !Validate::isGenericName($budget1)) {
           $output .= $this->displayError($this->l('Configuration failed'));
       } else {
           Configuration::updateValue('Budget1', $budget1);
           
           $output .= $this->displayConfirmation($this->l('Update successful'));
       }
													}
	return $output . $this->displayForm();
	}	
	


	//Formulaire du configurateur	
	public function displayForm()
	{
	$fields_form = array();
	$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	$fields_form[0]['form'] = array(
       'legend' => array(
           'title' => $this->l('Budget settings'),
       ),
       'input' => array(
           array(
               'type' => 'text',
               'label' => $this->l('Budget 1'),
               'name' => 'Budget1',
               'size' => 20,
               'required' => true
           )
       
          
       ),
       'submit' => array(
           'title' => $this->l('Save'),
           'class' => 'btn btn-default'
       )
		);
		
		
		
	$helper = new HelperForm();
	$helper->module = $this;
	$helper->name_controller = $this->name;
	$helper->token = Tools::getAdminTokenLite('AdminModules');
	$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
	$helper->default_form_language = $default_lang;
	$helper->allow_employee_form_lang = $default_lang;
	$helper->title = $this->displayName;
	$helper->show_toolbar = true;
	$helper->toolbar_scroll = true;
	$helper->submit_action = 'submit' . $this->name;
	$helper->toolbar_btn = array(
		'save' =>
       array(
           'desc' => $this->l('Save'),
           'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
               '&token=' . Tools::getAdminTokenLite('AdminModules'),
			),
				'back' => array(
       'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
       'desc' => $this->l('Back to list')
								)
								);	
		
		
		
		
			$helper->tpl_vars = array(
				'fields_value' => array(
					'Budget1' => Configuration::get('Budget1')
										),
									);	
									
			return $helper->generateForm($fields_form);						
		
		}	
		
		
		public function hookheader()
		{
			$this->context->controller->addJS($this->_path . '/views/js/suivibudget.js', 'all');
			$this->context->controller->addCSS($this->_path . '/views/css/suivibudget.css', 'all');
		}
		
		
		
		
	public function hookDisplayShoppingCart($params)
	{
		
		return $this->display(__FILE__, 'views/templates/hook/displayCart.tpl');
		
	}
		
		
		
		
	
	public function hookActionCartListOverride($params)
	{
		
		return $this->display(__FILE__, 'views/templates/hook/displayCart.tpl');
		
	}	
		
		
	public function hookSuiviBudgetHook ($params) {
					$id_customer=(int)$this->context->customer->id;
					
					$sql = 'SELECT * FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$this->context->customer->id;
					$infosbudgetactualiser=Db::getInstance()->ExecuteS($sql);
	
					foreach ($infosbudgetactualiser as $budget)
					{
					$budget1=$budget['budget'];
	   
					$balance1=$budget['balance'];
	   
					$depenses1=$budget['depenses'];
					$start1=$budget['start'];
					$dt = DateTime::createFromFormat('Y-m-d H:i:s', $start1);
					$start1=$dt->format('d/m/Y');
	   
	   
					$end1=$budget['end'];
					$dt = DateTime::createFromFormat('Y-m-d H:i:s', $end1);
					$end1=$dt->format('d/m/Y');
	   
	   
					$this->context->smarty->assign(
					array(
					'budget1' => $budget1,
					'depenses1' => $depenses1,
					'balance1' => $balance1,
					'start1'=>$start1,
					'end1'=>$end1,
						)
												);
	   
	   }
	
	               return $this->display(__FILE__, 'views/templates/hook/displayCart.tpl');
													}	
		
		
		public function hookDisplayCustomerAccount($params)
	{
	
	
	
	if($budget = Tools::getValue('budget1') && $start= Tools::getValue('start') && $end= Tools::getValue('end')  )
	{
	
	$datas = array();
	
	 //Configuration::updateValue('Budget1', $budget);
	$id_customer=(int)$this->context->customer->id;
	$budget=Tools::getValue('budget1');
	$start=Tools::getValue('start');
	$end= Tools::getValue('end');
	if (preg_match("/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/",$start)) {
	$dt = DateTime::createFromFormat('d/m/Y', $start);
    $start=$dt->format('Y-m-d H:i:s');
	}else{  }
	
	if (preg_match("/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/",$end)) {
	$dt2 = DateTime::createFromFormat('d/m/Y', $end);
	$end=$dt2->format('Y-m-d H:i:s');
	}else{  }
	
	
	if($datas=$this->depenses($id_customer,$start,$end))
	{
	$depenses=$datas['depenses'];
	$balance= $budget-$depenses;
	}
	
	
	
	$sql = 'SELECT id_customer FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$this->context->customer->id;
    $existe = Db::getInstance()->getValue($sql); 
	if($existe)
	{
	//update
	Db::getInstance()->update($this->name, array('id_shop' =>(int)$this->context->shop->id,'id_customer'=> (int)$this->context->customer->id,'budget' => Tools::getValue('budget1'),'depenses' => $depenses,'balance'=>$balance,'start' => $start,'end' => $end), '`id_customer`='.(int)$this->context->customer->id);
	
	
	}
	else{
	
	//insert
	Db::getInstance()->insert($this->name,
	array('id_shop' =>(int)$this->context->shop->id,
	'id_customer'=> (int)$this->context->customer->id,
	'budget' => Tools::getValue('budget1'),
	'depenses' => $depenses,
	'balance'=> $balance,
	'start' => $start,
	'end' => $end)
	);
	
	
	} 
	
   	
	
	
	}
	
	$sql = 'SELECT * FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$this->context->customer->id;
    $infosbudget=Db::getInstance()->ExecuteS($sql);
    //si resultat faire
	if($infosbudget)
	{
	foreach ($infosbudget as $budget)
	   {
	   //recup des infos
	   $id_customer=$this->context->customer->id;
	   $budget1=$budget['budget'];
	   
	   $balance1=$budget['balance'];
	   
	   $depenses1=$budget['depenses'];
	   $start1=$budget['start'];
	   //$dt = DateTime::createFromFormat('Y-m-d H:i:s', $start1);
	   //$start1=$dt->format('d/m/Y');
	   
	   
	   $end1=$budget['end'];
	   //$dt = DateTime::createFromFormat('Y-m-d H:i:s', $end1);
	   //$end1=$dt->format('d/m/Y');
	   //mise à jour de la balance
	   $this->majbalance($id_customer,$budget1,$start1,$end1);
	
	
	  }
	  
		$sql = 'SELECT * FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$this->context->customer->id;
		$infosbudgetactualiser=Db::getInstance()->ExecuteS($sql);
	
	   foreach ($infosbudgetactualiser as $budget)
	   {
	   $budget1=$budget['budget'];
	   
	   $balance1=$budget['balance'];
	   
	   $depenses1=$budget['depenses'];
	   $start1=$budget['start'];
	   $dt = DateTime::createFromFormat('Y-m-d H:i:s', $start1);
	   $start1=$dt->format('d/m/Y');
	   
	   
	   $end1=$budget['end'];
	   $dt = DateTime::createFromFormat('Y-m-d H:i:s', $end1);
	   $end1=$dt->format('d/m/Y');
	   
	   
	   $this->context->smarty->assign(
		array(
           'budget1' => $budget1,
		   'depenses1' => $depenses1,
		   'balance1' => $balance1,
		   'start1'=>$start1,
		   'end1'=>$end1,
           
			)
			);
	   
	   }
	   
		
			
			
			
			
	}else{
	
	
	
	}

	return $this->display(__FILE__, 'views/templates/hook/displayCustomerAccount.tpl');

	
	}
	
	
	public function depenses($id_customer,$start,$end)
	{
		$sql = 'SELECT SUM(total_paid_tax_excl) as depenses
				FROM `'._DB_PREFIX_.'orders` o
				WHERE o.id_customer='.(int)$id_customer.'
				AND o.current_state IN(14,1,10,2,26,24) 
				AND o.date_add BETWEEN "'.$start.'" and "'.$end.'"';
				
				
		return Db::getInstance()->getRow($sql);
	}
	
	
	
	public function recupbalance($id_customer)
	{
	$sql = 'SELECT * FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$id_customer;
    $infosbudget=Db::getInstance()->ExecuteS($sql);
		   foreach ($infosbudget as $budget)
	   {
	   $budget1=$budget['budget'];
	   
	   $balance1=$budget['balance'];
	   
	   $depenses1=$budget['depenses'];
	   $start1=$budget['start'];
	   $dt = DateTime::createFromFormat('Y-m-d H:i:s', $start1);
	   $start1=$dt->format('d/m/Y');
	   
	   
	   $end1=$budget['end'];
	   $dt = DateTime::createFromFormat('Y-m-d H:i:s', $end1);
	   $end1=$dt->format('d/m/Y');
	   
	   
	   return $this->context->smarty->assign( array('budget1' => $budget1,'depenses1' => $depenses1,'balance1' => $balance1,'start1'=>$start1,'end1'=>$end1,));
	   
	   }
	
	}
	
	
	public function majbalance($id_customer,$budget,$start,$end)
	{
	$sql = 'SELECT id_customer FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$id_customer;
    $existe = Db::getInstance()->getValue($sql); 
	if($existe)
	{
	
	if($datas=$this->depenses($id_customer,$start,$end))
	{
	$depenses=$datas['depenses'];
	$balance= $budget-$depenses;
	
	Db::getInstance()->update($this->name, array('id_shop' =>(int)$this->context->shop->id,'id_customer'=> (int)$this->context->customer->id,'budget' => $budget,'depenses' => $depenses,'balance'=>$balance,'start' => $start,'end' => $end), '`id_customer`='.(int)$this->context->customer->id);
	
	}
	}
		
	
	
	}
	
	
	
   
}