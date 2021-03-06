<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 16:33
 */
namespace app\vke\controller;
use app\vke\controller\Common;
use think\Request;

class Affordable extends Common
{
    protected $sort_now;
    protected $sorts_type;
    protected $sorts = "ASC";

    /**
     * 超实惠首页-9.9
     */
    public function indexNine()
    {
        //2.商店分类名称
        //$nine_arr = cache('type_name_nine');
        if(empty($type_name_nine)){
            $type_name_nine = model('StoreType')->getTypeName(4);
            $type_name_nine['type_id'] = 4;
            //3.三个展示商品
            $fields = 'id,title,pict_url,small_images,reserve_price,zk_final_price';
            $nine_goods = model('Product')->getIndexGoods('',4,$fields,3);
            $nine_arr[] = $type_name_nine;
            $nine_arr[] = $nine_goods;
            cache('type_name_nine',$nine_arr);
        }



        $result = [
            'data' => [
                    'type' => $nine_arr[0],
                    'goods' => $nine_arr[1]
            ]
        ];

        return resultArray($result);
    }
    /**
     * 超实惠-19.9
     * @return array|\think\response\Json
     */
    public function indexNineteen()
    {
        //19.9专区
        //$nineteen_arr = cache('type_name_nineteen');
        if(empty($type_name_nineteen)){
            $type_name_nineteen = model('StoreType')->getTypeName(7);
            $type_name_nineteen['type_id'] = 7;
            $fields = 'id,title,pict_url,small_images,reserve_price,zk_final_price';
            $nineteen_goods = model('Product')->getIndexGoods('',7,$fields,3);

            $nineteen_arr[] = $type_name_nineteen;
            $nineteen_arr[] = $nineteen_goods;
            cache('type_name_nineteen',$nineteen_arr);
        }

        $result = [
            'data' => [
                    'type' => $nineteen_arr[0],
                    'goods' => $nineteen_arr[1],
            ]
        ];

        return resultArray($result);

    }
    /**
     * 超实惠-聚折扣
     * @return array|\think\response\Json
     */
    public function indexDiscount()
    {

        //$discount_arr = cache('type_name_discount');
        if(empty($discount_arr)){
            $fields = 'id,title,pict_url,small_images,reserve_price,zk_final_price';
            //聚折扣
            $type_name_discount = model('StoreType')->getTypeName(5);
            $discount_goods = model('Product')->getIndexGoods('',5,$fields,3);

            $discount_arr[] = $type_name_discount;
            $discount_arr[] = $discount_goods;
            cache('type_name_discount',$discount_arr);
        }

        $result = [
            'data' => [
                'type' => $discount_arr[0],
                'goods' => $discount_arr[1]
            ],
        ];
        return resultArray($result);
    }
    /**
     * 超实惠-应季必备
     * @return array|\think\response\Json
     */
    public function indexSeason()
    {
        //$season_arr = cache('type_name_season');
        if(empty($season_arr)){
            //应季必备
            $fields = 'id,title,pict_url,small_images,reserve_price,volume,zk_final_price';
            $type_name_season = model('StoreType')->getTypeName(6);
            $season_goods = model('Product')->getIndexGoods('',6,$fields);

            $season_arr[] = $type_name_season;
            $season_arr[] = $season_goods;
            cache('type_name_season',$season_arr);
        }

        $result = [
            'data' => [
                    'type' => $season_arr[0],
                    'goods' => $season_arr[1]
            ]
        ];
        return resultArray($result);
    }

    /**
     * 应季必备页-banner
     * @return array|\think\response\Json
     */
    public function seasonBanner()
    {
        $banner = model('Banner')->getBannerList(4);
        $result = [
            'data' => [
                'banner' => $banner
            ]
        ];
        return resultArray($result);
    }


    //查询应季必备的banner
    public function seasonIndex()
    {
        //查询应急必备的商品信息
        $fields = 'id,num_iid,pict_url,small_images,title,zk_final_price,volume,reserve_price';
        $seasonProducts = model('Product')->getIndexGoods('',6,$fields);

        $result = [
            'data' => [
                'season_products' => $seasonProducts
            ]
        ];
        return resultArray($result);
    }

    //聚折扣  调用商品查询api查询6折以下的商品展示在此
    public function discount()
    {
        //查询聚折扣商品
        $fields = 'id,num_iid,pict_url,small_images,title,reserve_price,zk_final_price,volume';
        $discountProducts = model('Product')->getIndexGoods('',5,$fields);
        //根据商品原价与折后价计算折扣
        foreach($discountProducts as $key => $value){
            $num = $value['zk_final_price']['rmb']/$value['reserve_price']['rmb']*10;
            $discountProducts[$key]['number'] = $num;
        }
        $result = [
            'data' => [
                'discount_products' => $discountProducts
            ]
        ];
        return resultArray($result);
    }



    //9.9专区  调用商品查询api查找价格在9.9的商品
    /**
     * 9.9专区排序
     * @return array|\think\response\Json
     */
    public function nineSort()
    {
        $sorts_type = db('sort')
            ->where(['type_id'=>2,'status'=>1])
            ->order('sorts','desc')
            ->field('id,sort_name')
            ->select();
        $result = [
            'data' => [
                'sorts_type' => $sorts_type
            ]
        ];
        return resultArray($result);
    }

    /**
     * 9.9元专区商品
     * @return array|\think\response\Json
     */
    public function nine()
    {
        //查询排序类型
        $sorts_type = db('sort')
            ->where(['type_id'=>2,'status'=>1])
            ->order('sorts','desc')
            ->field('id,sort_name')
            ->select();
        //查询排序字段
        $this->sorts_type = db('sort')
            ->where(['type_id'=>2,'status'=>1])
            ->order('sorts','desc')
            ->column('id,field');
        //接收排序类型
        $sort = input('sort');
        if(empty($sort)){
            $sort = $sorts_type[0]['id'];
        }

        $type = Request::instance()->post('type_id');

//        $sorts = input('sorts');
//        if(!empty($sorts)){
//            $this->sorts = $sorts;
//        }

//        //当前排序分类
//        $sort_now = session('sort_now');
//        if(!empty($sort_now)){
//           if($sort == $sort_now){ //排序类型未改变,改变的是升序倒叙
//               if($this->sorts == "ASC"){
//                   $this->sorts = "DESC";
//               }else{
//                   $this->sorts = "ASC";
//               }
//           }else{
//               $this->sorts = "ASC";
//           }
//        }
//        session('sort_now',$sort);
        $sort_type = $this->sorts_type;

        //查询9.9元商品
        $fields = 'id,num_iid,title,reserve_price,zk_final_price,pict_url,volume';
        $nineProducts = model('Product')->getIndexGoods('',$type,$fields,'',$sort_type[$sort]);

        $result = [
            'data' => [
                'sort' => $sort,
//                'sorts' => $this->sorts,
                'nine_products' => $nineProducts
            ]
        ];
       return resultArray($result);
    }

//    //19.9专区   调用商品查询api查找价格在19.9左右的商品
//    public function nineteen()
//    {
//        //查询19.9商品
//        $goodsId = model('ProductStore')->getGoodsIdList(6);
//        if(empty($goodsId)){
//            $nineteenProducts = [];
//        }else{
//            $nineteenProducts = model('Product')->getGoodsList($goodsId);
//        }
//
//        $result = [
//            'data' => [
//                'nineteen_products' => $nineteenProducts
//            ]
//        ];
//        resultArray($result);
//    }
}