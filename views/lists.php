<?=$header ?>
<?=$actions?>
<div class="rowSearch">
    <form action="<?=site_url('service/store/action/lists') ?>" method="get">
    <input type="text" name="keyword" value="<?=$uri_query['keyword'] ?>" class="row_ipt" placeholder="请输入关键字" />
    <input type="submit" class="Btn searchBtn" value="查询">
    </form>
</div>
<div class="rowTable">
	<table>
		<thead class="pagethead">
			<tr>
				<td>转盘编号</td>
                <td>转盘标题</td>
                <td>是否开启</td>
                <td>时间</td>
                <td>操作</td>
             </tr>
        </thead>
        <tbody>
        <?php if($lists){?>
        <?php foreach($lists as $row){?>
             <tr>
                 <td><?=$row->id?></td>
                 <td><?=$row->name?></td>
                 <td><?=$row->isopen?></td>
                 <td><?=$row->stime.'~'.$row->etime?></td>
                 <td><div class="operatebox"><?=$row->action?></div></td>
             </tr>
        <?php } ?>
        <?php }else{ ?>
             <tr>
                                  
             </tr>
        <?php } ?>                   
         </tbody>
    </table>
</div>
<div class="pages"><?=$links ?></div>
<script type="text/Javascript" src="/js/service/lottery/lottery.js?v=<?=time()?>"></script>
<script type="text/Javascript">
Lottery.event_lists();
</script>
<?=$footer ?>