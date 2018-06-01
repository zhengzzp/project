<?=$header?>
</div>
<link rel="stylesheet" href="/css/addpage.css">
<div id="cashCoupon" class="pageRow pd15">
<form id="form_insert">
	<?=form_hidden('ajax_do',1) ?>
	<div class="rowItem">
		<div class="itemTitle"><label><span class="star">*</span> 转盘标题： </label></div>
		<div class="itemCon"><input type="text" id="" class="ipt ipt200" name="name"></div>
	</div>
	<div class="rowItem">
		<div class="itemTitle"><label> <span class="star"></span> 开始时间： </label></div>
		<div class="itemCon"><input type="text" id="" class="ipt ipt200" name="stime"></div>
	</div>
	<div class="rowItem">
		<div class="itemTitle"><label> <span class="star"></span> 结束时间： </label></div>
		<div class="itemCon"><input type="text" id="" class="ipt ipt200" name="etime"></div>
	</div>
</form>
<div class="pageRow pageBtnBox">
	<button type="button" class="bigBtn submitBtn jbtn_save">完 成</button>
	<button type="reset" class="bigBtn grayBtn">取 消</button>
</div>
</div>
<script type="text/javascript" src="/js/service/lottery/lottery.js?v=<?=date('YmdHi')?>"/></script>
<script type="text/javascript">
Lottery.event_insert();
</script>
<?=$footer?>