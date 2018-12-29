
<?php 

/**
 * this class is auto generated by program
 * 
 * @package application.modules.products.components.AmazonTiresAndWheels 
 */
class AmazonComputers extends AmazonUpload implements IAmazonUpload
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
		$arr = array(	'CarryingCaseOrBag',
						'ComputerAddOn',
						'ComputerComponent',
						'ComputerCoolingDevice',
						'ComputerDriveOrStorage',
						'ComputerInputDevice',
						'Keyboards',
						'Monitor',
						'Motherboard',
						'NetworkingDevice',
						'RamMemory',
						'SoundCard',
						'Webcam',);

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

		$this->SolidUpload($cate, $subcate, $data);

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
			$array = $this->removeEmptyItemByKey1($this->$method());
            $data['ProductData'][$cate]['ProductType'][$subcate] = $array;
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');
		}
		//多属性子SKU当单品
		else if ($type == 2) {
            $data['ProductData'] = array(
				$cate => array(
					'ProductType' => array(
						$subcate => array(
								'VariationData' =>array(
		            				'Parentage' => 'parent',
		            			   'VariationTheme' => $this->product->variation_theme,
	            				),

							),
						),
//					'Color'=>'normal',
//					'ColorMap'=>'default',

                ),

			);
            $array = $this->$method();
            $data['ProductData'][$cate]['ProductType'][$subcate] = $array;
            $data['ProductData'][$cate]['ProductType'][$subcate]['VariationData']['Parentage']='parent';
            $data['ProductData'][$cate]=$this->removeEmptyItemByKey2($data['ProductData'][$cate]);
            $data['ProductData'][$cate]['ProductType'][$subcate]['VariationData']['VariationTheme']=$this->product->variation_theme;
			$xmls[$this->product->sku] = $this->arr2xml->buildXML($this->removeEmptyItem($data), 'Product');

			foreach ($this->product->sonskues as $sonprd) {

				$data['DescriptionData']['MfrPartNumber'] = $sonprd->mfr;
				$child = $data;

				$variations = json_decode($sonprd->variations, true);
                $child['ProductData'][$cate]['ProductType'][$subcate]['VariationData']['VariationTheme']=$this->product->variation_theme;
                $child['ProductData'][$cate]['ProductType'][$subcate]['VariationData']['Parentage']='child';
				$child['ProductData'][$cate]['Color']=isset($variations['Color']) ? $variations['Color'] : '';
				$child['ProductData'][$cate]['ColorMap']=isset($variations['Color']) ? 'default' : '';
				$child['ProductData'][$cate]['Size']= isset($variations['Size']) ? $variations['Size'] : '';
				$suffix     = sprintf('(%s)', implode('-', array_values($variations)));

				//修改子sku产品sku码
				$child['SKU'] = $sonprd->seller_sku;

				//修改子sku UPC码
				$child['StandardProductID']['Type']  = $sonprd['standard_product_id_type'];
				$child['StandardProductID']['Value'] = $sonprd['standard_product_id'];

				//修改子sku产品的标题
				$child['DescriptionData']['Title'] = $child['DescriptionData']['Title']. $suffix;
				$array = $this->$method();
				//$array = $this->removeEmptyItemByKey($this->$method());
				
				$child['ProductData'][$cate]['ProductType'][$subcate] = $array;
                $child['ProductData'][$cate]['ProductType'][$subcate]['VariationData']['VariationTheme']=$this->product->variation_theme;
				$child['ProductData'][$cate]=$this->removeEmptyItemByKey2($child['ProductData'][$cate]);
//				 echo "<pre>";
//				 print_r($this->removeEmptyItem($child))
//				 ;die;
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
	protected function removeEmptyItemByKey1(array $array)
	{
		foreach ($array as $k => $value) {
			if ($this->isEmpty($value)) {
				unset($array[$k]);
			}
		}
		return $array;
	}
    protected function removeEmptyItemByKey2(array $arr)
    {

            foreach ($arr as $k=>$v)
            {
                if(is_array($arr[$k]))
                {
                    $arr[$k] = $this->removeEmptyItemByKey2($arr[$k]);
                }
                else
                {
                    if(empty($arr[$k]))
                    {
                        unset($arr[$k]);
                    }
                }
            }
            return $arr;

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
				parent::updateQuantityAvaiable();
				parent::assignPrice();
				parent::sendProductImage();
				parent::establishRelationships();

			//提交事务
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback();			

			$ret->errcode = false;
			$ret->message = $e->getMessage();
		}

		return $ret;
	}

	protected function _CarryingCaseOrBag()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
            		'CheckpointTSAFriendly' => '',
            		'CompatibleDeviceSize' => '',
            		'CompatibleDeviceFormFactor' => '',
            		'HandOrientation' => '',
            		'HolderCapacity' => '',
            		'MaterialType' => 'default',
            		'ModelNumber' => '',
	            	);
	}

	protected function _ComputerAddOn()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"ACAdapterCurrent" => '',
					"CableLength" => '',
					"CableSpeed" => '',
					"CableType" => 'default',
					"CompatibleDevices" => '',
					"CompatibleDeviceSize" => '',
					"Conductor" => '',
					"ConnectionType" => '',
					"ConnectorNumber" => '',
					"MaximumHorizontalVideoResolution" => '',
					"MaximumVerticalVideoResolution" => '',
					"ModelNumber" => '',
					"NumberOfFans" => '',
					"NumberOfMonitorConnections" => '',
					"PrivacyScreenMaterialType" => '',
					"PrivacyScreenSize" => '',
					"Voltage" => '',
					"Wattage" => '',
	            	);
	}

	protected function _ComputerComponent()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"Efficiency" => 'good',
                    'ProcessorSocket'=>'default',
                    'RAID'=>'default',
                    'WirelessStandard'=>'802_11_A'
	            	);
	}

	protected function _ComputerCoolingDevice()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"AdditionalFeatures" => 'default',
					"CoolingType" => '',
					"CPUSocketCompatability" => '',
					"FanIncluded" => '',
					"FanLED" => '',
					"FanMaximumAirflow" => '',
					"FanMaximumNoiseLevel" => '',
					"FanMaximumSpeed" => '',
					"FanPowerConnector" => '',
					"HeatsinkMaterial" => '',
					"LargestFanSize" => '',
					"ModelNumber" => '',
	            	);
	}

	protected function _ComputerDriveOrStorage()
	{
            return array(
            	    'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"Efficiency" => 'good',
	            	);
	}

	protected function _ComputerInputDevice()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"AdditionalFeatures" => 'default',
					"BuiltInMicrophone" => '',
					"DeviceHardwareCompatability" => '',
					"DeviceSoftwareCompatability" => '',
					"HeadphoneStyle" => '',
					"InputDeviceDesignStyle" => '',
					"InputDeviceInterfaceTechnology" => '',
					"ModelNumber" => '',
					"NoiseCanceling" => '',
					"NumberOfButtons" => '',
					"PresentationRemoteLaserColor" => '',
					"PresentationRemoteMemory" => '',
					"PresentationRemoteOperatingDistance" => '',
					"PrimaryHeadphoneUse" => '',
					"TrackingMethod" => '',
	            	);
	}

	protected function _Keyboards()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"HandOrientation" => '',
					"InputDeviceDesignStyle" => '',
					"KeyboardDescription" => 'default',
					"ModelNumber" => '',
					"Voltage" => '',
					"Wattage" => '',
					"WirelessInputDeviceProtocol" => '',
					"WirelessInputDeviceTechnology" => '',
	            	);
	}

	protected function _Monitor()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"BuiltinSpeaker" => '',
					"ContrastRatio" => '',
					"DisplayResolutionMaximum" => '',
					"DisplayTechnology" => '',
					"HasColorScreen" => '',
					"ModelNumber" => '',
					"MonitorTunerTechnology" => '',
					"MonitorBrightness" => '',
					"MonitorConnectors" => '',
					"ResponseTime" => '',
					"ScreenResolution" => '',
					"ScreenSize" => '',
					"TunerTechnology" => '',
					"ViewingAngle" => '',
					"Voltage" => '',
					"Wattage" => '',
					"Efficiency" => 'good',	
	            	);
	}

	protected function _Motherboard()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"AdditionalFeatures" => 'default',
					"CPUSocketType" => '',
					"FrontSideBusSpeed" => '',
					"GraphicsCardInterface" => '',
					"HDMIPorts" => '',
					"IntegratedAudioChannels" => '',
					"MaxEthernetSpeed" => '',
					"MaxMemorySupported" => '',
					"MemoryStandard" => '',
					"ModelNumber" => '',
					"MotherboardFormFactor" => '',
					"MultiGPUTechnology" => '',
					"Northbridge" => '',
					"NumberOfeSATAPorts" => '',
					"NumberOfEthernetPorts" => '',
					"NumberOfFireWire400Ports" => '',
					"NumberOfFireWire800Ports" => '',
					"NumberOfIDEPorts" => '',
					"NumberOfMemorySlots" => '',
					"NumberOfPCIExpressSlots" => '',
					"NumberOfSATAPorts" => '',
					"NumberOfUSBPorts" => '',
					"OnboardVideo" => '',
					"SATARAID" => '',
					"SATAStandardsSupported" => '',
					"Southbridge" => '',
					"SPDIFOutput" => '',
					"TypeOfMemorySlot" => '',
					"USBPortType" => '',
					"Voltage" => '',
					"Wattage" => '',
	            	);
	}

	protected function _NetworkingDevice()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"AdditionalFeatures" => 'default',
					"AdditionalFunctionality" => '',
					"IPProtocolStandards" => '',
					"LANPortBandwidth" => '',
					"LANPortNumber" => '',
					"MaxDownstreamTransmissionRate" => '',
					"MaxUpstreamTransmissionRate" => '',
					"ModelNumber" => '',
					"ModemType" => '',
					"NetworkAdapterType" => '',
					"OperatingSystemCompatability" => '',
					"SecurityProtocol" => '',
					"SimultaneousSessions" => '',
					"Voltage" => '',
					"Wattage" => '',
					"WirelessDataTransferRate" => '',
					"WirelessRouterTransmissionBand" => '',
					"WirelessTechnology" => '',					
	            	);
	}

	protected function _RamMemory()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"AdditionalFeatures" => 'default',
					"CasLatency" => '',
					"ComputerMemoryFormFactor" => '',
					"ComputerMemoryTechnology" => '',
					"MaxMemorySpeed" => '',
					"MemoryCapacityPerSTICK" => '',
					"ModelNumber" => '',
					"MultiChannelKit" => '',
					"NumberOfMemorySticks" => '',
					"RAMClockSpeed" => '',
					"Voltage" => '',
					"VoltageRating" => '',
					"Wattage" => '',				
	            	);
	}

	protected function _SoundCard()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"AdditionalFeatures" => 'default',
					"BundledSoftware" => '',
					"Channels" => '',
					"LineIn" => '',
					"ModelNumber" => '',
					"OpticalIn" => '',
					"OpticalOut" => '',
					"SampleRate" => '',
					"SoundCardInterface" => '',
					"SoundCardMinSystemRequirements" => '',
					"SpeakerOut" => '',
					"Voltage" => '',
					"Wattage" => '',			
	            	);
	}

	protected function _Webcam()
	{
            return array(
            		'VariationData' =>array(
            				'Parentage' => 'child',
            				'VariationTheme' => '',
            			),
					"BuiltInMicrophone" => '',
					"CameraType" => 'default',
					"DigitalStillResolution" => '',
					"ImageSensor" => '',
					"InputType" => '',
					"MaxWebcamImageResolution" => '',
					"MinimumSystemRequirements" => '',
					"ModelNumber" => '',
					"NetworkingProtocol" => '',
					"VideoCallingResolution" => '',
					"Voltage" => '',
					"Wattage" => '',
					"WebcamVideoCaptureResolution" => '',
					"WirelessStandard" => '',
	            	);
	}


}
// end of class
