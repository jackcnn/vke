<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 09:13
 */
namespace app\weke\controller;
use app\weke\model\ProductAcer;
use app\weke\validate\User;
use app\weke\model\Member;
use app\weke\controller\Common;
use app\weke\controller\Pay;
class AcerStore extends Common
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        //调用验证登录方法
        $this->checkLogin();
    }

    //积分商城商品列表
    public function acerList()
    {
        //根据用户id查询当前剩余元宝
        $userInfo = $this->user_info;
        $acerGoodsList = model('ProductAcer')->getAcerGoodsList();
        return [
            'status' => '1',
            'message' => '请求成功',
            'data' => [
                'user_id' => $this->user_id,
                'user_acer' => $userInfo ?  $userInfo['member_acer'] : 0
            ]
        ];
    }

    //兑换积分商品 兑换的产品id、兑换数量、手机号、支付宝账号
    public function exchangeProduct()
    {
        //验证用户登录状态
        $this->checkLogin();
        $user_id = $this->user_id;
        $data = input('exchange');
        $validate = new User;
        $result   = $validate->scene('acer')->check($data);
        if(!$result){
           return [
               'status' => '0',
               'message' => $validate->getError()
           ];
        }

        //根据商品id查询该商品的类型
        $acerProductType = ProductAcer::get($data['product_id']);
        $product_type = $acerProductType->product_type;
        //该产品库存
        $product_stock = $acerProductType->stock;
        if($product_stock < $data['number']){
            return [
                'status' => '0',
                'message' => '该商品库存不足'
            ];
        }

        //获得价格
        $one_price = $acerProductType->exchange_acer;
        $total_price = $one_price * $data['number'];

        //判断会员元宝数量是否足够
        $member_model = Member::get($this->user_id);
        $member_acer = $member_model->member_acer;
        if($member_acer < $total_price){
            return [
                'status' => '0',
                'message' => '会员元宝不足'
            ];
        }

        //生成订单
        $payModel = action('Pay');
        $makeOrder = $payModel->makeOrder($user_id,$data['product_id'],$data['number'],$one_price,$total_price,$product_type);


        if($makeOrder){
            $result = $payModel->acerPay($makeOrder);
            return $result;
        }else{
            return [
                'status' => '0',
                'message' => '兑换失败'
            ];
        };

    }
}