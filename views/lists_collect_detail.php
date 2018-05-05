<?=$header ?>
<div class="rowSearch">
	<form action="<?=site_url('service/member/integral/action/lists_collect_detail') ?>" method="get">
	<input type="hidden" name="member_id" value="<?=$uri_query['member_id'] ?>" />
	<input type="hidden" name="source_type" value="<?=$uri_query['source_type'] ?>" />
	<label>产生时间：</label>
	<?=form_input(array('name'=>'stime','value'=>$uri_query['stime'],'id'=>'stime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd'})",'size'=>10)); ?> - 
	<?=form_input(array('name'=>'etime','value'=>$uri_query['etime'],'id'=>'etime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd',minDate:'#F{\$dp.\$D(\'stime\')}'})",'size'=>10)); ?>
	<input type="submit" class="Btn searchBtn" value="查 询">
	</form>
</div>
<div class="rowTable">
	<table>
		<thead class="pagethead">
			<tr>
				<td>时间</td>
				<td>会员姓名</td>
				<td>会员卡号</td>
				<td>产生积分</td>
				<td>剩余积分</td>
				<td>描述</td>
			</tr>
		</thead>
		<tbody>
			<?php if($lists){ ?>
			<?php foreach($lists as $row){ ?>
			<tr>
				<td><?=$row->ctime_str ?></td>
				<td><?=$row->fullname ?></td>
				<td><?=$row->card_id ?></td>
				<td><?=$row->integral_str ?></td>
				<td><?=$row->integral_total_str ?></td>
				<td><?=$row->remark ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="3">合计：</td>
				<td><?=$total->integral_str ?></td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr>
				<td colspan="3">总合计：</td>
				<td><?=$total_all->integral_str ?></td>
				<td>-</td>
				<td>-</td>
			</tr>
			<?php }else{ ?>
			<tr>
				<td colspan="6">暂无数据！</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<div class="pages"><?=$links ?></div>
<?=$footer ?>