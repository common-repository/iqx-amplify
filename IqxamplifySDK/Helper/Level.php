<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

abstract class Level
{
    const ERROR = 'error';
    const CRITICAL = 'critical';
    const WARNING = 'warning';
    const INFO = 'info';
    const DEBUG = 'debug';
}
