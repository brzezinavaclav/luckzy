<?php

if (!empty($_GET['confirmDP']) || !empty($_GET['deleteDP'])) {

    if (!empty($_GET['confirmDP'])){
        $m = 'confirm';
        $id = (int)$_GET['confirmDP'];
    }
    else if (!empty($_GET['deleteDP'])) $id = (int)$_GET['deleteDP'];

    $dp_q = db_query("SELECT * FROM `deposits` WHERE `id`=$id LIMIT 1");

    if (db_num_rows($dp_q) != 0) {

        $dp = db_fetch_array($dp_q);
        $currency = '';
        if ($m == 'confirm' && $dp['confirmed'] == 0) {
            if($dp['currency'] == 'btc'){
                $currency = 'btc_balance';
            }
            else{
                $currency = db_fetch_array(db_query("SELECT `currency` FROM `currencies` WHERE `id`=".$dp['currency']." LIMIT 1"));
                $currency = $currency['currency'].'_balance';
            }

            db_query("UPDATE `players` SET `balance`=`balance`+" . $dp['coins_amount'] . ", `$currency`=`$currency`+" . $dp['amount'] . " WHERE `id`=$dp[player_id] LIMIT 1");
            db_query("UPDATE `deposits` SET `confirmed`=1 WHERE `id`=$dp[id] LIMIT 1");

            echo '<div class="zprava zpravagreen"><b>Success:</b> Deposit confirmed.</div>';
        }
        else{
            db_query("DELETE FROM `deposits` WHERE `id`=$dp[id] LIMIT 1");
            echo '<div class="zprava zpravagreen"><b>Success:</b> Record deleted.</div>';
        }
    }
}

if(isset($_GET['c'])) $where = " AND `currency`='".$_GET['c']."'";
$query=db_query("SELECT * FROM `deposits` WHERE `received`=1 $where ORDER BY `time_generated`");

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
        $('#deposits_table').DataTable( {
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
                url: "ajax/upload_screenshot.php?tid="+id+"&type=deposit",
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
                        window.location.href = '?p=deposits&uploaded';
                    }
                    else window.location.href = '?p=deposits&error&message='+data['message'];
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
    function dp_confirm(tid) {
        location.href = './?p=deposits&confirmDP=' + tid;
    }
    function dp_delete(tid) {
        if (!confirm('Do you really want to erase this record?')) return;
        location.href = './?p=deposits&deleteDP=' + tid;
    }
    function upload_screenshot(tid){
        id = tid;
        $('#screenshot').trigger('click');
    }
</script>
<h1>Deposits</h1>
<div class="menu_ menu-horizontal">
    <ul>
        <li><a href="?p=deposits" class="<?php if (!isset($_GET['c']) || empty($_GET['c'])) echo 'active_'; ?>">All currencies</a></li>
        <li><a href="?p=deposits&c=btc" class="<?php if (isset($_GET['c']) && $_GET['c']=='btc') echo 'active_'; ?>">Bitcoin</a></li>


        <?php
        $currencies=db_query("SELECT * FROM `currencies`");
        while ($row=db_fetch_array($currencies)) : ?>
            <li><a href="?p=deposits&c=<?php echo $row['id']; ?>" class="<?php if (isset($_GET['c']) && $_GET['c']== $row['id']) echo 'active_'; ?>"><?php echo $row['currency']; ?></a></li>
        <?php endwhile; ?>

    </ul>
</div>
<div class="zprava" style="margin-top: 20px;">
    <table class="vypis_table" id="deposits_table">
        <thead>
        <tr class="vypis_table_head">
            <th>Time</th>
            <th>Player</th>
            <th>Currency</th>
            <th>Amount</th>
            <th>Coins</th>
            <th><?php if(isset($_GET['c']) && $_GET['c'] == 'btc') echo'Address'; elseif(!isset($_GET['c'])) echo 'Address/ID'; else echo 'Deposit ID'; ?></th>
            <th>Screens</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($dp = db_fetch_array($query)) {
            $shots = '';
            if (db_num_rows(db_query("SELECT `username` FROM `players` WHERE `id`=$dp[player_id] LIMIT 1"))!=0)
                $player=db_fetch_array(db_query("SELECT `username` FROM `players` WHERE `id`=$dp[player_id] LIMIT 1"));
            else $player['username']='[unknown]';

                if($dp['currency'] == 'btc'){
                    $name = 'Bitcoin';
                    if($dp['confirmed'] == 1){
                        $status = 'Confirmed';
                        $actions = '<a title="Delete" href="#" onclick="dp_delete('.$dp['id'].');"><span class="glyphicon glyphicon-trash"></a>';
                    }
                    else $status = 'Waiting for confirmation';
                }
                else {
                    $currency = db_fetch_array(db_query("SELECT `currency` FROM `currencies` WHERE `id`='".$dp['currency']."' LIMIT 1"));
                    $name = $currency['currency'];

                    $actions = '<a style="margin-right: 10px" title="Upload screenshot" href="#" onclick="upload_screenshot('.$dp['id'].')"><span class="glyphicon glyphicon-upload"></a>';

                    $screenshots = db_query("SELECT * FROM `screenshots` WHERE `tid`=".$dp['id']." AND `type`='deposit'");
                    if(db_num_rows($screenshots) != 0){
                        while ($screenshot = db_fetch_array($screenshots)) {
                                $shots .= '<a style="margin-right: 10px" data-title="'.$screenshot['name'].'" href="'.$screenshot['path'].'" data-lightbox="'.$dp['id'].'"><img src="'.$screenshot['path'].'" height="10" width="10"></a>';
                        }
                    }
                    if($dp['confirmed'] == 1) {
                        $status = 'Confirmed';
                        $actions .= '<a title="Delete" href="#" onclick="dp_delete('.$dp['id'].');"><span class="glyphicon glyphicon-trash"></a>';
                    }
                    else{
                        $status = 'Initiated';
                        $actions .= '<a style="margin-right: 10px" title="Confirm" href="#" onclick="dp_confirm('.$dp['id'].');"><span class="glyphicon glyphicon-ok"></a><a title="Delete" href="#" onclick="dp_delete('.$dp['id'].');"><span class="glyphicon glyphicon-trash"></a>';
                    }
                }

            echo '<tr class="vypis_table_obsah" id="row_'.$dp['id'].'">';
            echo '<td><small><small>'.str_replace(' ','<br>',$dp['time_generated']).'</small></small></td>';
            echo '<td><small>'.$player['username'].'</small></td>';
            echo '<td><small>'.$name.'</small></td>';
            echo '<td><small>'.$dp['amount'].'</small></td>';
            echo '<td><small>'.$dp['coins_amount'].'</small></td>';
            echo '<td><small><small>'.$dp['address'].'</small></small></td>';
            echo '<td class="shots">'.$shots.'</td>';
            echo '<td><small>'.$status.'</small></td>';
            echo '<td>'.$actions.'</td>';
            echo '</tr>';
        }
        if (!db_num_rows($query)) echo '<tr><td colspan="5"><i><small>No deposits.</small></i></td></tr>';
        ?>
        <input type="file" id="screenshot" multiple style="display: none">
        </tbody>
    </table>
</div>