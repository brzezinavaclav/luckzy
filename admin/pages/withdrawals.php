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
        $('#withdrawals_table').DataTable( {
            columnDefs: [
                { "orderable": false, "targets": 6}
            ]
        } );
    });

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
        <li><a href="?p=withdrawals" class="<?php if (!isset($_GET['c']) || empty($_GET['c'])) echo 'active_'; ?>">All
                currencies</a></li>
        <li><a href="?p=withdrawals&c=btc"
               class="<?php if (isset($_GET['c']) && $_GET['c'] == 'btc') echo 'active_'; ?>">Bitcoin</a></li>
        <li><a href="?p=withdrawals&c=rns3"
               class="<?php if (isset($_GET['c']) && $_GET['c'] == 'rns3') echo 'active_'; ?>">Runescape 3</a></li>
        <li><a href="?p=withdrawals&c=orns"
               class="<?php if (isset($_GET['c']) && $_GET['c'] == 'orns') echo 'active_'; ?>">Oldschool runescape</a>
        </li>
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
            <th>Address/ID</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php

        while ($tx = db_fetch_array($query)) {
            if (db_num_rows(db_query("SELECT `username` FROM `players` WHERE `id`=$tx[player_id] LIMIT 1")) != 0)
                $player = db_fetch_array(db_query("SELECT `username` FROM `players` WHERE `id`=$tx[player_id] LIMIT 1"));
            else $player['username'] = '[unknown]';

            if ($tx['withdrawned']) {
                $status = "Confirmed";
                $actions = '<a title="Delete" href="#" onclick="wr_delete(' . $tx['id'] . ');"><span class="glyphicon glyphicon-trash"></a>';
            } else {
                $status = "Initiated";
                $actions = '<a title="Approve" href="#" onclick="wr_approve(' . $tx['id'] . ');"><span class="glyphicon glyphicon-ok"></a>&nbsp;&nbsp;<a title="Disapprove (return coins to player)" href="#" onclick="wr_deny(' . $tx['id'] . ');"><span class="glyphicon glyphicon-remove"></a>';
            }

            echo '<tr class="vypis_table_obsah">';
            echo '<td><small><small>' . str_replace(' ', '<br>', $tx['time']) . '</small></small></td>';
            echo '<td><small>' . $player['username'] . '</small></td>';
            echo '<td><small>' . $tx['currency'] . '</small></td>';
            echo '<td><small>' . $tx['amount'] . '</small></td>';
            echo '<td><small><small>' . $tx['address'] . '</small></small></td>';
            echo '<td><small>' . $status . '</small></td>';
            echo '<td>' . $actions . '</td>';
            echo '</tr>';

        }
        if (!db_num_rows($query)) echo '<tr><td colspan="5"><i><small>No withdraws.</small></i></td></tr>';
        ?>
        </tbody>
    </table>
</div>