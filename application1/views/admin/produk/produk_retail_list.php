<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Home</a>
        </li>
        <li class="">Manajemen Produk</li>
        <li class="active">Produk</li>
      </ul><!-- /.breadcrumb -->


    </div>

    <div class="page-content">
      <div class="ace-settings-container" id="ace-settings-container">
        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
          <i class="ace-icon fa fa-cog bigger-130"></i>
        </div>

        <div class="ace-settings-box clearfix" id="ace-settings-box">
          <div class="pull-left width-50">
            <div class="ace-settings-item">
              <div class="pull-left">
                <select id="skin-colorpicker" class="hide">
                  <?php echo $theme_option ?>
                </select>
              </div>
              <span>&nbsp; Choose Skin</span>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
              <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
              <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
              <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
              <label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
              <label class="lbl" for="ace-settings-add-container">
                Inside
                <b>.container</b>
              </label>
            </div>
          </div><!-- /.pull-left -->

          <div class="pull-left width-50">
            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
              <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
              <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
            </div>

            <div class="ace-settings-item">
              <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
              <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
            </div>
          </div><!-- /.pull-left -->
        </div><!-- /.ace-settings-box -->
      </div><!-- /.ace-settings-container -->

      <div class="page-header">
        <h1>
          <?= $produk->nama_produk ? $produk->nama_produk : 'Daftar Produk' ?>
        </h1>
      </div><!-- /.page-header -->

      <div class="row">
        <div class="col-xs-12">
          <!-- PAGE CONTENT BEGINS -->

          <div class="row" style="margin-bottom: 10px">
            <div class="col-md-4">
            </div>
            <div class="col-md-4 text-center">
              <div style="margin-top: 4px" id="message">
                <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
              </div>
            </div>
            <div class="col-md-4 text-right">
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px">
            <div class="col-md-12">
              <?php if ($this->uri->segment(4)) { ?>
                <?php echo anchor(site_url('admin/produk_retail/generate_varian/' . $this->uri->segment(4)), 'Generate Varian', 'class="btn btn-info"'); ?>
              <?php } ?>
              <?php echo anchor(site_url('admin/produk_retail/create/' . $this->uri->segment(4)), $this->uri->segment(4) ? 'Tambah Varian' : 'Tambah Produk', 'class="btn btn-primary"'); ?>
              <?php if ($id_modul == '5') { ?>
                <button type="button" class="btn btn-success" id="btn_import_excel">Import Excel</button>
                <div id="myModal" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form class="form-horizontal" action="<?php echo base_url() ?>admin/produk_retail/import_excel/" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title">Import Excel</h4>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label class="control-label col-md-3 no-padding-right" for="">File</label>
                            <div class="col-md-9">
                              <input type="file" class="form-control" name="file" id="file" required accept=".xlsx" />
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Kirim</button>
                          <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php } ?>
              <?php echo anchor(site_url('admin/migrasi'), 'Import Excel', 'class="btn btn-success"'); ?>
              <?php echo anchor(site_url('admin/produk_retail/excel'), 'Export Excel', 'class="btn btn-inverse"'); ?>
              <?php echo anchor(site_url('admin/produk_retail/word'), 'Export Word', 'class="btn btn-inverse"'); ?>
              <span class="pull-right">
                <a href="<?php echo base_url() ?>admin/produk_retail/edit_semua" class="btn btn-primary">Edit Semua</a>
              </span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="mytable">
              <thead>
                <tr>
                  <th width="20px">No</th>
                  <th>Barcode</th>
                  <th>Kategori</th>
                  <th>Nama Produk</th>
                  <th>Hrg Jual</th>
                            <th>Hrg Grosir</th>
                            <th>Hrg Member</th>
                  <!-- <th>Hrg 4</th> -->
                  <!-- <th>Hrg 5</th>
                  <th>Hrg 6</th> -->
                  <th width="200px">Action</th>
                </tr>
              </thead>
            </table>
          </div>
          <script src="<?php echo base_url('assets/datatables/jquery.dataTables.js') ?>"></script>
          <script src="<?php echo base_url('assets/datatables/dataTables.bootstrap.js') ?>"></script>
          <script type="text/javascript">
            $(document).ready(function() {
              $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
                return {
                  "iStart": oSettings._iDisplayStart,
                  "iEnd": oSettings.fnDisplayEnd(),
                  "iLength": oSettings._iDisplayLength,
                  "iTotal": oSettings.fnRecordsTotal(),
                  "iFilteredTotal": oSettings.fnRecordsDisplay(),
                  "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                  "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
                };
              };

              var t = $("#mytable").dataTable({
                initComplete: function() {
                  var api = this.api();
                  $('#mytable_filter input')
                    .off('.DT')
                    .on('keyup.DT', function(e) {
                      api.search(this.value).draw();
                    });
                },
                oLanguage: {
                  sProcessing: "loading..."
                },
                pageLength: <?php echo $this->session->userdata("page") * 1 ?>,
                paging: true,
                processing: true,
                serverSide: true,
                ajax: {
                  "url": "<?php echo base_url() ?>admin/produk_retail/json/<?php echo $this->uri->segment(4) ? $this->uri->segment(4) : ''  ?>",
                  "type": "POST"
                },
                columns: [{
                    "data": "id_produk_2",
                    "orderable": false
                  }, {
                    "data": "barcode"
                  }, {
                    "data": "nama_kategori"
                  }, {
                    "data": "nama_produk"
                  }, {
                    "data": "harga_1"
                  }, {
                    "data": "harga_2"
                  }, 
                  {
                    "data": "harga_3"
                  }, 
                  // {
                  //   "data": "harga_4"
                  // }, {
                  //   "data": "harga_5"
                  // }, {
                  //   "data": "harga_6"
                  // },
                  {
                    "data": "action",
                    "orderable": false,
                    "className": "text-center",
                    render: function(data) {
                      if (data) return data;
                      return '';
                    }
                  }
                ],
                order: [
                  [0, 'desc']
                ],
                rowCallback: function(row, data, iDisplayIndex) {
                  var info = this.fnPagingInfo();
                  var page = info.iPage;
                  var length = info.iLength;
                  var index = page * length + (iDisplayIndex + 1);
                  $('td:eq(0)', row).html(index);
                  // $('td:eq(5), td:eq(6), td:eq(7), td:eq(8)', row).addClass('text-right');
                  $('td:eq(5), td:eq(6), td:eq(7), td:eq(8)', row).addClass('text-right');
                  $('td:eq(5)', row).html(tandaPemisahTitik(data.harga_1 * 1));
                  $('td:eq(6)', row).html(tandaPemisahTitik(data.harga_2 * 1));
                  // $('td:eq(7)', row).html(tandaPemisahTitik(data.harga_3 * 1));
                  // $('td:eq(8)', row).html(tandaPemisahTitik(data.harga_4 * 1));
                  // $('td:eq(9)', row).html(tandaPemisahTitik(data.harga_5 * 1));
                }
              });
              $('#mytable').on('draw.dt', function() {
                $('select[name=mytable_length]').on('change', function() {
                  var val = $(this).val();
                  document.location = '<?php echo base_url("admin/produk_retail?page='+val+'"); ?>';
                });
              })
            });
          </script>

          <div class="hr hr32 hr-dotted"></div>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.page-content -->
  </div>
</div><!-- /.main-content -->

<?php if ($id_modul == '5') { ?>
  <script>
    jQuery(function($) {
      $("#btn_import_excel").click(function() {
        $("#myModal").modal('show');
      });
    });
  </script>
<?php } ?>