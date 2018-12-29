
<?php 

/**
 * this class is auto generated by program
 * 
 * @package application.modules.products.components.AmazonTiresAndWheels 
 */
class AmazonWireless extends AmazonUpload implements IAmazonUpload
{

	/**
	 * CE子分类名
	 * 
	 * @var string
	 */
	public $subcategory = null;

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
	protected function categoryMethod($subcate) 
	{
		$arr = array(	'WirelessAccessories',
						'WirelessDownloads',
						);

		if (in_array($subcate, $arr)) {
			$method = '_'.$subcate;
		} else {
			throw new Exception("{$subcate}子分类暂未定义", 1);
		}
		return $method;
	}
    /**
	 * 上传产品
	 *
	 * @return bool
	 */
	protected function createProduct()
	{
		$data = $this->_getBaseInfo();
		$xsd1 = UebModel::model('AmazonProdataxsd')->findByPk($this->product->xsd_type[0]);
		$xsd2 = UebModel::model('AmazonProdataxsd')->findByPk($this->product->xsd_type[1]);
		if (empty($xsd1)) {
			throw new Exception("缺少精确的分类模板信息", 1);
		}
		if (empty($xsd2)) {
			throw new Exception("缺少精确的XSD模板信息", 1);
		}
		$cate = $xsd1->category;
		$subcate = $xsd2->category;
		$this->subcategory = $subcate;
		if ($subcate == 'WirelessAccessories') {
			$this->VariationUpload($cate, $subcate, $data);
		} else {
			$this->SolidUpload($cate, $subcate, $data);
		}
	}

