<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ItemPurchaseFrequency extends BaseConfig
{
    public $quantity_sold_today_table_header = 'QST';
    public $average_quantity_sold_per_week_table_header = 'AQSPW';
    public $average_quantity_sold_per_month_table_header = 'AQSPM';
    public $reorder_level_threshold = 0.40;
}