<?php

class SuivibudgetDetailbudgetModuleFrontController extends ModuleFrontController
{


    
public $display_column_left = false;
public $display_column_right = false;
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
		
		
		
		//getOrdersIdByDate($date_from, $date_to, $id_customer = null, $type = null)
		//'budget1' => $budget1,'depenses1' => $depenses1,'balance1' => $balance1,'start1'=>$start1,'end1'=>$end1
		$data = $this->recupbalance($this->context->customer->id);
		
		$budget=$data['budget1'];
		$balance=$data['balance1'];
		$depense=$data['depenses1'];
		$start=$data['start1'];
		$end=$data['end1'];
		
		$this->context->smarty->assign($data);
		
		//if ($orders = Order::getOrdersIdByDate($start,$end,$this->context->customer->id,NULL)){
		
		if ($orders = $this->getCustomerOrders($this->context->customer->id,$start,$end)) {
            foreach ($orders as &$order) {
                $myOrder = new Order((int)$order['id_order']);
                if (Validate::isLoadedObject($myOrder)) {
                    $order['virtual'] = $myOrder->isVirtual(false);
                }
            }
        }
        $this->context->smarty->assign(array(
            'orders' => $orders,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'slowValidation' => Tools::isSubmit('slowvalidation')
        ));

        $this->context->link->getModuleLink('suivibudget', 'detailbudget', array(), true);
		
        $this->setTemplate('detailbudget.tpl');
		
    }
	
	
	
	
	
	
	
	//recuperer balance budget
	
	public function recupbalance($id_customer)
	{
	
	$this->name = 'suivibudget';
	
	$sql = 'SELECT * FROM '._DB_PREFIX_.$this->name.' WHERE `id_customer`='.(int)$id_customer;
    $infosbudget=Db::getInstance()->ExecuteS($sql);
		foreach ($infosbudget as $budget)
	   {
	   $budget1=$budget['budget'];
	   
	   $balance1=$budget['balance'];
	   
	   $depenses1=$budget['depenses'];
	   $start1=$budget['start'];
	   $dt = DateTime::createFromFormat('Y-m-d H:i:s', $start1);
	   $start1front=$dt->format('d/m/Y');
	   
	   
	   $end1=$budget['end'];
	   $dt = DateTime::createFromFormat('Y-m-d H:i:s', $end1);
	   $end1front=$dt->format('d/m/Y');
	   
	   $array= array('budget1' => $budget1,'depenses1' => $depenses1,'balance1' => $balance1,'start1'=>$start1,'end1'=>$end1,'end1front'=>$end1front,'start1front'=>$start1front);
	   return $array ;
	   
	
	
	
	}
	
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
	
	 public static function getCustomerOrders($id_customer,$start,$end,$show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
		FROM `'._DB_PREFIX_.'orders` o
		WHERE o.`id_customer` = '.(int)$id_customer.'
		AND o.`date_add` BETWEEN "'.$start.'" AND "'.$end.'"
		AND o.current_state IN(14,1,10,2,26,24) 
		GROUP BY o.`id_order`
		ORDER BY o.`date_add` DESC');
        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
				FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
				INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
			WHERE oh.`id_order` = '.(int)$val['id_order'].(!$show_hidden_status ? ' AND os.`hidden` != 1' : '').'
			AND oh.`date_add` BETWEEN "'.$start.'" AND "'.$end.'"
			ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
			LIMIT 1');

            if ($res2) {
                $res[$key] = array_merge($res[$key], $res2[0]);
            }
        }
        return $res;
    }
	
	
}
