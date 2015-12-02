// <?php

// namespace Mall\Mall\Controllers;

// use  Mall\Mdu\OrderModule as Order;
// //use Mall\Utils\RedisLib;

// class CronController extends ControllerBase
// {
//     const ORDER_EXPIRED = 172800;

//     //private $redis;
//     private $order;

//     public function initialize()
//     {
//         //$this->redis = RedisLib::getRedis($this->di);
//         $this->order = new Order;
//     }

//     public function orderExpiredAction()
//     {
//         //查找出订单超过时间的订单order_ids
//         $exOids = $this->order->getExOids(self::ORDER_EXPIRED)[0]['oids'];

//         //通过order_id 找到 order_goods 所有的 goods_id attrs_ids
//         $orderInfos = $this->order->getGidsAids($exOids);
//         $exOrderInfo = [];

//         foreach($orderInfos as $key => $val)
//         {
//             $attrsInfo = json_decode($val['attrs_info'], true);
//             $k = $val['goods_id'] . '_' . $attrsInfo[0]['id'] . ',' . $attrsInfo[1]['id'];
//             if(!isset($exOrderInfo[$k]))
//                 $exOrderInfo[$k] = $val['num'];
//             else
//                 $exOrderInfo[$k] += $val['num'];
//         }

//         $tmp['gids'] = $tmp['attrs'] = $tmp['nums'] = [];
//         foreach ($exOrderInfo as $ek => $v)
//         {
//             $ek = explode('_', $ek);
//             array_push($tmp['gids'] , $ek[0]);
//             array_push($tmp['attrs'] , $ek[1]);
//             array_push($tmp['nums'] , $v);
//         }

//         //批量添加所有商品 组合属性商品的库存
//         $orderInfos = $this->order->uptGoodsNums($exOids, $tmp['gids'], $tmp['attrs'], $tmp['nums']);
//     }
// }
