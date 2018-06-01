<?=$header ?>
<div class="rowSearch">
    <form action="<?=site_url('service/lottery/action/activity_data') ?>" method="get">
    <label>产生时间：</label>
    <?=form_hidden('id',$uri_query['id']) ?>
	<?=form_input(array('name'=>'stime','value'=>$uri_query['stime'],'id'=>'stime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd'})",'size'=>10)); ?> - 
	<?=form_input(array('name'=>'etime','value'=>$uri_query['etime'],'id'=>'etime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd',minDate:'#F{\$dp.\$D(\'stime\')}'})",'size'=>10)); ?>
    <input type="submit" class="Btn searchBtn" value="查询">
    </form>
</div>
<div class="rowTable">
	<table>
		<thead class="pagethead">
			<tr>
			    <td>时间</td>
				<td>抽奖人数</td>
                <td>一等奖人数</td>
                <td>二等奖人数</td>
                <td>三等奖人数</td>
                <td>四等奖人数</td>
                <td>五等奖人数</td>
                <td>未中奖人数</td>
                <td>提交人数</td>
             </tr>
        </thead>
        <tbody>
        <?php if($lists){?>
        <?php foreach($lists as $row){?>
             <tr>
                 <td><?=$row->lottery_date?></td>
                 <td><?=$row->count_all?></td>
                 <td><?=$row->count_prize_1?></td>
                 <td><?=$row->count_prize_2?></td>
                 <td><?=$row->count_prize_3?></td>
                 <td><?=$row->count_prize_4?></td>
                 <td><?=$row->count_prize_5?></td>
                 <td><?=$row->count_prize_0?></td>
                 <td><?=$row->count_submit?></td>
             </tr>
        <?php } ?>
        <?php }else{ ?>
             <tr>
                 <td colspan="7">暂无数据！</td>                 
             </tr>
        <?php } ?>                   
         </tbody>
    </table>
</div>
<div class="pages"><?=$links ?></div>
<script type="text/Javascript" src="/js/service/lottery/lottery.js?v=<?=time()?>"></script>
<?=$footer ?>