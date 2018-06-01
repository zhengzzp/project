<?=$header?>
</div>
<link rel="stylesheet" type="text/css" href="/js/ueditor/1.4.3/third-party/webuploader/webuploader.css">
<script class="script_webuploader" type="text/javascript" src="/js/ueditor/1.4.3/third-party/webuploader/webuploader.js"></script>
<link rel="stylesheet" href="/css/addpage.css">
<div id="cashCoupon" class="pageRow pd15">
<form id="form_insert">
	<?=form_hidden('ajax_do',1) ?>
	<?=form_hidden('id',$info->id) ?>	
	<div class="rowItem">
		<div class="itemTitle"><label><span class="star">*</span> 转盘标题： </label></div>
		<div class="itemCon"><input type="text" id="" class="ipt ipt200" value="<?=$info->name?>" name="name"></div>
	</div>
	<div class="rowItem">
		<div class="itemTitle"><label><span class="star"></span> 开始时间： </label></div>
		<div class="itemCon"><input type="text" id="" class="ipt ipt200" value="<?=$info->stime?>" name="stime"></div>
	</div>
	<div class="rowItem">
		<div class="itemTitle"><label><span class="star"></span> 结束时间： </label></div>
		<div class="itemCon"><input type="text" id="" class="ipt ipt200" value="<?=$info->etime?>" name="etime"></div>
	</div>
</form>
<div class="pageRow pageBtnBox">
	<button type="button" class="bigBtn submitBtn jbtn_save">完 成</button>
	<button type="reset" class="bigBtn grayBtn">取 消</button>
</div>
</div>
<script type="text/javascript" src="/js/service/lottery/lottery.js?v=<?=date('YmdHi')?>"/></script>
<script type="text/javascript">
Lottery.event_update();
</script>
<?=$footer?>