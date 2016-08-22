<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();

  $query_=db_query("SELECT `id`,`username`,`balance`,`time_last_active`,`lastip`,`email`,`state`, `password` FROM `players` WHERE `password`!='' ORDER BY `time_created` DESC");

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
  /*function edit_player(p_id,r_id,p_e,p_s) {
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
  }*/
  function edit_player(id, row, email, status, password, username){
    $('#myModal #username').val(username);
    $('#myModal #email').val(email);
    $('#myModal #password').val(password);
    $('#myModal #status option[value="'+status+'"]').attr('selected', 'selected');
    $('#save').attr('onclick', "save_player(this, "+id+",'"+row+"')");
    $('#myModal').modal();
  }
  function save_player(elem, id, row){
    var username = $(elem).parent('div').parent('div').find('#username').val();
    var email = $(elem).parent('div').parent('div').find('#email').val();
    var password = $(elem).parent('div').parent('div').find('#password').val();
    var status = $(elem).parent('div').parent('div').find('#status').val();
    $.ajax({
      'url': 'ajax/edit_player.php?_player='+id+'&s='+status+'&e='+email+'&p='+password+'&u='+username,
      'dataType': "json",
      'success': function() {
        $('#myModal').modal('hide');
        $("tr#"+row+" td.p__username").html('<small>'+username+'</small>');
        $("tr#"+row+" td.p__mail").html('<small>'+email+'</small>');
        $("tr#"+row+" td.p__state").html('<small>'+status+'</small>');
        $("tr#"+row+" a#edit_karos").attr('onclick',"javascript:edit_player("+id+",'"+row+"'"+email+"','"+status+"','"+password+"','"+username+"');return false;");
        message('success','Player has been updated.');
      }
    });
    
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
    echo '<td class="p__username"><small>'.$row['username'].'</small></td>';
    echo '<td class="p__mail"><small>'.$row['email'].'</small></td>';
    echo '<td><small><b>'.$row['balance'].'</b> Coins</small></td>';
    echo '<td class="p__state"><small>'.$row['state'].'</small></td>';
    echo '<td><small><small>'.$row['time_last_active'].'<br><b>IP:</b> '.$row['lastip'].'</small></small></td>';
    echo '<td><a href="#" onclick="javascript:delete_player('.$row['id'].',\'row'.$row_.'\');return false;" title="Delete Player"><img src="./imgs/cross.png" style="width: 16px;"></a>&nbsp;<a href="#" onclick="';
    echo "javascript:edit_player(".$row['id'].",'row".$row_."','".$row['email']."','".$row['state']."','".$row['password']."', '".$row['username']."');return false;";
    echo '" title="Edit Player" id="edit_karos"><img src="./imgs/edit.png" style="width: 16px;"></a></td>';
    echo '</tr>'."\n";
    $row_++;
  }
    
  ?>
  </tbody>
</table>
  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Modal title</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username">
          </div>
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="email" class="form-control" id="email">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password">
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status">
                <option value="0">Pending</option>
                <option value="1">Activated</option>
              </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="save">Save changes</button>
        </div>
      </div>
    </div>
  </div>
</div>