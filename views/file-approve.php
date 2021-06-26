<?php
session_start();
$user_id = $_SESSION['user_id'];
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<div>
  Bulk operations<br>
  User_ID:- <input type="number" id="user_id" placeholder="<?php echo $user_id; ?>">
  Operation:- <input type="text" id="operation" placeholder="approve/remove">
  <button onclick="bulk_action()" style="color: red;">!!Operate!!</button><br><br>
</div>

<div>
  <table id="table_id" class="display">
    <thead>
      <tr>
        <th>User ID</th>
        <th>File</th>
        <th>Size</th>
        <th>Dimension</th>
        <th>Time</th>
        <th>IP</th>
        <th>Privacy</th>
        <th>Actions</th>
        <th>Type</th>
      </tr>
    </thead>
  </table>
</div>

<script defer>
  var table = $('#table_id').DataTable({
    processing: true,
    serverSide: true,
    ajax: './tables/get_tables.php?action=scheduled',
    columns: [{
        data: 1
      },
      {
        data: 2,
        render: function(ref_id, a, b) {
          op = `<a href="${ref_id}" target="_blank">${b[3]}</a>`;
          return op;
        }
      },
      {
        data: 4
      },
      {
        data: 5
      },
      {
        data: 6
      },
      {
        data: 7
      },
      {
        data: 8
      },
      {
        data: 9
      },
      {
        data: 0,
        render: function(ref_id, a, b) {
          op = `<button onclick="approve(${ref_id}, '${b[9]}')"><i class="fas fa-check-circle"></i></button><button onclick="remove(${ref_id}, '${b[9]}')"><i class="fas fa-times-circle"></i></button>`;
          return op;
        }
      }
    ]
  });

  function remove(id, type) {
    alert("remove " + id + " of type " + type);
    $.post("actions/remove_" + type + ".php", {
      id: id,
      user_id: user_id
    }, function(result) {
      console.log(result);
      table.ajax.reload();
    });
  }

  function approve(id, type) {
    $.post("actions/approve_" + type + ".php", {
      id: id,
      user_id: user_id
    }, function(result) {
      console.log(result);
      table.ajax.reload();
    });
  }

  function bulk_action() {
    id = $("#user_id").val();
    operation = $("#operation").val();
    if (confirm(`Going to perform ${operation} on user ${id} uplods`)) {
      $.post("actions/bulk_action.php", {
        user_id: user_id,
        id: id,
        operation: operation
      }, function(result) {
        console.log(result);
        table.ajax.reload();
      });
    }
  }
</script>