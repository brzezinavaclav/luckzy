<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();

  $query_=db_query("SELECT `id`,`username`,`balance`,`time_last_active`,`lastip` FROM `players` WHERE `password`!='' ORDER BY `time_created` DESC");

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
  function edit_player(p_id,r_id,p_b,p_a,p_h) {
    var p_alias=prompt('Username:',p_a);
    if (p_alias==null) return false;
    var p_hash=prompt('Hash:',p_h);
    if (p_hash==null) return false;
    var p_bal=prompt('Balance:',p_b);
    if (p_bal==null && typeof(p_bal)!='string') return false;
    if (p_alias!='' && p_hash!='' && p_alias!=null && p_hash!=null && p_bal!=null) {
      $.ajax({
        'url': 'ajax/edit_player.php?_player='+p_id+'&a='+p_alias+'&h='+p_hash+'&b='+p_bal,
        'dataType': "json",
        'success': function(data) {
          $("tr#"+r_id+" td.p__ali").html('<small>'+p_alias+'</small>');
          $("tr#"+r_id+" td.p__hash").html('<small><small>'+p_hash+'</small></small>');
          $("tr#"+r_id+" td.p__bal").html('<small><b>'+p_bal+'</b> Coins</small>');
          $("tr#"+r_id+" a#edit_karos").attr('onclick',"javascript:edit_player("+p_id+",'"+r_id+"','"+p_bal+"','"+p_alias+"','"+p_hash+"');return false;");
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
    <th>Balance</th>
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
    echo '<td class="p__ali"><small>'.$row['username'].'</small></td>';
    echo '<td class="p__bal"><small><b>'.$row['balance'].'</b> Coins</small></td>';
    echo '<td><small><small>'.$row['time_last_active'].'<br><b>IP:</b> '.$row['lastip'].'</small></small></td>';
    echo '<td><a href="#" onclick="javascript:delete_player('.$row['id'].',\'row'.$row_.'\');return false;" title="Delete Player"><img src="./imgs/cross.png" style="width: 16px;"></a>&nbsp;<a href="#" onclick="javascript:edit_player('.$row['id'].',\'row'.$row_.'\',\''.$row['balance'].'\',\''.$row['username'].'\',\''.$row['hash'].'\');return false;" title="Edit Player" id="edit_karos"><img src="./imgs/edit.png" style="width: 16px;"></a></td>';
    echo '</tr>'."\n";
    $row_++;
  }
    
  ?>
  </tbody>
</table>

</div>