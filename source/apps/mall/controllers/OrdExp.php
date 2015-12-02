<?php 
set_time_limit(0);

function runOrdExp()
{
    require_once __DIR__ . '/../../utils/OrdExpCron.php';
    $cron = new OrdExpCron();
    $cron->orderExpired();
}

runOrdExp();
?>