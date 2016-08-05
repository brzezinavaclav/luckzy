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

        if ($m == 'confirm' && $dp['confirmed'] == 0) {
            db_query("UPDATE `players` SET `balance`=`balance`+" . $dp['coins_amount'] . " WHERE `id`=$dp[player_id] LIMIT 1");
            db_query("UPDATE `deposits` SET `confirmed`=1 WHERE `id`=$dp[id] LIMIT 1");

            echo '<div class="zprava zpravagreen"><b>Success:</b> Deposit confirmed.</div>';
        }
        else{
            db_query("DELETE FROM `deposits` WHERE `id`=$dp[id] LIMIT 1");
            echo '<div class="zprava zpravagreen"><b>Success:</b> Record deleted.</div>';
        }
    }
}

if(isset($_GET['c'])) $where .= " AND `currency`='".$_GET['c']."'";
$query=db_query("SELECT * FROM `deposits` WHERE `received`=1 $where ORDER BY `time_generated`");

?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
<style>
    table.dataTable tbody tr {
        background-color: inherit;
    }
</style>
<script>
    $(document).ready(function(){
        $('#deposits_table').DataTable( {
            columnDefs: [
                { "orderable": false, "targets": 6},
                { "searchable": false, "targets": 6}
            ]
        } );
    });
    function dp_confirm(tid) {
        location.href = './?p=deposits&confirmDP=' + tid;
    }
    function dp_delete(tid) {
        if (!confirm('Do you really want to erase this record?')) return;
        location.href = './?p=deposits&deleteDP=' + tid;
    }
</script>
<h1>Deposits</h1>
<div class="menu_ menu-horizontal">
    <ul>
        <li><a href="?p=deposits" class="<?php if (!isset($_GET['c']) || empty($_GET['c'])) echo 'active_'; ?>">All currencies</a></li>
        <li><a href="?p=deposits&c=btc" class="<?php if (isset($_GET['c']) && $_GET['c']=='btc') echo 'active_'; ?>">Bitcoin</a></li>
        <li><a href="?p=deposits&c=rns3" class="<?php if (isset($_GET['c']) && $_GET['c']=='rns3') echo 'active_'; ?>">Runescape 3</a></li>
        <li><a href="?p=deposits&c=orns" class="<?php if (isset($_GET['c']) && $_GET['c']=='orns') echo 'active_'; ?>">Oldschool runescape</a></li>
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
            <th><?php if(isset($_GET['c']) && $_GET['c'] == 'btc') echo'Address'; elseif(!isset($_GET['c'])) echo 'Address/ID'; else echo 'Deposit ID'; ?></th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($dp = db_fetch_array($query)) {
            if (db_num_rows(db_query("SELECT `username` FROM `players` WHERE `id`=$dp[player_id] LIMIT 1"))!=0)
                $player=db_fetch_array(db_query("SELECT `username` FROM `players` WHERE `id`=$dp[player_id] LIMIT 1"));
            else $player['username']='[unknown]';

                if($dp['currency'] == 'btc'){
                    if($dp['confirmed'] == 1){
                        $status = 'Confirmed';
                        $actions = '<a title="Delete" href="#" onclick="dp_delete('.$dp['id'].');"><span class="glyphicon glyphicon-trash"></a>';
                    }
                    else $status = 'Waiting for confirmation';
                }
                else {
                    if($dp['confirmed'] == 1) {
                        $status = 'Confirmed';
                        $actions = '<a title="Delete" href="#" onclick="dp_delete('.$dp['id'].');"><span class="glyphicon glyphicon-trash"></a>';
                    }
                    else{
                        $status = 'Initiated';
                        $actions = '<a title="Confirm" href="#" onclick="dp_confirm('.$dp['id'].');"><span class="glyphicon glyphicon-ok"></a>&nbsp;&nbsp;<a title="Delete" href="#" onclick="dp_delete('.$dp['id'].');"><span class="glyphicon glyphicon-trash"></a>';
                    }
                }

            echo '<tr class="vypis_table_obsah">';
            echo '<td><small><small>'.str_replace(' ','<br>',$dp['time_generated']).'</small></small></td>';
            echo '<td><small>'.$player['username'].'</small></td>';
            echo '<td><small>'.$dp['currency'].'</small></td>';
            echo '<td><small>'.$dp['amount'].'</small></td>';
            echo '<td><small><small>'.$dp['address'].'</small></small></td>';
            echo '<td><small>'.$status.'</small></td>';
            echo '<td>'.$actions.'</td>';
            echo '</tr>';
        }
        if (!db_num_rows($query)) echo '<tr><td colspan="5"><i><small>No deposits.</small></i></td></tr>';
        ?>
        </tbody>
    </table>
</div>