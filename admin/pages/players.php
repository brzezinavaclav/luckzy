<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();

  $query_=db_query("SELECT `id`,`username`,`balance`,`time_last_active`,`lastip`,`email`,`state` FROM `players` WHERE `password`!='' ORDER BY `time_created` DESC");

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
    $('#players_table').DataTable( {
      columnDefs: [
        { "orderable": false, "targets": 4},
        { "searchable": false, "targets": 4}
      ]
    } );
  });
</script>
<h1>Players</h1>
<script type="text/javascript">
  function delete_player(p_id,r_id) {
    if (confirm('Do you really want to delete this player?')) {
      $.ajax({
        'url': 'ajax/delete_player.php?_player='+p_id,
        'dataType': "json",
        'success': function(data) {
          $("tr#"+r_id).remove();
          message('success','Player has been deleted.');
        }
      });
    }
  }
  function edit_player(p_id,r_id,p_e,p_s) {
    var p_email=prompt('Email:',p_e);
    if (p_email==null) return false;
    var p_state=prompt('Status:',p_s);
    if (p_state==null) return false;

    if (p_email!='' && p_state!='') {
      $.ajax({
        'url': 'ajax/edit_player.php?_player='+p_id+'&s='+p_state+'&e='+p_email,
        'dataType': "json",
        'success': function() {
          $("tr#"+r_id+" td.p__mail").html('<small>'+p_email+'</small>');
          $("tr#"+r_id+" td.p__state").html('<small>'+p_state+'</small>');
          $("tr#"+r_id+" a#edit_karos").attr('onclick',"javascript:edit_player("+p_id+",'"+r_id+"'"+p_email+"','"+p_state+"');return false;");
          message('success','Player has been updated.');
        }
      });
    } else message('error',"One of fields has an incorrect value. Please, try again.");
  }
</script>
<div class="zprava" style="margin-top: 20px">
<table class="vypis_table" id="players_table">
  <thead>
  <tr class="vypis_table_head">
    <th>ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Balance</th>
    <th>Status</th>
    <th>Last Access</th>
    <th>Manage</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $row_=0;                   
  while ($row=db_fetch_array($query_)) {
    $row['lastip']=($row['lastip']=='')?'[unknown]':$row['lastip'];
    echo '<tr class="vypis_table_obsah" id="row'.$row_.'">';
    echo '<td><small>'.$row['id'].'</small></td>';
    echo '<td><small>'.$row['username'].'</small></td>';
    echo '<td class="p__mail"><small>'.$row['email'].'</small></td>';
    echo '<td><small><b>'.$row['balance'].'</b> Coins</small></td>';
    echo '<td class="p__state"><small>'.$row['state'].'</small></td>';
    echo '<td><small><small>'.$row['time_last_active'].'<br><b>IP:</b> '.$row['lastip'].'</small></small></td>';
    echo '<td><a href="#" onclick="javascript:delete_player('.$row['id'].',\'row'.$row_.'\');return false;" title="Delete Player"><img src="./imgs/cross.png" style="width: 16px;"></a>&nbsp;<a href="#" onclick="javascript:edit_player('.$row['id'].',\'row'.$row_.'\',\''.$row['email'].'\',\''.$row['state'].'\');return false;" title="Edit Player" id="edit_karos"><img src="./imgs/edit.png" style="width: 16px;"></a></td>';
    echo '</tr>'."\n";
    $row_++;
  }
    
  ?>
  </tbody>
</table>

</div>