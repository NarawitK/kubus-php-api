$(document).ready(function(){
  $.ajax({
      type: 'GET',
      url: 'http://localhost/tramapi/api/tramLocation.php',
      data: { id: 3 },
      dataType: 'json',
      success: function (data) {
        console.log(data);
      }
  });
});
