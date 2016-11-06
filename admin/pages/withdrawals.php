<?php
if (!empty($_GET['approveTX']) || !empty($_GET['denyTX']) || !empty($_GET['deleteTX'])) {

    if (!empty($_GET['approveTX'])){
        $m = 'approve';
        $id = (int)$_GET['approveTX'];
    }
    else if (!empty($_GET['denyTX'])){
        $m = 'deny';
        $id = (int)$_GET['denyTX'];
    }
    else{
        $m = 'delete';
        $id = (int)$_GET['deleteTX'];
    }

    $tx_q = db_query("SELECT * FROM `withdrawals` WHERE `id`=$id LIMIT 1");

    if (db_num_rows($tx_q) != 0) {

        $tx = db_fetch_array($tx_q);

        if ($m == 'deny') {
            db_query("UPDATE `players` SET `balance`=`balance`+" . $tx['coins_amount'] . " WHERE `id`=$tx[player_id] LIMIT 1");
            db_query("DELETE FROM `withdrawals` WHERE `id`=$tx[id] LIMIT 1");

            echo '<div class="zprava zpravagreen"><b>Success:</b> Withdrawal rejected.</div>';
        }
        else if($m == 'approve'){
            db_query("UPDATE `withdrawals` SET `withdrawned`=1 WHERE `id`=$tx[id] LIMIT 1");
            if($tx['currency'] == 'btc') {
                $amount = (double)$tx['amount'] * 1;
                if ($tx['currency'] == 'btc') $txid = walletRequest('sendtoaddress', array($tx['address'], $amount));
                db_query("INSERT INTO `transactions` (`player_id`,`amount`,`txid`) VALUES ($tx[player_id],($amount*-1),'$txid')");
            echo '<div class="zprava zpravagreen"><b>Success:</b> Withdrawal approved.<br>Transaction ID: <i>' . $txid . '</i></div>';
            }
            else echo '<div class="zprava zpravagreen"><b>Success:</b> Withdrawal approved.</div>';
        }
        else{
            db_query("DELETE FROM `withdrawals` WHERE `id`=$tx[id] LIMIT 1");
            echo '<div class="zprava zpravagreen"><b>Success:</b> Record deleted.</div>';
        }
    }
}


if (isset($_GET['c'])) $where = "WHERE `currency`='" . $_GET['c'] . "'";

$query = db_query("SELECT * FROM `withdrawals` $where ORDER BY `time` DESC");

if(isset($_GET['uploaded'])) echo '<div class="zprava zpravagreen"><b>Success!</b> Data was successfuly saved.</div>';
if(isset($_GET['error'])) echo '<div class="zprava zpravagreen"><b>Error!</b> '.$_GET['message'].'</div>';

?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
<style>
    table.dataTable tbody tr {
        background-color: inherit;
    }
</style>
<script>
    var id;
    $(document).ready(function(){
        $('#withdrawals_table').DataTable( {
            columnDefs: [
                { "orderable": false, "targets": 6},
                { "searchable": false, "targets": 6}
            ],
            "order": [[ 0, "desc" ]]
        } );
        $('#screenshot').change(function(){
            var formData = new FormData();
            for(var i = 0; i <  $(this).get(0).files.length; i++){
                formData.append('file[]', $(this).get(0).files[i]);
            }
            $.ajax({
                url: "ajax/upload_screenshot.php?tid="+id+"&type=withdrawal",
                type: "POST",
                data:  formData,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if(myXhr.upload){
                        myXhr.upload.addEventListener('progress',progress, false);
                    }
                    return myXhr;
                },
                dataType: "json",
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                    if(data['error'] == 'no'){
                        window.location.href = '?p=withdrawals&uploaded';
                    }
                    else window.location.href = '?p=withdrawals&error&message='+data['message'];
                }
            });
        });
    });

    function progress(e){

        if(e.lengthComputable){
            var max = e.total;
            var current = e.loaded;

            var Percentage = (current * 100)/max;
            $('#row_'+id+' .shots').html('<small>'+Percentage+' %</small>');


            if(Percentage >= 100)
            {
            }
        }
    }

    function upload_screenshot(tid){
        id = tid;
        $('#screenshot').trigger('click');
    }

    function wr_approve(tid) {
        if (!confirm('Do you really want to make this payment?')) return;
        location.href = './?p=withdrawals&approveTX=' + tid;
    }
    function wr_deny(tid) {
        location.href = './?p=withdrawals&denyTX=' + tid;
    }
    function wr_delete(tid) {
        if (!confirm('Do you really want to erase this record?')) return;
        location.href = './?p=withdrawals&deleteTX=' + tid;
    }