	/**
	*变体上传
	*/
	protected function VariationUpload($cate, $subcate, $data) 
	{
		//获取分类方法名
		$method = $this->categoryMethod($subcate);

		$type = $this->product->product_is_multi;

		$data['ProductData'] = array(
			$cate => array(
				'ProductType' => array(
					$subcate => array(),
					),
				),
			);

		//单品
		if ($type == 0) {
			$array = $this->$method($type);
			$data['ProductData'][$cate]['ProductType'][$subcate] = $array;
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');
		}
		//多属性
		else if ($type == 2) {
			//父体设置 $parentage 1为父体 2为子体
			$array = $this->$method($type,$parentage = 1);

			$data['ProductData'][$cate]['ProductType'][$subcate] = $array;
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');

			foreach ($this->product->sonskues as $sonprd) {
				$data['DescriptionData']['MfrPartNumber'] = $sonprd->mfr;
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

				$array = $this->$method($type,$parentage = 2);

				$array['Color'] = isset($variations['Color']) ? $variations['Color'] : $sonprd->seller_sku;
				
				$child['ProductData'][$cate]['ProductType'][$subcate] = $array;

				$xmls[$sonprd->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($child), 'Product');

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
	*单品上传
	*/
	protected function SolidUpload($cate, $subcate, $data) 
	{
		//获取分类方法名
		$method = $this->categoryMethod($subcate);

		$type = $this->product->product_is_multi;

		$data['ProductData'] = array(
			$cate => array(
				'ProductType' => array(
					$subcate => array(),
					),
				),
			);
		//单品
		if ($type == 0) {
			$array = $this->removeEmptyItemByKey($this->$method());
			$data['ProductData'][$cate]['ProductType'][$subcate] = $array;
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');
		}
		//多属性子SKU当单品
		else if ($type == 2) {

			foreach ($this->product->sonskues as $sonprd) {
				$data['DescriptionData']['MfrPartNumber'] = $sonprd->mfr;
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

				$array = $this->removeEmptyItemByKey($this->$method());

				$child['ProductData'][$cate]['ProductType'][$subcate] = $array;

				$xmls[$sonprd->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($child), 'Product');

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
	 * 覆盖父类方法
	 * 
	 * @param  array  $array
	 * @param  string $key
	 * @return array 
	 */
	protected function removeEmptyItemByKey(array $array)
	{
		foreach ($array as $k => $value) {
			if ($this->isEmpty($value)) {
				unset($array[$k]);
			}
		}
		return $array;
	}
	/**
	 *覆盖父类uploadProduct方法
	 * @return bool
	 */
	public function uploadProduct($id = null)	
	{
		$ret = new stdClass();
		$ret->errcode = true;

		// 开启事务
        $transaction = UebModel::model('AmazonProductMain')->getDbConnection()->beginTransaction();

		try {
				$this->createProduct();
				
			if ($this->subcategory == 'WirelessAccessories') {
				parent::updateQuantityAvaiable();
				parent::assignPrice();
				parent::sendProductImage();
				parent::establishRelationships();	
			} else {
				$this->updateQuantityAvaiable();
				$this->assignPrice();
				$this->sendProductImage();
				$this->establishRelationships();
			}
					

			//提交事务
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback();			

			$ret->errcode = false;
			$ret->message = $e->getMessage();
		}

		return $ret;
	}

	protected function _WirelessAccessories($type,$parentage)
	{       //单品
        if ($type == 0) {
            return array(
                    'Color' => 'default',
	            	);
        }
       	//多属性 
        else if ($type == 2 && $parentage == 1) {
        	//父体数据
        	return array(
                    'VariationData' => array(
	                        'Parentage' => 'parent',
	                        'VariationTheme' => 'Color',
                    		),
                    );
        }
        //子体数据 
        else if ($type == 2 && $parentage == 2) {
        	return array(
                    'VariationData' => array(
	                        'Parentage' => 'child',
	                        'VariationTheme' => 'Color',
                    		),
                    'Color' => 'default',
	                );
        } else {
        	throw new Exception("分类产品数据异常", 1);
        }
	}

	protected function _WirelessDownloads()
	{       
            return array(
            		'CompatiblePhoneModels' => '',
                    'ManufacturerName' => $this->description->manufacture,
                    'AdditionalFeatures' => '',
                    'Keywords' => '',
                    'ApplicationVersion' => '',
	            	);
	}

	/**
	 * 上传关系
	 * 
	 * @return bool
	 */
	protected function establishRelationships()
	{
		//覆盖父类方法,不建立父子关系
	}
	/**
	 * 上传图片,覆盖父类方法
	 * 
	 * @return bool
	 */
	protected function sendProductImage()
	{
		if($this->product->product_is_multi == 0){
			if (empty($this->product->upload_images)) {
			return;
			}
		
			foreach ($this->product->upload_images as $key => $id) {
				$type= ($key==0)?'Main':sprintf('PT%d', $key);
				$urldir=UebModel::model('AmazonImage')->findByPk($id)->image_url;
				$url = self::IMAGE_BASE_URL . $urldir;
				$reqArrList[] = array(
					'sku' => $this->product->seller_sku,
					'type' => $type,
					'url' => $url,
				);	
			}
		}

		//子sku图片上传
		if ($this->product->product_is_multi == 2) {
			foreach ($this->sonskues as $index => $sonprd) {

				$idArr= json_decode($sonprd->upload_images, true);

				if (empty($idArr)) {
					continue;
				}
				$images = array();
				foreach ($idArr as $key => $value) {
					$criteria = new CDbCriteria();
					$criteria->select = 'image_url';
					$criteria->condition = "id='".$value."'";
					$images[] = UebModel::model('AmazonImage')->find($criteria)->image_url;

					if (empty($images)) {
						continue;
					}
				}

				if (empty($images)) {
					continue;
				}


				$reqArrList [] = array(
					'sku' => $sonprd->seller_sku,
					'type' => 'Main',
					'url' => self::IMAGE_BASE_URL . $images[0],
				);

				$i = 0;

				foreach ($images as $val) {
					if ($i == 0) {
						$i++;
						continue;
					}
					if ($i > 8) break;

					$reqArrList[] = array(
						'sku' => $sonprd->seller_sku,
						'type' => sprintf('PT%d', $i),
						'url' => self::IMAGE_BASE_URL . $val,
					);

					$i++;
				}
			}
		}

		//一次性组装该XML, 不分开处理

		$found = UebModel::model('AmazonProductTask')->find("account_id=:aid AND amz_product_id=:id AND type=:type AND sku=:sku",
			array(
				':aid' => $this->product->account_id,
				':id' => $this->product->id,
				':type' => self::IMAGE,
				':sku' => $this->product->sku,
				));

		$model = !empty($found) ? $found : new AmazonProductTask();

		$model->flow_no = $this->genUniqidId();
		$model->account_id = $this->product->account_id;
		$model->amz_product_id = $this->product->id;
		$model->sku = $this->product->sku;
		$model->type = self::IMAGE;
		$model->xml = $this->getRealXML($reqArrList, SubmitFeedRequest::SEND_IMAGE);
		$model->status = 1;
		$model->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
		$model->create_date = time();	

		$model->save();

		if (empty($model->id)) {
			throw new Exception("保存图片XML数据出错", 1);
		}

		//日志
		$log = new AmazonUpLog();

		$log->account_id = $this->product->account_id;
		$log->amz_product_id = $this->product->id;
		$log->title = empty($found) ? '上传图片' : '更新图片';
		$log->content = '';
		$log->type = self::IMAGE;
		$log->num = count($reqArrList);
		$log->operator = empty($found) ? 1: 2;
		$log->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
		$log->create_date = time();

		$log->save();

		if (empty($log->id)) {
			throw new Exception('添加图片日志出错', 1);
		}
	}

	/**
	 * 上传库存,覆盖父类方法
	 * 
	 * @return bool
	 */
	protected function updateQuantityAvaiable()
	{
		$xmls = array();

		//单品
		if ($this->product->product_is_multi == 0) {
			$reqArrList = array(
				array(
					'sku' => $this->product->seller_sku,
					'qty' => $this->product->inventory ? $this->product->inventory : 100,
					'latency' => 2,
				),
			);			
		}

		//多属性
		if ($this->product->product_is_multi == 2) {
			foreach ($this->sonskues as $sonprd) {
				$reqArrList [] = array(
					'sku' => $sonprd->seller_sku,
					'qty' => $sonprd->inventory,
					'latency' => 2,
				);
			}
		}

		//一次性组装该XML, 不分开处理

		$found = UebModel::model('AmazonProductTask')->find("account_id=:aid AND amz_product_id=:id AND type=:type AND sku=:sku",
			array(
				':id' => $this->product->id,
				':aid' => $this->product->account_id,
				':type' => self::INVENTORY,
				':sku' => $this->product->sku,
				));

		$model = !empty($found) ? $found : new AmazonProductTask();

		$model->flow_no = $this->genUniqidId();
		$model->account_id = $this->product->account_id;
		$model->amz_product_id = $this->product->id;
		$model->sku = $this->product->sku;
		$model->type = self::INVENTORY;
		$model->xml = $this->getRealXML($reqArrList, SubmitFeedRequest::AVAILABLE_INVENTORY);
		$model->status = 1;
		$model->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
		$model->create_date = time();	

		$model->save();

		if (empty($model->id)) {
			throw new Exception("保存库存XML数据出错", 1);
		}

		//日志

		$log = new AmazonUpLog();

		$log->account_id = $this->product->account_id;
		$log->amz_product_id = $this->product->id;
		$log->title = empty($found) ? '上传库存' : '更新库存';
		$log->content = '';
		$log->type = self::INVENTORY;
		$log->num = count($reqArrList);
		$log->operator = empty($found) ? 1: 2;
		$log->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
		$log->create_date = time();

		$log->save();

		if (empty($log->id)) {
			throw new Exception('添加库存日志出错', 1);
		}
	}

	/**
	 * 上传价格,覆盖父类方法
	 * 
	 * @return bool
	 */
	protected function assignPrice()
	{
		$xmls  = array(); // ???

		$sdate    = date("Y-m-d\TH:i:s\Z", time());
		$edate    = date('Y-m-d\TH:i:s\Z', time() + 90* 24 * 60 * 60);
		$currency = $this->_getCurrencyBySite($this->_getWebSite());
		if($this->product->product_is_multi == 0){
			$reqArrList = array(
			array(
				'sku' => $this->product->seller_sku,
				'currency' => $currency, 
				'stdprice' => $this->product->price,
				'stime' => $sdate,
				'etime' => $edate,
				'saleprice' => round($this->product->price*$this->product->discountrate, 2),
			),
		);
		}
		


		if ($this->product->product_is_multi == 2) {
			foreach ($this->sonskues as $sonprd) {
				$reqArrList [] = array(
					'sku' => $sonprd->seller_sku,
					'currency' => $currency,
					'stdprice' => $sonprd->price,
					'stime' => $sdate,
					'etime' => $edate,
					'saleprice' => round($sonprd->price*$this->product->discountrate, 2),
				);
			}
		}

		//一次性组装该XML, 不分开处理

		$found = UebModel::model('AmazonProductTask')->find("account_id=:aid AND amz_product_id=:id AND type=:type AND sku=:sku",
			array(
				':id' => $this->product->id,
				':aid' => $this->product->account_id, 
				':type' => self::PRICE,
				':sku' => $this->product->sku,
				));

		$model = !empty($found) ? $found : new AmazonProductTask();

		$model->flow_no = $this->genUniqidId();
		$model->account_id = $this->product->account_id;
		$model->amz_product_id = $this->product->id;
		$model->sku = $this->product->sku;
		$model->type = self::PRICE;
		$model->xml = $this->getRealXML($reqArrList, SubmitFeedRequest::PRICE);
		$model->status = 1;
		$model->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
		$model->create_date = time();	

		$model->save();

		if (empty($model->id)) {
			throw new Exception("保存价格XML数据出错", 1);
		}

		//日志

		$log = new AmazonUpLog();

		$log->account_id = $this->product->account_id;
		$log->amz_product_id = $this->product->id;
		$log->title = empty($found) ? '上传价格' : '更新价格';
		$log->content = '';
		$log->type = self::PRICE;
		$log->num = count($reqArrList);
		$log->operator = empty($found) ? 1: 2;
		$log->creator = Yii::app()->user->id?Yii::app()->user->id:$this->uid;
		$log->create_date = time();

		$log->save();

		if (empty($log->id)) {
			throw new Exception('添加价格日志出错', 1);
		}
	}


}
// end of class
