<?=$header?>
</div>
<link rel="stylesheet" href="/css/addpage.css">
<div id="cashCoupon" class="pageRow pd15">
<form id="form_insert">
	<?=form_hidden('ajax_do',$info['insert']) ?>
	<?=form_hidden('lottery_id',$this->input->get('id')) ?>
	<?php for($i = 0;$i <= 5;$i++){?>
	<div id="rowItem">
		<label><span class="star">*</span> 奖品等级： </label>
		<input type="text" id="" class="ipt ipt200" name="prize_grade[]" value="<?=$info[$i]->prize_grade?>">
		<label> <span class="star">*</span> 奖品名称： </label>
		<input type="text" id="" class="ipt ipt200" name="name[]" value="<?=$info[$i]->name?>">
		<label> <span class="star">*</span> 概率： </label>
		<input type="text" id="" class="ipt ipt200" name="probability[]" value="<?=$info[$i]->probability?>">
		<label> <span class="star">*</span> 消息： </label>
		<input type="text" id="" class="ipt ipt200" name="information[]" value="<?=$info[$i]->information?>">
		<input type="hidden" id="" class="ipt ipt200" name="id[]" value="<?=$info[$i]->id?>">
	</div>
	<br/>
	<?php }?>

</form>
<div class="pageRow pageBtnBox">
	<button type="button" class="bigBtn submitBtn jbtn_save">完 成</button>
	<button type="reset" class="bigBtn grayBtn">取 消</button>
</div>
</div>
<script type="text/javascript" src="/js/service/lottery/lottery.js?v=<?=date('YmdHi')?>"/></script>
<script type="text/javascript">
Lottery.event_setPrize();
</script>
<?=$footer?>