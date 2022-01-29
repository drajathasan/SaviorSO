<?php
/**
 * Plugin Name: SaviorSO
 * Plugin URI: https://github.com/drajathasan/stock_take_savior
 * Description: Plugin for make dummy database for Stock Take before real stock take started
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan
 */

// get plugin instance
$plugin = \SLiMS\Plugins::getInstance();

// registering menus
$plugin->registerMenu('stock_take', 'Trial SO', __DIR__ . '/index.php');
