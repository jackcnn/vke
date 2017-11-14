<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 17:40
 */

namespace app\admin\controller;
use app\common\controller\Base;
use think\Request;

class Acerstore extends Base
{
    protected $request;
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->request = Request::instance();
    }

    /**
     * 元宝商城管理 - 20171113
     */
    public function acerList()
    {
        $request = $this->request;
        //接收奖品类型
        $product_type = $request->post('product_type');
        //接收上架状态
        $is_sale = $request->post('is_sale');

        if(!empty($product_type)){
            $map['product_type'] = $product_type;
        }

        if(!empty($is_sale)){
            $map['is_sale'] = $is_sale;
        }

        //查询积分商城商品
    }
}