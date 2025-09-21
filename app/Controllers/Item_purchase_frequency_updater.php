<?php

namespace App\Controllers;

use App\Libraries\ItemPurchaseFrequency_lib;
use CodeIgniter\Controller;

class ItemPurchaseFrequencyUpdater extends Controller
{
    /**
     * @var ItemPurchaseFrequency_lib
     */
    protected $item_purchase_frequency_lib;

    public function __construct()
    {
        // Load the library and pass the database connection.
        $this->item_purchase_frequency_lib = new ItemPurchaseFrequency_lib();
    }
    
    public function index()
    {
        $this->reset_quantity_sold_today();
        $this->recompute_aqspw();
        $this->recompute_aqspm();
        $this->recompute_reorder_level();
    }
    
    public function reset_quantity_sold_today()
    {
        $this->item_purchase_frequency_lib->reset_quantity_sold_today();
    }
    
    public function recompute_aqspw()
	{
		$this->item_purchase_frequency_lib->recompute_aqspw();
	}

	public function recompute_aqspm()
	{
		$this->item_purchase_frequency_lib->recompute_aqspm();
	}
	
	public function recompute_reorder_level()
	{
		$this->item_purchase_frequency_lib->recompute_reorder_level();
	}
}