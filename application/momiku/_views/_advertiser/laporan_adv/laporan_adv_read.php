<!doctype html>
<html>
    <head>
        <title>harviacode.com - codeigniter crud generator</title>
        <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>"/>
        <style>
            body{
                padding: 15px;
            }
        </style>
    </head>
    <body>
        <h2 style="margin-top:0px">Laporan_adv Read</h2>
        <table class="table">
	    <tr><td>Tanggal</td><td><?php echo $tanggal; ?></td></tr>
	    <tr><td>Konversi</td><td><?php echo $konversi; ?></td></tr>
	    <tr><td>Klik</td><td><?php echo $klik; ?></td></tr>
	    <tr><td>View</td><td><?php echo $view; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('laporan_adv') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>