<link rel="stylesheet" href="./games/slots/styles/main.css">
<script type="text/javascript" src="./games/slots/scripts/main.js"></script>
    <div class="slots">
        <div class="slot slot1"><div class="slotCon"></div></div>
        <div class="slot slot2"><div class="slotCon"></div></div>
        <div class="slot slot3"><div class="slotCon"></div></div>
    </div>
    <div class="control">
        <div class="bRegul">
            <button onclick="wagerDiv();">½</button><!--
           --><button onclick="wagerMultip();">x2</button><!--
           --><button onclick="wagerMax();">MAX</button><!--
           --><button onclick="bot.toggle();" data-toggle="tooltip" data-placement="top" title="AutoSpin" class="tooltips autoBotButton"><div class="autoBotCheck"><span class="glyphicon glyphicon-ok"></span></div></button>
        </div>
        <div class="ybtext">Your Bet:</div>
        <input type="text" class="wager" value="0.00000000">
        <a class="spinbtn btn btn-primary" onclick="javascript:bet();return false;">SPIN</a>
    </div>