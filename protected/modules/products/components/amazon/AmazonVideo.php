
<?php 

/**
 * this class is auto generated by program
 * 
 * @package application.modules.products.components.AmazonHome 
 */
class AmazonVideo extends AmazonUpload implements IAmazonUpload
{
	/**
	 * @inheritdoc
	 * @noreturn
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * 根据子分类名映射相应的子分类方法处理
	 * @return string
	 */
	protected function categoryMethod($subcate) {
		$arr1 = array('VideoDVD');
		$arr2 = array('VideoVHS');
		
		if (in_array($subcate, $arr1)) {
			$method = '_subCategory1';
		} else if (in_array($subcate, $arr2)) {
			$method = '_subCategory2';
		} else {
			throw new Exception("{$subcate}子分类暂时未设置", 1);
		}
		return $method;
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

		$subcate = $xsd2->category;
		//获取分类方法名
		$method = $this->categoryMethod($subcate);

		$type = $this->product->product_is_multi;

		$data['ProductData'] = array(
			'Video' => array(
				'ProductType' => array(
					$subcate => array(),
					),
				),
			);
		//单品
		if ($type == 0) {
			$array = $this->$method($type);
			$data['ProductData']['Video']['ProductType'][$subcate] = $array;
			
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');
		}
		//多属性 Video分类XSD模板暂未定义多属性产品
		else if ($type == 2) {
			throw new Exception("Video分类多属性未被定义！", 1);
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
		$this->addLog(array(
			'account_id' => $this->product->account_id,
			'amz_product_id' => $this->product->id,
			'title' => empty($found) ? '添加产品' : '更新产品',
			'content' => '',
			'type' => self::PRODUCT,
			'num' => 1,
			'operator' => empty($found) ? 1 : 2,
		));

	}

	/**
	 * ('VideoDVD')分类组装数据
	 * 
	 * @throws Exception
	 */
	protected function _subCategory1($type,$parentage)
	{       //单品
        if ($type == 0) {
            return array(
            		'FSKRating' => 'unknown',
            		'MPAARating' => 'unrated',
                    'DVDRegion' => '2',
                    'Binding' => 'dvd',
	            	);
        } else {
        	throw new Exception("分类产品数据异常", 1);
        }
	}

	/**
	 * ('VideoVHS')分类组装数据
	 * 
	 * @throws Exception
	 */
	protected function _subCategory2($type,$parentage)
	{       //单品
        if ($type == 0) {
            return array(
            		'FSKRating' => 'unknown',
            		'MPAARating' => 'unrated',
                    'Binding' => 'VHStape',
	            	);
        } else {
        	throw new Exception("分类产品数据异常", 1);
        }
	}

}
// end of class
