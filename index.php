<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Coop & Carrefour Scraper</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css?v=1.5">
  <!-- Resources -->

</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <span class="navbar-brand mb-0 h1">Coop & Carrefour Scraper</span>
</nav>
<div class="container-fluid">
  <div class="row filter-row">
    <div class="col-lg-4">
      <button class="btn btn-primary" data-toggle="modal" data-target="#importModal"><i class="fa fa-upload"></i> Import</button>
      <a class="btn btn-primary" href="export.php"><i class="fa fa-download"></i> Export</a>
    </div>
    <div class="col-lg-12 spacer">
      <table class="table table-bordered table-responsive-lg table-hover">
        <thead class="thead-dark">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Store</th>
          <th scope="col">EAN</th>
          <th scope="col">Zipcode</th>
          <th scope="col">Address</th>
          <th scope="col">City</th>
          <th scope="col"><i class="fa fa-globe"></i></th>
        </tr>
        </thead>
        <tbody id="infoTbl">

        </tbody>
      </table>
    </div>

  </div>

  <!-- Modal -->
  <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
       aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Import</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="form">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="col-lg-12">
                  Store:
                  <select name="store" class="form-control">
                    <option value="1">Carrefour</option>
                    <option value="2">Coop</option>
                  </select>
                </div>
                <div class="col-md-12 spacer">
                  <input type="file" name="importFile" id="import-file-holder">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-md pull-right" id="submitBtn">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>


<script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
    getTable();
    $('#form').on('submit',function(e){
      e.preventDefault();
      var formData = new FormData($(this)[0]);
      $.ajax({
        url: 'Controller/api.php?action=import',
        type: 'POST',
        xhr: function() {
          var myXhr = $.ajaxSettings.xhr();
          $('#submitBtn').attr('disabled', true).html('Importing....');
          return myXhr;
        },
        success: function (data) {
          if(data == 1){
            alert('Inputs successfully imported.');
            getTable();
          }else{
            alert('Oops somehting went wrong, please try again.')
          }
          $('#submitBtn').removeAttr('disabled').html('Import');
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
      });
      return false;
    });


    function getTable() {
      $('#infoTbl').html('<tr><td colspan="6"><div class="loader"></div></td></tr>');
      $.ajax({
        url: 'Controller/api.php?action=get-inputs',
        type: 'get',
        dataType: 'json',
        success: function (r) {
          $tbl = '';
          $.each(r, function (index, key) {
            $tbl += '<tr>'
              + '<td>' + (index+1) + '</td>'
              + '<td>' + key.name + '</td>'
              + '<td>' + key.ean + '</td>'
              + '<td>' + (key.zip_code != null ? key.zip_code : '') + '</td>'
              + '<td>' + (key.address != null ? key.address : '' ) + '</td>'
              + '<td>' + (key.city != null ? key.city : '') + '</td>'
              + '<td><a target="_blank" href="' + key.link + '"><i class="fa fa-globe"></i></a></td>'
            '</tr>';
          });

          $('#infoTbl').html($tbl);
        }
      });
    }

  });
</script>
</body>

</html>