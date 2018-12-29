
<?php 

/**
 * this class is auto generated by program
 * 
 * @package application.modules.products.components.AmazonHome 
 */
class AmazonSoftwareVideoGames extends AmazonUpload implements IAmazonUpload
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
	protected function categoryMethod($subcate) 
	{
		$arr1 = array('Software','HandheldSoftwareDownloads','SoftwareGames','VideoGames','VideoGamesAccessories','VideoGamesHardware');

		if (in_array($subcate, $arr1)) {
			$method = '_'.$subcate;
		} else {
			throw new Exception("{$subcate}子分类未定义", 1);
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
		//获取分类方法名
		$method = $this->categoryMethod($subcate);

		$type = $this->product->product_is_multi;

		$data['ProductData'] = array(
				$cate => array(
					'ProductType' => array(
						$subcate => array(),
						),
					),
				'Parentage' => '',
				'VariationTheme' => $type == 2 ? $this->product->variation_theme : '',
				'ColorName' => '',
				'Size' => '',
				);

		//单品
		if ($type == 0) {
			$array = $this->$method();
			$data['ProductData'][$cate]['ProductType'][$subcate] = $array;
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');
		}
		//多属性
		else if ($type == 2) {
			//父体设置 $parentage 1为父体 2为子体
			$array = $this->$method();

			$data['ProductData']['Parentage'] = 'parent';

			$data['ProductData'][$cate]['ProductType'][$subcate] = $array;

			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');

			foreach ($this->product->sonskues as $sonprd) {
				$data['DescriptionData']['MfrPartNumber'] = $sonprd->mfr;
				$data['ProductData']['Parentage'] = 'child';

				$variations = json_decode($sonprd->variations, true);
				$suffix     = sprintf('(%s)', implode('-', array_values($variations)));

				$data['ProductData']['ColorName'] = isset($variations['Color']) ? $variations['Color'] : '';
				$data['ProductData']['Size'] = isset($variations['Size']) ? $variations['Size'] : '';

				$child = $data;
				//修改子sku产品sku码
				$child['SKU'] = $sonprd->seller_sku;

				//修改子sku UPC码
				$child['StandardProductID']['Type']  = $sonprd['standard_product_id_type'];
				$child['StandardProductID']['Value'] = $sonprd['standard_product_id'];

				//修改子sku产品的标题
				$child['DescriptionData']['Title'] = $child['DescriptionData']['Title']. $suffix;

				$array = $this->$method();

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
	 * 移除值为空的项
	 * 
	 * @return array
	 */
	protected function removeEmptyItem(array $data)
	{
		//handle descript first
		foreach ($data['DescriptionData'] as $key => $value) {
			if ($this->isEmpty($value)) {
				unset($data['DescriptionData'][$key]);
			}
		}

		foreach ($data['ProductData'] as $key => $value) {
			if ($this->isEmpty($value)) {
				unset($data['ProductData'][$key]);
			}
		}

		foreach ($data as $key => $value) {
			if ($this->isEmpty($value)) {
				unset($data[$key]);
			}
		}

		return $data;
	}

	
	public function _Software()
	{
		return array(
			'MediaFormat' => 'Avi',
			'OperatingSystem' => 'Windows 8',
			'ESRBRating' => 'Everyone 10 and Older',
			);
	}

	public function _HandheldSoftwareDownloads()
	{
		return array(
			'ApplicationVersion' => 'new',
			'DownloadableFile' => array(
				'DownloadableFileFormat' => 'default',
				'FileSize' => array(
					'@unitOfMeasure' => 'MB',
					'%' => '30',
					),
				),
			'OperatingSystem' => 'Windows 8',
			);
	}

	public function _SoftwareGames()
	{
		return array(
			'SoftwareVideoGamesGenre' => 'default',
			'ESRBRating' => 'Everyone 10 and Older',
			'MediaFormat' => 'Avi',
			'OperatingSystem' => 'Windows 8',
			);
	}

	public function _VideoGames()
	{
		return array(
			'ConsoleVideoGamesGenre' => '',
			'ESRBRating' => 'Everyone 10 and Older',
			'HardwarePlatform' => 'Windows',
			);
	}

	public function _VideoGamesAccessories()
	{
		return array(
			'HardwarePlatform' => 'Windows',
			);
	}

	public function _VideoGamesHardware()
	{
		return array(
			'HardwarePlatform' => 'Windows',
			);
	}

}
// end of class
