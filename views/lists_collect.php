<?=$header ?>
<div class="rowSearch">
	<form action="<?=site_url('service/member/integral/action/lists_collect') ?>" method="get">
	<input type="text" name="keyword" value="<?=$uri_query['keyword'] ?>" class="ipt" placeholder="姓名|卡号|电话" />
	<?=form_combobox('source_type', $source_type_array,$uri_query['source_type'],'','产生积分类型'); ?>
	<label>产生时间：</label>
	<?=form_input(array('name'=>'stime','value'=>$uri_query['stime'],'id'=>'stime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd'})",'size'=>10)); ?> - 
	<?=form_input(array('name'=>'etime','value'=>$uri_query['etime'],'id'=>'etime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd',minDate:'#F{\$dp.\$D(\'stime\')}'})",'size'=>10)); ?>
	<input type="submit" class="Btn searchBtn" value="查 询">
	<?=$actions ?>
	</form>
</div>
<div class="rowTable">
	<table>
		<thead class="pagethead">
			<tr>
				<td>会员姓名</td>
				<td>会员卡号</td>
				<td>会员电话</td>
				<td>所属门店</td>
				<td>附近门店</td>
				<td>积分</td>
				<td>操作</td>
			</tr>
		</thead>
		<tbody>
			<?php if($lists){ ?>
			<?php foreach($lists as $row){ ?>
			<tr>
				<td><?=$row->fullname ?></td>
				<td><?=$row->card_id ?></td>
				<td><?=$row->mobile_phone ?></td>
				<td><?=$row->store_name_str ?></td>
				<td><?=$row->store_name_daogou_str ?></td>
				<td><?=$row->integral_str ?></td>
				<td><div class="operateBox" style="width:auto;"><?=$row->action ?></div></td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="5" style="text-align:right"><b>合计</b></td>
				<td><?=$total->integral_str ?></td>
				<td>-</td>
			</tr>
			<tr>
				<td colspan="5" style="text-align:right"><b>总合计</b></td>
				<td><?=$total_all->integral_str ?></td>
				<td>-</td>
			</tr>
			<?php }else{ ?>
			<tr>
				<td colspan="7">暂无数据！</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<div class="pages"><?=$links ?></div>
<script type="text/javascript" src="/js/business/service/member/member_integral.js"></script>
<script type="text/javascript">
MemberIntegral.event_lists_collect();
</script>
<?=$footer ?>