<?php

namespace Mall\Mdu;

class CodesModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const CODE_EXPIRE = 10042;
    const CODE_DISABLE = 10043;
    const CODE_USED = 10044;
    const ENABLE_IMG_CODE = 10045;
    const CODE_ERROR = 10046;
    const CODE_NOT_EXIST = 10048;

    protected $code;

    public function __construct()
    {
        $this->code = $this->initModel('\Mall\Mdu\Models\CodesModel');
    }

    /**
     * [innserCode 生成云码]
     * @param  [type] $value     [description]
     */
    public function batchInnserCode($value)
    {
        return $this->code->addCode($value);
    }

    /**
     * [showCodeList 云码列表]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $where  [description]
     * @return [type]         [description]
     */
    public function showCodeList($num, $offset, $where)
    {
        $condition = $this->codesSearch($where);
        return $this->code->getCodes($num, $offset, $condition);
    }

    /**
     * [getTotalCode 云码总数]
     * @return [type] [description]
     */
    public function getTotalCode($where)
    {
        $condition = $this->codesSearch($where);
        return $this->code->getTotalCode($condition);
    }

    /**
     * [editeCodeInfo description]
     * @param  [type] $status [description]
     * @param  [type] $uid    [description]
     * @param  [type] $id     [description]
     * @return [type]         [description]
     */
    public function editeCodeInfo($status, $id)
    {
        return $this->code->setStatus($status, $id);
    }

    /**
     * [codesSearch 条件搜索]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function codesSearch($where)
    {
        if(!empty($where))
        {
            $condition = '';
            if($where['status'] == '0')
            {
                $condition .= ' where yc_status in(1,3) and ';
            }
            else
            {
                $condition .= ' where yc_status = '.$where['status'].' and ';
            }
            if($where['type'] == '0')
            {
                $condition .= 'yc_type in(1,3) ';
            }
            else
            {
                $condition .= 'yc_type = '.$where['type'].'';
            }
            if(!empty($where['starTime']))
            {
                $condition .= " and yc_start_time >='".strtotime($where['starTime'])."'";
            }
            if(!empty($where['endTime']))
            {
                $condition .= " and yc_end_time <='".strtotime($where['endTime'])."'";
            }
            if(!empty($where['code']))
            {
                $condition .= " and yc_ctx ='".$where['code']."'";
            }
        }
        else
        {
            $condition = '';
        }

        return $condition;
    }

    public function checkCode($uid, $code,$codeImg)
    {
        $RedisLib = new \Mall\Utils\RedisLib($this->di);
        $redis = $RedisLib::getRedis($this->di);
        $key = 'code:' . $uid;
        $num = $redis->get($key);

        if($num == FALSE)
        {
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
            $redis->setex($key, 86400, 1);
        }
        else
            $RedisLib::autoOption($this->di, $key, 'incr');
        if($num >= 3)
        {
            if(!empty($codeImg))
            {
                if(strtolower($codeImg) != $this->di['session']->get('code'))
                    return self::CODE_ERROR;
            }
            else
                return self::ENABLE_IMG_CODE;
        }

        $res = $this->code->getCodeByCode($code);
        if(!empty($res))
        {
            if($res['yc_end_time'] < $_SERVER['REQUEST_TIME'])
                return self::CODE_EXPIRE;
            elseif($res['yc_status'] == 3)
                return self::CODE_DISABLE;
            elseif(!empty($res['yc_used_time']))
                return self::CODE_USED;
            else
                return $res;
        }
        else
            return self::CODE_NOT_EXIST;
    }

    public function getCodeInfo($codeId)
    {
        return $this->code->getCodeByCodeId($codeId);
    }
}