</script>
<h1>Withdrawals</h1>
<div class="menu_ menu-horizontal">
    <ul>
        <li><a href="?p=withdrawals" class="<?php if (!isset($_GET['c']) || empty($_GET['c'])) echo 'active_'; ?>">All currencies</a></li>
        <li><a href="?p=withdrawals&c=btc" class="<?php if (isset($_GET['c']) && $_GET['c'] == 'btc') echo 'active_'; ?>">Bitcoin</a></li>


        <?php
        $currencies=db_query("SELECT * FROM `currencies`");
        while ($row=db_fetch_array($currencies)) : ?>
            <li><a href="?p=withdrawals&c=<?php echo $row['id']; ?>" class="<?php if (isset($_GET['c']) && $_GET['c']== $row['id']) echo 'active_'; ?>"><?php echo $row['currency']; ?></a></li>
        <?php endwhile; ?>

    </ul>
</div>
<div class="zprava" style="margin-top: 20px;">
    <table class="vypis_table" id="withdrawals_table">
        <thead>
        <tr class="vypis_table_head">
            <th>Time</th>
            <th>Player</th>
            <th>Currency</th>
            <th>Amount</th>
            <th>Coins</th>
            <th>Address/ID</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php

        while ($tx = db_fetch_array($query)) {
            $shots = '';
            if (db_num_rows(db_query("SELECT `username` FROM `players` WHERE `id`=$tx[player_id] LIMIT 1")) != 0)
                $player = db_fetch_array(db_query("SELECT `username` FROM `players` WHERE `id`=$tx[player_id] LIMIT 1"));
            else $player['username'] = '[unknown]';

        if($tx['currency'] == 'btc') {
            $name = 'Bitcoin';
            $actions = '';
        }
        else{
            $currency = db_fetch_array(db_query("SELECT `currency` FROM `currencies` WHERE `id`='".$tx['currency']."' LIMIT 1"));
            $name = $currency['currency'];
            $actions = '<a style="margin-right: 10px" title="Upload screenshot" href="#" onclick="upload_screenshot('.$tx['id'].')"><span class="glyphicon glyphicon-upload"></a>';


            $screenshots = db_query("SELECT * FROM `screenshots` WHERE `tid`=".$tx['id']." AND `type`='withdrawal'");
            if(db_num_rows($screenshots) != 0){
                $actions .= '<a style="margin-right: 10px" title="Screenshots" href="?p=screenshots&tid='.$tx['id'].'&type=withdrawal"><span class="glyphicon glyphicon-camera"></a>';
            }
        }

            if ($tx['withdrawned']) {
                $status = "Confirmed";
                $actions .= '<a title="Delete" href="#" onclick="wr_delete(' . $tx['id'] . ');"><span class="glyphicon glyphicon-trash"></a>';
            } else {
                $status = "Initiated";
                $actions .= '<a title="Approve" href="#" onclick="wr_approve(' . $tx['id'] . ');"><span class="glyphicon glyphicon-ok"></a>&nbsp;&nbsp;<a title="Disapprove (return coins to player)" href="#" onclick="wr_deny(' . $tx['id'] . ');"><span class="glyphicon glyphicon-remove"></a>';
            }

            echo '<tr class="vypis_table_obsah" id="row_'.$tx['id'].'">';
            echo '<td><small><small>' . str_replace(' ', '<br>', $tx['time']) . '</small></small></td>';
            echo '<td><small>' . $player['username'] . '</small></td>';
            echo '<td><small>' . $name . '</small></td>';
            echo '<td><small>' . $tx['amount'] . '</small></td>';
            echo '<td><small>' . $tx['coins_amount'] . '</small></td>';
            echo '<td><small><small>' . $tx['address'] . '</small></small></td>';
            echo '<td><small>' . $status . '</small></td>';
            echo '<td>' . $actions . '</td>';
            echo '</tr>';

        }
        if (!db_num_rows($query)) echo '<tr><td colspan="5"><i><small>No withdraws.</small></i></td></tr>';
        ?>
        <input type="file" id="screenshot" multiple style="display: none">
        </tbody>
    </table>
</div>