<?php 

/**
 * The default template
 *
 * @author mrlina <714480119@qq.com>
 */
Class AmazonArts extends AmazonUpload implements IAmazonUpload
{
    /**
     * @inheridoc
     *
     * @noreturn
     */
    public function init()
    {
        parent::init();
    }

	/**
	 * 移除值为空的项
	 * 
	 * @return array
	 */
    protected function trimTier(array $data,$pcate,$chcate)
    {
        //handle descript first
        foreach ($data['DescriptionData'] as $key => $value) {
            if ($this->isEmpty($value)) {
                unset($data['DescriptionData'][$key]);
            }
        }

        foreach ($data['ProductData'][$pcate]['ProductType'][$chcate] as $key => $value) {
            if ($this->isEmpty($value)) {
                unset($data['ProductData'][$pcate]['ProductType'][$chcate][$key]);
            }
        }

        foreach ($data as $key => $value) {
            if ($this->isEmpty($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    protected function mapVarisTheme($key)
    {
        $themes = array(
            'Size' => 'SizeName',
//            'Color' => 'FrameMaterial',
            'Size-Color' => 'Size-Material',
        );

        return $themes[$key];
    }

    /**
	 * 上转产品
	 *
	 * @return bool
	 */
	protected function createProduct()
	{
		$data = $this->_getBaseInfo();
		$xsd2 = UebModel::model('AmazonProdataxsd')->findByPk($this->product->xsd_type[1]);

		if (empty($xsd2)) {
			throw new Exception("缺少精确的XSD模板信息", 1);
		}

        //子分类方法名
        $method ='_'.$xsd2->category;

		//单品
        //$arrt=unserialize($this->product->productdata->product_data);
        $theme=$this->mapVarisTheme($this->product->variation_theme);
		if ($this->product->product_is_multi == 0) {
			//仅指定分类信息
			$data['ProductData'] = array(
				'Arts' => array(
					'ProductType' => array(
                        $xsd2->category=>$this->$method()
                    ),
				),
			);

			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->trimTier($data,'Arts',$xsd2->category), 'Product');
		}
		//多属性
		else if($this->product->product_is_multi == 2) {
			//父体变体设置
			$data['ProductData'] = array(
                'Arts' => array(
                    'ProductType' => array(
                        $xsd2->category=>$this->$method(),
                    ),
                    'VariationData' => array(
                        'Parentage' => 'parent', //指定为子体
                        'VariationTheme' => $theme, //指定与父体相同的Theme
                    ),
                ),
			);

			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->trimTier($data,'Arts',$xsd2->category), 'Product');

			//循环所有子sku产品
			foreach ($this->product->sonskues as $sonprd) {
				//clone父体信息
				$child = $data;

				$variations = json_decode($sonprd->variations, true);
				$suffix     = sprintf('(%s)', implode('-', array_values($variations)));

				//修改子sku产品sku码
				$child['SKU'] = $sonprd->seller_sku;

				//修改子sku UPC码
				$child['StandardProductID']['Type']  = $sonprd['standard_product_id_type'];
				$child['StandardProductID']['Value'] = $sonprd['standard_product_id'];

				//修改子sku产品的标题
				$child['DescriptionData']['Title'] = $child['DescriptionData']['Title']. $suffix;
				//修改子sku变体内容
				$child['ProductData'] = array(
                    'Arts' => array(
                        'ProductType' => array(
                            $xsd2->category=>$this->$method($variations),
                        ),
                        'VariationData' => array(
                            'Parentage' => 'child', //指定为子体
                            'VariationTheme' => $theme, //指定与父体相同的Theme
                        ),
                    ),
				);

				$xmls[$sonprd->sku] = $this->arr2xml->buildXML($this->trimTier($child,'Arts',$xsd2->category), 'Product');
			}
		}


		//将其推到任务队列
		foreach ($xmls as $sku => $xml) {

			//查找是否已经存在
			$found = UebModel::model('AmazonProductTask')->find("account_id=:aid AND amz_product_id=:id AND type=:type AND sku=:sku",
				array(
					':id' => $this->product->id,
					':aid' => $this->product->account_id,
					':type' => self::PRODUCT,
					':sku' => $sku,
					));

			$model = !empty($found) ? $found : new AmazonProductTask();

			$model->flow_no = $this->genUniqidId();
			$model->account_id = $this->product->account_id;
			$model->amz_product_id = $this->product->id;
			$model->sku = $sku;
			$model->type = self::PRODUCT;
			$model->xml = $this->getRealXML(array($xml), SubmitFeedRequest::NEW_PRODUCT);
			$model->status = 1;
			$model->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
			$model->create_date = time();

			$model->save();

			if (empty($model->id)) {
				throw new Exception("保存{$sku}产品XML数据出错", 1);
			}
		}

		//记录日志
        if(!empty($this->product->id) && !empty($model->id)){
            $log = new AmazonUpLog();
            $log->account_id = $this->product->account_id;
            $log->amz_product_id = $this->product->id;
            $log->title = empty($found) ? '添加产品' : '更新产品';
            $log->content = '';
            $log->type = self::PRODUCT;
            $log->num = 1;
            $log->operator = empty($found) ? 1: 2;
            $log->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
            $log->create_date = time();
            $log->save();
        }

		if (empty($log->id)) {
			throw new Exception('添加产品日志出错', 1);
		}
	}

	protected function _FineArt($variations=''){
	    $item=array(
            'ArtworkType'=>'ArtworkType',
            'Artist'=>'Artist',
            'ArtworkMedium'=>'ArtworkMedium',
            'Date'=>date('Y-m-d\TH:i:s'),
            'FrameMaterial'=>$variations['Color'],
            'Framed'=>"false",//boolean
            'SizeName'=>$variations['Size'],
            'SaleType'=>'SaleType'
        );
        return $item;
    }

    protected function _FineArtEditioned($variations=''){
        $item=array(
            'ArtworkType'=>'ArtworkType',
            'Artist'=>'Artist',
            'ArtworkMedium'=>'ArtworkMedium',
            'Date'=>date('Y-m-d\TH:i:s'),
            'FrameMaterial'=>$variations['Color'],
            'Framed'=>"false",//boolean
            'LimitedEditionSize'=>'1',
            'SizeName'=>$variations['Size'],
            'SaleType'=>'SaleType'
        );
        return $item;
    }

}
