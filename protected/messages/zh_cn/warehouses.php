<?php
/**
 * Language package
 * Automatic translation into Chinese.
 * @author Gordon
 */
return array (
	'Warehouse'                                                 => '仓库', 
    'Warehouse Name'                                            => '仓库名称',  
	'Type'                                                      => '仓库类型',
	'Warehouse District'                              			=> '仓库所在地',
	'Own' 														=> '自有仓库',
	'Third Party'                                               => '第三方仓库',
	'Warehouse Code'											=> '仓库编码',
	'Add a warehouse'                                  		 	=> '新建仓库',
	'Batch delete the warehouses'                 				=> '批量删除仓库',
	'Batch disable the warehouses'								=> '批量停用',
	'Batch open the warehouses' 								=> '批量开启',
	'Basic Information' 										=> '基本资料',
	'Edit the warehouse'										=> '编辑仓库',
	'Sku Print'													=> 'sku打印',
	'Is FBA Tc'													=> 'FBA特采',
	'Is Del'													=> '删除标志',
/*==================================stock in============================================*/
	'Add purchase order receipt'								=> '新建到货记录',
	'Edit purchase order receipt'								=> '修改到货记录',
	'Edit receipt qc qty'										=> '修改QC数量',
	'Stock receipt'												=> '新到货',
	'Stock process'												=> '待处理',
	'Stock Choose'												=> '挑选',
	'Receipt status'											=> '到货进度',
	'Have QC'													=> '已 QC',
	'Stock good'												=> '确认良品',
	'Pack Stock good'                                           => '确认良品',
	'QC And StockIn'                                            => 'QC抽检和良品确认',
	'Stock generated'											=> '生成入库单',
	'Stock In OK'												=> '已入库',
	'Stock In Part'												=> '部分入库',
	'Stock refund'												=> '已退货',
	'Stock Refunding'											=> '退货',
    'Full Check'    											=> '全检',
    'Good Sepcial'												=> '特采',
	'Documentary people'										=> '跟单人',
	'Consignee'													=> '收货人',
	'Recieve Time'												=> '收货时间',
	'Ship Time'													=> '发货时间',
	'Pack Time'													=> '包装时间',
	'Pack User'													=> '包装人',
	'Receive User And Pack User'                                 => '收货人 / 包装人',
	'Pack Info'													=> '包装信息',
	'Express No'												=> '快递单号',
	'QC detail'													=> 'QC明细',
	'For detail'												=> '获取明细',
	'Batch delete the receipt'									=> '批量删除到货记录',
	'Delete the receipt'										=> '删除到货记录',
	'Purchase Num'                                              => '采购数',
	'Check full'												=> '全检',
	'Check part'												=> '抽检',
	'Del default'												=> '正常',
	'Del OK'													=> '删除',
	'Delete failure, Please delete one'							=> '操作失败，请选择单条!',
	'Delete failure, Not receipt status, not delete'			=> '操作失败，未确认到货状态不能删除!',
	'Delete failure, Not qc status, not delete'					=> '操作失败，未QC状态不能删除!',
	'Delete failure, This receipt is to wms, not delete'		=> '此收货单已推入WMS，不能操作！',
	'Delete failure, Not qc, not delete'						=> '此收货单未QC，不能操作！',
	'Operation failure, Please select one'						=> '操作失败，请选择单条!',
/*==================================Set to be processed=================================*/
	'Set pending'												=> '设为待处理',
	'Cancel pending'											=> '取消待处理',
	'Cancel QC'													=> '取消QC',
	'Really want to set these records to be processed?'			=> '确认要设为待处理吗?',
	'Really want to cancel these records to be processed?'		=> '确认要取消待处理吗?',
	'Set successful'											=> '设置成功',
	'Set failure'												=> '设置失败',
	'Cancel successful'											=> '取消成功',
	'Cancel failure'											=> '取消失败',
	'Really want to refund purchase order?'						=> '确认要退货吗?',
	'Refund successful'											=> '退货成功',
	'Refund failure'											=> '退货失败',
	'Refund Qty'												=> '退货数量',
	'Status'													=> '状态',
	'Refund Time'												=> '退货时间',
	'Note'														=> '备注',
	'Status is not process, can not operation'					=> '不全为待处理状态，不能进行操作!',
	'Really want to delete these qc records?'					=> '真的想删除这些已QC记录吗?',
/*==================================QC====================================================*/
	'Documentary people'										=> '跟单人',	
	'QC sampling inspection'									=> 'QC抽检',
	'QC result'													=> 'QC结果',
	'Enter the box number for the poor'							=> '输入框为不良数',
	'QC quality'												=> 'QC记录',
	'QC way'													=> '检验方式',
	'QC No'														=> 'QC 单号',
	'Receipt No'												=> '收货单号',
	'No QC'														=> '未QC',
	'QC Time'													=> 'QC时间',
	'Firt QC Time'												=> '首次 QC时间',
	'QC user'													=> '抽检人',	
'PURCHASE ORDER:{##PURCHASE_ORDER_NO##} corresponding sku:{##SKU##} not QC,please confirm again after QC fine products'	=> '采购单：<font color=red>{##PURCHASE_ORDER_NO##}</font> 对应 sku: <font color=red>{##SKU##}</font> 还没QC，请先QC抽检后再确认良品',
'PURCHASE ORDER:{##PURCHASE_ORDER_NO##} corresponding sku:{##SKU##} have sampling, skip!'	=> '采购单：<font color=red>{##PURCHASE_ORDER_NO##}</font> 对应 sku: <font color=red>{##SKU##}</font> 已抽检过,跳过!',
'PURCHASE ORDER:{##PURCHASE_ORDER_NO##} corresponding sku:{##SKU##} has confirmed that don\'t have to confirm again'	=> '采购单：<font color=red>{##PURCHASE_ORDER_NO##}</font> 对应 sku: <font color=red>{##SKU##}</font> 已确认过，不用再次确认',
	'Purchase order: {order} can not be modified'				=> '采购单：<font color=red>{order}</font> 不能修改，请重新选择',
	'QC bad'													=> 'QC不良',
	'Packing bad'												=> '包装不良',
	'Parcel type'												=> '邮包类型',
	'Maximum number of packages installed'						=> '最大装包数',
	'Max'														=> '最大数',
	'Need to be weighed'										=> '点击称重',
	'No weighing record in a day'								=> '一天之内无称重记录',
	'Sku: {SKU} ,No weighing record in a day'					=> 'Sku: <font color=red>{SKU}</font> ,一天之内无称重记录',
	'Product info'												=> '产品信息',
	'The first weight'											=> '第一次称重(g)',
	'The second weight'											=> '第二次称重(g)',
	'The third weight'											=> '第三次称重(g)',
	'Average weight'											=> '平均重量(g)',
	'Weight record'												=> '称重记录',
	'Weight successful'											=> '称重完成',
	'Weight failure'											=> '称重保存失败',
	'Weight old'												=> '原重',
	'Weight now'												=> '现重',
	'Weight defference'											=> '重量差异',
	'Weight person'												=> '称重人',
	'Weight time'												=> '称重时间',

/*==================================Warehouse Location============================================*/
	'Warehouse Location Code'									=> '库位编号',
	'Warehouse Shelf Code'										=> '货架编号',
	'Warehouse Area Code'										=> '分区编号',
	'Location Type'												=> '库位类型', 
	'Location Order'											=> '库位排序',
	'Pick Location'												=> '拿货',
	'Stock Location'											=> '备货',
	'Add a Warehouse Location'									=> '新增仓库库位',
	'Edit the warehouse location'								=> '修改仓库库位',
	'Warehouse Location'										=> '库位',
	'Warehouse Location Sprint'									=> '库位打印',
	'The Location Code Is Exist Or Is Not The Right Format'		=> '库位号已存在或格式不正确',
	'The Area Is Not Correct'									=> '分区不正确',
	'The Shelf Is Not Correct'									=> '货架不正确',
	'The type of Location'										=> '库位排序类别',
/*==================================Warehouse Area============================================*/	
	'Add a Warehouse Area'										=> '新增仓库分区',
	'Shelf Number'												=> '货架个数',
	'Area Order'												=> '分区排序',
	'Warehouse Area'											=> '仓库分区',
	'Edit the warehouse area'									=> '编辑仓库分区',
	'The Area Code Is Exist'									=> '该分区在此仓库已存在',
	'The Area Order Is Exist in the designated warehouse'		=> '该分区排序编号在此仓库已被占用,<br>同一个仓库里的排序编号必须唯一.',
/*==================================Warehouse Shelf============================================*/	
	'Location Number'											=> '库位个数',
	'Shelf Order'												=> '货架排序',
	'Add a Warehouse Sheld'										=> '新增仓库货架',
	'The Shelf Code Is Exist'									=> '该货架在此分区已存在',
	'Create Location Automatically'								=> '自动创建库位',
	'The Location Generation Rules'								=> '库位生成规则',	
	'The Layer Of Shelf'										=> '货架层数',
	'Qty Record Query'									 		=> '库存记录查询',
	'Product Stockin Query'										=> '产品入库单查询',
	
/*==================================Warehouse Shelf Rules============================================*/
	'Add a Warehouse Shelf Rule'								=> '添加货架规则',
	'Rule Name'													=> '规则名称',
	'Layer Code'												=> '层号',
	'Shelf Location Rules'										=> '货架生成库位规则',
	'Rule Detail'												=> '规则详情',
	'Floor'														=> '层',
	'Create Locations'											=> '生成库位',
/*==================================Generate GRN============================================*/
	'Confirm stockin'											=> '确认入库',
	'Really want to generate GRN?'								=> '确认要生成入库单吗?',
	'Generate GRN'												=> '生成入库单',
	'Stockin qty'												=> '确认良品数',
	'Good qty'													=> '良品数',
	'Receipt qty'												=> '收货数',
	'Receipt qty Stockin qty and bad qty'                       => '到货/良品/不良',
	'QC Ask Qty'												=> '要求QC数',
	'QC Reality Qty'											=> '实际QC数',
	'WMS Reality Qty'											=> '上架数',
	'WMS Not Qty'												=> '未上架',
	'WMS Bad Qty'												=> '不良W',
	'Good Products'												=> '合格',
	'Purchase No: {PurchaseNO} status is not storage state'		=> '采购单：{PurchaseNO} 状态不为可生成入库单状态',
	'Submit the interval time is too short'						=> '提交间隔时间太短',
	'Please select one purchase order no' 						=> '请至少选中一条采购单',
	'Sku: {SKU} not input quality record, please re-enter'		=> 'Sku: <font color=red>{SKU}</font>，未填写品质记录，请重新输入',
	'Sku: {SKU} number less than zero, please re-enter'			=> 'Sku: <font color=red>{SKU}</font>，数量小于零，请重新输入',
	'Bad number less than zero, please re-enter'				=> '不良数小于零，请重新输入',
	'Sku: {SKU} Bad number less than zero, please re-enter'		=> 'Sku: <font color=red>{SKU}</font>，不良数小于零，请重新输入',
	'Sku: {SKU} Qty input is not ok, please re-enter'			=> 'Sku: <font color=red>{SKU}</font>，数量输入不正确，请重新输入',
	'Arrival is zero, please re-enter'							=> '到货数为零，请重新输入',
	'Poor more than the arrival of several'						=> '不良数超过到货数，请重新输入',
	'Sku: {SKU} Poor more than the arrival of several'			=> 'Sku: <font color=red>{SKU}</font>，不良数超过到货数，请重新输入',
	'Sku: {SKU} Have no qc record, not let receipt'				=> 'Sku: <font color=red>{SKU}</font>，有未QC收货记录，请稍后再收货',
	'Cumulative arrival greater than the number of purchases, please re-enter' => '累计到货数大于采购数，请重新输入',
	'Arrival greater than the number of purchases, please re-enter' => '到货数大于采购数，请重新输入',
	'Sku: {SKU} Arrival greater than the number of purchases, please re-enter' => 'Sku: <font color=red>{SKU}</font>，到货数大于采购数，请重新输入',
	'Arrival greater than the number of need, please re-enter' => '到货数大于欠货数，请重新输入',
	'Sku: {SKU} Arrival greater than the number of need, please re-enter' => 'Sku: <font color=red>{SKU}</font>，到货数大于可收数，请重新输入',
	'Sku: {##SKU##} number is greater than the purchase of the arrival of the goods, please input again before submission' 
			=>'sku:<font color=red>{##SKU##}</font> 的这个采购单的到货数大于采购数,请重新输入后再提交',
	'Sku: {SKU} This record is deleted, can not be deleted'		=> 'Sku: <font color=red>{SKU}</font>，已经为删除，不能QC',
	'Sku: {SKU} , Receipt number is not equal to the number of poor' => 'Sku: <font color=red>{SKU}</font>，不良数不等于到货数，请重新输入',
	'Sku: {SKU} , QC qty greater than the arrival of several, please re-enter' => 'Sku: <font color=red>{SKU}</font>，数量大于到货数，请重新输入',
	'Generate GRN success'										=> '生成入库单成功',
	'Generate GRN failure'										=> '生成入库单失败',
	'Contains {value} delivery record'							=> '包含{value}条到货记录',
	'Stock In No.'												=> '入库单号',
	'Stock In'													=> '入库单',
	'Stock In Order Status'										=> '处理状态',
	'Complete User'												=> '入库人',
	'Delivery User'												=> '交货人',	
	'Loading User'												=> '上货人',		
	'Complete Time'												=> '入库时间',
	'Express Information'                               		=> '快递信息[公司/单号]',
	'Receiving Records'                               			=> '收货记录',
	'No receipt records'                               			=> '没有收货记录',
	'Sku: {SKU}, Please input the reason of modify'				=> 'Sku: <font color=red>{SKU}</font>，请填写修改原因',
/*==================================stock in status ============================================*/
	'Purchase order has not been put in storage'				=> '未入库',
	'Purchase order has been put in storage'					=> '已入库',
	'Purchase order has been return'							=> '已退货',
	'Confirm arrival'											=> '确认到货',
	
/*=================================location sku map relation ============================================*/	
	'Inventory'													=> '库存',
	'Add location, SKU relations'								=> '绑定库位',
	'Cancel location, SKU relations'							=> '取消库位绑定',
	'Belong to warehouse'										=> '所属仓库',
	'Confirm to cancel the location with the sku mapping relationship?'	=> '确认要取消该库位与sku的绑定关系吗?<br />注意：只有库存数量为空时才可取消!',
	'Please select a record'									=> '请选择记录',
	'This location has been binding sku, please check it again after binding'=> '该库位已绑定sku,请核对后再进行绑定',	
	'Taking goods location repeatedly'							=> '该sku已绑定一个拿货区，不能再次绑定<br />(同一仓库同一个sku只能绑定一个拿货区位)',	
	'Remove the binding success'								=> '取消绑定成功',
	'Remove the binding failure'								=> '取消绑定失败失败',
	'Only one operation at a receipt'							=> '一次只能操作一条入库单',
	'Please select the stock in no'								=> '请选择入库单号',
	'Sku: {##SKU##} in the warehouse:{##WarehouseName##} not binding in the location, can\'t put in storage'	
			=> 'sku:{##SKU##}在仓库:{##WarehouseName##}里还没有绑定库位，不能入库',
	'Number of put in storage'									=> '入库数',
	'Stock in success'											=> '入库成功',
	'Stock in failure'											=> '入库失败',
	
	'Add warehouse and sku map'									=> '添加仓库sku库存数量',
	'Update warehouse and sku map'								=> '更新仓库sku库存数量',	
/*=================================warehouse record ============================================*/		
	'Last quantity'												=> '上次数量',
	'Change quantity'											=> '改变数量',
	'Quantity'													=> '最后数量',
	'Warehouse Type'											=> '出入库种类',
	'Physical inventory'										=> '实际库存',
	'Available inventory'										=> '可用库存',
	'in-transit inventory'										=> '在途',
	'Order Occupied Inventory'									=> '订单占用库存',
	'The number of arrival'										=> '到货数量',
	'Storage quantity'											=> '入库数量',
	'The number of poor'										=> '不良数量',
	'The number of spare parts'									=> '备品数量',
	'Details Status'											=> '明细状态',
	'Products Details'											=> '产品库存',
	
	'Picking storage location'									=> '拣货库位',
	'Stock in by purchase'										=> '采购入库',
	'Stock in by cancel shipment'								=> '取消发货',
	'Stock in by cancel resent'									=> '取消重寄',
	'Stock in by cancel package'								=> '取消退回',
	'Stock in by return shipment'								=> '邮局退回',
	'Stock in by abroad'										=> '海外仓入库',
	'Stock in by abroad change'									=> '海外仓改为本地仓入库',
	'Stock in by other'											=> '其它入库',
	
	'Stock out by ship'											=> '包裹发货出库',
	'Stock out by import order'									=> '订单导入出库',
	'Stock out by resent package'								=> '重寄包裹的出库',
	'Stock out by foreign trade'								=> '外贸出库',
	'Stock out by loan'											=> '借用出库',
	'Stock out by damaged'										=> '损坏出库',
	'Stock out by special abroad'								=> '特殊包裹',
	'Stock out by abroad'										=> '海外仓出库',
	'Stock out by other'										=> '其它出库',
	'Receipt Date'												=> '收货日期',
	'No data yet'												=> '暂无数据！',
	
	'Asc'														=> '升序',
	'Desc'														=> '倒序',
	'Refresh the order'											=> '刷新库位排序号',
	'Really want to refresh order?'								=> '确认要刷新库位排序号吗？刷新后生成的库位排序号会重新生成',
	'Refresh order successfully'								=> '刷新库位排序号成功',
	'Refresh order fail'										=> '刷新库位排序号失败',
	'Last base position sort number has not been completed'		=> '上次操作还未完成，请稍后再尝试刷新排序',
	
	'Appropriate No'											=> '调拨单号',
	'Stock out no'												=> '未出库',
	'Stock out yes'												=> '已出库未入库',
	'Stock in part'												=> '部分入库',
	'Stock in all'												=> '全部入库',	
	'Bring up the warehouse'									=> '调出仓库',
	'Transferred to the warehouse'								=> '调入仓库',
	'Create appropriate no'										=> '新建调拨单',
	'Cancel appropriate no'										=> '取消调拨单',	
	'Appropriate quantity'										=> '调拨数量',
	'In allocating the number is not greater than the actual location inventory'	=> '调拨数量不能大于该库位实际库存!',
	'Please select warehouse, system will judge whether the sku in the warehouse have enough inventory'	
			=> '请先选择调出仓库,系统会判断该sku在该仓库是否有足够库存供调出!',
	'Please choose the need to allocate goods!'					=> '请选择需要调拨的商品!',
	'Transferred to warehouse not to bring up the same!'		=> '调入仓库不能跟调出仓库相同!',
	'The choice of all the goods in allocating number must be a positive integer'		=> '所选择的所有商品调拨数量必须为大于等于0的整数!',
	
	'Direct shipment'											=> '直接出库',
	'Shipfee'													=> '运费',
	'Note'														=> '备注',
	'Stock in time'												=> '调入时间',
	'Stock out time'											=> '调出时间',
	'Real inventory'											=> '实际库存',
	'Batch delete appropriate no'								=> '批量删除调拨单',
	'Allot to and in is not same' 								=> '调入调出仓库不能相同',
	'Each sku in allocating the number and must be greater than zero' => '每个sku调拨数量和必须大于0',
	'Allocat number must be greater than or equal to 0' 		=> '调拨数量必须大于等于0',	
	'Warehouse number must be greater than or equal to 0' 		=> '入库数量必须大于等于0',
	'Confirm the delivery'										=> '确认出库',
	'Has been put in storage quantity'							=> '已入库数',
	'Beyond the scope of allocating total number of incoming combined'	=> '入库数总和超出调拨总数范围',
	'In allocating storage increase actual inventory, available inventory'	=> '调拨入库增加实际库存、可用库存',
	'In allocating outbound change the outbound warehouse inventory'	=> '调拨出库更改出库仓库库存',
	'Increase in road inventory transfers outbound call in warehouse'	=> '调拨出库调入仓库增加在途库存',
	'Inventory shortage, can not allocate'						=> '库存不足，不能调拨',
	
	'Stock taking No.'											=> '轮盘编号',	
	'StockTaking status'										=> '盘点状态',
	'Start taking Time'											=> '盘点开始时间',
	'End taking Time'											=> '盘点结束时间',
	'Inventory people'											=> '盘点人',
	'Not Start'													=> '未开始',
	'In the inventory'											=> '未盘但已结束',
	'Complete'													=> '已结束',
	'Quantity of sale'											=> '销售量',
	'Price Unit'												=> '产品成本',
	'Jod per day'												=> '日工作量',
	'Day range'													=> '日期范围',
	'Work date'													=> '盘点工作日',
	'Inventory days'											=> '盘点天数',
	'Warehouse man'												=> '仓库工作人员',	
	'Set stocktaking'											=> '设置轮盘计划',
	'Counted quantity'											=> '盘点数量',
	'No Plate Number'											=> '未盘数',
	'Plate Number'												=> '已盘数',
	'Sku Number'												=> 'sku总数',
	'End of the current inventory'								=> '结束本轮盘点',
	'Assigning Task'											=> '分配轮盘任务',
	'Sure you want to allocate the roulette task today?'		=> '确定要分配今天的轮盘任务吗?',
	'The program is running about {value} seconds'				=> '程序运行时间大约{value}秒',
	'For not total disc'                                         => '获取未盘总数',
	'InventoryDetails'                                          => '盘点明细',
	'SKU List'                                                  => 'SKU清单',
	'View Detail'                                               => '查看详细',
	'Create Time'                                               =>'创建时间',
	'SKU'                                               		=>'产品编号',
	'Location Code'                                    		 	=> '库位号',
	'Task Code'                                     			=> '任务编号',
	'Sys Qty'                                     				=> '系统数',
	'Qty'                                     					=> '盘点数',
	'User Full Name'                                    		=> '盘点人',
	'Modify Time'                                     			=> '盘点时间',
	'Location Qty'                                     			=> '原有数',
	'Stock Taking No'                                     		=> '轮盘编号',
	'Differ'                                               		=> '盘点差异',
	'Stock STATUS'                                          	=>'盘点状态',
	
	'Please select a warehouse staff'							=> '请选择仓库工作人员',
	'Please select a inventory'                      			=> '请选择盘点人员',	
	'Please select a warehouse'                      			=> '请选择仓库',
	'Please set the roulette task for task assignment again!'   => '请先设置轮盘任务再进行任务分配!',
	'Today still have unfinished task, please finish before further distribution'    => '今天还有未完成的的任务,请先完成再另行分配',
	'Not arrange inventory date within today, can not be generated task'    => '今天不在安排盘点日期内,不能生成任务',
	'The wheel inventory has been completed'    				=> '该轮盘点已全部完成',
	'Left work'    												=> '剩余工作量',
	'Left sku num'    											=> '剩余sku数',
	'Not Ever'                                  				=> '不限',
    'Number'	                               					=> '数量:', 
//     'Location Type'                             => '区域',
    'Sku Put In Storage'                        				=> '该SKU已入库',
    'Sku Number is Null'                        				=> '入库数量不能为空且不能超过入库单中的数量',
   
 	'Result'                                    				=> '查看结果',
 	'Stock Result'                              				=> '盘点结果',
 	'Assign Num'                                				=> '分配的工作量',
 	'SKU Num'                                   				=> '分配的SKU数量',
 	'Total SKU Num'                             				=> '完成SKU数量',
 	'Total Work'                                				=> '总完成量',
 	'Work Qty'                                  				=> '总工作量',
 	'Stock Sku Num'                             				=> '完成SKU数',
 	'User Work'                                 				=> '完成工作量',
 	'Stock Sku Percent'                         				=> '完成SKU数百分比',
 	'Stock Work Percent'                        				=> '完成工作量百分比',
 	'Total Stock Sku Percent'                   				=> '总完成SKU数百分比',
 	'Total Stock Work Percent'                  				=> '总完成工作量百分比',
 	'Stock Time'                                				=> '盘点时间',
 	'Taking Task Code'                          				=> '盘点任务号', 
 	'Stock User'                                				=> '盘点人',
	
	'Excel File'                                				=> 'excel文件',
	'Purchase Order Refund'                                		=> '退货',
    
    
/*=================================pda/pdastockdetail/list ============================================*/
			
	'The qty of product of the warehouseLocation is not empty' 	=> '库位产品的数量不为0',
	'Product ID can not be empty' 								=> '产品编号不能为空!',
	'Remove location number can not be empty' 					=> '移出库位号不能为空!',
	'In location number can not be empty' 						=> '移入库位号不能为空!',
	'The actual number can not be blank out' 					=> '实际移出数量不能为空!',
	'The actual number can not be blank in' 					=> '移入数量不能为空!',
	'Location Edit successful'                  				=> '库位修改成功',
	'Edit Failure'												=> 'sku绑定库位冲突，修改失败',
	'Purorders not set to be processed'							=>'所选采购单中有的是 确认良品状态,不能不设置待处理',
	'Selected purOrder not QCstatus,not toBe processed'			=>'所选采购单中有不是已QC检验状态,不能不设置待处理',
	'QC sampling of selected products are not, and can not print'=>'所选产品有未QC抽检的,不能打印',
	
	
	'Location Not Bind Sku'                                     => '未绑定SKU',
	'Location Is Bind Sku'                                      => '已绑定SKU',
	'Is Bind Sku'                                               => '库位绑定SKU状态',
	
	'Can Be Update'                                             => '是否更新',
	'delivery time'                                             => '发货时间',
	'Create User'                                               => '创建人',
	'Is FBA Tc'                                                 => 'FBA特采',
		
	
	'This is old system, please do not deal here'				=> '这是老系统遗留数据，请在老系统中完成',
	'System is close, please use the old!'						=> '系统暂时关闭，请使用老系统，谢谢合作',
	
	/*================================= allot ============================================*/
	
	'In allocating the number is not greater than the actual warehouse inventory'	=> '调拨数量超过实际库存' 
	
	
	
);
?>
