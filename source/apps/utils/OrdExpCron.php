<?php
use Phalcon\Mvc\Application;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\DI\FactoryDefault;
use  Mall\Mdu\OrderModule;

spl_autoload_register(function ($class) {
    $matches = explode("\\", $class);
    if (file_exists(__DIR__ . '/../mdu/Models/' . $matches[count($matches) - 1] . '.php')) {
        require_once __DIR__ . '/../mdu/Models/' . $matches[count($matches) - 1] . '.php';
    } elseif (file_exists(__DIR__ . '/../utils/' . $matches[count($matches) - 1] . '.php')) {
        require_once __DIR__ . '/../utils/' . $matches[count($matches) - 1] . '.php';
    } elseif (file_exists(__DIR__ . '/../mdu/' . $matches[count($matches) - 1] . '.php')) {
        require_once __DIR__ . '/../mdu/' . $matches[count($matches) - 1] . '.php';
    }
});

class OrdExpCron
{
    //订单过期时间为30分钟
    const ORDER_EXPIRED = 1800;

    protected $application = null;
    protected $di = null;
    protected $order = null;

    public function setDi() {
        $this->di = new FactoryDefault();

        $this->di['sysconfig'] = function ()
        {
            return require __DIR__ . '/../../config/sysconfig.php';
        };

        /**
         * Read configuration
         */
        $config = require __DIR__ . '/../mall/config/config.php';

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $this->di['db'] = new Mysql(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname,
                "options" => array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                    \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                    \PDO::ATTR_PERSISTENT => false
                )
            ));

        return $this->di;
    }

    public function __construct()
    {
        $this->application = new Application();
        try {
             /**
              * Assign the DI
              */
             $this->application->setDI($this->setDi());
        } catch (Phalcon\Exception $e) {
            echo $e->getMessage();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $this->order = new OrderModule();
    }

    public function orderExpired()
    {
        //查找出订单超过时间的订单order_ids
        $exOids = $this->order->getExOids(self::ORDER_EXPIRED)[0]['oids'];

        if (!$exOids) {
            echo date("Y-m-d H:i:s") . ", run order expiring, no expired order.\n";
            return;
        }

        //通过order_id 找到 order_goods 所有的 goods_id attrs_ids
        $orderInfos = $this->order->getGidsAids($exOids);
        $exOrderInfo = [];

        foreach($orderInfos as $key => $val)
        {
            $attrsInfo = json_decode($val['attrs_info'], true);
            $k = $val['goods_id'] . '_' . $attrsInfo[0]['id'] . ',' . $attrsInfo[1]['id'];
            if(!isset($exOrderInfo[$k]))
                $exOrderInfo[$k] = $val['num'];
            else
                $exOrderInfo[$k] += $val['num'];
        }

        $tmp['gids'] = $tmp['attrs'] = $tmp['nums'] = [];
        foreach ($exOrderInfo as $ek => $v)
        {
            $ek = explode('_', $ek);
            array_push($tmp['gids'] , $ek[0]);
            array_push($tmp['attrs'] , $ek[1]);
            array_push($tmp['nums'] , $v);
        }

        //批量添加所有商品 组合属性商品的库存
        $orderInfos = $this->order->uptGoodsNums($exOids, $tmp['gids'], $tmp['attrs'], $tmp['nums']);

        echo date("Y-m-d H:i:s") . ", run order expiring, expired orderIDs: ($exOids).\n";
    }
}
