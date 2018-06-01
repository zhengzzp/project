<?=$header ?>
<div class="rowSearch">
    <form action="<?=site_url('service/lottery/action/lists_record') ?>" method="get">
    <label>日期：</label>
    <?=form_hidden('id',$uri_query['id']) ?>
	<?=form_input(array('name'=>'stime','value'=>$uri_query['stime'],'id'=>'stime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd'})",'size'=>10)); ?> - 
	<?=form_input(array('name'=>'etime','value'=>$uri_query['etime'],'id'=>'etime','class'=>'Wdate','onfocus'=>"WdatePicker({skin:'default',dateFmt:'yyyy-MM-dd',minDate:'#F{\$dp.\$D(\'stime\')}'})",'size'=>10)); ?>
    <input type="text" name="keyword" value="<?=$uri_query['keyword'] ?>" class="row_ipt" placeholder="请输入关键字" />
    <input type="submit" class="Btn searchBtn" value="查询">
    </form>
</div>
<div class="rowTable">
	<table>
		<thead class="pagethead">
			<tr>
			    <td>id</td>
				<td>奖品等级</td>
                <td>奖品名称</td>
                <td>中奖人姓名</td>
                <td>中奖人手机</td>
                <td>地址</td>
             </tr>
        </thead>
        <tbody>
        <?php if($lists){?>
        <?php foreach($lists as $row){?>
             <tr>
                 <td><?=$row->id?></td>
                 <td><?=$row->prize_grade?></td>
                 <td><?=$row->prize_name?></td>
                 <td><?=$row->name?></td>
                 <td><?=$row->phone?></td>
                 <td><?=$row->address?></td>
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
<script type="text/Javascript">
Lottery.event_lists_record();
</script>
<?=$footer ?>