<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>大转盘</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="/css/kinerLottery.css">

    <style>
    	
	h1{
		width: 100%;
		height: 3rem;
		line-height: 3rem;
		font-size: 1.8rem;
		color:#c60;
		text-align: center;
		font-weight: bolder;
	}

    </style>
</head>

<body>

	<h1><?=$lottery->name ?></h1>

    <div id="box" class="box">
        <div class="outer KinerLottery KinerLotteryContent"><img src=<?=$lottery->url?>></div>
        <!-- 大专盘分为三种状态：活动未开始（no-start）、活动进行中(start)、活动结束(completed),可通过切换class进行切换状态，js会根据这3个class进行匹配状态 -->
        <div class="inner KinerLotteryBtn start"></div>
    </div>
    <form id='ajax_form' style="border-style: solid;width:350px;display:none">
    	名字：<span style="font-size:20px">必填*</span><input type='text' name="name"/><br/>
    	电话：<span style="font-size:20px">必填*</span><input type='text' name="phone"/><br/>
    	地址：<input type='text' name="address"/><br/>
    	<input type='button' id="ajax_insert_info_lucky" style="" value="确定">
    </form>
    <script type="text/javascript" src="/js/service/lottery/lottery.js?v=<?=date('YmdHi')?>">
	Lottery.event_lists();
	</script>
    <script src="/js/lottery/zepto.min.js"></script>
    <script src="/js/lottery/kinerLottery.js"></script>
    <script>
    /*var whichAward = function(deg) 
	{
    	 console.log('deg:'+deg);
    	 return ret.data.message;
    }*/
    var prize_grade = 0;
    var lottery_id = 0;
    var message = 'fff';
    var KinerLottery = new KinerLottery({
        rotateNum: 8, //转盘转动圈数
        body: "#box", //大转盘整体的选择符或zepto对象
        direction: 1, //0为顺时针转动,1为逆时针转动

        disabledHandler: function(key) {

            switch (key) {
                case "noStart":
                    alert("活动尚未开始");
                    break;
                case "completed":
                    alert("活动已结束");
                    break;
            }
        },//禁止抽奖时回调

        clickCallback: function() {
			var $this = this;
	        var angle = 0;
			$.post('/service/lottery/action/lists',{ajax_do:1},function(ret){
				if(ret.code==1){
					message = ret.data.message;
					lottery_id = ret.data.lottery_id;
					prize_grade = ret.data.prize_grade;
					angle = ret.data.deg;
				    console.log(ret.data.deg);
					$this.goKinerLottery(angle);
				}
        	},'json');
        	//点击抽奖按钮,再次回调中实现访问后台获取抽奖结果,拿到抽奖结果后显示抽奖画面
        },
        KinerLotteryHandler: function(deg) {
            alert(message);
            if(prize_grade != 0){
				$('#ajax_form').show();
            }else{
                $('#ajax_form').hide();
            }

         }, //抽奖结束回调
    });
    $('#ajax_insert_info_lucky').click(function(){
        alert(prize_grade);
	var $this = $(this);
	$.post('/service/lottery/action/ajax_insert',$.param($('#ajax_form').serializeArray())+"&"+$.param({'prize_grade':prize_grade,'lottery_id':lottery_id}),function(ret){
		if(ret.code==0){
			alert(ret.message);
			}
		if(ret.code==1){
			alert('提交成功');
			window.location.href = '/service/action';
		}
	},'json');
});
    </script>
</body>

</html>